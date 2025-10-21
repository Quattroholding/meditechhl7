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
        $user_id = auth()->id();
        if (empty($user_id)) {
            $user_id = 1;
        }
        AppointmentStatus::create([
            'appointment_id' => $appointment->id,
            'status' => $appointment->status,
            'user_id' => $user_id, // Asume que estás usando autenticación
        ]);
    }

    /**
     * Handle the Appointment "updated" event.
     */
    public function updated(Appointment $appointment): void
    {
        if(auth()->id()) {
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
                'user_id' =>$user_id, // Asume que estás usando autenticación
            ]);
        }
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
                'user_id' => auth()->id(), // Asume que estás usando autenticación
            ]);
        }
    }
}
