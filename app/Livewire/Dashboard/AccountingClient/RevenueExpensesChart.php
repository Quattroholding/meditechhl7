<?php

namespace App\Livewire\Dashboard\AccountingClient;

use App\Enums\AccountType;
use App\Models\Accounting\AccountingAccount;
use Livewire\Component;

class RevenueExpensesChart extends Component
{
    public array $chartData = [];

    public array $monthLabels = [];

    public function mount(): void
    {
        $this->loadChartData();
    }

    public function loadChartData(): void
    {
        $clientId = auth()->user()->getCurrentClient()->id;
        $revenues = [];
        $expenses = [];
        $labels = [];

        // Datos de los últimos 6 meses
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $labels[] = $date->locale('es')->translatedFormat('M');

            // Ingresos del mes
            $monthRevenue = AccountingAccount::where('client_id', $clientId)
                ->where('account_type', AccountType::INCOME)
                ->whereMonth('updated_at', $date->month)
                ->whereYear('updated_at', $date->year)
                ->sum('balance');

            $revenues[] = max(0, abs($monthRevenue));

            // Gastos del mes
            $monthExpense = AccountingAccount::where('client_id', $clientId)
                ->where('account_type', AccountType::EXPENSE)
                ->whereMonth('updated_at', $date->month)
                ->whereYear('updated_at', $date->year)
                ->sum('balance');

            $expenses[] = max(0, abs($monthExpense));
        }

        $this->monthLabels = $labels;
        $this->chartData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Ingresos',
                    'data' => $revenues,
                    'backgroundColor' => 'rgba(40, 167, 69, 0.1)',
                    'borderColor' => 'rgba(40, 167, 69, 1)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Gastos',
                    'data' => $expenses,
                    'backgroundColor' => 'rgba(220, 53, 69, 0.1)',
                    'borderColor' => 'rgba(220, 53, 69, 1)',
                    'borderWidth' => 2,
                ],
            ],
        ];
    }

    public function render()
    {
        return view('livewire.dashboard.accounting-client.revenue-expenses-chart');
    }
}
