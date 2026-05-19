<?php

namespace App\Livewire\Dashboard\Tiles;

use App\Models\Invoice;
use Carbon\Carbon;
use Livewire\Component;

class RevenueChart extends Component
{
    public $labels = [];

    public $data = [];

    public $total_month = 0;

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $currentMonth = Carbon::now();
        $startOfMonth = $currentMonth->copy()->startOfMonth();
        $today = Carbon::today();

        $monthDays = collect();

        // Iterar desde el día 1 del mes hasta hoy
        for ($date = $startOfMonth->copy(); $date->lte($today); $date->addDay()) {
            $revenue = Invoice::whereDate('created_at', $date)->sum('total_net') ?? 0;

            $monthDays->push([
                'date' => $date->format('d/m'),
                'revenue' => round($revenue, 2),
            ]);
        }

        $this->labels = $monthDays->pluck('date')->toArray();
        $this->data = $monthDays->pluck('revenue')->toArray();
        $this->total_month = round($monthDays->sum('revenue'), 2);
    }

    public function render()
    {
        return view('livewire.dashboard.tiles.revenue-chart');
    }
}
