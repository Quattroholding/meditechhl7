<?php

namespace App\Livewire\Dashboard\AccountingClient;

use App\Models\Finance\SupplierInvoice;
use Livewire\Component;

class AccountsPayableCard extends Component
{
    public float $totalPayable = 0;

    public int $overdueCount = 0;

    public float $overdueAmount = 0;

    public function mount(): void
    {
        $this->calculatePayables();
    }

    public function calculatePayables(): void
    {
        $clientId = auth()->user()->getCurrentClient()->id;

        // Total pendiente por pagar
        $this->totalPayable = SupplierInvoice::where('client_id', $clientId)
            ->where('status', '!=', 'paid')
            ->sum('balance');

        // Facturas vencidas
        $this->overdueCount = SupplierInvoice::where('client_id', $clientId)
            ->where('status', 'overdue')
            ->count();

        $this->overdueAmount = SupplierInvoice::where('client_id', $clientId)
            ->where('status', 'overdue')
            ->sum('balance');
    }

    public function render()
    {
        return view('livewire.dashboard.accounting-client.accounts-payable-card');
    }
}
