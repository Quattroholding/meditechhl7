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

        // Get client IDs without triggering this scope again (prevent infinite recursion)
        $clientIds = $user->clients()->withoutGlobalScopes()->pluck('client_id');

        if(!$user->hasRole('admin')) {
            $builder->whereIn('clients.id', $clientIds);  // Fixed: was 'ids', should be 'id'
        }

    }
}
