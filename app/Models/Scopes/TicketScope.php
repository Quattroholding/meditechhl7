<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TicketScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Si no hay usuario autenticado, no aplicar filtros (seeders, comandos)
        if (! auth()->check()) {
            return;
        }

        $user = auth()->user();

        // Admin global ve TODOS los tickets
        if ($user->hasRole('admin')) {
            return;
        }

        // Admin client ve tickets de sus clientes
        if ($user->hasRole('admin client')) {
            $clientIds = $user->clients()->pluck('client_id');
            $builder->whereIn('client_id', $clientIds);

            return;
        }

        // Usuarios normales ven:
        // 1. Tickets creados por ellos
        // 2. Tickets asignados a ellos
        $builder->where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->orWhere('assigned_to', $user->id);
        });

        // Además, filtrar por clientes del usuario
        $clientIds = $user->clients()->pluck('client_id');
        $builder->whereIn('client_id', $clientIds);
    }
}
