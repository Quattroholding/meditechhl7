<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class MedicalSpecialityScope implements Scope
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
            // Filter specialties by practitioners who belong to the WhatsApp client
            $builder->whereHas('practitioners', function ($q) use ($whatsappClientId) {
                $q->whereHas('user', function ($q2) use ($whatsappClientId) {
                    $q2->whereHas('clients', function ($q3) use ($whatsappClientId) {
                        $q3->where('client_id', $whatsappClientId);
                    });
                });
            });

            return;
        }

        // Skip scope if no authenticated user (e.g., webhooks, API calls)
        if (! $user) {
            return;
        }

        // Filter by user's accessible clients
        if ($user->hasRole('recepcionista')
            or $user->hasRole('asistente medico')) {
            $builder->whereHas('practitioners', function ($q) use ($user) {
                $q->whereHas('user', function ($q2) use ($user) {
                    $q2->whereHas('clients', function ($q3) use ($user) {
                        $q3->whereIn('client_id', $user->clients()->pluck('client_id'));
                    });
                });
            });
        }
    }
}
