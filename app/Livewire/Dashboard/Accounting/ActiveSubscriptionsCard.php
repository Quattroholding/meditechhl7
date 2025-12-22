<?php

namespace App\Livewire\Dashboard\Accounting;

use App\Enums\SubscriptionStatus;
use App\Models\ClientSubscription;
use Livewire\Component;

class ActiveSubscriptionsCard extends Component
{
    public $activeCount = 0;

    public $totalCount = 0;

    public $monthlyRecurringRevenue = 0;

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        // Suscripciones activas
        $this->activeCount = ClientSubscription::query()
            ->where('status', SubscriptionStatus::ACTIVE)
            ->count();

        // Total de suscripciones
        $this->totalCount = ClientSubscription::query()->count();

        // MRR (Monthly Recurring Revenue)
        $this->monthlyRecurringRevenue = ClientSubscription::query()
            ->where('status', SubscriptionStatus::ACTIVE)
            ->with('package')
            ->get()
            ->sum(function ($subscription) {
                return $subscription->package->price ?? 0;
            });
    }

    public function render()
    {
        return view('livewire.dashboard.accounting.active-subscriptions-card');
    }
}
