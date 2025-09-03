<?php

namespace App\Livewire\User;

use App\Models\User;
use Livewire\Component;

class ActiveUsers extends Component
{
    public $user_id;

    public $visibility = '';

    public function activateUser($userId)
    {
        try {
            $user = User::findOrFail($userId);

            $user->active = true;
            $user->save();
            $this->visibility = 'display: none;';
            $this->dispatch('showToastr'.$this->user_id, [
                'type' => 'success',
                'message' => '¡Usuario('.$user->id.'): '.$user->full_name.' reactivado con exito!',
            ]);
        } catch (\Exception $e) {
            $this->visibility = '';
            $this->dispatch('showToastr'.$this->user_id, [
                'type' => 'error',
                'message' => 'Error al activar el usuario',
            ]);
        }
    }

    public function render()
    {
        return view('livewire.user.active-users');
    }
}
