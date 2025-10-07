<?php

namespace App\Policies;

use App\Models\Encounter;
use App\Models\User;

class ConsultationPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function view(User $user, Encounter $encounter)
    {
        return $user->hasRole('admin') or ($user->hasRole('doctor') and $user->practitioner->id == $encounter->practitioner_id);
    }

    public function edit(User $user, Encounter $encounter)
    {
        return $user->hasRole('admin') or ($user->hasRole('doctor') and $user->practitioner->id == $encounter->practitioner_id);
    }
}
