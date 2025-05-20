<?php

namespace App\Livewire\Patient;

use App\Models\Patient;
use Livewire\Component;

class Conditions extends Component
{
    public $patient;
    public $patient_id;

    public function render()
    {
        $this->patient = Patient::find($this->patient_id);
        $data = $this->patient->conditions()->paginate(5);

        return view('livewire.patient.conditions',['data'=>$data]);

    }
}
