<?php

namespace App\Services\Accounting;

use App\Enums\JournalEntryStatus;
use App\Models\AccountingAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\PaymentSchedule;
use App\Models\SupplierInvoice;
use Illuminate\Support\Facades\DB;

class AccountingEngineService
{
    protected AccountingService $accountingService;

    protected JournalEntryService $journalEntryService;

    public function __construct(
        AccountingService $accountingService,
        JournalEntryService $journalEntryService
    ) {
        $this->accountingService = $accountingService;
        $this->journalEntryService = $journalEntryService;
    }

    /**
     * Procesa un evento contable y genera asientos automáticamente
     */
    public function processEvent(string $eventCode, $source, array $data = []): ?JournalEntry
    {
        return match ($eventCode) {
            'SUPPLIER_INVOICE_CREATED' => $this->processSupplierInvoiceCreated($source),
            'SUPPLIER_INVOICE_APPROVED' => $this->processSupplierInvoiceApproved($source),
            'SUPPLIER_PAYMENT' => $this->processSupplierPayment($source),
            'TREASURY_MOVEMENT' => null, // Los movimientos de tesorería se manejan en TreasuryService
            default => null,
        };
    }

    /**
     * Genera asiento contable cuando se aprueba factura de proveedor
     * Débito: Gasto/Inventario (según CostCenter)
     * Crédito: CxP
     */
    public function processSupplierInvoiceCreated(SupplierInvoice $invoice): JournalEntry
    {
        return DB::transaction(function () use ($invoice) {
            $period = $this->accountingService->getCurrentPeriod($invoice->client_id);

            $entry = JournalEntry::create([
                'client_id' => $invoice->client_id,
                'entry_date' => now()->toDateString(),
                'document_type' => 'supplier_invoice',
                'document_number' => $invoice->invoice_number,
                'description' => "Factura del proveedor {$invoice->supplier->legal_name} - {$invoice->invoice_number}",
                'status' => JournalEntryStatus::POSTED,
                'accounting_period_id' => $period->id,
                'source_type' => SupplierInvoice::class,
                'source_id' => $invoice->id,
                'posted_at' => now(),
                'posted_by' => auth()->id(),
                'created_by' => auth()->id(),
            ]);

            // Obtener distribuciones de costos
            $distributions = $invoice->costDistributions()->get();

            if ($distributions->isEmpty()) {
                // Sin distribución específica, usar costo center principal o default
                $costCenterId = $invoice->cost_center_id;
                $amount = $invoice->total_amount;

                // Obtener cuenta contable del costo center
                $expenseAccount = $this->getExpenseAccountForCostCenter(
                    $invoice->client_id,
                    $costCenterId
                );

                // Línea débito: Gasto
                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'accounting_account_id' => $expenseAccount->id,
                    'cost_center_id' => $costCenterId,
                    'debit' => $amount,
                    'credit' => 0,
                    'description' => "Gasto factura {$invoice->invoice_number}",
                ]);
            } else {
                // Con distribución por centros de costo
                foreach ($distributions as $distribution) {
                    $expenseAccount = $this->getExpenseAccountForCostCenter(
                        $invoice->client_id,
                        $distribution->cost_center_id
                    );

                    JournalEntryLine::create([
                        'journal_entry_id' => $entry->id,
                        'accounting_account_id' => $expenseAccount->id,
                        'cost_center_id' => $distribution->cost_center_id,
                        'debit' => $distribution->amount,
                        'credit' => 0,
                        'description' => "Gasto distribuido - {$distribution->costCenter->name}",
                    ]);
                }
            }

            // Línea crédito: CxP (Cuentas por Pagar)
            JournalEntryLine::create([
                'journal_entry_id' => $entry->id,
                'accounting_account_id' => $invoice->supplier->accounting_account_id,
                'debit' => 0,
                'credit' => $invoice->total_amount,
                'description' => "CxP {$invoice->supplier->legal_name}",
            ]);

            // Vincular asiento a factura
            $invoice->update(['journal_entry_id' => $entry->id]);

            // Actualizar balances de cuentas
            $entry->lines->each(fn ($line) => $line->accountingAccount->updateBalance());

            return $entry;
        });
    }

    /**
     * Procesa cuando se aprueba una factura de proveedor
     */
    public function processSupplierInvoiceApproved(SupplierInvoice $invoice): void
    {
        // Regenerar asiento si la aprobación generó cambios
        if ($invoice->journal_entry_id) {
            $invoice->journalEntry->reverse('Regeneración por cambios en aprobación');
            $this->processSupplierInvoiceCreated($invoice);
        }
    }

    /**
     * Obtiene la cuenta de gasto apropiada para un centro de costo
     */
    private function getExpenseAccountForCostCenter(int $clientId, ?int $costCenterId): AccountingAccount
    {
        // Lógica: buscar cuenta de gasto asociada al centro de costos
        // Por ahora retorna cuenta default de gastos

        return AccountingAccount::where('client_id', $clientId)
            ->where('code', '6101') // Gastos Administrativos (default)
            ->firstOrFail();
    }

    /**
     * Procesa el pago de una factura de proveedor
     * Débito: CxP
     * Crédito: Banco/Caja
     */
    public function processSupplierPayment(PaymentSchedule $schedule): JournalEntry
    {
        return DB::transaction(function () use ($schedule) {
            $invoice = $schedule->supplierInvoice;
            $period = $this->accountingService->getCurrentPeriod($invoice->client_id);

            $entry = JournalEntry::create([
                'client_id' => $invoice->client_id,
                'entry_date' => now()->toDateString(),
                'document_type' => 'payment_schedule',
                'document_number' => "PAG-{$invoice->invoice_number}",
                'description' => "Pago de factura {$invoice->invoice_number} del proveedor {$invoice->supplier->legal_name}",
                'status' => JournalEntryStatus::POSTED,
                'accounting_period_id' => $period->id,
                'source_type' => PaymentSchedule::class,
                'source_id' => $schedule->id,
                'posted_at' => now(),
                'posted_by' => auth()->id(),
                'created_by' => auth()->id(),
            ]);

            // Obtener cuenta de banco o caja del movimiento de tesorería
            $treasuryMovement = $schedule->treasuryMovement;
            $bankAccountId = null;

            if ($treasuryMovement && $treasuryMovement->bank) {
                $bankAccountId = $treasuryMovement->bank->accounting_account_id;
            } elseif ($treasuryMovement && $treasuryMovement->cashRegister) {
                $bankAccountId = $treasuryMovement->cashRegister->accounting_account_id;
            }

            // Línea débito: CxP (Cuentas por Pagar)
            JournalEntryLine::create([
                'journal_entry_id' => $entry->id,
                'accounting_account_id' => $invoice->supplier->accounting_account_id,
                'debit' => $schedule->amount,
                'credit' => 0,
                'description' => "Pago CxP {$invoice->supplier->legal_name}",
            ]);

            // Línea crédito: Banco/Caja
            if ($bankAccountId) {
                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'accounting_account_id' => $bankAccountId,
                    'debit' => 0,
                    'credit' => $schedule->amount,
                    'description' => "Egreso pago a {$invoice->supplier->legal_name}",
                ]);
            }

            // Actualizar balances de cuentas
            $entry->lines->each(fn ($line) => $line->accountingAccount->updateBalance());

            return $entry;
        });
    }

    /**
     * Revierte un asiento y regenera si es necesario
     */
    public function reverseAndRegenerate(JournalEntry $entry): JournalEntry
    {
        $source = $entry->source;

        // Revertir asiento existente
        $entry->reverse('Regeneración de asiento');

        // Regenerar nuevo asiento
        if ($source instanceof SupplierInvoice) {
            return $this->processSupplierInvoiceCreated($source);
        }

        throw new \Exception('No se puede regenerar asiento de tipo desconocido');
    }
}
