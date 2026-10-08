<?php

namespace App\Livewire\Dashboard\AccountingClient;

use App\Models\Treasury\TreasuryMovement;
use Livewire\Component;

class CashFlowSummary extends Component
{
    public float $totalIncome = 0;

    public float $totalExpense = 0;

    public float $netCashFlow = 0;

    public function mount(): void
    {
        $this->loadCashFlowData();
    }

    public function loadCashFlowData(): void
    {
        $clientId = auth()->user()->getCurrentClient()->id;
        $currentMonth = now()->startOfMonth();

        // Ingresos del mes (depósitos)
        $this->totalIncome = TreasuryMovement::where('client_id', $clientId)
            ->where('movement_type', 'deposit')
            ->where('movement_date', '>=', $currentMonth)
            ->sum('amount');

        // Egresos del mes (retiros)
        $this->totalExpense = TreasuryMovement::where('client_id', $clientId)
            ->where('movement_type', 'withdrawal')
            ->where('movement_date', '>=', $currentMonth)
            ->sum('amount');

        // Flujo neto
        $this->netCashFlow = $this->totalIncome - $this->totalExpense;
    }

    public function render()
    {
        return view('livewire.dashboard.accounting-client.cash-flow-summary');
    }
}
