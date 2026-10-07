<?php

namespace App\Livewire\Dashboard\AccountingClient;

use App\Models\Accounting\JournalEntryLine;
use App\Models\Finance\CostCenter;
use Livewire\Component;

class CostCentersDistribution extends Component
{
    public array $costCenters = [];

    public array $chartData = [];

    public function mount(): void
    {
        $this->loadCostCentersData();
    }

    public function loadCostCentersData(): void
    {
        $clientId = auth()->user()->getCurrentClient()->id;

        // Obtener distribución de costos por centro
        $costCenterData = JournalEntryLine::whereHas('journalEntry', function ($q) use ($clientId) {
            $q->where('client_id', $clientId)->where('status', 'posted');
        })
            ->whereNotNull('cost_center_id')
            ->selectRaw('cost_center_id, SUM(COALESCE(debit, 0) + COALESCE(credit, 0)) as total')
            ->groupBy('cost_center_id')
            ->get();

        $labels = [];
        $data = [];
        $colors = ['#28a745', '#dc3545', '#ffc107', '#17a2b8', '#6f42c1', '#e83e8c'];

        foreach ($costCenterData as $index => $item) {
            if ($item->costCenter) {
                $labels[] = $item->costCenter->name;
                $data[] = abs($item->total);
            }
        }

        $this->chartData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => array_slice($colors, 0, count($data)),
                    'borderColor' => array_slice($colors, 0, count($data)),
                    'borderWidth' => 2,
                ],
            ],
        ];

        $this->costCenters = CostCenter::where('client_id', $clientId)
            ->where('status', 'active')
            ->take(5)
            ->get()
            ->toArray();
    }

    public function render()
    {
        return view('livewire.dashboard.accounting-client.cost-centers-distribution');
    }
}
