<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ServiceCatalogScope implements Scope
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

        if ($user->hasRole('doctor')) {
            // Doctor solo ve sus propios servicios
            $builder->where('service_catalog.created_by', $user->id);
        } elseif ($user->hasRole('asistente medico')) {
            // Asistente médico ve servicios de su clínica + los suyos
            $clientIds = $user->clients()->pluck('clients.id');
            $builder->where(function ($q) use ($user, $clientIds) {
                $q->where('service_catalog.created_by', $user->id)
                    ->orWhereIn('service_catalog.client_id', $clientIds);
            });
        } elseif ($user->hasRole('recepcionista') || $user->hasRole('admin client')) {
            $builder->whereIn('service_catalog.client_id', $user->clients()->pluck('id'));
        }
    }
}
