<?php

namespace App\Livewire\Patient;

use App\Models\Patient;
use Livewire\Component;
use Livewire\WithPagination;

class Encounters extends Component
{
    use WithPagination;
    public $patient;
    public $patient_id;

    public function render()
    {
        $this->patient = Patient::find($this->patient_id);
        $data = $this->patient->encounters()->paginate(5);
        return view('livewire.patient.encounters',['data'=>$data]);
    }
}
