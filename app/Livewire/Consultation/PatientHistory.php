<?php

namespace App\Livewire\Consultation;

use App\Models\Patient;
use Livewire\Component;

class PatientHistory extends Component
{
    public $patient_id;
    public $patient;
    public $encounters;
    public $vital_signs=[];
    public $medications=[];
    public $services=[];
    public $conditions=[];
    public $physicalExams=[];

    public function mount(){
        $this->patient = Patient::find($this->patient_id);
    }

    public function render()
    {
        $this->encounters =   $this->patient ->encounters()->take(5)->latest()->get();
        return view('livewire.consultation.patient-history');
    }

    public function conditionsEnc(){
        $this->conditions =   $this->patient->conditions()->take(5)->latest()->get();
    }

    public function vitalSigns(){

        foreach ($this->encounters as $e){
            $this->vital_signs[$e->id] = $e->vitalSigns()->get();
        }
    }

    public function physicalExams(){
        dd('physicalExams');
        foreach ($this->encounters as $e){
            $this->physicalExams[$e->id] = $e->physicalExams()->get();
        }
    }

    public function medicationRequests(){

        foreach ($this->encounters as $e){
            $this->medications[$e->id] = $e->medicationRequests()->get();
        }

    }

    public function serviceRequests(){

        foreach ($this->encounters as $e){
            $this->services[$e->id] = $e->serviceRequests()->get();
        }
    }
}
