<?php

namespace App\Services\Accounting;

use App\Enums\AccountType;
use App\Models\Accounting\AccountingAccount;
use App\Models\Finance\CostCenter;
use App\Models\Accounting\JournalEntry;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Servicio para generar reportes financieros
 * Proporciona balance de comprobación, balance general, estado de resultados
 * y reportes específicos por centro de costo y antigüedad
 */
class FinancialReportService
{
    protected AccountingService $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    /**
     * Genera balance de comprobación entre fechas
     * Lista todas las cuentas con sus saldos de débito y crédito
     */
    public function generateTrialBalance(
        int $clientId,
        Carbon $startDate,
        Carbon $endDate
    ): array {
        $accounts = AccountingAccount::where('client_id', $clientId)
            ->where('status', 'active')
            ->where('allows_transaction', true)
            ->orderBy('code')
            ->get();

        $totalDebit = 0;
        $totalCredit = 0;
        $details = [];

        foreach ($accounts as $account) {
            $debit = $this->getAccountDebit($account, $startDate, $endDate);
            $credit = $this->getAccountCredit($account, $startDate, $endDate);
            $balance = $account->account_type->isDebitNormal()
                ? $debit - $credit
                : $credit - $debit;

            if ($debit != 0 || $credit != 0) {
                $details[] = [
                    'account_code' => $account->code,
                    'account_name' => $account->name,
                    'debit' => $debit,
                    'credit' => $credit,
                    'balance' => $balance,
                    'account_type' => $account->account_type->value,
                ];

                $totalDebit += $debit;
                $totalCredit += $credit;
            }
        }

        return [
            'report_type' => 'trial_balance',
            'period_start' => $startDate->toDateString(),
            'period_end' => $endDate->toDateString(),
            'total_debit' => round($totalDebit, 2),
            'total_credit' => round($totalCredit, 2),
            'difference' => round($totalDebit - $totalCredit, 2),
            'is_balanced' => abs($totalDebit - $totalCredit) < 0.01,
            'details' => $details,
        ];
    }

    /**
     * Genera balance general (balance sheet) a una fecha específica
     * Agrupa activos, pasivos y patrimonio
     */
    public function generateBalanceSheet(int $clientId, Carbon $asOfDate): array
    {
        $accounts = $this->accountingService->getChartOfAccounts($clientId, [
            'status' => 'active',
            'allows_transaction' => true,
        ]);

        $assets = [];
        $liabilities = [];
        $equity = [];

        foreach ($accounts as $account) {
            $balance = $this->getAccountBalance($account, $asOfDate);

            if ($balance == 0) {
                continue;
            }

            $accountData = [
                'code' => $account->code,
                'name' => $account->name,
                'balance' => $balance,
                'parent_code' => $account->parent?->code,
                'level' => $account->level,
            ];

            match ($account->account_type) {
                AccountType::ASSET => $assets[] = $accountData,
                AccountType::LIABILITY => $liabilities[] = $accountData,
                AccountType::EQUITY => $equity[] = $accountData,
                default => null,
            };
        }

        $totalAssets = collect($assets)->sum('balance');
        $totalLiabilities = collect($liabilities)->sum('balance');
        $totalEquity = collect($equity)->sum('balance');

        return [
            'report_type' => 'balance_sheet',
            'as_of_date' => $asOfDate->toDateString(),
            'assets' => [
                'details' => $assets,
                'total' => round($totalAssets, 2),
            ],
            'liabilities' => [
                'details' => $liabilities,
                'total' => round($totalLiabilities, 2),
            ],
            'equity' => [
                'details' => $equity,
                'total' => round($totalEquity, 2),
            ],
            'total_liabilities_and_equity' => round($totalLiabilities + $totalEquity, 2),
            'is_balanced' => abs($totalAssets - ($totalLiabilities + $totalEquity)) < 0.01,
        ];
    }

    /**
     * Genera estado de resultados (income statement) para un período
     * Muestra ingresos, costos, gastos y resultado neto
     * Estructura: Ingresos - Costos = Utilidad Bruta - Gastos = Utilidad Neta
     */
    public function generateIncomeStatement(
        int $clientId,
        Carbon $startDate,
        Carbon $endDate
    ): array {
        $accounts = $this->accountingService->getChartOfAccounts($clientId, [
            'status' => 'active',
            'allows_transaction' => true,
        ]);

        $income = [];
        $costs = [];
        $expenses = [];

        foreach ($accounts as $account) {
            // Solo considerar cuentas de ingresos, costos y gastos
            if (! in_array($account->account_type, [AccountType::INCOME, AccountType::COST, AccountType::EXPENSE])) {
                continue;
            }

            $debit = $this->getAccountDebit($account, $startDate, $endDate);
            $credit = $this->getAccountCredit($account, $startDate, $endDate);

            // Calcular balance según tipo de cuenta
            $balance = match ($account->account_type) {
                AccountType::INCOME => $credit - $debit,           // Crédito normal
                AccountType::COST, AccountType::EXPENSE => $debit - $credit,  // Débito normal
            };

            if ($balance == 0) {
                continue;
            }

            $accountData = [
                'code' => $account->code,
                'account_name' => $account->name,
                'balance' => $balance,
                'parent_code' => $account->parent?->code,
                'level' => $account->level,
            ];

            match ($account->account_type) {
                AccountType::INCOME => $income[] = $accountData,
                AccountType::COST => $costs[] = $accountData,
                AccountType::EXPENSE => $expenses[] = $accountData,
            };
        }

        $totalIncome = collect($income)->sum('balance');
        $totalCosts = collect($costs)->sum('balance');
        $totalExpenses = collect($expenses)->sum('balance');
        $grossProfit = $totalIncome - $totalCosts;
        $netIncome = $grossProfit - $totalExpenses;

        return [
            'report_type' => 'income_statement',
            'period_start' => $startDate->toDateString(),
            'period_end' => $endDate->toDateString(),
            'income' => $income,
            'income_total' => round($totalIncome, 2),
            'costs' => $costs,
            'costs_total' => round($totalCosts, 2),
            'gross_profit' => round($grossProfit, 2),
            'expenses' => $expenses,
            'expenses_total' => round($totalExpenses, 2),
            'net_income' => round($netIncome, 2),
        ];
    }

    /**
     * Genera reporte de movimientos por centro de costo
     * Útil para análisis departamental
     */
    public function generateCostCenterReport(
        int $clientId,
        int $costCenterId,
        Carbon $startDate,
        Carbon $endDate
    ): array {
        $costCenter = CostCenter::where('client_id', $clientId)->findOrFail($costCenterId);

        $entries = JournalEntry::where('client_id', $clientId)
            ->whereBetween('entry_date', [$startDate, $endDate])
            ->whereStatus('posted')
            ->with('lines')
            ->get();

        $movements = [];
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($entries as $entry) {
            foreach ($entry->lines as $line) {
                if ($line->cost_center_id === $costCenterId) {
                    $movements[] = [
                        'entry_date' => $entry->entry_date->toDateString(),
                        'entry_number' => $entry->entry_number,
                        'description' => $entry->description,
                        'account_code' => $line->accountingAccount->code,
                        'account_name' => $line->accountingAccount->name,
                        'debit' => $line->debit,
                        'credit' => $line->credit,
                        'balance' => $line->accountingAccount->account_type->isDebitNormal()
                            ? $line->debit - $line->credit
                            : $line->credit - $line->debit,
                    ];

                    $totalDebit += $line->debit;
                    $totalCredit += $line->credit;
                }
            }
        }

        return [
            'report_type' => 'cost_center',
            'cost_center_code' => $costCenter->code,
            'cost_center_name' => $costCenter->name,
            'period_start' => $startDate->toDateString(),
            'period_end' => $endDate->toDateString(),
            'total_debit' => round($totalDebit, 2),
            'total_credit' => round($totalCredit, 2),
            'net_movement' => round($totalDebit - $totalCredit, 2),
            'movements' => $movements,
            'movement_count' => count($movements),
        ];
    }

    /**
     * Genera reporte de antigüedad (aging report)
     * Para cuentas por cobrar o por pagar
     */
    public function generateAgingReport(
        int $clientId,
        Carbon $asOfDate,
        string $type = 'receivable'
    ): array {
        if ($type === 'receivable') {
            return $this->generateReceivableAgingReport($clientId, $asOfDate);
        } else {
            return $this->generatePayableAgingReport($clientId, $asOfDate);
        }
    }

    /**
     * Reporte de antigüedad para cuentas por cobrar
     */
    protected function generateReceivableAgingReport(int $clientId, Carbon $asOfDate): array
    {
        $receivables = DB::table('accounts_receivable')
            ->where('client_id', $clientId)
            ->where('status', '!=', 'paid')
            ->where('status', '!=', 'cancelled')
            ->get();

        $ranges = [
            'current' => ['min' => 0, 'max' => 30, 'total' => 0, 'count' => 0],
            '31_60' => ['min' => 31, 'max' => 60, 'total' => 0, 'count' => 0],
            '61_90' => ['min' => 61, 'max' => 90, 'total' => 0, 'count' => 0],
            'over_90' => ['min' => 91, 'max' => PHP_INT_MAX, 'total' => 0, 'count' => 0],
        ];

        $items = [];
        $totalAmount = 0;

        foreach ($receivables as $receivable) {
            $daysOverdue = $asOfDate->diffInDays($receivable->due_date);
            $range = $this->getAgeRange($daysOverdue);

            $ranges[$range]['total'] += $receivable->balance;
            $ranges[$range]['count'] += 1;
            $totalAmount += $receivable->balance;

            $items[] = [
                'invoice_number' => $receivable->invoice_number,
                'invoice_date' => $receivable->invoice_date,
                'due_date' => $receivable->due_date,
                'days_overdue' => max(0, $daysOverdue),
                'balance' => $receivable->balance,
                'range' => $range,
            ];
        }

        return [
            'report_type' => 'aging_receivable',
            'as_of_date' => $asOfDate->toDateString(),
            'ranges' => [
                'current' => [
                    'label' => '0-30 días',
                    'total' => round($ranges['current']['total'], 2),
                    'count' => $ranges['current']['count'],
                    'percentage' => $totalAmount > 0
                        ? round(($ranges['current']['total'] / $totalAmount) * 100, 2)
                        : 0,
                ],
                '31_60' => [
                    'label' => '31-60 días',
                    'total' => round($ranges['31_60']['total'], 2),
                    'count' => $ranges['31_60']['count'],
                    'percentage' => $totalAmount > 0
                        ? round(($ranges['31_60']['total'] / $totalAmount) * 100, 2)
                        : 0,
                ],
                '61_90' => [
                    'label' => '61-90 días',
                    'total' => round($ranges['61_90']['total'], 2),
                    'count' => $ranges['61_90']['count'],
                    'percentage' => $totalAmount > 0
                        ? round(($ranges['61_90']['total'] / $totalAmount) * 100, 2)
                        : 0,
                ],
                'over_90' => [
                    'label' => 'Más de 90 días',
                    'total' => round($ranges['over_90']['total'], 2),
                    'count' => $ranges['over_90']['count'],
                    'percentage' => $totalAmount > 0
                        ? round(($ranges['over_90']['total'] / $totalAmount) * 100, 2)
                        : 0,
                ],
            ],
            'grand_total' => round($totalAmount, 2),
            'items' => $items,
        ];
    }

    /**
     * Reporte de antigüedad para cuentas por pagar
     */
    protected function generatePayableAgingReport(int $clientId, Carbon $asOfDate): array
    {
        $payables = DB::table('supplier_invoices')
            ->where('client_id', $clientId)
            ->where('status', '!=', 'paid')
            ->where('status', '!=', 'cancelled')
            ->get();

        $ranges = [
            'current' => ['min' => 0, 'max' => 30, 'total' => 0, 'count' => 0],
            '31_60' => ['min' => 31, 'max' => 60, 'total' => 0, 'count' => 0],
            '61_90' => ['min' => 61, 'max' => 90, 'total' => 0, 'count' => 0],
            'over_90' => ['min' => 91, 'max' => PHP_INT_MAX, 'total' => 0, 'count' => 0],
        ];

        $items = [];
        $totalAmount = 0;

        foreach ($payables as $payable) {
            $daysOverdue = $asOfDate->diffInDays($payable->due_date);
            $range = $this->getAgeRange($daysOverdue);

            $ranges[$range]['total'] += $payable->balance;
            $ranges[$range]['count'] += 1;
            $totalAmount += $payable->balance;

            $items[] = [
                'invoice_number' => $payable->invoice_number,
                'invoice_date' => $payable->invoice_date,
                'due_date' => $payable->due_date,
                'days_overdue' => max(0, $daysOverdue),
                'balance' => $payable->balance,
                'range' => $range,
            ];
        }

        return [
            'report_type' => 'aging_payable',
            'as_of_date' => $asOfDate->toDateString(),
            'ranges' => [
                'current' => [
                    'label' => '0-30 días',
                    'total' => round($ranges['current']['total'], 2),
                    'count' => $ranges['current']['count'],
                    'percentage' => $totalAmount > 0
                        ? round(($ranges['current']['total'] / $totalAmount) * 100, 2)
                        : 0,
                ],
                '31_60' => [
                    'label' => '31-60 días',
                    'total' => round($ranges['31_60']['total'], 2),
                    'count' => $ranges['31_60']['count'],
                    'percentage' => $totalAmount > 0
                        ? round(($ranges['31_60']['total'] / $totalAmount) * 100, 2)
                        : 0,
                ],
                '61_90' => [
                    'label' => '61-90 días',
                    'total' => round($ranges['61_90']['total'], 2),
                    'count' => $ranges['61_90']['count'],
                    'percentage' => $totalAmount > 0
                        ? round(($ranges['61_90']['total'] / $totalAmount) * 100, 2)
                        : 0,
                ],
                'over_90' => [
                    'label' => 'Más de 90 días',
                    'total' => round($ranges['over_90']['total'], 2),
                    'count' => $ranges['over_90']['count'],
                    'percentage' => $totalAmount > 0
                        ? round(($ranges['over_90']['total'] / $totalAmount) * 100, 2)
                        : 0,
                ],
            ],
            'grand_total' => round($totalAmount, 2),
            'items' => $items,
        ];
    }

    /**
     * Obtiene el débito total de una cuenta en un período
     */
    protected function getAccountDebit(
        AccountingAccount $account,
        Carbon $startDate,
        Carbon $endDate
    ): float {
        return (float) $account->journalEntryLines()
            ->whereHas('journalEntry', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('entry_date', [$startDate, $endDate])
                    ->where('status', 'posted');
            })
            ->sum('debit');
    }

    /**
     * Obtiene el crédito total de una cuenta en un período
     */
    protected function getAccountCredit(
        AccountingAccount $account,
        Carbon $startDate,
        Carbon $endDate
    ): float {
        return (float) $account->journalEntryLines()
            ->whereHas('journalEntry', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('entry_date', [$startDate, $endDate])
                    ->where('status', 'posted');
            })
            ->sum('credit');
    }

    /**
     * Obtiene el balance de una cuenta a una fecha específica
     */
    protected function getAccountBalance(
        AccountingAccount $account,
        Carbon $asOfDate
    ): float {
        $debit = (float) $account->journalEntryLines()
            ->whereHas('journalEntry', function ($query) use ($asOfDate) {
                $query->whereDate('entry_date', '<=', $asOfDate)
                    ->where('status', 'posted');
            })
            ->sum('debit');

        $credit = (float) $account->journalEntryLines()
            ->whereHas('journalEntry', function ($query) use ($asOfDate) {
                $query->whereDate('entry_date', '<=', $asOfDate)
                    ->where('status', 'posted');
            })
            ->sum('credit');

        if ($account->account_type->isDebitNormal()) {
            return $debit - $credit;
        }

        return $credit - $debit;
    }

    /**
     * Calcula el rango de antigüedad en días
     */
    protected function getAgeRange(int $daysOverdue): string
    {
        if ($daysOverdue <= 30) {
            return 'current';
        } elseif ($daysOverdue <= 60) {
            return '31_60';
        } elseif ($daysOverdue <= 90) {
            return '61_90';
        }

        return 'over_90';
    }
}
