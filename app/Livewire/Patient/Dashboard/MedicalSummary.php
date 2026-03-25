<?php

namespace App\Livewire\Patient\Dashboard;

use App\Models\Condition;
use App\Models\Encounter;
use App\Models\MedicalHistory;
use App\Models\VitalSign;
use Livewire\Component;

class MedicalSummary extends Component
{
    public $patient;

    public $order;

    public $isLoading = true;

    public $activeMedicalConditions = [];

    public $allergies = [];

    public $currentMedications = [];

    public $lastVitalSigns = null;

    protected $listeners = ['loadData'];

    public function mount($order = null)
    {
        $this->patient = auth()->user()->patient;
        $this->order = $order;
        // Initialize empty data to avoid errors during loading
        $this->activeMedicalConditions = collect();
        $this->allergies = collect();
        $this->currentMedications = collect();
        $this->lastVitalSigns = null;
    }

    public function loadData()
    {
        $this->loadActiveMedicalConditions();
        $this->loadAllergies();
        $this->loadCurrentMedications();
        $this->loadLastVitalSigns();
        $this->isLoading = false;
    }

    public function loadActiveMedicalConditions()
    {
        if (! $this->patient) {
            $this->activeMedicalConditions = collect();

            return;
        }

        $this->activeMedicalConditions = Condition::where('patient_id', $this->patient->id)
            ->where('clinical_status', 'active')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    public function loadAllergies()
    {
        if (! $this->patient) {
            $this->allergies = collect();

            return;
        }

        $this->allergies = MedicalHistory::where('patient_id', $this->patient->id)
            ->where('category', 'allergy')
            ->where('clinical_status', 'active')
            ->orderBy('category', 'desc')
            ->get();
    }

    public function loadCurrentMedications()
    {
        if (! $this->patient) {
            $this->currentMedications = collect();

            return;
        }

        // Get medications from recent encounters
        $this->currentMedications = Encounter::where('patient_id', $this->patient->id)
            ->whereHas('medicationRequests')
            ->whereDate('start', '>=', now()->subMonths(6))
            ->orderBy('start', 'desc')
            ->limit(3)
            ->get();
    }

    public function loadLastVitalSigns()
    {
        if (! $this->patient) {
            $this->lastVitalSigns = null;

            return;
        }

        $vitalSigns = VitalSign::where('patient_id', $this->patient->id)
            ->with('observationType')
            ->orderBy('effective_date', 'desc')
            ->limit(4)
            ->get();

        $this->lastVitalSigns = $vitalSigns->isNotEmpty() ? [
            'date' => $vitalSigns->first()->effective_date,
            'vital_signs' => $vitalSigns,
        ] : null;
    }

    public function render()
    {
        return view('livewire.patient.dashboard.medical-summary');
    }
}
