<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ConsultingRoomScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $user = auth()->user();

        // Check for WhatsApp client filter first
        $whatsappClientId = request()->attributes->get('whatsapp_client_id');

        if ($whatsappClientId) {
            // Filter consulting rooms by WhatsApp client through branch
            $builder->whereHas('branch', function ($q) use ($whatsappClientId) {
                $q->where('client_id', $whatsappClientId);
            });

            return;
        }

        // Skip scope if no authenticated user (e.g., webhooks, API calls)
        if (! $user) {
            return;
        }

        if ($user->hasRole('doctor')
                or $user->hasRole('recepcionista')
                or $user->hasRole('asistente medico')
                or $user->hasRole('admin client')) {  // el doctor solo ve los clientes que tiene asociados
            $builder->whereHas('branch', function ($q) use ($user) {
                $q->whereIn('client_id', $user->clients()->pluck('client_id'));
            });
        }
    }
}
