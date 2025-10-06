<?php

namespace App\Livewire\Doctor;

use App\Models\PatientClient;
use Carbon\Carbon;
use Livewire\Component;

class PatientsByAgeBlock extends Component
{
    public $age0to20Count;

    public $age20to40Count;

    public $age40to60Count;

    public $age60PlusCount;

    public $age0to20Percentage;

    public $age20to40Percentage;

    public $age40to60Percentage;

    public $age60PlusPercentage;

    public $order;

    public $isLoading = true;

    public function mount()
    {
        // Inicializar variables para evitar errores
        $this->age0to20Count = 0;
        $this->age20to40Count = 0;
        $this->age40to60Count = 0;
        $this->age60PlusCount = 0;
        $this->age0to20Percentage = 0;
        $this->age20to40Percentage = 0;
        $this->age40to60Percentage = 0;
        $this->age60PlusPercentage = 0;

        \Log::info('PatientsByAgeBlock component mounted successfully');
    }

    public function loadData()
    {
        \Log::info('PatientsByAgeBlock loadData method called');
        try {
            $this->getPercentageByAgeBlock();
            $this->isLoading = false;

            // Pasar los datos al JavaScript
            $this->dispatch('loadAgeBlockGraph', [
                'age0to20Count' => $this->age0to20Count,
                'age20to40Count' => $this->age20to40Count,
                'age40to60Count' => $this->age40to60Count,
                'age60PlusCount' => $this->age60PlusCount,
                'age0to20' => $this->age0to20Percentage,
                'age20to40' => $this->age20to40Percentage,
                'age40to60' => $this->age40to60Percentage,
                'age60Plus' => $this->age60PlusPercentage,
            ]);

            \Log::info('PatientsByAgeBlock loadData completed successfully');
        } catch (\Exception $e) {
            \Log::error('Error in PatientsByAgeBlock loadData: '.$e->getMessage());
            $this->isLoading = false;
        }
    }

    public function render()
    {
        return view('livewire.doctor.patients-by-age-block');
    }

    public function getPercentageByAgeBlock()
    {
        $userclient = auth()->user()->clients->pluck('id')->toArray();
        $today = Carbon::today();

        // 0-20 años
        $date20YearsAgo = $today->copy()->subYears(20);
        $this->age0to20Count = PatientClient::join('patients', 'patient_clients.patient_id', 'patients.id')
            ->whereNull('patient_clients.deleted_at')
            ->whereNotNull('patients.birth_date')
            ->where('patients.birth_date', '>=', $date20YearsAgo)
            ->whereIn('patient_clients.client_id', $userclient)
            ->count();

        // 20-40 años
        $date40YearsAgo = $today->copy()->subYears(40);
        $this->age20to40Count = PatientClient::join('patients', 'patient_clients.patient_id', 'patients.id')
            ->whereNull('patient_clients.deleted_at')
            ->whereNotNull('patients.birth_date')
            ->where('patients.birth_date', '<', $date20YearsAgo)
            ->where('patients.birth_date', '>=', $date40YearsAgo)
            ->whereIn('patient_clients.client_id', $userclient)
            ->count();

        // 40-60 años
        $date60YearsAgo = $today->copy()->subYears(60);
        $this->age40to60Count = PatientClient::join('patients', 'patient_clients.patient_id', 'patients.id')
            ->whereNull('patient_clients.deleted_at')
            ->whereNotNull('patients.birth_date')
            ->where('patients.birth_date', '<', $date40YearsAgo)
            ->where('patients.birth_date', '>=', $date60YearsAgo)
            ->whereIn('patient_clients.client_id', $userclient)
            ->count();

        // 60+ años
        $this->age60PlusCount = PatientClient::join('patients', 'patient_clients.patient_id', 'patients.id')
            ->whereNull('patient_clients.deleted_at')
            ->whereNotNull('patients.birth_date')
            ->where('patients.birth_date', '<', $date60YearsAgo)
            ->whereIn('patient_clients.client_id', $userclient)
            ->count();

        // Calcular total de pacientes
        $allpatients = PatientClient::whereIn('client_id', $userclient)
            ->whereNull('deleted_at')
            ->count();

        // Calcular porcentajes
        if ($allpatients > 0) {
            $this->age0to20Percentage = number_format((($this->age0to20Count / $allpatients) * 100), 1);
            $this->age20to40Percentage = number_format((($this->age20to40Count / $allpatients) * 100), 1);
            $this->age40to60Percentage = number_format((($this->age40to60Count / $allpatients) * 100), 1);
            $this->age60PlusPercentage = number_format((($this->age60PlusCount / $allpatients) * 100), 1);
        } else {
            $this->age0to20Percentage = 0;
            $this->age20to40Percentage = 0;
            $this->age40to60Percentage = 0;
            $this->age60PlusPercentage = 0;
        }
    }
}
