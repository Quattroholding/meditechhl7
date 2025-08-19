<?php

namespace App\Livewire\Patient\Tabs;

use App\Models\Condition;
use Livewire\Component;
use Livewire\WithPagination;

class Conditions extends Component
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
        $conditions = Condition::where('patient_id', $this->patient_id)
            ->with(['practitioner', 'icd10Code'])
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.patient.tabs.conditions', compact('conditions'));
    }

    public function loadMore()
    {
        $this->perPage += 5;
    }
}
