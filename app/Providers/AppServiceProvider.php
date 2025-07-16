<?php

namespace App\Providers;

use App\Models\Appointment;
use App\Models\Encounter;
use App\Models\Patient;
use App\Observers\AppointmentObserver;
use App\Observers\EncounterObserver;
use App\Policies\AppointmentPolicy;
use App\Policies\ConsultationPolicy;
use App\Policies\PatientPolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
       //Paginator::useTailwind();
       Appointment::observe(AppointmentObserver::class);
       Encounter::observe(EncounterObserver::class);
       Gate::policy(Appointment::class,AppointmentPolicy::class);
       Gate::policy(Patient::class,PatientPolicy::class);
       Gate::policy(Encounter::class,ConsultationPolicy::class);
    }
}
