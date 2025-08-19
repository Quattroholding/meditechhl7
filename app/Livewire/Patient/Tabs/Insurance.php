<?php

namespace App\Livewire\Patient\Tabs;

use App\Models\PatientInsurancePolicy;
use Livewire\Component;
use Livewire\WithPagination;

class Insurance extends Component
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
        $insurancePolicies = PatientInsurancePolicy::where('patient_id', $this->patient_id)
            ->with(['insuranceCompany', 'subscriberPatient'])
            ->orderBy('priority')
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.patient.tabs.insurance', compact('insurancePolicies'));
    }

    public function loadMore()
    {
        $this->perPage += 5;
    }
}
