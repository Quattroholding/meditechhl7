<?php

namespace App\Livewire\User;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class ActiveUsers extends Component
{
    public $user_id;

    public $visibility = '';

        public function activateUser($userId)
    {
        if (!Gate::allows('create', auth()->user())) {
            $this->showError('Superó el límite de usuarios de su plan');
            return;
        }

        try {
            $user = User::findOrFail($userId);
            $userRole = $user->roles->first()->name ?? null;
            //dd($userRole);
            
            // Validar si ya existe usuario activo con ese rol
            $singleUserRoles = ['doctor', 'recepcionista', 'asistente medico'];
            if ($userRole && in_array($userRole, $singleUserRoles)) {
                //dd(auth()->user()->getCurrentClient()->users()->active()->role($userRole)->where('users.id', '!=', $userId)->get());
                $existingCount = auth()->user()->getCurrentClient()
                    ->users()
                    ->active()
                    ->role($userRole)
                    ->where('users.id', '!=', $userId) // Excluir el usuario actual
                    ->count();
                //dd($existingCount);
                if ($existingCount > 0) {
                    $this->showError("Ya existe un usuario activo con el rol '{$userRole}'");
                    return;
                }
            }
            
            $user->active = true;
            $user->save();
            
            $this->visibility = 'display: none;';
            $this->dispatch('showToastr'.$this->user_id, [
                'type' => 'success',
                'message' => '¡Usuario reactivado con éxito!',
            ]);
            
        } catch (\Exception $e) {
            $this->showError('Error al activar el usuario');
        }
    }

    private function showError($message)
    {
        $this->visibility = '';
        $this->dispatch('showToastr'.$this->user_id, [
            'type' => 'error',
            'message' => $message,
        ]);
    }

    public function render()
    {
        return view('livewire.user.active-users');
    }
}
