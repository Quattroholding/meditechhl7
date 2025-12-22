<?php

namespace App\Livewire\Dashboard\Accounting;

use App\Enums\PaymentStatus;
use App\Models\ClientInvoicePayment;
use Livewire\Component;

class RevenueChart extends Component
{
    public $months = [];

    public $revenues = [];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        // Últimos 6 meses
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthName = $date->locale('es')->format('M Y');

            $revenue = ClientInvoicePayment::query()
                ->where('status', PaymentStatus::COMPLETED)
                ->whereYear('payment_date', $date->year)
                ->whereMonth('payment_date', $date->month)
                ->sum('amount');

            $this->months[] = $monthName;
            $this->revenues[] = $revenue;
        }
    }

    public function render()
    {
        return view('livewire.dashboard.accounting.revenue-chart');
    }
}
