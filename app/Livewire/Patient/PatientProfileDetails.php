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
    public $id_number;


    public function render()
    {
        return view('livewire.patient.patient-profile-details');
    }

    public function mount(){

        $this->patient = Patient::find($this->patient_id);
        $this->tabs = explode(',',$this->tabs);

        foreach ($this->tabs as $tab){
             $count = $this->patient->getSectionHistory($tab)->count();
            if($count>0){
                $this->tabs2[$tab]['title'] =$tab;
                $this->tabs2[$tab]['active']='';
                $this->tabs2[$tab]['component']='patient.'.$tab;
                $this->tabs2[$tab]['count']=$count;
                if($tab == $this->activeTab){
                    $this->tabs2[$tab]['active']='show active';
                }
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

    private function getIdPattern()
    {
        switch ($this->id_type) {
            case 'CC': // Cédula de Ciudadanía (Panamá): 8-123-456 o PE-123-456
                return '/^[A-Z]*[0-9]+-[0-9]+-[0-9]+$/';
            case 'CE': // Cédula Extranjera: Similar a CC
                return '/^[A-Z]*[0-9]+-[0-9]+-[0-9]+$/';
            case 'PA': // Pasaporte: N1234567
                return '/^[A-Z0-9-]{5,20}$/';
            case 'PT': // Permiso Temporal: Formato flexible
                return '/^[A-Z0-9-]{8,15}$/';
            case 'SS': // Seguro Social: XXX-XX-XXXX
                return '/^\d{3}-?\d{2}-?\d{4}$/';
            default:
                return '/^[A-Z0-9-]{5,20}$/'; // Universal para cualquier tipo
        }
    }

    public function getIdPlaceholder()
    {
        switch ($this->id_type) {
            case 'CC':
                return 'Ej: 8-123-456 o PE-123-456';
            case 'CE':
                return 'Ej: 4-123-456-12345';
            case 'PA':
                return 'Ej: PA1234567';
            case 'PT':
                return 'Ej: PT-12345678';
            case 'SS':
                return 'Ej: 123-45-6789';
            default:
                return 'Ingrese el número de documento';
        }
    }
}
