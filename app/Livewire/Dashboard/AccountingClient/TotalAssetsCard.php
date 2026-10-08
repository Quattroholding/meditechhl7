<?php

namespace App\Livewire\Dashboard\AccountingClient;

use App\Enums\AccountType;
use App\Models\Accounting\AccountingAccount;
use Livewire\Component;

class TotalAssetsCard extends Component
{
    public float $totalAssets = 0;

    public float $previousMonthAssets = 0;

    public float $percentageChange = 0;

    public function mount(): void
    {
        $this->calculateAssets();
    }

    public function calculateAssets(): void
    {
        $clientId = auth()->user()->getCurrentClient()->id;

        // Activos actuales
        $this->totalAssets = AccountingAccount::where('client_id', $clientId)
            ->where('account_type', AccountType::ASSET)
            ->sum('balance');

        // Activos del mes anterior (aproximado - usando fecha)
        $previousMonth = now()->subMonth()->endOfMonth();
        $this->previousMonthAssets = AccountingAccount::where('client_id', $clientId)
            ->where('account_type', AccountType::ASSET)
            ->where('created_at', '<=', $previousMonth)
            ->sum('balance');

        // Calcular porcentaje de cambio
        if ($this->previousMonthAssets > 0) {
            $this->percentageChange = (($this->totalAssets - $this->previousMonthAssets) / $this->previousMonthAssets) * 100;
        } else {
            $this->percentageChange = $this->totalAssets > 0 ? 100 : 0;
        }
    }

    public function render()
    {
        return view('livewire.dashboard.accounting-client.total-assets-card');
    }
}
