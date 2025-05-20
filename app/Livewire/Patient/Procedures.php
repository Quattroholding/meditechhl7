<?php

namespace App\Livewire\Patient;

use App\Models\Patient;
use Livewire\Component;

class Procedures extends Component
{
    public $patient_id;

    public function render()
    {
        $this->patient = Patient::find($this->patient_id);
        $data = $this->patient->procedures()->paginate(5);

        return view('livewire.patient.procedures',['data'=>$data]);
    }
}
