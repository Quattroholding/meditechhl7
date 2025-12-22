<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;

class AppointmentPolicy
{
    /**
     * Determine if user can create appointments based on subscription status
     */
    public function create(User $user): bool
    {
        // Check if user can schedule appointments based on subscription
        return $user->canScheduleAppointments();
    }

    public function viewConsultation(User $user, Appointment $appointment): bool
    {
        if ($user->hasRole('paciente') or $user->hasRole('admin client') or $user->hasRole('recepcionista')) {
            return false;
        }

        if ($user->hasRole('doctor') && $appointment->practitioner_id == $user->practitioner->id && in_array($appointment->status, ['fulfilled', 'checked-in'])) {
            return true;
        }

        return $appointment->status == 'fulfilled';
    }

    public function booked(User $user, Appointment $appointment): bool
    {
        return in_array($appointment->status, ['pending', 'proposed']) && ($user->hasRole('doctor') && $user->practitioner->id == $appointment->practitioner_id);
    }

    public function arrived(User $user, Appointment $appointment): bool
    {
        return in_array($appointment->status, ['booked', 'confirm']) && ! $user->hasRole('paciente') && ! $user->hasRole('admin client');
    }

    public function checked_in(User $user, Appointment $appointment): bool
    {
        return $appointment->status == 'arrived' && ($appointment->practitioner->user_id == $user->id or $user->hasRole('recepcionista'));
    }

    public function fulfilled(User $user, Appointment $appointment): bool
    {
        return $appointment->status == 'checked-in' && ! $user->hasRole('paciente') && ! $user->hasRole('admin client') && ! $user->hasRole('recepcionista');
    }

    public function cancelled(User $user, Appointment $appointment): bool
    {
        return in_array($appointment->status, ['proposed', 'pending', 'booked', 'arrived']) && now()->isBefore(Carbon::parse($appointment->start));
    }

    public function noshow(User $user, Appointment $appointment): bool
    {
        return in_array($appointment->status, ['pending', 'booked']) && now()->isAfter(Carbon::parse($appointment->start)) && ! $user->hasRole('admin client');
    }

    public function changeStatus(User $user, Appointment $appointment): bool
    {
        return $this->booked($user, $appointment) or $this->arrived($user, $appointment) or
               $this->checked_in($user, $appointment) or $this->cancelled($user, $appointment) or
               $this->noshow($user, $appointment) or $this->fulfilled($user, $appointment) or
               $this->viewConsultation($user, $appointment);
    }

    public function edit(User $user, Appointment $appointment): bool
    {
        return $this->booked($user, $appointment) or $this->arrived($user, $appointment);
    }

    public function delete(User $user, Appointment $appointment): bool
    {
        return $this->booked($user, $appointment) && ! $user->hasRole('admin client');
    }

    public function reject(User $user, Appointment $appointment): bool
    {
        return $this->booked($user, $appointment) && ! $user->hasRole('admin client');
    }

    public function addNote(User $user, Appointment $appointment): bool
    {
        return ! $user->hasRole('paciente') && ! $user->hasRole('admin client');
    }
}
