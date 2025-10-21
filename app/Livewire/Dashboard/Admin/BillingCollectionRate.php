<?php

namespace App\Livewire\Dashboard\Admin;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class BillingCollectionRate extends Component
{
    public $year;

    public $isLoading = true;

    public function mount()
    {
        $this->year = now()->year;
    }

    public function loadData()
    {
        $chartData = $this->getChartData();
        $this->isLoading = false;
        $this->dispatch('loadBillingCollectionRateChart', $chartData);
    }

    public function updatedYear()
    {
        $this->dispatch('updateBillingCollectionRateChart', $this->getChartData());
    }

    public function getChartData()
    {
        // Facturación mensual (total_amount de invoices)
        $billingData = Invoice::query()
            ->whereYear('date', $this->year)
            ->whereIn('status', ['issued', 'balanced'])
            ->select(
                DB::raw('MONTH(date) as month'),
                DB::raw('SUM(total_amount) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        // Cobros realizados mensualmente (suma de payments)
        $paymentsData = Payment::query()
            ->whereYear('payment_date', $this->year)
            ->where('status', 'completed')
            ->select(
                DB::raw('MONTH(payment_date) as month'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $billing = [];
        $collections = [];
        $collectionRate = [];

        for ($month = 1; $month <= 12; $month++) {
            $billingAmount = $billingData->has($month)
                ? (float) $billingData->get($month)->total
                : 0;

            $collectionAmount = $paymentsData->has($month)
                ? (float) $paymentsData->get($month)->total
                : 0;

            $billing[] = $billingAmount;
            $collections[] = $collectionAmount;

            // Calcular tasa de cobro (porcentaje)
            $rate = $billingAmount > 0 ? ($collectionAmount / $billingAmount) * 100 : 0;
            $collectionRate[] = round($rate, 2);
        }

        return [
            'billing' => $billing,
            'collections' => $collections,
            'collectionRate' => $collectionRate,
            'categories' => [
                'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre',
            ],
        ];
    }

    public function render()
    {
        $chartData = $this->getChartData();

        // Calcular resumen anual
        $totalBilling = array_sum($chartData['billing']);
        $totalCollections = array_sum($chartData['collections']);
        $averageRate = $totalBilling > 0 ? ($totalCollections / $totalBilling) * 100 : 0;

        return view('livewire.dashboard.admin.billing-collection-rate', [
            'chartData' => $chartData,
            'totalBilling' => $totalBilling,
            'totalCollections' => $totalCollections,
            'averageRate' => round($averageRate, 2),
        ]);
    }
}
