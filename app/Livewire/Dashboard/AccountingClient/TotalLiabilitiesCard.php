<?php

namespace App\Livewire\Dashboard\AccountingClient;

use App\Enums\AccountType;
use App\Models\Accounting\AccountingAccount;
use Livewire\Component;

class TotalLiabilitiesCard extends Component
{
    public float $totalLiabilities = 0;

    public float $previousMonthLiabilities = 0;

    public float $percentageChange = 0;

    public function mount(): void
    {
        $this->calculateLiabilities();
    }

    public function calculateLiabilities(): void
    {
        $clientId = auth()->user()->getCurrentClient()->id;

        // Pasivos actuales
        $this->totalLiabilities = AccountingAccount::where('client_id', $clientId)
            ->where('account_type', AccountType::LIABILITY)
            ->sum('balance');

        // Pasivos del mes anterior
        $previousMonth = now()->subMonth()->endOfMonth();
        $this->previousMonthLiabilities = AccountingAccount::where('client_id', $clientId)
            ->where('account_type', AccountType::LIABILITY)
            ->where('created_at', '<=', $previousMonth)
            ->sum('balance');

        // Calcular porcentaje de cambio
        if ($this->previousMonthLiabilities > 0) {
            $this->percentageChange = (($this->totalLiabilities - $this->previousMonthLiabilities) / $this->previousMonthLiabilities) * 100;
        } else {
            $this->percentageChange = $this->totalLiabilities > 0 ? 100 : 0;
        }
    }

    public function render()
    {
        return view('livewire.dashboard.accounting-client.total-liabilities-card');
    }
}
