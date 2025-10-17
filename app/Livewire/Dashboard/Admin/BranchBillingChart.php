<?php

namespace App\Livewire\Dashboard\Admin;

use App\Models\Branch;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class BranchBillingChart extends Component
{
    public $year;

    public $selectedBranches = [];

    public $isLoading = true;

    public function mount()
    {
        $this->year = now()->year;
    }

    public function loadData()
    {
        $chartData = $this->getChartData();
        $this->isLoading = false;
        $this->dispatch('loadBranchBillingChart', $chartData);
    }

    public function updatedYear()
    {
        $this->dispatch('updateChart', $this->getChartData());
    }

    public function updatedSelectedBranches()
    {
        $this->dispatch('updateChart', $this->getChartData());
    }

    public function getChartData()
    {
        // Si no hay sedes seleccionadas, mostrar las primeras 3 sedes con más facturas
        if (empty($this->selectedBranches)) {
            $branches = Branch::query()
                ->withCount(['invoices' => function ($query) {
                    $query->whereNotNull('branch_id');
                }])
                ->having('invoices_count', '>', 0)
                ->orderByDesc('invoices_count')
                ->limit(3)
                ->get();
        } else {
            $branches = Branch::query()
                ->whereIn('id', $this->selectedBranches)
                ->get();
        }

        $series = [];

        foreach ($branches as $branch) {
            $monthlyData = Invoice::query()
                ->where('branch_id', $branch->id)
                ->whereYear('date', $this->year)
                ->whereIn('status', ['issued', 'balanced'])
                ->whereIn('payment_status', ['paid', 'partial', 'unpaid'])
                ->select(
                    DB::raw('MONTH(date) as month'),
                    DB::raw('SUM(total_amount) as total')
                )
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->keyBy('month');

            $data = [];
            for ($month = 1; $month <= 12; $month++) {
                $data[] = $monthlyData->has($month)
                    ? (float) $monthlyData->get($month)->total
                    : 0;
            }

            $series[] = [
                'name' => $branch->name,
                'data' => $data,
            ];
        }

        return [
            'series' => $series,
            'categories' => [
                'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre',
            ],
        ];
    }

    public function render()
    {
        $branches = Branch::orderBy('name')->get();
        $chartData = $this->getChartData();

        return view('livewire.dashboard.admin.branch-billing-chart', [
            'branches' => $branches,
            'chartData' => $chartData,
        ]);
    }
}
