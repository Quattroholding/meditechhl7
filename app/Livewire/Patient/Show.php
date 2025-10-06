<?php

namespace App\Livewire\Patient;

use App\Models\Patient;
use Livewire\Component;

class Show extends Component
{
    public $patient_id;

    public $patient;

    public $activeTab = 'general';

    public $loadedTabs = ['general']; // Track which tabs have been loaded

    public $isLoading = false;

    protected $listeners = ['fileDeleted' => 'refreshFiles'];

    public function mount($patient_id)
    {
        $this->patient_id = $patient_id;
        $this->loadPatientData();
    }

    public function loadPatientData()
    {
        $this->patient = Patient::with(['user'])->findOrFail($this->patient_id);
    }

    public function switchTab($tab)
    {
        if ($this->activeTab !== $tab) {
            $this->activeTab = $tab;

            // Mark tab as loading if not already loaded
            if (! in_array($tab, $this->loadedTabs)) {
                $this->isLoading = true;

                // Simulate async loading delay
                $this->dispatch('tab-loading', $tab);

                // Delay the actual loading to show skeleton animation
                $this->js("
                    setTimeout(() => {
                        \$wire.call('loadTabContent', '$tab');
                    }, 500);
                ");
            }
        }
    }

    public function loadTabContent($tab)
    {
        // Add to loaded tabs after delay
        if (! in_array($tab, $this->loadedTabs)) {
            $this->loadedTabs[] = $tab;
        }
        $this->isLoading = false;
    }

    public function refreshFiles()
    {
        // Refresh the files tab
        $this->loadPatientData();
    }

    public function render()
    {
        return view('livewire.patient.show');
    }
}
