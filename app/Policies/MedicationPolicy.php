<?php

namespace App\Policies;

use App\Models\Medication;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MedicationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Medication $medication): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Medication $medication): Response
    {
        // Check if user has permission to edit medicines
        if (! $user->can('medicines.edit')) {
            return Response::deny('No tienes permiso para editar medicamentos.');
        }

        // Check if medication is custom
        if ($medication->code_system !== 'CUSTOM') {
            return Response::deny('Solo se pueden actualizar medicamentos personalizados.');
        }

        // Check if medication is associated with any prescriptions
        if ($medication->medicationRequests()->exists()) {
            return Response::deny('No se puede actualizar un medicamento que ya está asociado a recetas médicas.');
        }

        if ($user->id !== $medication->created_by) {
            return Response::deny('No se puede actualizar un medicamento que no fue creado por usted.');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can delete the model.
     *
     * A medication can only be deleted if:
     * 1. It's a custom medication (not from standard catalog)
     * 2. User has permission to delete medicines
     * 3. It's not associated with any prescriptions
     */
    public function delete(User $user, Medication $medication): Response
    {
        // Check if user has permission to delete medicines
        if (! $user->can('medicines.delete')) {
            return Response::deny('No tienes permiso para eliminar medicamentos.');
        }

        // Check if medication is custom
        if ($medication->code_system !== 'CUSTOM') {
            return Response::deny('Solo se pueden eliminar medicamentos personalizados.');
        }

        // Check if medication is associated with any prescriptions
        if ($medication->medicationRequests()->exists()) {
            return Response::deny('No se puede eliminar un medicamento que ya está asociado a recetas médicas.');
        }

        if ($user->id !== $medication->created_by) {
            return Response::deny('No se puede eliminar un medicamento que no fue creado por usted.');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Medication $medication): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Medication $medication): bool
    {
        return false;
    }
}
