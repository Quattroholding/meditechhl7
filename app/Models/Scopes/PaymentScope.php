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
        if (auth()->user() && (auth()->user()->hasRole('doctor'))) {
            // Filter by client_id based on user's associated clients
            $builder->whereHas('invoice',function ($q){
                $q->wherePerformerPractitionerId(auth()->user()->practitioner->id);
            });
        } elseif (auth()->user() && (auth()->user()->hasRole('admin_client') or auth()->user()->hasRole('asistente'))) {
            // Filter by patient - through the patient relationship
            $builder->whereIn('client_id', auth()->user()->clients()->pluck('client_id'));
        } elseif (auth()->user() && auth()->user()->hasRole('paciente')) {
            // Filter by patient - through the patient relationship
            $builder->whereHas('patient', function ($q) {
                $q->where('user_id', auth()->user()->id);
            });
        }
    }
}
