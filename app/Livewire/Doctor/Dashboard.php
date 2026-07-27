<?php

namespace App\Livewire\Doctor;

use Livewire\Component;

class Dashboard extends Component
{
    public function loadData()
    {
        // Este componente no requiere carga de datos adicional
    }

    public function render()
    {
        return view('livewire.doctor.dashboard');
    }
}
