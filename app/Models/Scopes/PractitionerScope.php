<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\DB;

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
            // Filter practitioners by WhatsApp client - Optimized version
            $userIds = DB::table('user_clients')
                ->where('client_id', $whatsappClientId)
                ->pluck('user_id');

            $builder->whereIn('user_id', $userIds);

            return;
        }

        // Skip scope if no authenticated user (e.g., webhooks, API calls)
        if (! $user) {
            return;
        }

        if ($user->hasRole('admin client')
                or $user->hasRole('recepcionista')
                or $user->hasRole('asistente medico')) {  // el doctor solo ve los clientes que tiene asociados
            // Optimized version - Use whereIn instead of nested whereHas
            $clientIds = $user->clients()->pluck('client_id');
            $userIds = DB::table('user_clients')
                ->whereIn('client_id', $clientIds)
                ->pluck('user_id');

            $builder->whereIn('user_id', $userIds);
        }
    }
}
