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
        $user = auth()->user();

        // Skip scope if no authenticated user (e.g., webhooks, API calls)
        if (! $user) {
            return;
        }

        if ($user->hasRole('doctor')) {  // el doctor solo ve sus citas
            $builder->where('encounters.practitioner_id', $user->practitioner->id);
            $builder->whereHas('patient', function ($q) use ($user) {
                $q->whereHas('clients', function ($q2) use ($user) {
                    $q2->whereIn('client_id', $user->clients()->pluck('client_id'));
                });
            });
        } elseif ($user->hasRole('paciente')) { // el paciente ve sus consultas
            $builder->where('encounters.patient_id', $user->patient->id);
        } elseif ($user->hasRole('recepcionista') ||
            $user->hasRole('admin client') ||
            $user->hasRole('asistente medico') ||
            $user->hasRole('registro medico')) { // el recepcionista ve todas las citas de los doctores asociados a cu cliente
            $builder->whereHas('appointment', function ($q) use ($user) {
                $q->whereIn('client_id', $user->clients()->pluck('client_id'));
            });
        }
    }
}
