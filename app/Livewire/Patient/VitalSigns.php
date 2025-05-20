<?php

namespace App\Livewire\Patient;

use App\Models\Patient;
use App\Models\VitalSign;
use Livewire\Component;
use Livewire\WithPagination;

class VitalSigns extends Component
{
    use WithPagination;
    public $patient;
    public $patient_id;

    public function render()
    {
        $this->patient = Patient::find($this->patient_id);
        $data = $this->patient->vitalSigns()->paginate(5);

        return view('livewire.patient.vital-signs',['data'=>$data]);
    }
}
