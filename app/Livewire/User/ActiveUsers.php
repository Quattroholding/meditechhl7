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
        if(Gate::allows('create', auth()->user())){
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
        }}else{
                $this->visibility = '';
                $this->dispatch('showToastr'.$this->user_id, [
                'type' => 'error',
                'message' => 'Error al activar el usuario, superó el limite de usuarios de su plan',
            ]);
        }
    }

    public function render()
    {
        return view('livewire.user.active-users');
    }
}
