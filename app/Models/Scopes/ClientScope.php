<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ClientScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $user = auth()->user();

        // Skip scope if no authenticated user (e.g., webhooks, API calls)
        if (! $user) {
            return;
        }

        if ($user->hasRole('doctor') && $user->practitioner) {  // el doctor solo ve los clientes que tiene asociados
            $builder->whereIn('id', $user->clients()->pluck('client_id'));
        } elseif ($user->hasRole('admin client')) {
            $builder->whereIn('id', $user->clients()->pluck('client_id'));
        }
    }
}
