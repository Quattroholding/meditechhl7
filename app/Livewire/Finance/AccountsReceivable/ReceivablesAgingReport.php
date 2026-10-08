<?php

namespace App\Livewire\Finance\AccountsReceivable;

use App\Models\Patient;
use App\Services\Finance\AccountsReceivableService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Livewire\Component;

/**
 * Componente Livewire para reporte de antigüedad de cuentas por cobrar
 * Proporciona análisis detallado de facturas vencidas por rango de días
 */
class ReceivablesAgingReport extends Component
{
    public string $asOfDate = '';

    public string $patientFilter = '';

    public int $limit = 25;

    public array $ageingData = [];

    public array $agingBuckets = [
        '0-30' => 0,
        '31-60' => 0,
        '61-90' => 0,
        '90+' => 0,
    ];

    public array $summary = [
        '0-30' => 0,
        '31-60' => 0,
        '61-90' => 0,
        '90+' => 0,
        'total' => 0,
        'overdue_30' => 0,
        'overdue_60' => 0,
    ];

    protected $queryString = [
        'asOfDate' => ['except' => ''],
        'patientFilter' => ['except' => ''],
        'limit' => ['except' => 25],
    ];

    public function mount(): void
    {
        $this->authorize('receivables.view');
        if (! $this->asOfDate) {
            $this->asOfDate = now()->toDateString();
        }
        $this->generateReport();
    }

    /**
     * Genera el reporte de antigüedad
     */
    public function generateReport(): void
    {
        $clientId = auth()->user()->getCurrentClient()->id;
        $service = app(AccountsReceivableService::class);

        $report = $service->getAgingReport($clientId, Carbon::parse($this->asOfDate));

        // Apply patient filter if set
        if ($this->patientFilter) {
            $report['items'] = array_filter($report['items'], function ($item) {
                return stripos($item['patient'], $this->patientFilter) !== false;
            });

            // Recalculate totals based on filtered items
            $this->recalculateSummary($report['items']);
        } else {
            $this->summary = [
                '0-30' => $report['0-30'],
                '31-60' => $report['31-60'],
                '61-90' => $report['61-90'],
                '90+' => $report['90+'],
                'total' => $report['total'],
                'overdue_30' => $report['31-60'] + $report['61-90'] + $report['90+'],
                'overdue_60' => $report['61-90'] + $report['90+'],
            ];
        }

        $this->ageingData = $report['items'];
        $this->getAgingBuckets();
    }

    /**
     * Recalcula el resumen basado en los elementos filtrados
     */
    protected function recalculateSummary(array $items): void
    {
        $summary = [
            '0-30' => 0,
            '31-60' => 0,
            '61-90' => 0,
            '90+' => 0,
            'total' => 0,
        ];

        foreach ($items as $item) {
            $summary[$item['range']] += $item['balance'];
            $summary['total'] += $item['balance'];
        }

        $summary['overdue_30'] = $summary['31-60'] + $summary['61-90'] + $summary['90+'];
        $summary['overdue_60'] = $summary['61-90'] + $summary['90+'];

        $this->summary = $summary;
    }

    /**
     * Agrupa receivables por rango de antigüedad
     */
    public function getAgingBuckets(): array
    {
        $buckets = [
            '0-30' => 0,
            '31-60' => 0,
            '61-90' => 0,
            '90+' => 0,
        ];

        foreach ($this->ageingData as $item) {
            $buckets[$item['range']] += $item['balance'];
        }

        $this->agingBuckets = $buckets;

        return $buckets;
    }

    /**
     * Calcula el porcentaje de cada rango respecto al total
     */
    public function calculatePercentage(string $range): float
    {
        $total = $this->summary['total'];

        if ($total == 0) {
            return 0;
        }

        return ($this->summary[$range] / $total) * 100;
    }

    /**
     * Calcula los días de vencimiento desde la fecha de vencimiento
     */
    public function calculateDaysOverdue(string $dueDate): int
    {
        return max(0, now()->diffInDays(Carbon::parse($dueDate)));
    }

    /**
     * Obtiene el badge CSS para un rango de antigüedad
     */
    public function getAgeingBadgeClass(string $range): string
    {
        return match ($range) {
            '0-30' => 'badge-success',
            '31-60' => 'badge-warning',
            '61-90' => 'badge-danger',
            '90+' => 'badge-dark',
            default => 'badge-secondary',
        };
    }

    /**
     * Obtiene el color CSS para un rango de antigüedad
     */
    public function getAgeingColorClass(string $range): string
    {
        return match ($range) {
            '0-30' => 'text-success',
            '31-60' => 'text-warning',
            '61-90' => 'text-danger',
            '90+' => 'text-dark',
            default => 'text-secondary',
        };
    }

    public function updatedAsOfDate(): void
    {
        $this->generateReport();
    }

    public function updatedPatientFilter(): void
    {
        $this->generateReport();
    }

    public function updatedLimit(): void
    {
        $this->generateReport();
    }

    public function render(): View
    {
        return view('livewire.finance.accounts-receivable.receivables-aging-report', [
            'ageingData' => $this->ageingData,
            'summary' => $this->summary,
            'agingBuckets' => $this->agingBuckets,
            'asOfDate' => $this->asOfDate,
            'limit' => $this->limit,
        ]);
    }
}
