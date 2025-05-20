<?php

namespace App\Livewire\Patient;

use App\Models\Patient;
use Livewire\Component;

class VitalSigns extends Component
{
    public $patient;
    public $patient_id;

    public function render()
    {
        $this->patient = Patient::find($this->patient_id);
        $data = $this->patient->vitalSigns()->paginate(5);
        return view('livewire.patient.vital-signs',['data'=>$data]);
    }
}
