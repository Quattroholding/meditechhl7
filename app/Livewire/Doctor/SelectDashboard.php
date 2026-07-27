<?php

namespace App\Livewire\Doctor;

use Livewire\Component;

class SelectDashboard extends Component
{
    public $selectedOption1;

    public $options1;

    public function mount()
    {
        $this->options1 = ['2023', '2022', '2021', '2020'];
    }

    public function loadData()
    {
        // Este componente no requiere carga de datos adicional
    }

    public function render()
    {
        return view('livewire.doctor.select-dashboard');
    }
}
