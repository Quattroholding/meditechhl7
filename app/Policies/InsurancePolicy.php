<?php

namespace App\Policies;

use App\Models\Insurance;
use App\Models\User;

class InsurancePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('manage insurances');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Insurance $insurance): bool
    {
        return $user->can('manage insurances') && $insurance->client_id === $user->client_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('manage insurances');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Insurance $insurance): bool
    {
        return $user->can('manage insurances') && $insurance->client_id === $user->client_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Insurance $insurance): bool
    {
        return $user->can('manage insurances') && $insurance->client_id === $user->client_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Insurance $insurance): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Insurance $insurance): bool
    {
        return false;
    }
}
