<?php

namespace App\Livewire\Doctor;

use App\Models\Encounter;
use Carbon\Carbon;
use Livewire\Component;

class ConsultasEnProgreso extends Component
{
    public $consultasEnProgreso;

    public $percentageChange;

    public $statusClass;

    public $icon;

    public $order;

    public $isLoading = true;

    public function mount()
    {
        // Inicializar variables para evitar errores
        $this->consultasEnProgreso = 0;
        $this->percentageChange = 0;
        $this->statusClass = 'status-green';
        $this->icon = 'sort-icon-01.svg';
    }

    public function loadData()
    {
        $this->getConsultasEnProgreso();
        $this->isLoading = false;
    }

    public function render()
    {
        return view('livewire.doctor.consultas-en-progreso');
    }

    public function getConsultasEnProgreso()
    {
        $practitionerId = auth()->user()->practitioner->id;

        // Consultas en progreso (estado 'in-progress')
        $this->consultasEnProgreso = Encounter::where('practitioner_id', $practitionerId)
            ->where('status', 'in-progress')
            ->whereNull('deleted_at')
            ->count();

        // Comparar con el mes anterior
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $lastMonth = Carbon::now()->subMonthNoOverflow()->month;
        $lastYear = Carbon::now()->subMonthNoOverflow()->year;

        $lastMonthConsultas = Encounter::where('practitioner_id', $practitionerId)
            ->where('status', 'in-progress')
            ->whereMonth('created_at', $lastMonth)
            ->whereYear('created_at', $lastYear)
            ->whereNull('deleted_at')
            ->count();

        // Calcular porcentaje de cambio
        if ($lastMonthConsultas > 0) {
            $this->percentageChange = (($this->consultasEnProgreso - $lastMonthConsultas) / $lastMonthConsultas) * 100;
            if ($this->percentageChange == 0) {
                $this->percentageChange = 100;
            }
        } else {
            $this->percentageChange = $this->consultasEnProgreso > 0 ? 100 : 0;
        }

        // Asignar icon y class según el porcentaje (menos consultas en progreso es mejor)
        $this->statusClass = $this->percentageChange <= 0 ? 'status-green' : 'status-pink';
        $this->icon = $this->percentageChange <= 0 ? 'sort-icon-01.svg' : 'sort-icon-02.svg';
    }
}
