<?php

namespace App\Livewire\Components;

use App\Models\Patient;
use Livewire\Attributes\Modelable;
use Livewire\Component;

class PatientSelector extends Component
{
    #[Modelable]
    public $selectedPatientId = '';

    public $searchTerm = '';

    public $showDropdown = false;

    public $patients = [];

    public $selectedPatientName = '';

    public $placeholder = 'Buscar paciente...';

    public $required = false;

    public function mount()
    {
        if ($this->selectedPatientId) {
            $patient = Patient::find($this->selectedPatientId);
            if ($patient) {
                $this->selectedPatientName = $patient->name;
                $this->searchTerm = $patient->name;
            }
        }
    }

    public function updatedSearchTerm()
    {
        if (strlen($this->searchTerm) >= 2) {
            $this->showDropdown = true;
            $this->searchPatients();
        } else {
            $this->showDropdown = false;
            $this->patients = [];
        }

        // Clear selection if search term changes
        if ($this->searchTerm !== $this->selectedPatientName) {
            $this->selectedPatientId = '';
            $this->selectedPatientName = '';
        }
    }

    public function searchPatients()
    {
        $this->patients = Patient::where(function ($query) {
            $query->where('name', 'like', '%'.$this->searchTerm.'%')
                ->orWhere('identifier', 'like', '%'.$this->searchTerm.'%');
        })
            ->limit(10)
            ->get();
    }

    public function selectPatient($patientId)
    {
        $patient = Patient::find($patientId);
        if ($patient) {
            $this->selectedPatientId = $patientId;
            $this->selectedPatientName = $patient->name;
            $this->searchTerm = $patient->name;
            $this->showDropdown = false;
            $this->patients = [];
        }
    }

    public function clearSelection()
    {
        $this->selectedPatientId = '';
        $this->selectedPatientName = '';
        $this->searchTerm = '';
        $this->showDropdown = false;
        $this->patients = [];
    }

    public function hideDropdown()
    {
        // Delay hiding to allow for click events
        $this->dispatch('hide-dropdown-after-delay');
    }

    public function render()
    {
        return view('livewire.components.patient-selector');
    }
}
