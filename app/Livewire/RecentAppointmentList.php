<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Appointment;

class RecentAppointmentList extends Component
{
    public $appointments;
    protected $listeners = ['refreshAppointments' => 'refreshAppointments'];
    public function mount()
    {
       $this->refreshAppointments();
    }

    // app/Http/Livewire/AppointmentList.php
    public function refreshAppointments()
    {
        $today =  \Carbon\Carbon::today();
        $doctor_id = auth()->user()->practitioner->id;
        $this->appointments = Appointment::whereDate('start', $today)
                                        ->wherePractitionerId($doctor_id)
                                        ->orderBy('start')
                                        ->get();
    }

    public function render()
    {
        return view('livewire.recent-appointment-list');
    }
}
