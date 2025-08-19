<?php

namespace App\Livewire\Patient\Tabs;

use App\Models\Appointment;
use Livewire\Component;
use Livewire\WithPagination;

class Appointments extends Component
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
        $appointments = Appointment::where('patient_id', $this->patient_id)
            ->with(['practitioner', 'consultingRoom'])
            ->orderBy('appointment_date', 'desc')
            ->paginate($this->perPage);

        return view('livewire.patient.tabs.appointments', compact('appointments'));
    }

    public function loadMore()
    {
        $this->perPage += 5;
    }
}
