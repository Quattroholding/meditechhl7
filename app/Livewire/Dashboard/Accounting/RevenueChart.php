<?php

namespace App\Livewire\Dashboard\Accounting;

use App\Models\ClientInvoice;
use Livewire\Component;

class RevenueChart extends Component
{
    public $months = [];

    public $totalRevenues = [];

    public $netUtilities = [];

    public $taxAmounts = [];

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

            // Ingreso total (suma de totales de facturas pagadas)
            $totalRevenue = ClientInvoice::query()
                ->where('status', 'paid')
                ->whereYear('paid_at', $date->year)
                ->whereMonth('paid_at', $date->month)
                ->sum('total');

            // Utilidad neta (subtotal - descuentos de facturas pagadas)
            $netUtility = ClientInvoice::query()
                ->where('status', 'paid')
                ->whereYear('paid_at', $date->year)
                ->whereMonth('paid_at', $date->month)
                ->selectRaw('SUM(subtotal - discount_amount) as net_utility')
                ->value('net_utility') ?? 0;

            // Monto de impuestos (suma de tax_amount de facturas pagadas)
            $taxAmount = ClientInvoice::query()
                ->where('status', 'paid')
                ->whereYear('paid_at', $date->year)
                ->whereMonth('paid_at', $date->month)
                ->sum('tax_amount');

            $this->months[] = $monthName;
            $this->totalRevenues[] = $totalRevenue;
            $this->netUtilities[] = $netUtility;
            $this->taxAmounts[] = $taxAmount;
        }
    }

    public function render()
    {
        return view('livewire.dashboard.accounting.revenue-chart');
    }
}
