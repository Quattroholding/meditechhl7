<?php

namespace App\Livewire\Patient;

use App\Models\Patient;
use Livewire\Component;
use Livewire\WithPagination;

class MedicalHistory extends Component
{
    use WithPagination;
    public $patient;
    public $patient_id;

    public function render()
    {
        $this->patient = Patient::find($this->patient_id);
        $data = $this->patient->medicalHistories()->paginate(5);

        return view('livewire.patient.medical-history',[ 'data' => $data]);
    }
}
