<?php

namespace App\Livewire\Doctor;

use App\Models\PatientClient;
use Livewire\Component;
use Carbon\Carbon;

class OldPatients extends Component
{
    public $userclient;
    //public $patients;
    public $percentageChange;
    public $statusClass;
    public $icon;
    public $oldPatients;
    public $allOldPatients;

    public function mount(){
        $this->getOldPatients();
    }

    public function render()
    {
        return view('livewire.doctor.old-patients');
    }

    public function getOldPatients(){

        $currentMonth = Carbon::now()->month;
        $lastMonth = Carbon::now()->subMonthNoOverflow()->month;
        $currentYear = Carbon::now()->year;
        //TRAE LOS CLIENTES A LOS CUALES EL PRACTITIONER ESTÁ ASOCIADO
        $this->userclient=auth()->user()->clients->pluck('id')->toArray();
        //QUERY PARA TRAER LOS PACIENTES ASOCIADOS/REGISTRADOS ESTE MES
        $patients = PatientClient::whereMonth('created_at', $currentMonth)
        ->whereYear('created_at', $currentYear)
        ->whereIn('client_id', $this->userclient)
        ->count();
        //TRAE TODOS LOS PACIENTES REGISTRADOS POR CLIENTE QUE NO ESTÉN ELIMINADOS
        $this->oldPatients = PatientClient::whereIn('client_id', $this->userclient)
        ->whereNull('deleted_at')
        ->whereMonth('created_at', '<=', Carbon::now()->subMonthNoOverflow()->month)
        ->count();
        //TRAE TODOS LOS PACIENTES REGISTRADOS POR CLIENTE
        $allPatients = PatientClient::whereIn('client_id', $this->userclient)
        ->count();
        //SE RESTAN LOS PACIENTES DE ESTE MES PARA OBTENER LOS OLD PATIENTS
        $this->allOldPatients = $allPatients - $patients;
        //$oldPatientsActive = ($oldPatientsActive < 0) ? 0 : $oldPatientsActive;
        //QUERY PARA OBTENER LOS PACIENTES ELIMINADOS Y SACAR EL PORCENTAJE
        $oldPatientsDeleted = PatientClient::whereIn('client_id', $this->userclient)
        ->whereNotNull('deleted_at')
        ->whereDate('deleted_at', '<=', Carbon::now())
        ->count();
        //SACAR PORCENTAJE DE PACIENTES ACTIVOS
        if ($this->allOldPatients > 0) {
            $this->percentageChange = ($oldPatientsDeleted/$this->allOldPatients) * 100;
        } else {
            $this->percentageChange = $this->allOldPatients > 0 ? 100 : 0;
        }
        //AGINAR ÍCONO Y CLASS SEGÚN EL PORCENTAJE
        $this->statusClass = $this->percentageChange < 100 ? 'status-pink' : 'status-green';
        $this->icon = $this->percentageChange < 100 ?  'sort-icon-02.svg': 'sort-icon-01.svg';
    }
}
