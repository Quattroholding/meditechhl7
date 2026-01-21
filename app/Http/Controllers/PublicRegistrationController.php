<?php

namespace App\Http\Controllers;

use App\Http\Requests\PublicRegistrationRequest;
use App\Models\Client;
use App\Models\Package;
use App\Models\User;
use App\Models\UserClient;
use App\Notifications\PractitionerCredentialsNotification;
use App\Notifications\PractitionerSetupRequiredNotification;
use App\Services\FileService;
use App\Services\PractitionerService;
use App\Services\ReferralService;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PublicRegistrationController extends Controller
{
    public function showForm(Request $request)
    {
        $packageId = $request->query('package');
        $packages = Package::where('is_active', true)
            ->where('id', '<>', 4) // Excluir empresarial
            ->orderBy('base_price')
            ->get();

        $selectedPackage = $packageId ? Package::find($packageId) : null;

        return view('public.register', compact('packages', 'selectedPackage'));
    }

    public function store(
        PublicRegistrationRequest $request,
        SubscriptionService $subscriptionService,
        FileService $fileService,
        PractitionerService $practitionerService,
        ReferralService $referralService
    ) {
        try {
            /*
            // Validar Turnstile solo en producción (excepto cuando viene de force_login)
            if (config('app.env') === 'production' && ! $request->has('force_login')) {
                $turnstileResponse = $request->input('cf-turnstile-response');

                if (! $turnstileResponse || ! $this->validateTurnstile($turnstileResponse)) {
                    return back()->withErrors([
                        'cf-turnstile-response' => 'Por favor, completa la verificación de seguridad.',
                    ])->withInput($request->all());
                }
            }
            */

            return DB::transaction(function () use ($request, $subscriptionService, $fileService, $practitionerService, $referralService) {

                // Verificar si es un usuario existente que puede completar registro:
                // 1. Usuario de SAMI Recetas (sin client asociado)
                // 2. Usuario de la app (active=0, registrado pero sin plan)
                $existingUser = User::where('email', $request->email)->first();
                $isExistingUserWithoutClient = $existingUser
                    && ! $existingUser->default_client_id
                    && ! $existingUser->clients()->exists();
                $isInactiveAppUser = $existingUser && ! $existingUser->active;

                // 1. Crear el cliente con valores por defecto
                $client = new Client;
                $prefix = 'Dr';
                if ($request->gender == 'female') {
                    $prefix = 'Dra';
                }
                $client->name = $prefix.' '.$request->first_name.' '.$request->last_name;
                $client->long_name = $prefix.' '.$request->first_name.' '.$request->last_name;

                $client->email = $request->email;
                $client->whatsapp = $request->phone;
                $client->package_id = $request->package_id;

                // Valores por defecto para campos no públicos
                $client->ruc = 'PENDIENTE-'.time();
                $client->dv = 0;
                $client->active = 1;
                $client->save();

                // 1b. Generar código de referido para el nuevo cliente
                $referralCode = $referralService->generateCode($client);

                Log::info('Referral code generated for new client', [
                    'client_id' => $client->id,
                    'referral_code' => $referralCode->code,
                ]);

                // 2. Obtener paquete
                $package = Package::findOrFail($request->package_id);

                // 3. Crear o reutilizar usuario
                if ($isExistingUserWithoutClient || $isInactiveAppUser) {
                    // Usuario existente (SAMI Recetas o app con active=0) - actualizar y reutilizar
                    $user = $existingUser;
                    $user->first_name = $request->first_name;
                    $user->last_name = $request->last_name;
                    $user->password = $request->password;
                    $user->first_login_at = now();
                    $user->default_client_id = $client->id;
                    $user->active = true; // Activar usuario
                    $user->save();

                    Log::info('Public registration: Reusing existing user', [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'client_id' => $client->id,
                        'was_inactive_app_user' => $isInactiveAppUser,
                        'was_sami_recetas_user' => $isExistingUserWithoutClient && ! $isInactiveAppUser,
                    ]);
                } else {
                    // Nuevo usuario
                    $user = new User;
                    $user->first_name = $request->first_name;
                    $user->last_name = $request->last_name;
                    $user->email = $request->email;
                    $user->password = $request->password;
                    $user->first_login_at = now();
                    $user->default_client_id = $client->id;
                    $user->assignRole('admin client');
                    $user->save();
                }

                // 4. Relación usuario-cliente
                // Si es usuario de la app (inactivo), limpiar relaciones anteriores
                if ($isInactiveAppUser) {
                    UserClient::where('user_id', $user->id)->delete();
                    Log::info('Public registration: Cleaned previous user-client relationships', [
                        'user_id' => $user->id,
                    ]);
                }

                $userClient = new UserClient;
                $userClient->user_id = $user->id;
                $userClient->client_id = $client->id;
                $userClient->save();

                // 5. Subir logo si existe
                if ($request->hasFile('logo')) {
                    $filename = 'logo_'.time();
                    $client->logo = $fileService->uploadSingleFile(
                        $request->file('logo'),
                        'clients',
                        $filename
                    );
                    $user->profile_picture = $client->logo;
                    $client->save();
                    $user->save();
                }

                // 6. Crear o actualizar Practitioner si paquete max_users=1
                $practitionerCreated = false;
                if ($package->max_users === 1 && $request->filled('identifier')) {
                    // Verificar si ya tiene practitioner (usuario existente de SAMI Recetas o app)
                    $existingPractitioner = ($isExistingUserWithoutClient || $isInactiveAppUser) ? $user->practitioner : null;

                    if ($existingPractitioner) {
                        // Actualizar practitioner existente con datos del formulario
                        $existingPractitioner->update([
                            'name' =>  $prefix.' '.$request->first_name.' '.$request->last_name,
                            'given_name' => $request->first_name,
                            'family_name' => $request->last_name,
                            'phone' => $request->phone,
                            'email' => $request->email,
                            'gender' => $request->gender,
                            'identifier_type' => $request->identifier_type,
                        ]);

                        // Actualizar especialidad si es diferente
                        if ($request->medical_speciality) {
                            $medicalSpeciality = \App\Models\MedicalSpeciality::find($request->medical_speciality);
                            $existingPractitioner->qualifications()->delete();
                            $existingPractitioner->qualifications()->create([
                                'medical_speciality_id' => $request->medical_speciality,
                                'code' => $request->medical_speciality,
                                'system' => 'http://terminology.hl7.org/CodeSystem/v2-0360',
                                'display' => $medicalSpeciality?->name,
                            ]);
                        }

                        $practitioner = $existingPractitioner;

                        Log::info('Public registration: Updated existing practitioner', [
                            'client_id' => $client->id,
                            'practitioner_id' => $practitioner->id,
                        ]);
                    } else {
                        // Crear nuevo practitioner
                        $practitioner = $practitionerService->createOrUpdatePractitioner(
                            $user,
                            $request->all(),
                            [$request->medical_speciality]
                        );

                        Log::info('Public registration: Practitioner created', [
                            'client_id' => $client->id,
                            'practitioner_id' => $practitioner->id,
                        ]);
                    }

                    // Asignar rol de doctor
                    if (! $user->hasRole('doctor')) {
                        $user->assignRole('doctor');
                    }
                    $user->removeRole('admin client');
                    $practitionerCreated = true;

                    // Notificar al médico sobre la configuración requerida
                    $user->notify(new PractitionerSetupRequiredNotification);
                }

                // 7. Crear suscripción (SIN trial, SIN descuentos)
                $subscription = $subscriptionService->create($client, $package, [
                    'trial_days' => 0,
                    'free_months' => 0,
                    'extra_doctors' => 0,
                    'billing_day' => now()->day,
                ]);

                // 7b. Aplicar código de referido si se proporcionó
                if ($request->filled('referral_code')) {
                    $referral = $referralService->applyReferralCode($client, $request->referral_code);

                    if ($referral) {
                        Log::info('Referral code applied during registration', [
                            'client_id' => $client->id,
                            'referral_code' => $request->referral_code,
                            'referral_id' => $referral->id,
                            'referrer_client_id' => $referral->referrer_client_id,
                        ]);
                    }
                }

                // Enviar notificación con credenciales temporales
                $user->notify(new PractitionerCredentialsNotification($user, $request->password, false));

                Log::info('Public client registration successful', [
                    'client_id' => $client->id,
                    'subscription_id' => $subscription->id,
                    'package' => $package->name,
                    'status' => $subscription->status->value,
                    'practitioner_created' => $practitionerCreated,
                    'existing_sami_recetas_user' => $isExistingUserWithoutClient,
                    'existing_inactive_app_user' => $isInactiveAppUser,
                ]);

                // 8. Redireccionar a success con información de pago
                return redirect()->route('public.register.success')
                    ->with('client_id', $client->id)
                    ->with('invoice_pending', $subscription->status->value === 'pending_activation');
            });

        } catch (\Exception $e) {
            Log::error('Public registration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Hubo un error al procesar tu registro. Por favor intenta nuevamente.');
        }
    }

    public function success()
    {
        if (! session()->has('client_id')) {
            return redirect()->route('welcome');
        }

        $clientId = session('client_id');
        $invoicePending = session('invoice_pending', false);

        // Obtener la factura pendiente para el botón de Yappy
        $pendingInvoice = null;
        if ($invoicePending) {
            $client = Client::find($clientId);
            if ($client) {
                $pendingInvoice = $client->invoices()
                    ->where('status', \App\Enums\InvoiceStatus::PENDING->value)
                    ->orderBy('created_at', 'desc')
                    ->first();
            }
        }

        return view('public.registration-success', compact('invoicePending', 'pendingInvoice'));
    }

    private function validateTurnstile(string $token): bool
    {
        $response = \Illuminate\Support\Facades\Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => config('services.turnstile.secret_key'),
            'response' => $token,
        ]);

        if ($response->successful()) {
            $result = $response->json();

            return $result['success'] ?? false;
        }

        return false;
    }
}
