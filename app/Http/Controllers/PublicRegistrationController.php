<?php

namespace App\Http\Controllers;

use App\Enums\InvoiceStatus;
use App\Http\Requests\PublicRegistrationRequest;
use App\Models\Client;
use App\Models\ClientCreditCard;
use App\Models\MedicalSpeciality;
use App\Models\Package;
use App\Models\User;
use App\Models\UserClient;
use App\Notifications\PractitionerCredentialsNotification;
use App\Notifications\PractitionerSetupRequiredNotification;
use App\Services\FileService;
use App\Services\NeoPaymentsService;
use App\Services\PractitionerService;
use App\Services\ReferralService;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
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
        ReferralService $referralService,
        ?NeoPaymentsService $neoPaymentsService = null
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

            return DB::transaction(function () use ($request, $subscriptionService, $fileService, $practitionerService, $referralService, $neoPaymentsService) {

                // Verificar si es un usuario existente que puede completar registro:
                // 1. Usuario de SAMI Recetas (sin client asociado)
                // 2. Usuario de la app (active=0, registrado pero sin plan)
                // 3. Usuario standalone de HemoScreen (migrando a full SAMI)
                $existingUser = User::where('email', $request->email)->first();
                $isExistingUserWithoutClient = $existingUser
                    && ! $existingUser->default_client_id
                    && ! $existingUser->clients()->exists();
                $isInactiveAppUser = $existingUser && ! $existingUser->active;

                // Check if this is a standalone HemoScreen user migrating to full SAMI
                $isStandaloneMigration = $existingUser
                    && $existingUser->practitioner
                    && $existingUser->practitioner->is_standalone;

                if ($isStandaloneMigration) {
                    Log::info('Public registration: Detected standalone HemoScreen user migration', [
                        'user_id' => $existingUser->id,
                        'email' => $existingUser->email,
                        'practitioner_id' => $existingUser->practitioner->id,
                        'old_client_id' => $existingUser->default_client_id,
                    ]);
                }

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
                if ($isExistingUserWithoutClient || $isInactiveAppUser || $isStandaloneMigration) {
                    // Usuario existente (SAMI Recetas, app con active=0, o standalone HemoScreen) - actualizar y reutilizar
                    $user = $existingUser;
                    $user->first_name = $request->first_name;
                    $user->last_name = $request->last_name;
                    $user->password = $request->password;
                    $user->first_login_at = $user->first_login_at ?? now();
                    $user->default_client_id = $client->id;
                    $user->active = true; // Activar usuario
                    $user->whatsapp_phone = $request->phone;

                    // Remove 'hemoscreen' role if migrating from standalone
                    if ($isStandaloneMigration && $user->hasRole('hemoscreen')) {
                        $user->removeRole('hemoscreen');
                    }

                    // Ensure admin client role
                    if (! $user->hasRole('admin client')) {
                        $user->assignRole('admin client');
                    }

                    $user->save();

                    Log::info('Public registration: Reusing existing user', [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'client_id' => $client->id,
                        'was_inactive_app_user' => $isInactiveAppUser,
                        'was_sami_recetas_user' => $isExistingUserWithoutClient && ! $isInactiveAppUser,
                        'was_standalone_hemoscreen' => $isStandaloneMigration,
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
                    $user->whatsapp_phone = $request->phone;
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

                // For standalone migration, keep the old client relationship but add the new one
                if (! $isStandaloneMigration) {
                    $userClient = new UserClient;
                    $userClient->user_id = $user->id;
                    $userClient->client_id = $client->id;
                    $userClient->save();
                } else {
                    // Add new client relationship while keeping old standalone client
                    if (! $user->clients()->where('client_id', $client->id)->exists()) {
                        $userClient = new UserClient;
                        $userClient->user_id = $user->id;
                        $userClient->client_id = $client->id;
                        $userClient->save();

                        Log::info('Public registration: Added new client relationship for standalone migration', [
                            'user_id' => $user->id,
                            'new_client_id' => $client->id,
                            'old_client_id' => $user->practitioner->scb_id ?? null,
                        ]);
                    }
                }

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
                    // Verificar si ya tiene practitioner (usuario existente de SAMI Recetas, app, o standalone)
                    $existingPractitioner = ($isExistingUserWithoutClient || $isInactiveAppUser || $isStandaloneMigration) ? $user->practitioner : null;

                    if ($existingPractitioner) {
                        // Actualizar practitioner existente con datos del formulario
                        $updateData = [
                            'name' => $prefix.' '.$request->first_name.' '.$request->last_name,
                            'given_name' => $request->first_name,
                            'family_name' => $request->last_name,
                            'phone' => $request->phone,
                            'email' => $request->email,
                            'gender' => $request->gender,
                            'identifier_type' => $request->identifier_type,
                        ];

                        // If migrating from standalone, enable full SAMI features
                        if ($isStandaloneMigration) {
                            $updateData['is_standalone'] = false;

                            Log::info('Public registration: Migrating standalone HemoScreen practitioner to full SAMI', [
                                'practitioner_id' => $existingPractitioner->id,
                                'old_client_id' => $existingPractitioner->scb_id,
                                'new_client_id' => $client->id,
                                'standalone_results_count' => $existingPractitioner->standaloneResults()->count(),
                            ]);
                        }

                        $existingPractitioner->update($updateData);

                        // Actualizar especialidad si es diferente
                        if ($request->medical_speciality) {
                            $medicalSpeciality = MedicalSpeciality::find($request->medical_speciality);
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
                            'was_standalone_migration' => $isStandaloneMigration ?? false,
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

                // 7. Aplicar código de referido ANTES de crear la suscripción
                // Esto permite que el descuento esté disponible cuando se genere la primera factura
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

                // 7.5. Tokenize Credit Card (if NeoPayments enabled and card data provided)
                // Payment will be automatically processed when subscription invoice is generated
                if (config('services.neopayments.enabled') && $request->filled('card_number')) {
                    if (! $neoPaymentsService) {
                        $neoPaymentsService = app(NeoPaymentsService::class);
                    }

                    // Create NeoPayments customer
                    $neoCustomer = $neoPaymentsService->createCustomer($client);
                    $client->update(['neopayments_customer_id' => $neoCustomer['id']]);

                    // Tokenize card
                    $cardData = [
                        'card_holder' => $request->input('card_holder'),
                        'card_number' => $request->input('card_number'),
                        'exp_date' => $request->input('exp_month').'/'.$request->input('exp_year'),
                    ];

                    $tokenizedCard = $neoPaymentsService->addCard($neoCustomer['id'], $cardData);

                    // Store card in database
                    $creditCard = new ClientCreditCard;
                    $creditCard->client_id = $client->id;
                    $creditCard->neopayments_customer_id = $neoCustomer['id'];
                    $creditCard->neopayments_card_id = $tokenizedCard['id'] ?? null;
                    $creditCard->card_token = $tokenizedCard['token'];
                    $creditCard->card_holder = $request->input('card_holder');
                    $creditCard->card_last_four = $tokenizedCard['card_last_four'];
                    $creditCard->card_brand = $tokenizedCard['card_brand'] ?? $tokenizedCard['account_type'] ?? null;
                    $creditCard->exp_month = $request->input('exp_month');
                    $creditCard->exp_year = $request->input('exp_year');
                    $creditCard->is_default = true;
                    $creditCard->is_active = true;
                    $creditCard->metadata = $tokenizedCard;
                    $creditCard->save();

                    Log::info('Credit card tokenized during registration', [
                        'client_id' => $client->id,
                        'card_last_four' => $creditCard->card_last_four,
                        'neopayments_customer_id' => $neoCustomer['id'],
                    ]);

                    // Store browser data for 3DS authentication (if available)
                    if ($request->has('browser_data') && config('services.neopayments.3ds_enabled')) {
                        $browserData = $request->input('browser_data');
                        $browserData['ip'] = $request->ip(); // Add real IP from server side

                        // Cache browser data for 5 minutes (will be used during payment processing)
                        Cache::put("neopayments_browser_data_{$client->id}", $browserData, now()->addMinutes(5));

                        Log::info('Browser data cached for 3DS authentication', [
                            'client_id' => $client->id,
                            'has_browser_data' => true,
                        ]);
                    }
                }

                // 8. Crear suscripción (la factura se generará y se intentará pago automático)
                // The subscription service will create the invoice, which will trigger automatic payment
                // If payment fails during registration, it will throw exception and rollback everything
                $subscription = $subscriptionService->create($client, $package, [
                    'trial_days' => 0,
                    'free_months' => 0,
                    'extra_doctors' => 0,
                    'billing_day' => now()->day,
                ]);

                // Check if payment was successful during registration (subscription is active)
                $paymentSuccessfulDuringRegistration = $subscription->status->value === 'active';

                // Check if 3DS challenge is required
                $challengeUrl = session('neopayments_3ds_challenge_url');
                if ($challengeUrl) {
                    Log::info('3DS challenge required, redirecting user', [
                        'client_id' => $client->id,
                        'challenge_url' => $challengeUrl,
                    ]);

                    // Store client_id for after challenge redirect
                    session(['registration_client_id' => $client->id]);

                    // Redirect to 3DS challenge
                    return redirect($challengeUrl);
                }

                // Send consolidated notification if payment was successful, otherwise send credentials only
                $delay = now()->plus(minutes: 1);
                if ($paymentSuccessfulDuringRegistration) {
                    // Payment successful: Send consolidated welcome notification
                    // This is handled by suppressing individual notifications
                    // The credentials notification will be sent, and the SubscriptionActivated and Invoice notifications will be suppressed
                    Log::info('Payment successful during registration - sending consolidated notification', [
                        'client_id' => $client->id,
                        'user_id' => $user->id,
                    ]);
                }

                // Always send credentials notification
                // If payment was successful, the SubscriptionActivatedNotification will include invoice details
                $user->notify((new PractitionerCredentialsNotification($user, $request->password, false))->delay($delay));
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

            // Check if it's a payment-related error
            if (str_contains($e->getMessage(), 'pago') || str_contains($e->getMessage(), 'tarjeta')) {
                $errorMessage = $e->getMessage();
            } else {
                $errorMessage = 'Hubo un error al procesar tu registro. Por favor intenta nuevamente.';
            }

            return redirect()->back()
                ->withInput($request->except(['card_number', 'card_cvv']))
                ->with('error', $errorMessage);
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
                    ->where('status', InvoiceStatus::PENDING->value)
                    ->orderBy('created_at', 'desc')
                    ->first();
            }
        }

        return view('public.registration-success', compact('invoicePending', 'pendingInvoice'));
    }

    /**
     * Handle redirect after 3DS challenge completion
     */
    public function paymentResult()
    {
        $clientId = session('registration_client_id');

        if (! $clientId) {
            return redirect()->route('welcome');
        }

        // Clear the registration client ID from session
        session()->forget('registration_client_id');

        // Check if payment was completed (webhook will have updated payment status)
        $client = Client::find($clientId);

        if ($client) {
            $latestInvoice = $client->invoices()->latest()->first();

            if ($latestInvoice && $latestInvoice->is_paid) {
                // Payment successful
                Log::info('3DS challenge completed successfully', [
                    'client_id' => $clientId,
                    'invoice_id' => $latestInvoice->id,
                ]);

                return redirect()->route('public.register.success')
                    ->with('client_id', $clientId)
                    ->with('invoice_pending', false)
                    ->with('payment_3ds_completed', true);
            }
        }

        // Payment still pending or failed
        Log::info('3DS challenge completed but payment not confirmed yet', [
            'client_id' => $clientId,
        ]);

        return redirect()->route('public.register.success')
            ->with('client_id', $clientId)
            ->with('invoice_pending', true)
            ->with('payment_3ds_pending', true);
    }

    private function validateTurnstile(string $token): bool
    {
        $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
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
