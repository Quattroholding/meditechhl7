<?php

namespace App\Livewire\Dashboard\Tiles;

use App\Models\ClientInvoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class SubscriptionRevenueChart extends Component
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
        $cacheKey = 'dashboard_subscription_revenue_'.Carbon::now()->format('Y-m');

        $chartData = Cache::tags(['dashboard', 'subscriptions'])
            ->remember($cacheKey, 300, function () {
                $last30Days = collect();

                for ($i = 29; $i >= 0; $i--) {
                    $date = Carbon::today()->subDays($i);
                    $revenue = ClientInvoice::whereDate('created_at', $date)
                        ->where('status', 'paid')
                        ->sum('total') ?? 0;

                    $last30Days->push([
                        'date' => $date->format('d/m'),
                        'revenue' => round($revenue, 2),
                    ]);
                }

                return [
                    'labels' => $last30Days->pluck('date')->toArray(),
                    'data' => $last30Days->pluck('revenue')->toArray(),
                    'total' => round($last30Days->sum('revenue'), 2),
                ];
            });

        $this->labels = $chartData['labels'];
        $this->data = $chartData['data'];
        $this->total_month = $chartData['total'];
    }

    public function render()
    {
        return view('livewire.dashboard.tiles.subscription-revenue-chart');
    }
}
