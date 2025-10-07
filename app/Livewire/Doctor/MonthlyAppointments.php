<?php

namespace App\Livewire\Doctor;

use App\Models\Appointment;
use Carbon\Carbon;
use Livewire\Component;

class MonthlyAppointments extends Component
{
    public $userclient;

    public $appointments;

    public $percentageChange;

    public $statusClass;

    public $icon;

    public $order;

    public $isLoading = true;

    public function mount()
    {
        // Inicializar variables para evitar errores
        $this->appointments = 0;
        $this->percentageChange = 0;
        $this->statusClass = 'status-green';
        $this->icon = 'sort-icon-01.svg';
    }

    public function loadData()
    {
        $this->getMonthlyAppointments();
        $this->isLoading = false;
    }

    public function render()
    {
        return view('livewire.doctor.monthly-appointments');
    }

    public function getMonthlyAppointments()
    {
        $currentMonth = Carbon::now()->month;
        $lastMonth = Carbon::now()->subMonthNoOverflow()->month;
        $currentYear = Carbon::now()->year;
        $lastYear = Carbon::now()->subMonthNoOverflow()->year;

        // CLIENTES ASOCIADOS AL PRACTITIONER
        $this->userclient = auth()->user()->clients->pluck('id')->toArray();

        // QUERY PARA MOSTRAR CITAS REGISTRADAS EN EL MES ACTUAL
        $this->appointments = Appointment::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->whereNull('deleted_at')
            ->whereIn('client_id', $this->userclient)
            ->count();

        // QUERY PARA MOSTRAR CITAS REGISTRADAS EL MES ANTERIOR
        $lastMonthAppointments = Appointment::whereMonth('created_at', $lastMonth)
            ->whereYear('created_at', $lastYear)
            ->whereNull('deleted_at')
            ->whereIn('client_id', $this->userclient)
            ->count();

        // PORCENTAJE QUE COMPARA LA CANTIDAD DE CITAS REGISTRADAS EL MES PASADO CON EL DE EL MES ACTUAL
        if ($lastMonthAppointments > 0) {
            $this->percentageChange = (($this->appointments - $lastMonthAppointments) / $lastMonthAppointments) * 100;
            if ($this->percentageChange == 0) {
                $this->percentageChange = 100;
            }
        } else {
            $this->percentageChange = $this->appointments > 0 ? 100 : 0;
        }

        // ASIGNAR ICON Y CLASS SEGÚN EL PORCENTAJE
        $this->statusClass = $this->percentageChange >= 0 ? 'status-green' : 'status-pink';
        $this->icon = $this->percentageChange >= 0 ? 'sort-icon-01.svg' : 'sort-icon-02.svg';
    }
}
