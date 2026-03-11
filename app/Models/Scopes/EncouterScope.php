<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class EncouterScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (auth()->user() && (auth()->user()->hasRole('doctor'))) {  // el doctor solo ve sus citas
            $builder->where('encounters.practitioner_id', auth()->user()->practitioner->id);
            $builder->whereHas('patient', function ($q) {
                $q->whereHas('clients', function ($q2) {
                    $q2->whereIn('client_id', auth()->user()->clients()->pluck('client_id'));
                });
            });
        } elseif (auth()->user() && auth()->user()->hasRole('paciente')) { // el paciente ve sus consultas
            $builder->where('encounters.patient_id', auth()->user()->patient->id);
        } elseif (auth()->user() && auth()->user()->hasRole('recepcionista') ||
            auth()->user() && auth()->user()->hasRole('admin client') ||
            auth()->user() && auth()->user()->hasRole('asistente medico')) { // el recepcionista ve todas las citas de los doctores asociados a cu cliente
            $builder->whereHas('appointment', function ($q) {
                $q->whereIn('client_id', auth()->user()->clients()->pluck('client_id'));
            });

        }
    }
}
