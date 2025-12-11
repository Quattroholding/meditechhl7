<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function create(User $user)
    {
        return $user->getCurrentClient()->package->max_users > ($user->getCurrentClient()->users()->role('recepcionista')->count() +
                $user->getCurrentClient()->users()->role('admin client')->count() + $user->getCurrentClient()->users()->role('doctor')->count() + $user->getCurrentClient()->users()->role('asistente medico')->count());
    }
}
