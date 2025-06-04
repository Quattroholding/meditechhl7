<?php

namespace App\Livewire;

use App\Models\Appointment;
use Livewire\Component;
use Livewire\WithPagination;

//use Illuminate\Support\Facades\Auth;

class AdminDashboardAppointments extends Component
{

    use WithPagination;

    public function mount()
    {
        //$user = Auth::user();
        //dd(auth()->user());
        //dd(auth()->user()->hasRole('admin'));


    }

    public function render()
    {

        if(auth()->user()->hasRole('admin')){
            $data = Appointment::whereRaw("date_format(start,'%Y-%m-%d')>='".now()->format('Y-m-d')."'")->orderBy('start')->paginate(5);
        }elseif(auth()->user()->hasRole('paciente')){
            $data = Appointment::whereRaw("date_format(start,'%Y-%m-%d')>='".now()->format('Y-m-d')."'")->where('patient_id', auth()->user()->id)->orderBy('start')->paginate(5);
        }elseif(auth()->user()->hasRole('doctor')){
            $data = Appointment::whereRaw("date_format(start,'%Y-%m-%d')>='".now()->format('Y-m-d')."'")->where('practitioner_id', auth()->user()->id)->orderBy('start')->paginate(5);
        }else{
            $data = collect();
        }

        return view('livewire.admin-dashboard-appointments',['data'=>$data]);
    }
}
