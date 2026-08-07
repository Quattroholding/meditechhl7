<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class SurveyResponsesScope implements Scope
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

        if ($user->hasRole('doctor') or $user->hasRole('recepcionista')
            or $user->hasRole('admin client') or $user->hasRole('asistente medico') or $user->hasRole('registro medico')) {
            $builder->whereIn('client_id', $user->clients()->pluck('client_id'));
        }

    }
}
