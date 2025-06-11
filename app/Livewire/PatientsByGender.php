<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\PatientClient;

class PatientsByGender extends Component
{
    public $femalePatientsPercentage;
    public $malePatientsPercentage;
    public $unknownGenderPercentage;

    public function mount(){
        $this->getPercentageByGender();        
    }
    public function render()
    {
        return view('livewire.patients-by-gender');
    }

    public function getPercentageByGender(){

        $userclient=auth()->user()->clients->pluck('id')->toArray();
        $femalepatients = PatientClient::join('patients', 'patient_clients.patient_id', 'patients.id')
        ->whereNull('patient_clients.deleted_at')
        ->where('patients.gender', '=', 'female')
        ->whereIn('patient_clients.client_id', $userclient)
        ->count();

        $malepatients = PatientClient::join('patients', 'patient_clients.patient_id', 'patients.id')
        ->whereNull('patient_clients.deleted_at')
        ->where('patients.gender', '=', 'male')
        ->whereIn('patient_clients.client_id', $userclient)
        ->count();

        $unknownGenderPatients = PatientClient::join('patients', 'patient_clients.patient_id', 'patients.id')
        ->whereNull('patient_clients.deleted_at')
        ->where('patients.gender', '=', 'unknown')
        ->whereIn('patient_clients.client_id', $userclient)
        ->count();

        $allpatients = PatientClient::whereIn('client_id', $userclient)
        ->whereNull('deleted_at')
        ->count();
        $this->femalePatientsPercentage = ($femalepatients > 0) ? (($femalepatients/$allpatients) * 100) : 0;
        $this->malePatientsPercentage = ($malepatients > 0) ? (($malepatients/$allpatients) * 100) : 0;
        $this->unknownGenderPercentage = ($unknownGenderPatients > 0) ? (($unknownGenderPatients/$allpatients) * 100) : 0;
        

        //dd($allpatients, $femalepatients, $malepatients);

        
    }
}
