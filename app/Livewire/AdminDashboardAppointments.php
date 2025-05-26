<?php

namespace App\Livewire;

use App\Models\Appointment;
use Livewire\Component;
//use Illuminate\Support\Facades\Auth;

class AdminDashboardAppointments extends Component
{
    
    public $appointments;

    public function mount()
    {
        //$user = Auth::user();
        //dd(auth()->user());
        //dd(auth()->user()->hasRole('admin'));
        if(auth()->user()->hasRole('admin')){
        $this->appointments = Appointment::orderBy('created_at')->get();}
        elseif(auth()->user()->hasRole('paciente')){
            $this->appointments = Appointment::where('patient_id', auth()->user()->id)->orderBy('created_at')->get();
        }elseif(auth()->user()->hasRole('doctor')){
            $this->appointments = Appointment::where('practitioner_id', auth()->user()->id)->orderBy('created_at')->get();
        }else{
            $this->appointments = collect();
        }
    }

    public function render()
    {
        return view('livewire.admin-dashboard-appointments');
    }
}
