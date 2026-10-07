<?php

namespace App\Livewire\Dashboard\AccountingClient;

use App\Enums\AccountType;
use App\Models\Accounting\AccountingAccount;
use Livewire\Component;

class NetIncomeCard extends Component
{
    public float $netIncome = 0;

    public float $previousMonthIncome = 0;

    public float $percentageChange = 0;

    public function mount(): void
    {
        $this->calculateNetIncome();
    }

    public function calculateNetIncome(): void
    {
        $clientId = auth()->user()->getCurrentClient()->id;

        // Calcular ingresos totales
        $income = AccountingAccount::where('client_id', $clientId)
            ->where('account_type', AccountType::INCOME)
            ->sum('balance');

        // Calcular costos totales
        $costs = AccountingAccount::where('client_id', $clientId)
            ->where('account_type', AccountType::COST)
            ->sum('balance');

        // Calcular gastos totales
        $expenses = AccountingAccount::where('client_id', $clientId)
            ->where('account_type', AccountType::EXPENSE)
            ->sum('balance');

        // Utilidad neta = Ingresos - Costos - Gastos
        $this->netIncome = abs($income) - abs($costs) - abs($expenses);

        // Mes anterior
        $previousMonth = now()->subMonth()->endOfMonth();
        $prevIncome = AccountingAccount::where('client_id', $clientId)
            ->where('account_type', AccountType::INCOME)
            ->where('created_at', '<=', $previousMonth)
            ->sum('balance');

        $prevCosts = AccountingAccount::where('client_id', $clientId)
            ->where('account_type', AccountType::COST)
            ->where('created_at', '<=', $previousMonth)
            ->sum('balance');

        $prevExpenses = AccountingAccount::where('client_id', $clientId)
            ->where('account_type', AccountType::EXPENSE)
            ->where('created_at', '<=', $previousMonth)
            ->sum('balance');

        $this->previousMonthIncome = abs($prevIncome) - abs($prevCosts) - abs($prevExpenses);

        // Calcular porcentaje de cambio
        if ($this->previousMonthIncome != 0) {
            $this->percentageChange = (($this->netIncome - $this->previousMonthIncome) / abs($this->previousMonthIncome)) * 100;
        } else {
            $this->percentageChange = $this->netIncome > 0 ? 100 : 0;
        }
    }

    public function render()
    {
        return view('livewire.dashboard.accounting-client.net-income-card');
    }
}
