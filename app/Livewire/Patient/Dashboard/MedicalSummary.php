<?php

namespace App\Livewire\Patient\Dashboard;

use App\Models\Condition;
use App\Models\MedicalHistory;
use App\Models\Encounter;
use Livewire\Component;

class MedicalSummary extends Component
{
    public $patient;

    public function mount()
    {
        $this->patient = auth()->user()->patient;
    }

    public function getActiveMedicalConditionsProperty()
    {
        if (!$this->patient) {
            return collect();
        }

        return Condition::where('patient_id', $this->patient->id)
            ->where('clinical_status', 'active')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    public function getAllergiesProperty()
    {
        if (!$this->patient) {
            return collect();
        }

        return MedicalHistory::where('patient_id', $this->patient->id)
            ->where('category', 'allergy')
            ->where('clinical_status', 'active')
            ->orderBy('category', 'desc')
            ->get();
    }

    public function getCurrentMedicationsProperty()
    {
        if (!$this->patient) {
            return collect();
        }

        // Get medications from recent encounters
        return Encounter::where('patient_id', $this->patient->id)
            ->whereHas('medicationRequests')
            ->whereDate('start', '>=', now()->subMonths(6))
            ->orderBy('start', 'desc')
            ->limit(3)
            ->get();
    }

    public function getLastVitalSignsProperty()
    {
        if (!$this->patient) {
            return null;
        }

        $vitalSigns = \App\Models\VitalSign::where('patient_id', $this->patient->id)
            ->with('observationType')
            ->orderBy('effective_date', 'desc')
            ->limit(4)
            ->get();

        return $vitalSigns->isNotEmpty() ? [
            'date' => $vitalSigns->first()->effective_date,
            'vital_signs' => $vitalSigns
        ] : null;
    }

    public function render()
    {
        return view('livewire.patient.dashboard.medical-summary');
    }
}
