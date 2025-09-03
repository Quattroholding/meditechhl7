<?php

namespace App\Livewire\Doctor;

use App\Models\PatientClient;
use Livewire\Component;

class PatientsByGender extends Component
{
    public $femalePatientsCount;

    public $malePatientsCount;

    public $unknownGenderCount;

    public $femalePatientsPercentage;

    public $malePatientsPercentage;

    public $unknownGenderPercentage;

    public $order;

    public $isLoading = true;

    public function mount()
    {
        // Inicializar variables para evitar errores
        $this->femalePatientsCount = 0;
        $this->malePatientsCount = 0;
        $this->unknownGenderCount = 0;
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
                'maleCount' => $this->malePatientsCount,
                'femaleCount' => $this->femalePatientsCount,
                'unknownCount' => $this->unknownGenderCount,
                'male' => $this->malePatientsPercentage,
                'female' => $this->femalePatientsPercentage,
                'unknown' => $this->unknownGenderPercentage,
            ]);

            \Log::info('PatientsByGender loadData completed successfully');
        } catch (\Exception $e) {
            \Log::error('Error in PatientsByGender loadData: '.$e->getMessage());
            $this->isLoading = false;
        }
    }

    public function render()
    {
        return view('livewire.doctor.patients-by-gender');
    }

    public function getPercentageByGender()
    {

        $userclient = auth()->user()->clients->pluck('id')->toArray();
        $this->femalePatientsCount = PatientClient::join('patients', 'patient_clients.patient_id', 'patients.id')
            ->whereNull('patient_clients.deleted_at')
            ->where('patients.gender', '=', 'female')
            ->whereIn('patient_clients.client_id', $userclient)
            ->count();

        $this->malePatientsCount = PatientClient::join('patients', 'patient_clients.patient_id', 'patients.id')
            ->whereNull('patient_clients.deleted_at')
            ->where('patients.gender', '=', 'male')
            ->whereIn('patient_clients.client_id', $userclient)
            ->count();

        $this->unknownGenderCount = PatientClient::join('patients', 'patient_clients.patient_id', 'patients.id')
            ->whereNull('patient_clients.deleted_at')
            ->where('patients.gender', '=', 'unknown')
            ->whereIn('patient_clients.client_id', $userclient)
            ->count();

        $allpatients = PatientClient::whereIn('client_id', $userclient)
            ->whereNull('deleted_at')
            ->count();
        $this->femalePatientsPercentage = ($this->femalePatientsCount > 0) ? number_format((($this->femalePatientsCount / $allpatients) * 100), 1) : 0;
        $this->malePatientsPercentage = ($this->malePatientsCount > 0) ? number_format((($this->malePatientsCount / $allpatients) * 100), 1) : 0;
        $this->unknownGenderPercentage = ($this->unknownGenderCount > 0) ? number_format((($this->unknownGenderCount / $allpatients) * 100), 1) : 0;

    }
}
