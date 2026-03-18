<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class PaymentScope implements Scope
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
            // Filter by client_id based on user's associated clients
            $builder->whereHas('invoice', function ($q) use ($user) {
                $q->wherePerformerPractitionerId($user->practitioner->id);
            });
        } elseif ($user->hasRole('admin_client')
                or $user->hasRole('recepcionista')
                or $user->hasRole('asistente medico')) {
            // Filter by patient - through the patient relationship
            $builder->whereIn('client_id', $user->clients()->pluck('client_id'));
        } elseif ($user->hasRole('paciente')) {
            // Filter by patient - through the patient relationship
            $builder->whereHas('patient', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }
    }
}
