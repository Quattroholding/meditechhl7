<?php

namespace App\Livewire\Dashboard\AccountingClient;

use App\Models\Finance\AccountsReceivable;
use Livewire\Component;

class AccountsReceivableCard extends Component
{
    public float $totalReceivable = 0;

    public int $overdueCount = 0;

    public float $overdueAmount = 0;

    public function mount(): void
    {
        $this->calculateReceivables();
    }

    public function calculateReceivables(): void
    {
        $clientId = auth()->user()->getCurrentClient()->id;

        // Total pendiente de cobro
        $this->totalReceivable = AccountsReceivable::where('client_id', $clientId)
            ->where('status', '!=', 'paid')
            ->sum('balance');

        // Cuentas vencidas
        $this->overdueCount = AccountsReceivable::where('client_id', $clientId)
            ->where('status', 'overdue')
            ->count();

        $this->overdueAmount = AccountsReceivable::where('client_id', $clientId)
            ->where('status', 'overdue')
            ->sum('balance');
    }

    public function render()
    {
        return view('livewire.dashboard.accounting-client.accounts-receivable-card');
    }
}
