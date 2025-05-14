<?php

namespace App\Livewire\Consultation;

use App\Models\Encounter;
use Livewire\Component;

class Reason extends Component
{
    public $reason;
    public $encounter_id;
    public $encounter;


    public function mount(){
        $this->encounter = Encounter::find($this->encounter_id);
        $this->reason = $this->encounter->reason;

    }

    public function render()
    {
        return view('livewire.consultation.reason');
    }

    public function updatedReason(){

        $this->encounter->reason = $this->reason;
        $this->encounter->save();
    }
}
