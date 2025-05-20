<?php

namespace App\Livewire\Patient;

use App\Models\Patient;
use Livewire\Component;

class Services extends Component
{
    public $patient_id;

    public function render()
    {
        $this->patient = Patient::find($this->patient_id);
        $data = $this->patient->serviceRequests()->paginate(5);

        return view('livewire.patient.services',['data'=>$data]);
    }
}
