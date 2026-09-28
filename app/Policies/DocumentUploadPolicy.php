<?php

namespace App\Policies;

use App\Models\DocumentUpload;
use App\Models\User;

class DocumentUploadPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DocumentUpload $documentUpload): bool
    {
        // Admins can view all documents
        if ($user->hasRole('admin') || $user->hasRole('super-admin')) {
            return true;
        }

        // User can view document if it belongs to their current client
        $currentClient = $user->getCurrentClient();

        return $currentClient && $currentClient->id === $documentUpload->client_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DocumentUpload $documentUpload): bool
    {
        return $this->view($user, $documentUpload);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DocumentUpload $documentUpload): bool
    {
        // Only admins can delete documents
        if ($user->hasRole('admin') || $user->hasRole('super-admin')) {
            return true;
        }

        return $this->view($user, $documentUpload);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, DocumentUpload $documentUpload): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, DocumentUpload $documentUpload): bool
    {
        return false;
    }
}
