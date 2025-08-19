<?php

namespace App\Livewire\Patient\Tabs;

use App\Models\Patient;
use Livewire\Component;

class General extends Component
{
    public $patient;

    public function mount(Patient $patient)
    {
        $this->patient = $patient;
    }

    public function render()
    {
        return view('livewire.patient.tabs.general');
    }
}
