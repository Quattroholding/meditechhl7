<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Appointment;
use Illuminate\Support\Facades\DB;

class AppointmentsBySpecialties extends Component
{
      public $app_specialties;

    public function mount(){
        $this->loadData();
    }


    public function loadData(){
        $this->app_specialties = Appointment::join('medical_specialties', 'appointments.medical_speciality_id', '=', 'medical_specialties.id')
                                ->whereNull('appointments.deleted_at')
                                ->select(
                                    'appointments.medical_speciality_id',
                                    'medical_specialties.name',
                                    DB::raw('COUNT(*) as quantity'),
                                    DB::raw('ROUND((COUNT(*) * 100.0 / (SELECT COUNT(*) FROM appointments WHERE deleted_at IS NULL)), 2) as percentage')
                                )
                                ->groupBy('appointments.medical_speciality_id', 'medical_specialties.name')
                                ->orderByDesc('quantity')
                                ->limit(5)
                                ->get();


    }

    public function render()
    {
        return view('livewire.admin.appointments-by-specialties');
    }
}
