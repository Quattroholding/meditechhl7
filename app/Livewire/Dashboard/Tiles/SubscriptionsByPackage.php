<?php

namespace App\Livewire\Dashboard\Tiles;

use App\Models\ClientSubscription;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SubscriptionsByPackage extends Component
{
    public $labels = [];

    public $data = [];

    public $colors = [];

    public $total = 0;

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $cacheKey = 'dashboard_subscriptions_by_package_'.now()->format('Y-m-d');

        $chartData = Cache::tags(['dashboard', 'subscriptions'])
            ->remember($cacheKey, 300, function () {
                $subscriptionsByPackage = ClientSubscription::select('package_id', DB::raw('count(*) as count'))
                    ->where('status', 'active')
                    ->groupBy('package_id')
                    ->with('package:id,name')
                    ->get();

                $labels = [];
                $data = [];
                $colors = [
                    '#4CAF50', // Verde
                    '#2196F3', // Azul
                    '#FFC107', // Amarillo
                    '#FF5722', // Rojo
                    '#9C27B0', // Morado
                    '#00BCD4', // Cian
                    '#FF9800', // Naranja
                ];

                foreach ($subscriptionsByPackage as $index => $sub) {
                    $packageName = $sub->package->name ?? 'Sin Paquete';
                    $labels[] = $packageName;
                    $data[] = $sub->count;
                }

                return [
                    'labels' => $labels,
                    'data' => $data,
                    'colors' => array_slice($colors, 0, count($labels)),
                    'total' => array_sum($data),
                ];
            });

        $this->labels = $chartData['labels'];
        $this->data = $chartData['data'];
        $this->colors = $chartData['colors'];
        $this->total = $chartData['total'];
    }

    public function render()
    {
        return view('livewire.dashboard.tiles.subscriptions-by-package');
    }
}
