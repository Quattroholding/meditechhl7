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
        $user = auth()->user();
        $isPractitioner = $user->practitioner !== null;
        $clientId = $user->default_client_id;

        // Query base para consultas en progreso
        $encountersQuery = Encounter::query()
            ->where('status', 'in-progress');

        // Filtrar según tipo de usuario
        if ($isPractitioner) {
            $encountersQuery->where('practitioner_id', $user->practitioner->id);
        } else {
            // Para recepcionistas, filtrar por practitioners del mismo cliente
            $encountersQuery->whereHas('practitioner', function ($q) use ($clientId) {
                $q->whereHas('user', function ($q2) use ($clientId) {
                    $q2->where('default_client_id', $clientId);
                });
            });
        }

        $this->consultasEnProgreso = $encountersQuery->count();

        // Comparar con el mes anterior
        $lastMonth = Carbon::now()->subMonthNoOverflow()->month;
        $lastYear = Carbon::now()->subMonthNoOverflow()->year;

        // Query para consultas del mes anterior
        $lastMonthQuery = Encounter::query()
            ->where('status', 'in-progress')
            ->whereMonth('created_at', $lastMonth)
            ->whereYear('created_at', $lastYear);

        // Aplicar mismo filtro según tipo de usuario
        if ($isPractitioner) {
            $lastMonthQuery->where('practitioner_id', $user->practitioner->id);
        } else {
            // Para recepcionistas, filtrar por practitioners del mismo cliente
            $lastMonthQuery->whereHas('practitioner', function ($q) use ($clientId) {
                $q->whereHas('user', function ($q2) use ($clientId) {
                    $q2->where('default_client_id', $clientId);
                });
            });
        }

        $lastMonthConsultas = $lastMonthQuery->count();

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
