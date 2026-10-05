<?php

namespace App\Services\Treasury;

use App\Enums\TreasuryMovementType;
use App\Models\Bank;
use App\Models\CashRegister;
use App\Models\TreasuryMovement;
use Illuminate\Support\Facades\DB;

/**
 * Servicio para generar y analizar reportes de flujo de caja
 * Proporciona análisis de ingresos, egresos y proyecciones de caja
 */
class CashFlowService
{
    /**
     * Genera reporte de flujo de caja para un período
     * Muestra ingresos, egresos, transferencias y cambios netos en caja
     */
    public function generateCashFlow(
        int $clientId,
        \DateTime $startDate,
        \DateTime $endDate,
        ?int $bankId = null,
        ?int $cashRegisterId = null
    ): array {
        $movements = $this->getMovements($clientId, $startDate, $endDate, $bankId, $cashRegisterId);

        // Calcular saldos iniciales
        $openingBalances = $this->getOpeningBalances($clientId, $startDate, $bankId, $cashRegisterId);

        // Agrupar movimientos por día
        $dailyFlows = $this->groupMovementsByDay($movements);

        // Calcular flujos acumulados
        $cumulativeFlow = $this->calculateCumulativeFlow($dailyFlows, $openingBalances);

        // Resumen general
        $summary = $this->calculateFlowSummary($movements, $openingBalances, $cumulativeFlow);

        return [
            'report_type' => 'cash_flow',
            'period_start' => $startDate->toDateString(),
            'period_end' => $endDate->toDateString(),
            'opening_balances' => $openingBalances,
            'daily_flows' => $dailyFlows,
            'cumulative_flow' => $cumulativeFlow,
            'summary' => $summary,
        ];
    }

    /**
     * Genera proyección de flujo de caja para los próximos días
     * Basado en patrones históricos y movimientos pendientes
     */
    public function generateCashFlowForecast(
        int $clientId,
        int $daysForward = 30,
        ?int $bankId = null,
        ?int $cashRegisterId = null
    ): array {
        $today = now();
        $endDate = $today->clone()->addDays($daysForward);

        // Obtener saldos actuales
        $currentBalances = $this->getCurrentBalances($clientId, $today, $bankId, $cashRegisterId);

        // Obtener histórico de últimos 90 días para análisis
        $historicalStart = $today->clone()->subDays(90);
        $historicalMovements = $this->getMovements(
            $clientId,
            $historicalStart,
            $today,
            $bankId,
            $cashRegisterId
        );

        // Calcular promedios diarios
        $averageDailyFlow = $this->calculateAverageDailyFlow($historicalMovements);

        // Generar proyección
        $forecast = $this->generateProjection(
            $currentBalances,
            $averageDailyFlow,
            $today,
            $endDate
        );

        // Identificar riesgos potenciales
        $risks = $this->identifyFlowRisks($forecast, $currentBalances);

        return [
            'report_type' => 'cash_flow_forecast',
            'forecast_start' => $today->toDateString(),
            'forecast_end' => $endDate->toDateString(),
            'days_forward' => $daysForward,
            'current_balances' => $currentBalances,
            'average_daily_flow' => $averageDailyFlow,
            'forecast' => $forecast,
            'risks' => $risks,
        ];
    }

    /**
     * Analiza la salud del flujo de caja
     * Retorna indicadores clave de desempeño
     */
    public function analyzeCashHealth(
        int $clientId,
        \DateTime $startDate,
        \DateTime $endDate
    ): array {
        $movements = $this->getMovements($clientId, $startDate, $endDate);

        $income = $movements
            ->where('movement_type', TreasuryMovementType::INCOME)
            ->sum('amount');

        $expenses = $movements
            ->where('movement_type', TreasuryMovementType::EXPENSE)
            ->sum('amount');

        $transfers = $movements
            ->where('movement_type', TreasuryMovementType::TRANSFER)
            ->sum('amount');

        $netFlow = $income - $expenses;
        $days = $endDate->diffInDays($startDate) + 1;
        $averageDailyFlow = $days > 0 ? $netFlow / $days : 0;

        // Calcular volatilidad del flujo
        $volatility = $this->calculateFlowVolatility($movements, $days);

        // Calcular índices de liquidez
        $liquidityIndex = $this->calculateLiquidityIndex($clientId, $endDate);

        return [
            'analysis_type' => 'cash_health',
            'period_start' => $startDate->toDateString(),
            'period_end' => $endDate->toDateString(),
            'total_income' => round($income, 2),
            'total_expenses' => round($expenses, 2),
            'total_transfers' => round($transfers, 2),
            'net_flow' => round($netFlow, 2),
            'average_daily_flow' => round($averageDailyFlow, 2),
            'flow_volatility' => round($volatility, 2),
            'liquidity_index' => round($liquidityIndex, 2),
            'health_status' => $this->getHealthStatus($averageDailyFlow, $volatility),
        ];
    }

    /**
     * Compara flujos de caja entre períodos
     * Útil para análisis year-over-year o periodo-a-periodo
     */
    public function compareCashFlows(
        int $clientId,
        \DateTime $period1Start,
        \DateTime $period1End,
        \DateTime $period2Start,
        \DateTime $period2End
    ): array {
        $period1 = $this->generateCashFlow($clientId, $period1Start, $period1End);
        $period2 = $this->generateCashFlow($clientId, $period2Start, $period2End);

        $period1Summary = $period1['summary'];
        $period2Summary = $period2['summary'];

        $incomeChange = $period2Summary['total_income'] - $period1Summary['total_income'];
        $expenseChange = $period2Summary['total_expenses'] - $period1Summary['total_expenses'];
        $netFlowChange = $period2Summary['net_flow'] - $period1Summary['net_flow'];

        return [
            'comparison_type' => 'period_comparison',
            'period_1' => [
                'start_date' => $period1Start->toDateString(),
                'end_date' => $period1End->toDateString(),
                'summary' => $period1Summary,
            ],
            'period_2' => [
                'start_date' => $period2Start->toDateString(),
                'end_date' => $period2End->toDateString(),
                'summary' => $period2Summary,
            ],
            'changes' => [
                'income_change' => round($incomeChange, 2),
                'income_change_percentage' => $period1Summary['total_income'] > 0
                    ? round(($incomeChange / $period1Summary['total_income']) * 100, 2)
                    : 0,
                'expense_change' => round($expenseChange, 2),
                'expense_change_percentage' => $period1Summary['total_expenses'] > 0
                    ? round(($expenseChange / $period1Summary['total_expenses']) * 100, 2)
                    : 0,
                'net_flow_change' => round($netFlowChange, 2),
                'net_flow_change_percentage' => $period1Summary['net_flow'] != 0
                    ? round(($netFlowChange / $period1Summary['net_flow']) * 100, 2)
                    : 0,
            ],
        ];
    }

    /**
     * Obtiene movimientos del período
     */
    protected function getMovements(
        int $clientId,
        \DateTime $startDate,
        \DateTime $endDate,
        ?int $bankId = null,
        ?int $cashRegisterId = null
    ) {
        $query = TreasuryMovement::where('client_id', $clientId)
            ->whereBetween('movement_date', [$startDate, $endDate]);

        if ($bankId) {
            $query->where('bank_id', $bankId);
        }

        if ($cashRegisterId) {
            $query->where('cash_register_id', $cashRegisterId);
        }

        return $query->orderBy('movement_date')->get();
    }

    /**
     * Obtiene saldos de apertura al inicio del período
     */
    protected function getOpeningBalances(
        int $clientId,
        \DateTime $startDate,
        ?int $bankId = null,
        ?int $cashRegisterId = null
    ): array {
        $openingDate = $startDate->clone()->subDay();

        $bankBalance = 0;
        $cashBalance = 0;

        if ($bankId) {
            $bank = Bank::find($bankId);
            $bankBalance = $bank ? $this->getBalanceAtDate($bank, $openingDate) : 0;
        } else {
            $banks = Bank::where('client_id', $clientId)->get();
            foreach ($banks as $bank) {
                $bankBalance += $this->getBalanceAtDate($bank, $openingDate);
            }
        }

        if ($cashRegisterId) {
            $cashReg = CashRegister::find($cashRegisterId);
            $cashBalance = $cashReg ? $this->getBalanceAtDate($cashReg, $openingDate) : 0;
        } else {
            $cashRegisters = CashRegister::where('client_id', $clientId)->get();
            foreach ($cashRegisters as $register) {
                $cashBalance += $this->getBalanceAtDate($register, $openingDate);
            }
        }

        return [
            'bank_balance' => round($bankBalance, 2),
            'cash_balance' => round($cashBalance, 2),
            'total_balance' => round($bankBalance + $cashBalance, 2),
        ];
    }

    /**
     * Obtiene saldos actuales
     */
    protected function getCurrentBalances(
        int $clientId,
        \DateTime $asOfDate,
        ?int $bankId = null,
        ?int $cashRegisterId = null
    ): array {
        return $this->getOpeningBalances($clientId, $asOfDate->clone()->addDay(), $bankId, $cashRegisterId);
    }

    /**
     * Agrupa movimientos por día
     */
    protected function groupMovementsByDay($movements): array
    {
        $grouped = [];

        foreach ($movements as $movement) {
            $date = $movement->movement_date->toDateString();

            if (! isset($grouped[$date])) {
                $grouped[$date] = [
                    'date' => $date,
                    'income' => 0,
                    'expenses' => 0,
                    'transfers' => 0,
                    'net_flow' => 0,
                    'details' => [],
                ];
            }

            $amount = $movement->amount;
            $type = $movement->movement_type;

            if ($type === TreasuryMovementType::INCOME) {
                $grouped[$date]['income'] += $amount;
            } elseif ($type === TreasuryMovementType::EXPENSE) {
                $grouped[$date]['expenses'] += $amount;
            } elseif ($type === TreasuryMovementType::TRANSFER) {
                $grouped[$date]['transfers'] += $amount;
            }

            $grouped[$date]['net_flow'] = $grouped[$date]['income'] - $grouped[$date]['expenses'];
            $grouped[$date]['details'][] = [
                'type' => $type->value,
                'amount' => $amount,
                'description' => $movement->description,
                'reference' => $movement->reference_number,
            ];
        }

        return array_values($grouped);
    }

    /**
     * Calcula flujos acumulados
     */
    protected function calculateCumulativeFlow($dailyFlows, $openingBalances): array
    {
        $cumulative = [
            'bank' => $openingBalances['bank_balance'],
            'cash' => $openingBalances['cash_balance'],
            'total' => $openingBalances['total_balance'],
        ];

        $cumulativeDays = [];

        foreach ($dailyFlows as $day) {
            $cumulative['total'] += $day['net_flow'];

            $cumulativeDays[] = [
                'date' => $day['date'],
                'daily_net_flow' => round($day['net_flow'], 2),
                'cumulative_balance' => round($cumulative['total'], 2),
            ];
        }

        return $cumulativeDays;
    }

    /**
     * Calcula resumen del flujo
     */
    protected function calculateFlowSummary($movements, $openingBalances, $cumulativeFlow): array
    {
        $income = $movements->where('movement_type', TreasuryMovementType::INCOME)->sum('amount');
        $expenses = $movements->where('movement_type', TreasuryMovementType::EXPENSE)->sum('amount');
        $transfers = $movements->where('movement_type', TreasuryMovementType::TRANSFER)->sum('amount');
        $netFlow = $income - $expenses;

        $closingBalance = $openingBalances['total_balance'] + $netFlow;

        return [
            'opening_balance' => round($openingBalances['total_balance'], 2),
            'total_income' => round($income, 2),
            'total_expenses' => round($expenses, 2),
            'total_transfers' => round($transfers, 2),
            'net_flow' => round($netFlow, 2),
            'closing_balance' => round($closingBalance, 2),
            'change_percentage' => $openingBalances['total_balance'] > 0
                ? round(($netFlow / $openingBalances['total_balance']) * 100, 2)
                : 0,
        ];
    }

    /**
     * Calcula flujo promedio diario del histórico
     */
    protected function calculateAverageDailyFlow($movements): array
    {
        if ($movements->isEmpty()) {
            return [
                'average_income' => 0,
                'average_expenses' => 0,
                'average_net_flow' => 0,
            ];
        }

        $days = $movements->pluck('movement_date')->unique()->count();
        $totalIncome = $movements->where('movement_type', TreasuryMovementType::INCOME)->sum('amount');
        $totalExpenses = $movements->where('movement_type', TreasuryMovementType::EXPENSE)->sum('amount');

        return [
            'average_income' => round($days > 0 ? $totalIncome / $days : 0, 2),
            'average_expenses' => round($days > 0 ? $totalExpenses / $days : 0, 2),
            'average_net_flow' => round($days > 0 ? ($totalIncome - $totalExpenses) / $days : 0, 2),
        ];
    }

    /**
     * Genera proyección de flujo
     */
    protected function generateProjection(
        $currentBalances,
        $averageFlow,
        \DateTime $startDate,
        \DateTime $endDate
    ): array {
        $projection = [];
        $currentBalance = $currentBalances['total_balance'];
        $current = $startDate->clone();

        while ($current <= $endDate) {
            $currentBalance += $averageFlow['average_net_flow'];

            $projection[] = [
                'date' => $current->toDateString(),
                'projected_daily_flow' => round($averageFlow['average_net_flow'], 2),
                'projected_balance' => round($currentBalance, 2),
            ];

            $current->addDay();
        }

        return $projection;
    }

    /**
     * Identifica riesgos potenciales en el flujo
     */
    protected function identifyFlowRisks($forecast, $currentBalances): array
    {
        $risks = [];

        foreach ($forecast as $day) {
            if ($day['projected_balance'] < 0) {
                $risks[] = [
                    'type' => 'negative_balance',
                    'date' => $day['date'],
                    'projected_balance' => $day['projected_balance'],
                    'severity' => 'critical',
                ];
            } elseif ($day['projected_balance'] < $currentBalances['total_balance'] * 0.1) {
                $risks[] = [
                    'type' => 'low_balance',
                    'date' => $day['date'],
                    'projected_balance' => $day['projected_balance'],
                    'severity' => 'warning',
                ];
            }
        }

        return $risks;
    }

    /**
     * Calcula volatilidad del flujo
     */
    protected function calculateFlowVolatility($movements, int $days): float
    {
        if ($days <= 1) {
            return 0;
        }

        $dailyFlows = [];
        $currentDate = null;
        $dayTotal = 0;

        foreach ($movements as $movement) {
            if ($currentDate && $currentDate !== $movement->movement_date->toDateString()) {
                $dailyFlows[] = $dayTotal;
                $dayTotal = 0;
            }

            $currentDate = $movement->movement_date->toDateString();
            $adjustment = $movement->movement_type === TreasuryMovementType::EXPENSE
                ? -$movement->amount
                : $movement->amount;
            $dayTotal += $adjustment;
        }

        if ($dayTotal != 0) {
            $dailyFlows[] = $dayTotal;
        }

        if (count($dailyFlows) <= 1) {
            return 0;
        }

        $mean = array_sum($dailyFlows) / count($dailyFlows);
        $variance = 0;

        foreach ($dailyFlows as $flow) {
            $variance += pow($flow - $mean, 2);
        }

        return sqrt($variance / count($dailyFlows));
    }

    /**
     * Calcula índice de liquidez
     */
    protected function calculateLiquidityIndex(int $clientId, \DateTime $asOfDate): float
    {
        $banks = Bank::where('client_id', $clientId)->get();
        $cashRegisters = CashRegister::where('client_id', $clientId)->get();

        $totalAssets = 0;
        foreach ($banks as $bank) {
            $totalAssets += $this->getBalanceAtDate($bank, $asOfDate);
        }

        foreach ($cashRegisters as $register) {
            $totalAssets += $this->getBalanceAtDate($register, $asOfDate);
        }

        // Para simplificar, calculamos un índice basado en movimientos recientes
        $thirtyDaysAgo = $asOfDate->clone()->subDays(30);
        $movementsLast30 = TreasuryMovement::where('client_id', $clientId)
            ->whereBetween('movement_date', [$thirtyDaysAgo, $asOfDate])
            ->sum(DB::raw('CASE WHEN movement_type = ? THEN amount ELSE -amount END',
                [TreasuryMovementType::EXPENSE->value]));

        if ($movementsLast30 == 0) {
            return 1.0; // Neutral
        }

        return $totalAssets / abs($movementsLast30);
    }

    /**
     * Obtiene balance a una fecha específica
     */
    protected function getBalanceAtDate($entity, \DateTime $asOfDate): float
    {
        if ($entity instanceof Bank) {
            return (float) TreasuryMovement::where('bank_id', $entity->id)
                ->whereDate('movement_date', '<=', $asOfDate)
                ->sum(DB::raw('CASE WHEN movement_type = ? THEN amount ELSE -amount END',
                    [TreasuryMovementType::EXPENSE->value]));
        }

        return (float) TreasuryMovement::where('cash_register_id', $entity->id)
            ->whereDate('movement_date', '<=', $asOfDate)
            ->sum(DB::raw('CASE WHEN movement_type = ? THEN amount ELSE -amount END',
                [TreasuryMovementType::EXPENSE->value]));
    }

    /**
     * Determina estado de salud basado en indicadores
     */
    protected function getHealthStatus(float $averageDailyFlow, float $volatility): string
    {
        if ($averageDailyFlow < 0 && abs($averageDailyFlow) > 100) {
            return 'critical';
        }

        if ($volatility > 10000 && $averageDailyFlow < 0) {
            return 'poor';
        }

        if ($averageDailyFlow > 0 && $volatility < 5000) {
            return 'healthy';
        }

        return 'moderate';
    }
}
