<?php

namespace App\Livewire\Patient\Tabs;

use App\Models\PatientRelationship;
use Livewire\Component;
use Livewire\WithPagination;

class Dependents extends Component
{
    use WithPagination;

    public $patient_id;

    public $perPage = 5;

    public function mount($patient_id)
    {
        $this->patient_id = $patient_id;
    }

    public function render()
    {
        $relationships = PatientRelationship::where(function ($query) {
            $query->where('patient_id', $this->patient_id)
                ->orWhere('related_patient_id', $this->patient_id);
        })
            ->with(['patient', 'relatedPatient'])
            ->active()
            ->paginate($this->perPage);

        return view('livewire.patient.tabs.dependents', compact('relationships'));
    }

    public function loadMore()
    {
        $this->perPage += 5;
    }
}
