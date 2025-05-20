<?php

namespace App\Livewire\Patient;

use App\Models\Patient;
use Livewire\Component;

class MedicalHistory extends Component
{
    public $patient;
    public $patient_id;

    public function render()
    {
        $this->patient = Patient::find($this->patient_id);
        $data = $this->patient->medicalHistories()->paginate(5);

        return view('livewire.patient.medical-history',[ 'data' => $data]);
    }
}
