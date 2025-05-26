<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;

class TopSpecialties extends Component
{
    public $data=[];

    public function render()
    {
        //$encounter
        return view('livewire.dashboard.top-specialties');
    }
}
