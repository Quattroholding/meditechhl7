<?php

namespace App\Livewire\Dashboard\Accounting;

use App\Enums\PaymentStatus;
use App\Models\ClientInvoicePayment;
use Livewire\Component;

class MonthlyRevenueCard extends Component
{
    public $currentMonthRevenue = 0;

    public $previousMonthRevenue = 0;

    public $percentageChange = 0;

    public function mount()
    {
        $this->calculateRevenue();
    }

    public function calculateRevenue()
    {
        // Ingresos del mes actual
        $this->currentMonthRevenue = ClientInvoicePayment::query()
            ->where('status', PaymentStatus::COMPLETED)
            ->whereYear('payment_date', now()->year)
            ->whereMonth('payment_date', now()->month)
            ->sum('amount');

        // Ingresos del mes anterior
        $this->previousMonthRevenue = ClientInvoicePayment::query()
            ->where('status', PaymentStatus::COMPLETED)
            ->whereYear('payment_date', now()->subMonth()->year)
            ->whereMonth('payment_date', now()->subMonth()->month)
            ->sum('amount');

        // Calcular porcentaje de cambio
        if ($this->previousMonthRevenue > 0) {
            $this->percentageChange = (($this->currentMonthRevenue - $this->previousMonthRevenue) / $this->previousMonthRevenue) * 100;
        } else {
            $this->percentageChange = $this->currentMonthRevenue > 0 ? 100 : 0;
        }
    }

    public function render()
    {
        return view('livewire.dashboard.accounting.monthly-revenue-card');
    }
}
