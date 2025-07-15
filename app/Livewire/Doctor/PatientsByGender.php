<?php

namespace App\Livewire\Doctor;

use Livewire\Component;

use App\Models\PatientClient;

class PatientsByGender extends Component
{
    public $femalePatientsPercentage;
    public $malePatientsPercentage;
    public $unknownGenderPercentage;
    public $order;
    public $isLoading = true;

    public function mount(){
        // Inicializar variables para evitar errores
        $this->femalePatientsPercentage = 0;
        $this->malePatientsPercentage = 0;
        $this->unknownGenderPercentage = 0;

        \Log::info('PatientsByGender component mounted successfully');
    }

    public function loadData()
    {
        \Log::info('PatientsByGender loadData method called');
        try {
            $this->getPercentageByGender();
            $this->isLoading = false;
            
            // Pasar los datos al JavaScript
            $this->dispatch('loadGraph', [
                'male' => $this->malePatientsPercentage,
                'female' => $this->femalePatientsPercentage,
                'unknown' => $this->unknownGenderPercentage
            ]);
            
            \Log::info('PatientsByGender loadData completed successfully');
        } catch (\Exception $e) {
            \Log::error('Error in PatientsByGender loadData: ' . $e->getMessage());
            $this->isLoading = false;
        }
    }
    public function render()
    {
        return view('livewire.doctor.patients-by-gender');
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
        $this->femalePatientsPercentage = ($femalepatients > 0) ? number_format((($femalepatients/$allpatients) * 100), 1) : 0;
        $this->malePatientsPercentage = ($malepatients > 0) ? number_format((($malepatients/$allpatients) * 100), 1) : 0;
        $this->unknownGenderPercentage = ($unknownGenderPatients > 0) ? number_format((($unknownGenderPatients/$allpatients) * 100), 1) : 0;


        //dd($this->femalePatientsPercentage, $this->malePatientsPercentage, $this->unknownGenderPercentage);


    }
}
