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

        if (! $user) {
            return;
        }

        // Caso DOCTOR
        if ($user->hasRole('doctor') && $user->practitioner) {
            $clientId = $user->default_client_id;

            // Obtener el paquete del cliente
            $client = DB::table('clients')
                ->join('packages', 'clients.package_id', '=', 'packages.id')
                ->where('clients.id', $clientId)
                ->select('packages.id as package_id', 'packages.max_users', 'packages.max_doctors_included')
                ->first();

            if (! $client) {
                $builder->whereIn('id', []);

                return;
            }

            // Roles que el doctor puede crear
            $allowedRoles = [
                'recepcionista' => 3,
                'asistente medico' => 6,
            ];

            // Determinar si es paquete empresarial
            $isEnterprisePackage = $client->package_id >= 4;

            if ($isEnterprisePackage) {
                // PAQUETE EMPRESARIAL: Validar límite total de usuarios
                $totalUsers = DB::table('users')
                    ->where('default_client_id', $clientId)
                    ->where('active', true)
                    ->count();

                $availableRoles = [];

                // Permitir crear roles si no se ha alcanzado el límite total de usuarios
                if ($totalUsers < $client->max_users) {
                    $availableRoles = [3, 6]; // recepcionista y asistente medico
                }

                if (empty($availableRoles)) {
                    $builder->whereIn('id', []);

                    return;
                }

                $builder->whereIn('id', $availableRoles);
            } else {
                // PAQUETE ESTÁNDAR/PREMIUM: Un usuario por rol
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

            return;
        }

        // Caso ADMIN CLIENT
        if ($user->hasRole('admin client')) {

            $clientId = $user->default_client_id;

            // Obtener el paquete del cliente
            $client = DB::table('clients')
                ->join('packages', 'clients.package_id', '=', 'packages.id')
                ->where('clients.id', $clientId)
                ->select('packages.id as package_id', 'packages.max_users', 'packages.max_doctors_included')
                ->first();

            if (! $client) {
                $builder->whereIn('id', []);

                return;
            }

            // Roles disponibles
            $allowedRoles = [
                'doctor' => 2,
                'recepcionista' => 3,
                'asistente medico' => 6,
            ];

            // Determinar si es paquete empresarial (permite múltiples usuarios del mismo rol)
            // Paquetes 1-3 (Básico, Estándar, Premium): un usuario por rol
            // Paquetes 4+ (Empresarial, Hospital): múltiples usuarios del mismo rol
            $isEnterprisePackage = $client->package_id >= 4;

            if ($isEnterprisePackage) {
                // PAQUETE EMPRESARIAL: Validar límites de doctores y total de usuarios

                // Contar doctores actuales
                $currentDoctors = DB::table('users')
                    ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                    ->where('users.default_client_id', $clientId)
                    ->where('active', true)
                    ->where('model_has_roles.role_id', 2) // rol doctor
                    ->count();

                // Contar total de usuarios activos
                $totalUsers = DB::table('users')
                    ->where('default_client_id', $clientId)
                    ->where('active', true)
                    ->count();

                // Determinar roles disponibles
                $availableRoles = [];

                // Permitir doctor si no se ha alcanzado el límite
                if ($currentDoctors < $client->max_doctors_included) {
                    $availableRoles[] = 2; // doctor
                }

                // Permitir otros roles si no se ha alcanzado el límite total de usuarios
                if ($totalUsers < $client->max_users) {
                    $availableRoles[] = 3; // recepcionista
                    $availableRoles[] = 6; // asistente medico
                }

                // Si no hay roles disponibles, mostrar vacío
                if (empty($availableRoles)) {
                    $builder->whereIn('id', []);

                    return;
                }

                $builder->whereIn('id', array_unique($availableRoles));
            } else {
                // PAQUETE ESTÁNDAR: Un usuario por rol (lógica original)

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
}
