<?php

namespace App\Livewire\Treasury;

use App\Models\Treasury\Bank;
use App\Services\Treasury\TreasuryService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Livewire\Component;

/**
 * Componente Livewire para reporte de flujo de caja
 * Proporciona análisis detallado de ingresos, egresos y balance neto
 */
class CashFlowReport extends Component
{
    public string $startDate = '';

    public string $endDate = '';

    public ?int $bankId = null;

    public array $reportData = [
        'initial_balance' => 0,
        'income' => 0,
        'expense' => 0,
        'transfer' => 0,
        'net_flow' => 0,
        'final_balance' => 0,
        'transactions' => [],
        'income_summary' => [],
        'expense_summary' => [],
    ];

    public array $summary = [];

    public array $banks = [];

    protected $queryString = [
        'startDate' => ['except' => ''],
        'endDate' => ['except' => ''],
        'bankId' => ['except' => null],
    ];

    public function mount(): void
    {
        $this->authorize('treasury.reports.view');

        if (! $this->startDate) {
            $this->startDate = now()->startOfMonth()->toDateString();
        }

        if (! $this->endDate) {
            $this->endDate = now()->endOfMonth()->toDateString();
        }

        $clientId = auth()->user()->getCurrentClient()->id;
        $this->banks = Bank::where('client_id', $clientId)
            ->where('status', 'active')
            ->orderBy('bank_name')
            ->get()
            ->toArray();

        $this->generateReport();
    }

    /**
     * Genera el reporte de flujo de caja
     */
    public function generateReport(): void
    {
        $this->validateDates();

        $clientId = auth()->user()->getCurrentClient()->id;
        $service = app(TreasuryService::class);

        $startDate = Carbon::parse($this->startDate);
        $endDate = Carbon::parse($this->endDate);

        // Obtener movimientos del período
        $movements = $service->getMovementsByPeriod(
            $clientId,
            $startDate,
            $endDate,
            $this->bankId
        );

        // Obtener resumen de movimientos
        $movementsSummary = $service->getMovementsSummary($clientId, $startDate, $endDate);

        // Calcular saldo inicial (balance del banco antes del período)
        $initialBalance = $this->getInitialBalance($clientId);

        // Agrupar transacciones
        $transactions = $this->groupTransactions($movements);

        // Agrupar ingresos y egresos
        $incomeSummary = $this->groupByType($movements, 'income');
        $expenseSummary = $this->groupByType($movements, 'expense');

        // Calcular flujo neto
        $netFlow = $movementsSummary['income'] - $movementsSummary['expense'];

        // Calcular saldo final
        $finalBalance = $initialBalance + $netFlow;

        // Calcular balances acumulativos
        $transactionsWithRunning = $this->calculateRunningBalance(
            $transactions,
            $initialBalance
        );

        $this->reportData = [
            'initial_balance' => $initialBalance,
            'income' => $movementsSummary['income'],
            'expense' => $movementsSummary['expense'],
            'transfer' => $movementsSummary['transfer'],
            'net_flow' => $netFlow,
            'final_balance' => $finalBalance,
            'transactions' => $transactionsWithRunning,
            'income_summary' => $incomeSummary,
            'expense_summary' => $expenseSummary,
        ];

        $this->calculateSummaryMetrics();
    }

    /**
     * Valida que las fechas sean válidas
     */
    protected function validateDates(): void
    {
        if (empty($this->startDate) || empty($this->endDate)) {
            $this->addError('dates', 'Las fechas inicial y final son requeridas.');

            return;
        }

        $startDate = Carbon::parse($this->startDate);
        $endDate = Carbon::parse($this->endDate);

        if ($startDate->greaterThan($endDate)) {
            $this->addError('dates', 'La fecha inicial debe ser menor que la fecha final.');
        }
    }

    /**
     * Obtiene el saldo inicial (saldo del banco antes del período)
     */
    protected function getInitialBalance(int $clientId): float
    {
        $startDate = Carbon::parse($this->startDate);

        if ($this->bankId) {
            $bank = Bank::find($this->bankId);
            if (! $bank) {
                return 0;
            }

            // Calcular movimientos antes del período
            $service = app(TreasuryService::class);
            $previousMovements = $service->getMovementsByPeriod(
                $clientId,
                Carbon::create(2000, 1, 1),
                $startDate->copy()->subDay(),
                $this->bankId
            );

            $income = $previousMovements->where('movement_type', 'income')->sum('amount');
            $expense = $previousMovements->where('movement_type', 'expense')->sum('amount');

            return $income - $expense;
        }

        return 0;
    }

    /**
     * Agrupa transacciones por banco
     */
    protected function groupTransactions($movements): array
    {
        $grouped = [];

        foreach ($movements as $movement) {
            $bankName = $movement->bank?->bank_name ?? 'Sin banco';
            if (! isset($grouped[$bankName])) {
                $grouped[$bankName] = [];
            }

            $grouped[$bankName][] = [
                'date' => $movement->movement_date->toDateString(),
                'description' => $movement->description,
                'reference' => $movement->reference_number,
                'type' => $movement->movement_type,
                'income' => $movement->movement_type === 'income' ? $movement->amount : 0,
                'expense' => $movement->movement_type === 'expense' ? $movement->amount : 0,
            ];
        }

        // Ordenar por fecha dentro de cada banco
        foreach ($grouped as &$transactions) {
            usort($transactions, function ($a, $b) {
                return strcmp($a['date'], $b['date']);
            });
        }

        return $grouped;
    }

    /**
     * Agrupa movimientos por tipo y fuente
     */
    protected function groupByType($movements, string $type): array
    {
        $grouped = [];
        $movementType = $type === 'income' ? 'income' : 'expense';

        $filtered = $movements->where('movement_type', $movementType);

        foreach ($filtered as $movement) {
            $source = $movement->source_type ?? 'General';
            if (! isset($grouped[$source])) {
                $grouped[$source] = 0;
            }
            $grouped[$source] += $movement->amount;
        }

        return $grouped;
    }

    /**
     * Calcula el balance acumulado para cada transacción
     */
    public function calculateRunningBalance(array $transactions, float $initialBalance): array
    {
        $runningBalance = $initialBalance;
        $result = [];

        // Aplanar todas las transacciones
        $allTransactions = [];
        foreach ($transactions as $bankName => $bankTransactions) {
            foreach ($bankTransactions as $transaction) {
                $transaction['bank'] = $bankName;
                $allTransactions[] = $transaction;
            }
        }

        // Ordenar por fecha
        usort($allTransactions, function ($a, $b) {
            return strcmp($a['date'], $b['date']);
        });

        // Calcular balance acumulado
        foreach ($allTransactions as $transaction) {
            $runningBalance += $transaction['income'] - $transaction['expense'];
            $result[] = array_merge($transaction, [
                'running_balance' => $runningBalance,
            ]);
        }

        return $result;
    }

    /**
     * Calcula métricas de resumen
     */
    protected function calculateSummaryMetrics(): void
    {
        $total = $this->reportData['income'] + $this->reportData['expense'];

        $this->summary = [
            'income_percentage' => $total > 0 ? ($this->reportData['income'] / $total) * 100 : 0,
            'expense_percentage' => $total > 0 ? ($this->reportData['expense'] / $total) * 100 : 0,
            'net_flow_percentage' => $total > 0 ? ($this->reportData['net_flow'] / $this->reportData['income'] * 100) : 0,
        ];
    }

    /**
     * Actualiza el reporte cuando cambian las fechas
     */
    public function updatedStartDate(): void
    {
        $this->generateReport();
    }

    /**
     * Actualiza el reporte cuando cambia el banco
     */
    public function updatedBankId(): void
    {
        $this->generateReport();
    }

    /**
     * Actualiza el reporte cuando cambia la fecha final
     */
    public function updatedEndDate(): void
    {
        $this->generateReport();
    }

    public function render(): View
    {
        return view('livewire.treasury.cash-flow-report', [
            'reportData' => $this->reportData,
            'summary' => $this->summary,
            'banks' => $this->banks,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'bankId' => $this->bankId,
        ]);
    }
}
