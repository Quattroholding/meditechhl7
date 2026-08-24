<?php

namespace App\Models\Scopes;

use App\Models\Client;
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

        // If querying the Client model, filter by 'id' instead of 'client_id'
        if ($model instanceof Client) {
            if (! $user->hasRole('admin')) {
                $clientIds = $user->clients()->withoutGlobalScopes()->pluck('clients.id');
                $builder->whereIn('clients.id', $clientIds);
            }

            return;
        }

        // Get client IDs without triggering this scope again (prevent infinite recursion)
        $clientIds = $user->clients()->withoutGlobalScopes()->pluck('clients.id');

        if (! $user->hasRole('admin')) {
            $builder->whereIn($model->getTable().'.client_id', $clientIds);
        }

    }
}
