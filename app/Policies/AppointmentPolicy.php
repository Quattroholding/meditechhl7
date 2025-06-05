<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Access\Response;

class AppointmentPolicy
{

    public function viewConsultation(User $user, Appointment $appointment): bool
    {
        if($user->hasRole('paciente')) return false;

        if($user->hasRole('doctor') && $appointment->practitioner_id == $user->practitioner->id && $appointment->status=='fulfilled')  return true;

        return $appointment->status=='fulfilled';
    }

    public function booked(User $user, Appointment $appointment): bool
    {
        return $appointment->status=='pending' || $appointment->status=='proposed';
    }

    public function arrived(User $user, Appointment $appointment): bool
    {
        return $appointment->status=='booked' && !$user->hasRole('paciente');
    }

    public function checked_in(User $user, Appointment $appointment): bool
    {
        return $appointment->status=='arrived' && !$user->hasRole('paciente');
    }

    public function fulfilled(User $user, Appointment $appointment): bool
    {
        return $appointment->status=='checked-in' && !$user->hasRole('paciente');
    }

    public function cancelled(User $user, Appointment $appointment): bool
    {
        return in_array($appointment->status,['pending','booked','arrived']) && now()->isBefore(Carbon::parse($appointment->start));
    }

    public function noshow(User $user, Appointment $appointment): bool
    {
        return in_array($appointment->status,['pending','booked']) && now()->isAfter(Carbon::parse($appointment->start));
    }

    public function changeStatus(User $user, Appointment $appointment): bool
    {
        return $this->booked($user,$appointment) or $this->arrived($user,$appointment) or
               $this->checked_in($user,$appointment) or   $this->cancelled($user,$appointment) or
               $this->noshow($user,$appointment) or $this->fulfilled($user,$appointment) or
               $this->viewConsultation($user,$appointment);
    }

    public function edit(User $user, Appointment $appointment): bool
    {
        return $this->booked($user,$appointment) or $this->arrived($user,$appointment);
    }

    public function delete(User $user, Appointment $appointment): bool
    {
        return $this->booked($user,$appointment);
    }

}
