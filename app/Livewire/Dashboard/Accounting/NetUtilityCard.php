<?php

namespace App\Livewire\Dashboard\Accounting;

use App\Models\ClientInvoice;
use Livewire\Component;

class NetUtilityCard extends Component
{
    public $currentMonthNetUtility = 0;

    public $previousMonthNetUtility = 0;

    public $percentageChange = 0;

    public function mount()
    {
        $this->calculateNetUtility();
    }

    public function calculateNetUtility()
    {
        // Utilidad neta del mes actual (subtotal - descuentos, solo facturas pagadas)
        $this->currentMonthNetUtility = ClientInvoice::query()
            ->where('status', 'paid')
            ->whereYear('paid_at', now()->year)
            ->whereMonth('paid_at', now()->month)
            ->selectRaw('SUM(subtotal - discount_amount) as net_utility')
            ->value('net_utility') ?? 0;

        // Utilidad neta del mes anterior (subtotal - descuentos, solo facturas pagadas)
        $this->previousMonthNetUtility = ClientInvoice::query()
            ->where('status', 'paid')
            ->whereYear('paid_at', now()->subMonth()->year)
            ->whereMonth('paid_at', now()->subMonth()->month)
            ->selectRaw('SUM(subtotal - discount_amount) as net_utility')
            ->value('net_utility') ?? 0;

        // Calcular porcentaje de cambio
        if ($this->previousMonthNetUtility > 0) {
            $this->percentageChange = (($this->currentMonthNetUtility - $this->previousMonthNetUtility) / $this->previousMonthNetUtility) * 100;
        } else {
            $this->percentageChange = $this->currentMonthNetUtility > 0 ? 100 : 0;
        }
    }

    public function render()
    {
        return view('livewire.dashboard.accounting.net-utility-card');
    }
}
