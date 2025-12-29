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

                // 2.1 Validar que ese co

                // 3. Crear usuario admin
                $user = new User;
                $user->first_name = $request->first_name;
                $user->last_name = $request->last_name;
                $user->email = $request->email;
                $user->password = $request->password;
                $user->first_login_at = now();
                $user->default_client_id = $client->id;
                $user->assignRole('admin client');
                $user->save();

                // 4. Relación usuario-cliente
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

                // 6. Crear Practitioner si paquete max_users=1
                $practitionerCreated = false;
                if ($package->max_users === 1 && $request->filled('identifier')) {
                    $practitioner = $practitionerService->createOrUpdatePractitioner(
                        $user,
                        $request->all(),
                        [$request->medical_speciality]
                    );

                    $user->assignRole('doctor');
                    $user->removeRole('admin client');
                    $practitionerCreated = true;

                    // Notificar al médico sobre la configuración requerida
                    $user->notify(new PractitionerSetupRequiredNotification);

                    Log::info('Public registration: Practitioner created', [
                        'client_id' => $client->id,
                        'practitioner_id' => $practitioner->id,
                    ]);
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

        return view('public.registration-success', compact('invoicePending'));
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
