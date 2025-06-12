<?php

namespace App\Livewire\Doctor;

use App\Models\Appointment;
use Carbon\Carbon;
use Livewire\Component;

class CompletedAppointments extends Component
{
    public $completedAppointments;
    public function mount(){
        $this->getCompletedAppointments();
    }
    public function render()
    {
        return view('livewire.doctor.completed-appointments');
    }

    public function getCompletedAppointments(){
        //$this->doctor = auth()->user()->practitioner->id;
        $this->completedAppointments = Appointment::fullFilled()->whereDate('start', Carbon::today())->get();
        //dd($this->doctor, $completed);
    }
}
