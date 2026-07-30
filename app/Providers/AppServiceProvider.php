<?php

namespace App\Providers;

use App\Models\Appointment;
use App\Models\ClientInvoice;
use App\Models\ClientInvoicePayment;
use App\Models\ClientSubscription;
use App\Models\Encounter;
use App\Models\Invoice;
use App\Models\MedicationRequest;
use App\Models\Patient;
use App\Models\User;
use App\Observers\AppointmentObserver;
use App\Observers\ClientInvoiceObserver;
use App\Observers\ClientInvoicePaymentObserver;
use App\Observers\ClientSubscriptionObserver;
use App\Observers\EncounterObserver;
use App\Observers\InvoiceObserver;
use App\Observers\MedicationRequestObserver;
use App\Observers\PatientObserver;
use App\Observers\UserObserver;
use App\Policies\AppointmentPolicy;
use App\Policies\ConsultationPolicy;
use App\Policies\PatientPolicy;
use App\Policies\UserPolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use Spatie\Dropbox\Client as DropboxClient;
use Spatie\FlysystemDropbox\DropboxAdapter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Disable SSL verification for local development only
        if (config('app.env') === 'local') {
            stream_context_set_default([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ],
            ]);
        }

        // Paginator::useTailwind();
        Appointment::observe(AppointmentObserver::class);
        ClientInvoice::observe(ClientInvoiceObserver::class);
        ClientInvoicePayment::observe(ClientInvoicePaymentObserver::class);
        ClientSubscription::observe(ClientSubscriptionObserver::class);
        Encounter::observe(EncounterObserver::class);
        Invoice::observe(InvoiceObserver::class);
        MedicationRequest::observe(MedicationRequestObserver::class);
        Patient::observe(PatientObserver::class);
        User::observe(UserObserver::class);
        Gate::policy(Appointment::class, AppointmentPolicy::class);
        Gate::policy(Patient::class, PatientPolicy::class);
        Gate::policy(Encounter::class, ConsultationPolicy::class);
        Gate::policy(User::class, UserPolicy::class);

        $this->registerDropboxDriver();
    }

    protected function registerDropboxDriver(): void
    {
        Storage::extend('dropbox', function ($app, $config) {
            $client = new DropboxClient($config['authorization_token']);
            $adapter = new DropboxAdapter($client);

            return new Filesystem($adapter, $config);
        });
    }
}
