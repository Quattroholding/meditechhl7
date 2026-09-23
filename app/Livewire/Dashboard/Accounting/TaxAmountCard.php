<?php

namespace App\Livewire\Dashboard\Accounting;

use App\Models\ClientInvoice;
use Livewire\Component;

class TaxAmountCard extends Component
{
    public $currentMonthTaxAmount = 0;

    public $previousMonthTaxAmount = 0;

    public $percentageChange = 0;

    public function mount()
    {
        $this->calculateTaxAmount();
    }

    public function calculateTaxAmount()
    {
        // Impuestos del mes actual (solo facturas pagadas)
        $this->currentMonthTaxAmount = ClientInvoice::query()
            ->where('status', 'paid')
            ->whereYear('paid_at', now()->year)
            ->whereMonth('paid_at', now()->month)
            ->sum('tax_amount');

        // Impuestos del mes anterior (solo facturas pagadas)
        $this->previousMonthTaxAmount = ClientInvoice::query()
            ->where('status', 'paid')
            ->whereYear('paid_at', now()->subMonth()->year)
            ->whereMonth('paid_at', now()->subMonth()->month)
            ->sum('tax_amount');

        // Calcular porcentaje de cambio
        if ($this->previousMonthTaxAmount > 0) {
            $this->percentageChange = (($this->currentMonthTaxAmount - $this->previousMonthTaxAmount) / $this->previousMonthTaxAmount) * 100;
        } else {
            $this->percentageChange = $this->currentMonthTaxAmount > 0 ? 100 : 0;
        }
    }

    public function render()
    {
        return view('livewire.dashboard.accounting.tax-amount-card');
    }
}
