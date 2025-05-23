<?php

namespace App\Observers;

use App\Models\Appointment;
use App\Models\AppointmentStatus;

class AppointmentObserver
{
    /**
     * Handle the Appointment "created" event.
     */
    public function created(Appointment $appointment): void
    {
        AppointmentStatus::create([
            'appointment_id' => $appointment->id,
            'status' => $appointment->status,
            'user_id' => auth()->id() // Asume que estás usando autenticación
        ]);
    }

    /**
     * Handle the Appointment "updated" event.
     */
    public function updated(Appointment $appointment): void
    {
        //
    }

    /**
     * Handle the Appointment "updated" event.
     */
    public function updating(Appointment $appointment): void
    {
        if ($appointment->isDirty('status')) {
            AppointmentStatus::create([
                'appointment_id' => $appointment->id,
                'previous_status' => $appointment->getOriginal('status'),
                'status' => $appointment->status,
                'user_id' => auth()->id() // Asume que estás usando autenticación
            ]);
        }
    }

}
