<?php

namespace App\Livewire\Client;

use App\Models\Appointment;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AppointmentsByBranch extends Component
{
         public $appointments;

    public function mount(){
        $this->loadData();
    }


    public function loadData(){
        $this->appointments = Appointment::select(            
            'branches.id',
            'branches.name',
            'branches.address',
            'branches.type', 
            DB::raw('COUNT(appointments.id) as total_appointments'))
            ->join('consulting_rooms', 'appointments.consulting_room_id', '=', 'consulting_rooms.id')
            ->join('branches', 'consulting_rooms.branch_id', '=', 'branches.id')
            ->groupBy('branches.id', 'branches.name', 'branches.address', 'branches.type') // agregar todos los campos no agregados del SELECT
            ->orderBy('total_appointments', 'DESC')
            ->limit(5)
            ->get();


    }
    public function render()
    {
        return view('livewire.client.appointments-by-branch');
    }
}
