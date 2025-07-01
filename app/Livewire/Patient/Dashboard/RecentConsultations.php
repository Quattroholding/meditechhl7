<?php

namespace App\Livewire\Patient\Dashboard;

use App\Models\Encounter;
use Livewire\Component;

class RecentConsultations extends Component
{
    public $patient;
    public $limit = 5;

    public function mount($limit = 5)
    {
        $this->patient = auth()->user()->patient;
        $this->limit = $limit;
    }

    public function getRecentConsultationsProperty()
    {
        if (!$this->patient) {
            return collect();
        }

        return Encounter::where('patient_id', $this->patient->id)
            ->with(['practitioner', 'appointment', 'vitalSigns.observationType'])
            ->orderBy('start', 'desc')
            ->limit($this->limit)
            ->get();
    }

    public function render()
    {
        return view('livewire.patient.dashboard.recent-consultations');
    }
}
