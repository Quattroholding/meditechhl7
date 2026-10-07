<?php

namespace App\Livewire\Dashboard\AccountingClient;

use App\Enums\AccountType;
use App\Models\Accounting\AccountingAccount;
use Livewire\Component;

class BalanceSheetSummary extends Component
{
    public float $totalAssets = 0;

    public float $totalLiabilities = 0;

    public float $totalEquity = 0;

    public bool $isBalanced = false;

    public function mount(): void
    {
        $this->calculateBalanceSheet();
    }

    public function calculateBalanceSheet(): void
    {
        $clientId = auth()->user()->getCurrentClient()->id;

        $this->totalAssets = AccountingAccount::where('client_id', $clientId)
            ->where('account_type', AccountType::ASSET)
            ->sum('balance');

        $this->totalLiabilities = AccountingAccount::where('client_id', $clientId)
            ->where('account_type', AccountType::LIABILITY)
            ->sum('balance');

        $this->totalEquity = AccountingAccount::where('client_id', $clientId)
            ->where('account_type', AccountType::EQUITY)
            ->sum('balance');

        // Validar que Activos = Pasivos + Patrimonio
        $this->isBalanced = abs($this->totalAssets - ($this->totalLiabilities + $this->totalEquity)) < 0.01;
    }

    public function render()
    {
        return view('livewire.dashboard.accounting-client.balance-sheet-summary');
    }
}
