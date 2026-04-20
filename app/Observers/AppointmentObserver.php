<?php

namespace App\Observers;

use App\Models\Appointment;
use App\Models\AppointmentStatus;
use Illuminate\Support\Facades\Cache;

class AppointmentObserver
{
    /**
     * Handle the Appointment "created" event.
     */
    public function created(Appointment $appointment): void
    {
        $user_id = auth()->id();
        if (empty($user_id)) {
            $user_id = 1;
        }
        AppointmentStatus::create([
            'appointment_id' => $appointment->id,
            'status' => $appointment->status,
            'user_id' => $user_id, // Asume que estás usando autenticación
        ]);

        // Invalidar cache de dashboards
        $this->clearDashboardCache($appointment);
    }

    /**
     * Handle the Appointment "updated" event.
     */
    public function updated(Appointment $appointment): void
    {
        if (auth()->id()) {
            $user_id = auth()->id();
        } else {
            $user_id = $appointment->patient->user_id;
        }

        $user_id = $user_id ?? 1;

        if ($appointment->isDirty('status')) {
            AppointmentStatus::create([
                'appointment_id' => $appointment->id,
                'previous_status' => $appointment->getOriginal('status'),
                'status' => $appointment->status,
                'user_id' => $user_id, // Asume que estás usando autenticación
            ]);
        }

        // Invalidar cache si cambió algo relevante
        $this->clearDashboardCache($appointment);
    }

    /**
     * Handle the Appointment "deleted" event.
     */
    public function deleted(Appointment $appointment): void
    {
        // Invalidar cache cuando se elimina una cita
        $this->clearDashboardCache($appointment);
    }

    /**
     * Clear dashboard cache for appointments
     */
    private function clearDashboardCache(Appointment $appointment): void
    {
        // Limpiar cache tags de appointments
        Cache::tags(['dashboard', 'appointments'])->flush();
        Cache::tags(['doctor_dashboard', 'appointments'])->flush();
        Cache::tags(['doctor_dashboard', 'effectiveness'])->flush(); // Consultation effectiveness
        Cache::tags(['doctor_dashboard', 'charts'])->flush(); // Yearly charts
    }
}
