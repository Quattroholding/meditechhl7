<?php

namespace App\Livewire\Doctor;

use App\Models\PatientClient;
use Livewire\Component;

class ActivePatients extends Component
{
    public $allpatients;

    public function mount(){
        $this->getActivePatients();
    }

    public function render()
    {
        return view('livewire.doctor.active-patients');
    } 

    public function getActivePatients(){
        //TRAE LOS CLIENTES A LOS CUALES EL PRACTITIONER ESTÁ ASOCIADO
        $userclient=auth()->user()->clients->pluck('id')->toArray();
        //QUERY PARA MOSTRAR PACIENTES ACTIVOS DEL PRACTITIONER
        $this->allpatients = PatientClient::whereIn('client_id', $userclient)
        ->whereNull('deleted_at')
        ->count();
    }
}
