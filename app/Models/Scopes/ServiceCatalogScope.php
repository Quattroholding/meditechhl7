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
            $builder->where('practitioner_id', $user->practitioner->id);
        } elseif ($user->hasRole('recepcionista')
                or $user->hasRole('asistente medico')
                or $user->hasRole('admin client')) {

            $builder->whereIn('client_id', $user->clients()->pluck('client_id'));
        }
    }
}
