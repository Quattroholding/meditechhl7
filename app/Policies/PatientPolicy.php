<?php

namespace App\Policies;

use App\Models\Patient;
use App\Models\User;

class PatientPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function addMedicalHistory(){

    }

    public function profile(User $user,Patient $patient){

        return ($user->hasRole('paciente') && $patient->user_id = $user->id) or
               ($user->hasRole('doctor') && $patient->user_id = $user->id)
            or $user->hasRole('admin');
    }
}
