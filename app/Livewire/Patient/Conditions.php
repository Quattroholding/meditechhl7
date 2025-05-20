<?php

namespace App\Livewire\Patient;

use App\Models\Patient;
use Livewire\Component;
use Livewire\WithPagination;

class Conditions extends Component
{
    use WithPagination;
    public $patient;
    public $patient_id;

    public function render()
    {
        $this->patient = Patient::find($this->patient_id);
        $data = $this->patient->conditions()->paginate(5);

        return view('livewire.patient.conditions',['data'=>$data]);

    }
}
