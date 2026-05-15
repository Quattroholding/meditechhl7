<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class SupplyDeliveryScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $user = auth()->user();

        // Skip scope if no authenticated user
        if (! $user) {
            return;
        }

        // Admin can see all supply deliveries
        if ($user->hasRole('admin')) {
            return;
        }

        // Filter by user's accessible clients for other roles
        if ($user->hasRole('doctor') || $user->hasRole('recepcionista')
            || $user->hasRole('admin client') || $user->hasRole('asistente medico')) {
            $builder->whereIn('client_id', $user->clients()->pluck('client_id'));
        }
    }
}
