<?php

namespace App\Livewire\Patient;

use App\Models\Patient;
use Livewire\Component;
use Livewire\WithPagination;

class Referrals extends Component
{
    use WithPagination;
    public $patient_id;

    public function render()
    {
        $this->patient = Patient::find($this->patient_id);
        $data = $this->patient->referrals()->paginate(5);
        return view('livewire.patient.referrals',['data'=>$data]);
    }
}
