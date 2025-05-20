<?php

namespace App\Livewire\Patient;

use App\Models\Patient;
use Livewire\Component;

class PatientProfileDetails extends Component
{
    public $data=[];
    public $patient;
    public $patient_id;
    public $tabs=[];
    public $tabs2=[];
    public $activeTab;


    public function render()
    {
        return view('livewire.patient.patient-profile-details');
    }

    public function mount(){

        $this->patient = Patient::find($this->patient_id);
        $this->tabs = explode(',',$this->tabs);

        foreach ($this->tabs as $tab){
            $this->tabs2[$tab]['title'] =$tab;
            $this->tabs2[$tab]['active']='';
            $this->tabs2[$tab]['component']='patient.'.$tab;
            if($tab == $this->activeTab){
                $this->tabs2[$tab]['active']='show active';
            }
        }
    }

    public function changeActiveTab($tab){
        $this->tabs2[$tab]['active']='show active';
        $this->activeTab = $tab;
        foreach ($this->tabs as $tab){
            $this->tabs2[$tab]['active']='';
            if($tab == $this->activeTab){
                $this->tabs2[$tab]['active']='show active';
            }
        }
    }

    public function getData($tab){
        $this->data[$tab] = $this->patient->getSectionHistory($tab);
    }
}
