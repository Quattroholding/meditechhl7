<?php

namespace App\Livewire\Patient\Dashboard;

use App\Models\Encounter;
use Livewire\Component;

class RecentConsultations extends Component
{
    public $patient;
    public $limit = 5;
    public $order;
    public $isLoading = true;
    public $recentConsultations = [];

    protected $listeners = ['loadData'];

    public function mount($limit = 5, $order = null)
    {
        $this->patient = auth()->user()->patient;
        $this->limit = $limit;
        $this->order = $order;
        // Initialize empty data to avoid errors during loading
        $this->recentConsultations = collect();
    }

    public function loadData()
    {
        $this->loadRecentConsultations();
        $this->isLoading = false;
    }

    public function loadRecentConsultations()
    {
        if (!$this->patient) {
            $this->recentConsultations = collect();
            return;
        }

        $this->recentConsultations = Encounter::where('patient_id', $this->patient->id)
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
