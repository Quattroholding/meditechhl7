<?php

namespace App\Livewire\Patient;

use App\Models\Patient;
use Livewire\Component;

class Encounters extends Component
{
    public $patient;
    public $patient_id;

    public function render()
    {
        $this->patient = Patient::find($this->patient_id);
        $data = $this->patient->encounters()->paginate(5);
        return view('livewire.patient.encounters',['data'=>$data]);
    }
}
