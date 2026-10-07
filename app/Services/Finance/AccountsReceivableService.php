<?php

namespace App\Services\Finance;

use App\Enums\ReceivableStatus;
use App\Models\Finance\AccountsReceivable;
use App\Models\Invoice;
use App\Services\Accounting\AccountingEngineService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Servicio para gestionar Cuentas por Cobrar
 * Maneja creación, seguimiento y cobros de facturas a crédito
 */
class AccountsReceivableService
{
    public function __construct(
        protected AccountingEngineService $accountingEngine,
    ) {}

    /**
     * Crea una CxC desde una factura a crédito
     */
    public function createFromInvoice(Invoice $invoice): AccountsReceivable
    {
        return DB::transaction(function () use ($invoice) {
            $receivable = AccountsReceivable::create([
                'client_id' => $invoice->client_id,
                'invoice_id' => $invoice->id,
                'patient_id' => $invoice->patient_id,
                'insurance_id' => $invoice->primary_insurance_id ?? null,
                'invoice_number' => $invoice->invoice_number,
                'invoice_date' => $invoice->issue_date,
                'due_date' => $invoice->due_date,
                'original_amount' => $invoice->total_amount,
                'paid_amount' => 0,
                'balance' => $invoice->total_amount,
                'cost_center_id' => $invoice->cost_center_id ?? null,
                'branch_id' => $invoice->branch_id,
                'status' => ReceivableStatus::PENDING,
                'created_by' => auth()->id(),
            ]);

            // Vincular CxC con Invoice
            $invoice->update(['accounts_receivable_id' => $receivable->id]);

            // Generar asiento contable: Débito CxC, Crédito Ingreso
            $this->accountingEngine->processEvent('INVOICE_CREDIT', $invoice);

            return $receivable;
        });
    }

    /**
     * Aplica un pago a una CxC
     */
    public function applyPayment(
        AccountsReceivable $receivable,
        float $amount
    ): void {
        DB::transaction(function () use ($receivable, $amount) {
            $receivable->applyPayment($amount);

            // Generar asiento contable: Débito Banco/Caja, Crédito CxC
            $this->accountingEngine->processEvent('PAYMENT_RECEIVED', $receivable);
        });
    }

    /**
     * Obtiene resumen de CxC por paciente
     */
    public function getCreditSummary(int $patientId, int $clientId): array
    {
        $receivables = AccountsReceivable::where('client_id', $clientId)
            ->where('patient_id', $patientId)
            ->get();

        return [
            'total_credit' => $receivables->sum('original_amount'),
            'total_paid' => $receivables->sum('paid_amount'),
            'total_balance' => $receivables->sum('balance'),
            'overdue_balance' => $receivables->where('status', ReceivableStatus::OVERDUE)->sum('balance'),
            'pending_count' => $receivables->where('status', ReceivableStatus::PENDING)->count(),
            'overdue_count' => $receivables->where('status', ReceivableStatus::OVERDUE)->count(),
            'paid_count' => $receivables->where('status', ReceivableStatus::PAID)->count(),
        ];
    }

    /**
     * Obtiene resumen general de CxC
     */
    public function getReceivablesSummary(int $clientId, ?string $status = null): array
    {
        $query = AccountsReceivable::where('client_id', $clientId);

        if ($status) {
            $query->where('status', $status);
        }

        $receivables = $query->get();

        return [
            'total_receivables' => $receivables->count(),
            'total_amount' => $receivables->sum('original_amount'),
            'total_paid' => $receivables->sum('paid_amount'),
            'total_balance' => $receivables->sum('balance'),
            'overdue_count' => $receivables->where('status', ReceivableStatus::OVERDUE)->count(),
            'overdue_amount' => $receivables->where('status', ReceivableStatus::OVERDUE)->sum('balance'),
            'partial_count' => $receivables->where('status', ReceivableStatus::PARTIAL)->count(),
        ];
    }

    /**
     * Obtiene CxC vencidas
     */
    public function getOverdueReceivables(int $clientId): Collection
    {
        return AccountsReceivable::where('client_id', $clientId)
            ->where('status', '!=', ReceivableStatus::PAID)
            ->where('status', '!=', ReceivableStatus::CANCELLED)
            ->whereDate('due_date', '<', now())
            ->orderBy('due_date')
            ->get();
    }

    /**
     * Actualiza estados de CxC vencidas (para ejecutarse como cron)
     */
    public function updateOverdueStatuses(int $clientId): int
    {
        $updated = 0;
        foreach ($this->getOverdueReceivables($clientId) as $receivable) {
            if ($receivable->status !== ReceivableStatus::OVERDUE) {
                $receivable->update(['status' => ReceivableStatus::OVERDUE]);
                $updated++;
            }
        }

        return $updated;
    }

    /**
     * Obtiene reporte de antigüedad
     */
    public function getAgingReport(int $clientId, ?\DateTime $asOfDate = null): array
    {
        $asOfDate = $asOfDate ?? now();

        $receivables = AccountsReceivable::where('client_id', $clientId)
            ->where('status', '!=', ReceivableStatus::PAID)
            ->where('status', '!=', ReceivableStatus::CANCELLED)
            ->with(['patient', 'invoice'])
            ->get();

        $report = [
            '0-30' => 0,
            '31-60' => 0,
            '61-90' => 0,
            '90+' => 0,
            'total' => 0,
            'items' => [],
        ];

        foreach ($receivables as $receivable) {
            $daysOverdue = $asOfDate->diffInDays($receivable->due_date);
            $range = $this->getAgeRange($daysOverdue);

            $report[$range] += $receivable->balance;
            $report['total'] += $receivable->balance;

            $report['items'][] = [
                'patient' => $receivable->patient->full_name ?? 'N/A',
                'invoice_number' => $receivable->invoice_number,
                'invoice_date' => $receivable->invoice_date->toDateString(),
                'due_date' => $receivable->due_date->toDateString(),
                'balance' => $receivable->balance,
                'days_overdue' => max(0, $daysOverdue),
                'range' => $range,
                'status' => $receivable->status->value,
            ];
        }

        return $report;
    }

    /**
     * Calcula el rango de antigüedad
     */
    private function getAgeRange(int $daysOverdue): string
    {
        if ($daysOverdue <= 30) {
            return '0-30';
        } elseif ($daysOverdue <= 60) {
            return '31-60';
        } elseif ($daysOverdue <= 90) {
            return '61-90';
        }

        return '90+';
    }

    /**
     * Cancela una CxC
     */
    public function cancel(AccountsReceivable $receivable, string $reason = ''): void
    {
        $receivable->update([
            'status' => ReceivableStatus::CANCELLED,
        ]);
    }

    /**
     * Obtiene CxC por rango de fechas
     */
    public function getReceivablesByPeriod(
        int $clientId,
        \DateTime $startDate,
        \DateTime $endDate,
        ?string $status = null
    ): Collection {
        $query = AccountsReceivable::where('client_id', $clientId)
            ->whereBetween('invoice_date', [$startDate, $endDate]);

        if ($status) {
            $query->where('status', $status);
        }

        return $query->orderByDesc('invoice_date')->get();
    }
}
