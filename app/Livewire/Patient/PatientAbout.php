<?php

namespace App\Livewire\Patient;

use App\Models\Patient;
use Livewire\Component;

class PatientAbout extends Component
{
    public $data;
    public $patient_id;

    public function mount(){
        $this->data = Patient::find($this->patient_id);
    }
    public function render()
    {
        return view('livewire.patient.patient-about');
    }
}
