<?php

namespace App\Livewire;
use App\Models\PatientClient;
use Livewire\Component;
use Carbon\Carbon;

class NewPatients extends Component
{
    public $userclient;
    public $patients;
    public $percentageChange;
    public $statusClass;
    public $icon;
    
     public function mount()
    {
        $this->getNewPatients();
    }
    public function render()
    {
        return view('livewire.new-patients');
    }

    public function getNewPatients(){
        
        $currentMonth = Carbon::now()->month;
        $lastMonth = Carbon::now()->subMonth()->month;
        $currentYear = Carbon::now()->year;
        $lastYear = Carbon::now()->subMonth()->year;
        //CLIENTES ASOCIADOS AL PRACTITIONER
        $this->userclient=auth()->user()->clients->pluck('id')->toArray();
        //QUERY PARA MOSTRAR PACEINTES REGISTRADOS EN EL MES ACTUAL
        $this->patients = PatientClient::whereMonth('created_at', $currentMonth)
        ->whereYear('created_at', $currentYear)
        ->whereIn('client_id', $this->userclient)
        ->count();
        //QUERY PARA MOSTRAR PACIENTES REGISTRADOS EL MES ANTERIOR
        $lastMonthPatients = PatientClient::whereMonth('created_at', $lastMonth)
        ->whereYear('created_at', $currentYear)
        ->whereIn('client_id', $this->userclient)
        ->count();
        //PORCENTAJE QUE COMPARA LA CANTIDAD DE PACIENTES REGISTRADOS EL MES PASADO CON EL DE EL MES ACTUAL
        if ($lastMonthPatients > 0) {
        $this->percentageChange = (($this->patients - $lastMonthPatients) / $lastMonthPatients) * 100;
    } else {
        $this->percentageChange = $this->patients > 0 ? 100 : 0;
    }
    //ASIGNAR ICON Y CLASS SEGÚN EL PORCENTAJE
    $this->statusClass = $this->percentageChange >= 0 ? 'status-green' : 'status-pink';
    $this->icon = $this->percentageChange >= 0 ? 'sort-icon-01.svg' : 'sort-icon-02.svg';
        //$this->patients=PatientClient::whereIn('client_id', $this->userclient)->count();
        //dd($userclient);
    }
}
