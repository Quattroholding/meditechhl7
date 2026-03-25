<?php

namespace App\Livewire\Dashboard\Tiles;

use App\Models\Appointment;
use Carbon\Carbon;
use Livewire\Component;

class AppointmentsBySource extends Component
{
    public $web = 0;

    public $whatsapp = 0;

    public $total = 0;

    public $chartData = [];

    public $chartLabels = [];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // Contar citas por source_creation del mes actual
        $this->web = Appointment::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->where('source_creation', 'web')
            ->count();

        $this->whatsapp = Appointment::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->where('source_creation', 'whatsapp')
            ->count();

        $this->total = $this->web + $this->whatsapp;

        // Preparar datos para el gráfico
        $this->chartLabels = ['Web', 'WhatsApp'];
        $this->chartData = [$this->web, $this->whatsapp];
    }

    public function render()
    {
        return view('livewire.dashboard.tiles.appointments-by-source');
    }
}
