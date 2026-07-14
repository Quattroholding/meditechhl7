<?php

namespace App\Policies;

use App\Models\Encounter;
use App\Models\User;
use Carbon\Carbon;

class ConsultationPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function view(User $user, Encounter $encounter)
    {
        if ($user->hasAnyRole(['admin', 'asistente medico', 'registro medico'])) {
            return true;
        } elseif ($user->hasRole(['doctor']) and $user->practitioner->id == $encounter->practitioner_id) {
            return true;
        }

        return false;
    }

    public function edit(User $user, Encounter $encounter)
    {
        // Admin y asistente médico siempre pueden editar
        if ($user->hasAnyRole(['admin', 'asistente medico'])) {
            return true;
        }

        // Doctor puede editar solo si es su encuentro
        if ($user->hasRole(['doctor']) && $user->practitioner->id == $encounter->practitioner_id) {
            // Si el status es "finished", verificar si pasaron más de 5 días
            if ($encounter->getRawOriginal('status') === 'finished') {
                $finishedRecord = $encounter->statusHistory()
                    ->where('status', 'finished')
                    ->latest('created_at')
                    ->first();

                if ($finishedRecord && Carbon::parse($finishedRecord->created_at)->addDays(60)->isPast()) {
                    // Pasaron más de 5 días desde que se finalizó, no permite editar
                    return false;
                }
            }

            return true;
        }

        return false;
    }
}
