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
        if ($user->hasAnyRole(['admin', 'asistente medico','registro medico'])) {
            return true;
        } elseif ($user->hasRole(['doctor']) and $user->practitioner->id == $encounter->practitioner_id) {
            return true;
        }

        return false;
    }

    public function edit(User $user, Encounter $encounter)
    {

        if ($user->hasAnyRole('admin', 'asistente medico')) {
            return true;
        } elseif ($user->hasRole(['doctor']) and $user->practitioner->id == $encounter->practitioner_id) {
            return true;
        }

        return false;
    }
}
