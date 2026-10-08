<?php

namespace App\Livewire\Dashboard\AccountingClient;

use App\Enums\AccountType;
use App\Models\Accounting\AccountingAccount;
use Livewire\Component;

class EquityCard extends Component
{
    public float $totalEquity = 0;

    public float $previousMonthEquity = 0;

    public float $percentageChange = 0;

    public function mount(): void
    {
        $this->calculateEquity();
    }

    public function calculateEquity(): void
    {
        $clientId = auth()->user()->getCurrentClient()->id;

        // Patrimonio actual
        $this->totalEquity = AccountingAccount::where('client_id', $clientId)
            ->where('account_type', AccountType::EQUITY)
            ->sum('balance');

        // Patrimonio del mes anterior
        $previousMonth = now()->subMonth()->endOfMonth();
        $this->previousMonthEquity = AccountingAccount::where('client_id', $clientId)
            ->where('account_type', AccountType::EQUITY)
            ->where('created_at', '<=', $previousMonth)
            ->sum('balance');

        // Calcular porcentaje de cambio
        if ($this->previousMonthEquity > 0) {
            $this->percentageChange = (($this->totalEquity - $this->previousMonthEquity) / $this->previousMonthEquity) * 100;
        } else {
            $this->percentageChange = $this->totalEquity > 0 ? 100 : 0;
        }
    }

    public function render()
    {
        return view('livewire.dashboard.accounting-client.equity-card');
    }
}
