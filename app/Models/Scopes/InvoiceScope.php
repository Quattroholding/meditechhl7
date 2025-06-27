<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class InvoiceScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (auth()->user() && (auth()->user()->hasRole('doctor') || auth()->user()->hasRole('asistente'))) {
            // Filter by client_id based on user's associated clients
            $builder->whereIn('client_id', auth()->user()->clients()->pluck('client_id'));
        } elseif (auth()->user() && auth()->user()->hasRole('paciente')) {
            // Filter by patient - through the patient relationship
            $builder->whereHas('patient', function ($q) {
                $q->where('user_id', auth()->user()->id);
            });
        }
    }
}
