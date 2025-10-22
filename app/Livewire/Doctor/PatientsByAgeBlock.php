<?php

namespace App\Livewire\Doctor;

use App\Models\PatientClient;
use Carbon\Carbon;
use Livewire\Component;

class PatientsByAgeBlock extends Component
{
    public $age0to12Count;

    public $age13to17Count;

    public $age18to25Count;

    public $age26to59Count;

    public $age60PlusCount;

    public $age0to12Percentage;

    public $age13to17Percentage;

    public $age18to25Percentage;

    public $age26to59Percentage;

    public $age60PlusPercentage;

    public $order;

    public $isLoading = true;

    public function mount()
    {
        // Inicializar variables para evitar errores
        $this->age0to12Count = 0;
        $this->age13to17Count = 0;
        $this->age18to25Count = 0;
        $this->age26to59Count = 0;
        $this->age60PlusCount = 0;
        $this->age0to12Percentage = 0;
        $this->age13to17Percentage = 0;
        $this->age18to25Percentage = 0;
        $this->age26to59Percentage = 0;
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
                'age0to12Count' => $this->age0to12Count,
                'age13to17Count' => $this->age13to17Count,
                'age18to25Count' => $this->age18to25Count,
                'age26to59Count' => $this->age26to59Count,
                'age60PlusCount' => $this->age60PlusCount,
                'age0to12' => $this->age0to12Percentage,
                'age13to17' => $this->age13to17Percentage,
                'age18to25' => $this->age18to25Percentage,
                'age26to59' => $this->age26to59Percentage,
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

        // 0-12 años
        $date12YearsAgo = $today->copy()->subYears(12);
        $this->age0to12Count = PatientClient::join('patients', 'patient_clients.patient_id', 'patients.id')
            ->whereNull('patient_clients.deleted_at')
            ->whereNotNull('patients.birth_date')
            ->where('patients.birth_date', '>=', $date12YearsAgo)
            ->whereIn('patient_clients.client_id', $userclient)
            ->count();

        // 13-17 años
        $date13YearsAgo = $today->copy()->subYears(17);
        $date17YearsAgo = $today->copy()->subYears(17);
        $this->age13to17Count = PatientClient::join('patients', 'patient_clients.patient_id', 'patients.id')
            ->whereNull('patient_clients.deleted_at')
            ->whereNotNull('patients.birth_date')
            ->where('patients.birth_date', '<', $date13YearsAgo)
            ->where('patients.birth_date', '>=', $date17YearsAgo)
            ->whereIn('patient_clients.client_id', $userclient)
            ->count();

        // 18-25 años
        $date18YearsAgo = $today->copy()->subYears(18);
        $date25YearsAgo = $today->copy()->subYears(25);
        $this->age18to25Count = PatientClient::join('patients', 'patient_clients.patient_id', 'patients.id')
            ->whereNull('patient_clients.deleted_at')
            ->whereNotNull('patients.birth_date')
            ->where('patients.birth_date', '<', $date18YearsAgo)
            ->where('patients.birth_date', '>=', $date25YearsAgo)
            ->whereIn('patient_clients.client_id', $userclient)
            ->count();

        // 26-59 años
        $date26YearsAgo = $today->copy()->subYears(26);
        $date59YearsAgo = $today->copy()->subYears(59);
        $this->age26to59Count = PatientClient::join('patients', 'patient_clients.patient_id', 'patients.id')
            ->whereNull('patient_clients.deleted_at')
            ->whereNotNull('patients.birth_date')
            ->where('patients.birth_date', '<', $date26YearsAgo)
            ->where('patients.birth_date', '>=', $date59YearsAgo)
            ->whereIn('patient_clients.client_id', $userclient)
            ->count();

        // 60+ años
        $date60YearsAgo = $today->copy()->subYears(60);
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
            $this->age0to12Percentage = number_format((($this->age0to12Count / $allpatients) * 100), 1);
            $this->age13to17Percentage = number_format((($this->age13to17Count / $allpatients) * 100), 1);
            $this->age18to25Percentage = number_format((($this->age18to25Count / $allpatients) * 100), 1);
            $this->age26to59Percentage = number_format((($this->age26to59Count / $allpatients) * 100), 1);
            $this->age60PlusPercentage = number_format((($this->age60PlusCount / $allpatients) * 100), 1);
        } else {
            $this->age0to12Percentage = 0;
            $this->age13to17Percentage = 0;
            $this->age18to25Percentage = 0;
            $this->age26to59Percentage = 0;
            $this->age60PlusPercentage = 0;
        }
    }
}
