<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class PractitionerScope implements Scope
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
            // Filter practitioners by WhatsApp client
            $builder->whereHas('user', function ($q) use ($whatsappClientId) {
                $q->whereHas('clients', function ($q2) use ($whatsappClientId) {
                    $q2->where('client_id', $whatsappClientId);
                });
            });

            return;
        }

        // Skip scope if no authenticated user (e.g., webhooks, API calls)
        if (! $user) {
            return;
        }

        if ($user->hasRole('admin client')
                or $user->hasRole('recepcionista')
                or $user->hasRole('asistente medico')) {  // el doctor solo ve los clientes que tiene asociados
            $builder->whereHas('user', function ($q) use ($user) {
                $q->whereHas('clients', function ($q2) use ($user) {
                    $q2->whereIn('user_clients.client_id', $user->clients()->pluck('client_id'));
                });
            });
        }
    }
}
