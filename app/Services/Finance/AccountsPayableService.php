<?php

namespace App\Services\Finance;

use App\Models\Finance\PaymentSchedule;
use App\Models\Finance\Supplier;
use App\Models\Finance\SupplierInvoice;
use App\Services\Accounting\AccountingEngineService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Servicio para gestionar Cuentas por Pagar
 * Maneja creación, aprobación y pagos de facturas de proveedores
 */
class AccountsPayableService
{
    public function __construct(
        protected AccountingEngineService $accountingEngine,
    ) {}

    /**
     * Crea una factura de proveedor
     */
    public function createSupplierInvoice(int $clientId, array $data): SupplierInvoice
    {
        return DB::transaction(function () use ($clientId, $data) {
            // Validar proveedor
            $supplier = Supplier::findOrFail($data['supplier_id']);
            if ($supplier->client_id !== $clientId) {
                throw new \Exception('Supplier does not belong to this client');
            }

            // Crear factura
            $invoice = SupplierInvoice::create([
                'client_id' => $clientId,
                'supplier_id' => $data['supplier_id'],
                'invoice_number' => $data['invoice_number'],
                'invoice_date' => $data['invoice_date'],
                'received_date' => $data['received_date'] ?? now(),
                'due_date' => $data['due_date'],
                'currency' => $data['currency'] ?? 'PAB',
                'subtotal' => $data['subtotal'],
                'tax_amount' => $data['tax_amount'] ?? 0,
                'total_amount' => $data['total_amount'],
                'cost_center_id' => $data['cost_center_id'] ?? null,
                'notes' => $data['notes'] ?? null,
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);

            return $invoice;
        });
    }

    /**
     * Aprueba una factura de proveedor
     * Genera asiento contable automáticamente
     */
    public function approveInvoice(SupplierInvoice $invoice): SupplierInvoice
    {
        return DB::transaction(function () use ($invoice) {
            $invoice->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => auth()->id(),
            ]);

            // Generar asiento contable automático
            $journalEntry = $this->accountingEngine->processSupplierInvoiceCreated($invoice);
            $invoice->update(['journal_entry_id' => $journalEntry->id]);

            return $invoice;
        });
    }

    /**
     * Crea un programa de pagos para una factura
     */
    public function createPaymentSchedule(
        SupplierInvoice $invoice,
        array $schedule
    ): Collection {
        return DB::transaction(function () use ($invoice, $schedule) {
            $totalAmount = collect($schedule)->sum('amount');

            if (abs($totalAmount - $invoice->total_amount) > 0.01) {
                throw new \Exception('Payment schedule total does not match invoice total');
            }

            $schedules = collect();
            foreach ($schedule as $payment) {
                $schedules->push(PaymentSchedule::create([
                    'supplier_invoice_id' => $invoice->id,
                    'payment_date' => $payment['payment_date'],
                    'amount' => $payment['amount'],
                    'notes' => $payment['notes'] ?? null,
                ]));
            }

            return $schedules;
        });
    }

    /**
     * Marca un pago de programa como realizado
     */
    public function markPaymentAsPaid(
        PaymentSchedule $schedule,
        ?int $treasuryMovementId = null
    ): PaymentSchedule {
        return DB::transaction(function () use ($schedule, $treasuryMovementId) {
            $schedule->update([
                'paid' => true,
                'paid_at' => now(),
                'treasury_movement_id' => $treasuryMovementId,
            ]);

            // Actualizar balance de factura
            $this->updateInvoiceBalance($schedule->supplierInvoice);

            // Generar asiento contable de pago
            $this->accountingEngine->processSupplierPayment($schedule);

            return $schedule;
        });
    }

    /**
     * Actualiza el balance de una factura basado en pagos
     */
    public function updateInvoiceBalance(SupplierInvoice $invoice): void
    {
        $paidAmount = $invoice->paymentSchedules()
            ->where('paid', true)
            ->sum('amount');

        $invoice->update([
            'paid_amount' => $paidAmount,
            'balance' => $invoice->total_amount - $paidAmount,
            'status' => $this->calculateInvoiceStatus($invoice, $paidAmount),
        ]);
    }

    /**
     * Calcula el estado de una factura basado en pagos y fecha de vencimiento
     */
    private function calculateInvoiceStatus(SupplierInvoice $invoice, float $paidAmount): string
    {
        if ($paidAmount >= $invoice->total_amount) {
            return 'paid';
        }

        if ($paidAmount > 0) {
            return 'partial';
        }

        if (now()->greaterThan($invoice->due_date)) {
            return 'overdue';
        }

        return 'approved';
    }

    /**
     * Distribuye los costos de una factura entre centros de costo
     */
    public function distributeByCenter(
        SupplierInvoice $invoice,
        array $distributions
    ): void {
        if (! $this->validateDistributions($distributions)) {
            throw new \Exception('Distributions must sum to 100%');
        }

        DB::transaction(function () use ($invoice, $distributions) {
            // Eliminar distribuciones anteriores
            $invoice->costDistributions()->delete();

            // Crear nuevas distribuciones
            foreach ($distributions as $distribution) {
                $invoice->costDistributions()->create([
                    'cost_center_id' => $distribution['cost_center_id'],
                    'percentage' => $distribution['percentage'],
                    'amount' => $invoice->total_amount * ($distribution['percentage'] / 100),
                ]);
            }
        });
    }

    /**
     * Valida que las distribuciones sumen 100%
     */
    private function validateDistributions(array $distributions): bool
    {
        $totalPercentage = collect($distributions)->sum('percentage');

        return abs($totalPercentage - 100) < 0.01;
    }

    /**
     * Obtiene resumen de cuentas por pagar
     */
    public function getPayablesSummary(int $clientId, ?string $status = null): array
    {
        $query = SupplierInvoice::where('client_id', $clientId);

        if ($status) {
            $query->where('status', $status);
        }

        $invoices = $query->get();

        return [
            'total_invoices' => $invoices->count(),
            'total_amount' => $invoices->sum('total_amount'),
            'total_paid' => $invoices->sum('paid_amount'),
            'total_balance' => $invoices->sum('balance'),
            'overdue_count' => $invoices->where('status', 'overdue')->count(),
            'overdue_amount' => $invoices->where('status', 'overdue')->sum('balance'),
        ];
    }

    /**
     * Obtiene facturas vencidas
     */
    public function getOverdueInvoices(int $clientId): Collection
    {
        return SupplierInvoice::where('client_id', $clientId)
            ->whereDate('due_date', '<', now())
            ->where('status', '!=', 'paid')
            ->where('status', '!=', 'cancelled')
            ->orderBy('due_date')
            ->get();
    }

    /**
     * Actualiza estados de facturas vencidas (para ejecutarse como cron)
     */
    public function updateOverdueStatuses(int $clientId): int
    {
        $updated = 0;
        foreach ($this->getOverdueInvoices($clientId) as $invoice) {
            if ($invoice->status !== 'overdue') {
                $invoice->update(['status' => 'overdue']);
                $updated++;
            }
        }

        return $updated;
    }

    /**
     * Aprueba múltiples facturas en lote
     */
    public function approveBatch(int $clientId, array $invoiceIds): int
    {
        $approved = 0;
        foreach ($invoiceIds as $invoiceId) {
            $invoice = SupplierInvoice::where('client_id', $clientId)->findOrFail($invoiceId);
            if ($invoice->status === 'draft') {
                $this->approveInvoice($invoice);
                $approved++;
            }
        }

        return $approved;
    }
}
