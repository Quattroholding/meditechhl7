<?php

namespace App\Livewire\Dashboard\Tiles;

use App\Enums\SubscriptionStatus;
use App\Models\ClientSubscription;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SubscriptionsByStatus extends Component
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
        $cacheKey = 'dashboard_subscriptions_by_status_'.now()->format('Y-m-d');

        $chartData = Cache::tags(['dashboard', 'subscriptions'])
            ->remember($cacheKey, 300, function () {
                $subscriptionsByStatus = ClientSubscription::select('status', DB::raw('count(*) as count'))
                    ->groupBy('status')
                    ->get();

                $statusColors = [
                    'pending_activation' => '#2196F3', // Azul (primary)
                    'trial' => '#00BCD4',              // Cian (info)
                    'active' => '#4CAF50',             // Verde (success)
                    'past_due' => '#FFC107',           // Amarillo (warning)
                    'suspended' => '#F44336',          // Rojo (danger)
                    'cancelled' => '#9E9E9E',          // Gris (secondary)
                    'expired' => '#424242',            // Gris oscuro (dark)
                ];

                $labels = [];
                $data = [];
                $colors = [];

                foreach ($subscriptionsByStatus as $sub) {
                    // $sub->status es un enum SubscriptionStatus
                    $statusEnum = $sub->status;
                    $statusValue = $statusEnum->value;

                    $labels[] = $statusEnum->label();
                    $data[] = $sub->count;
                    $colors[] = $statusColors[$statusValue] ?? '#607D8B';
                }

                return [
                    'labels' => $labels,
                    'data' => $data,
                    'colors' => $colors,
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
        return view('livewire.dashboard.tiles.subscriptions-by-status');
    }
}
