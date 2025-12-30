<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\DB;

class RolScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $user = auth()->user();

        if (!$user) {
            return;
        }

        // Caso DOCTOR
        if ($user->hasRole('admin')) {
            $builder->whereIn('id', [1,3,5,6,7]);
            return;
        }

        // Caso DOCTOR
        if ($user->hasRole('doctor') && $user->practitioner) {
            $builder->whereIn('id', [3, 6]);
            return;
        }

        // Caso ADMIN CLIENT
        if ($user->hasRole('admin client')) {

            $clientId = $user->default_client_id;

            // Roles disponibles en paquete estándar
            $allowedRoles = [
                'doctor' => 2,
                'recepcionista' => 3,
                'asistente medico' => 6,
            ];

            // Buscar roles que ya tienen UN usuario creado para ese cliente
            $existingRoles = DB::table('users')
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->where('users.default_client_id', $clientId)
                ->where('active', true)
                ->whereIn('roles.id', array_values($allowedRoles))
                ->pluck('roles.id')
                ->toArray();

            // Filtrar roles que no existen aún
            $availableRoles = array_diff(array_values($allowedRoles), $existingRoles);

            // Si no queda ningún rol disponible, devolvemos vacío
            if (empty($availableRoles)) {
                $builder->whereIn('id', []);
                return;
            }

            // Mostrar solo roles disponibles
            $builder->whereIn('id', $availableRoles);
        }
    }
}
