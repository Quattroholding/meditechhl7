<?php

namespace App\Livewire\Dashboard\AccountingClient;

use App\Models\Treasury\Bank;
use App\Models\Treasury\CashRegister;
use Livewire\Component;

class TreasuryBalance extends Component
{
    public float $totalBankBalance = 0;

    public float $totalCashBalance = 0;

    public float $totalTreasury = 0;

    public array $banks = [];

    public array $cashRegisters = [];

    public function mount(): void
    {
        $this->loadTreasuryData();
    }

    public function loadTreasuryData(): void
    {
        $clientId = auth()->user()->getCurrentClient()->id;

        // Bancos
        $this->banks = Bank::where('client_id', $clientId)
            ->where('status', 'active')
            ->get()
            ->map(function ($bank) {
                $this->totalBankBalance += $bank->balance;

                return [
                    'name' => $bank->bank_name,
                    'account' => $bank->account_number,
                    'balance' => $bank->balance,
                ];
            })
            ->toArray();

        // Cajas
        $this->cashRegisters = CashRegister::where('client_id', $clientId)
            ->where('status', 'active')
            ->get()
            ->map(function ($cash) {
                $this->totalCashBalance += $cash->balance;

                return [
                    'name' => $cash->name,
                    'balance' => $cash->balance,
                ];
            })
            ->toArray();

        $this->totalTreasury = $this->totalBankBalance + $this->totalCashBalance;
    }

    public function render()
    {
        return view('livewire.dashboard.accounting-client.treasury-balance');
    }
}
