<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class AppointmentScope implements Scope
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

        if ($user->hasRole('doctor') && $user->practitioner) {  // el doctor solo ve sus citas
            $builder->where('practitioner_id', $user->practitioner->id);
            $builder->whereHas('patient', function ($q) use ($user) {
                $q->whereHas('clients', function ($q2) use ($user) {
                    $q2->whereIn('client_id', $user->clients()->pluck('client_id'));
                });
            });
        } elseif ($user->hasRole('paciente') && $user->patient->id) { // el recepcionista ve todas las citas de los doctores asociados a cu cliente
            $builder->where('patient_id', $user->patient->id);
        } elseif ($user->hasRole('recepcionista') ||
            $user->hasRole('admin client') ||
            $user->hasRole('asistente medico')) { // el recepcionista ve todas las citas de los doctores asociados a cu cliente
            $builder->whereHas('patient', function ($q) use ($user) {
                $q->whereHas('clients', function ($q2) use ($user) {
                    $q2->whereIn('client_id', $user->clients()->pluck('client_id'));
                });
            })->whereHas('practitioner', function ($q) use ($user) {
                $q->whereHas('user', function ($q2) use ($user) {
                    $q2->whereHas('clients', function ($q3) use ($user) {
                        $q3->whereIn('client_id', $user->clients()->pluck('client_id'));
                    });
                });
            });
        }
    }
}
