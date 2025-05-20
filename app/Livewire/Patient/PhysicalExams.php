<?php

namespace App\Livewire\Patient;

use App\Models\Patient;
use Livewire\Component;
use Livewire\WithPagination;

class PhysicalExams extends Component
{
    use WithPagination;
    public $patient_id;

    public function render()
    {
        $this->patient = Patient::find($this->patient_id);
        $data = $this->patient->physicalExams()->paginate(5);

        return view('livewire.patient.physical-exams',['data'=>$data]);
    }
}
