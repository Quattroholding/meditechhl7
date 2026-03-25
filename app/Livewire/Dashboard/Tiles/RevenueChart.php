<?php

namespace App\Livewire\Dashboard\Tiles;

use App\Models\Invoice;
use Carbon\Carbon;
use Livewire\Component;

class RevenueChart extends Component
{
    public $labels = [];

    public $data = [];

    public $total_week = 0;

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $last7Days = collect();

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $revenue = Invoice::whereDate('created_at', $date)->sum('total_net');

            $last7Days->push([
                'date' => $date->format('d/m'),
                'revenue' => round($revenue, 2),
            ]);
        }

        $this->labels = $last7Days->pluck('date')->toArray();
        $this->data = $last7Days->pluck('revenue')->toArray();
        $this->total_week = round($last7Days->sum('revenue'), 2);
    }

    public function render()
    {
        return view('livewire.dashboard.tiles.revenue-chart');
    }
}
