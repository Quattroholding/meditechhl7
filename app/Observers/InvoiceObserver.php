<?php

namespace App\Observers;

use App\Models\Invoice;
use App\Models\User;
use App\Services\Accounting\AccountingEngineService;
use App\Services\Finance\AccountsReceivableService;
use App\Services\Treasury\TreasuryService;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InvoiceObserver
{
    public function __construct(
        protected AccountingEngineService $accountingEngine,
        protected AccountsReceivableService $accountsReceivable,
        protected TreasuryService $treasury,
    ) {}

    /**
     * Handle the Invoice "created" event.
     * NOTE: Accounting events are now handled by ConsultationInvoiceService::triggerAccountingEvents()
     * This observer is kept for backward compatibility and for invoices created through other means
     *
     * @deprecated Use ConsultationInvoiceService instead
     */
    public function created(Invoice $invoice): void
    {
        // Events are now handled in ConsultationInvoiceService to ensure correct totals
        // This method is kept for safety in case invoices are created through other channels
        Log::info('Invoice created event (events handled by ConsultationInvoiceService)', [
            'model' => 'Invoice',
            'id' => $invoice->id,
        ]);

        $this->clearDashboardCache();
    }

    /**
     * Handle the Invoice "updated" event.
     * Detecta cambios de estado de pago
     */
    public function updated(Invoice $invoice): void
    {
        try {
            // Si cambió payment_status de pending/partial a paid
            if (
                $invoice->isDirty('payment_status')
                && $invoice->payment_status === 'paid'
                && in_array($invoice->getOriginal('payment_status'), ['pending', 'partial'])
            ) {
                if (! $this->shouldProcess($invoice)) {
                    return;
                }

                DB::transaction(function () use ($invoice) {
                    $this->handlePaymentStatusChange($invoice);
                });

                Log::info('Invoice payment status updated', [
                    'model' => 'Invoice',
                    'id' => $invoice->id,
                    'old_status' => $invoice->getOriginal('payment_status'),
                    'new_status' => $invoice->payment_status,
                    'client_id' => $invoice->client_id,
                ]);
            }
        } catch (Exception $e) {
            Log::error('Failed to process invoice status change', [
                'model' => 'Invoice',
                'id' => $invoice->id,
                'error' => $e->getMessage(),
                'client_id' => $invoice->client_id,
            ]);
        }

        $this->clearDashboardCache();
    }

    /**
     * Handle the Invoice "deleted" event.
     * Revierte asientos contables y elimina CxC cuando se borra una factura
     */
    public function deleted(Invoice $invoice): void
    {
        try {
            DB::transaction(function () use ($invoice) {
                // Si la factura tiene un asiento contable, revertirlo
                if ($invoice->journal_entry_id) {
                    $journalEntry = $invoice->journalEntry;
                    if ($journalEntry && $journalEntry->isPosted()) {
                        // Revertir el asiento contable
                        $journalEntry->reverse(auth()->user() ?? User::find(1));

                        Log::info('Invoice journal entry reversed on deletion', [
                            'invoice_id' => $invoice->id,
                            'journal_entry_id' => $invoice->journal_entry_id,
                            'entry_number' => $journalEntry->entry_number,
                        ]);
                    }
                }

                // Si la factura tiene CxC, eliminarla
                if ($invoice->accounts_receivable_id) {
                    $receivable = $invoice->accountsReceivable;
                    if ($receivable) {
                        $receivable->delete();

                        Log::info('Accounts receivable deleted on invoice deletion', [
                            'invoice_id' => $invoice->id,
                            'receivable_id' => $invoice->accounts_receivable_id,
                        ]);
                    }
                }
            });
        } catch (Exception $e) {
            Log::error('Error reverting accounting entries on invoice deletion', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);
        }

        $this->clearDashboardCache();
    }

    /**
     * Handle the Invoice "restored" event.
     * Cuando se restaura una factura eliminada, se debe restaurar su asiento contable
     */
    public function restored(Invoice $invoice): void
    {
        try {
            DB::transaction(function () use ($invoice) {
                // Si la factura tiene un asiento marcado como REVERSED, restaurar una reversión inversa
                if ($invoice->journal_entry_id) {
                    $originalEntry = $invoice->journalEntry;
                    if ($originalEntry && $originalEntry->isReversed()) {
                        // Buscar la reversión creada
                        $reversingEntry = JournalEntry::where('reversed_entry_id', $originalEntry->id)
                            ->where('status', 'posted')
                            ->first();

                        if ($reversingEntry) {
                            // Crear una reversión de la reversión (restaura el original)
                            $unreversingEntry = JournalEntry::create([
                                'client_id' => $originalEntry->client_id,
                                'entry_number' => 'UNREVERSE-'.$originalEntry->entry_number.'-'.now()->timestamp,
                                'entry_date' => now()->toDateString(),
                                'document_type' => $originalEntry->document_type,
                                'document_number' => $originalEntry->document_number,
                                'description' => 'Restoration of reversed entry '.$originalEntry->entry_number,
                                'status' => 'posted',
                                'accounting_period_id' => $originalEntry->accounting_period_id,
                                'source_type' => $originalEntry->source_type,
                                'source_id' => $originalEntry->source_id,
                                'posted_at' => now(),
                                'posted_by' => auth()->id() ?? 1,
                                'created_by' => auth()->id() ?? 1,
                            ]);

                            // Copiar las líneas del asiento original (invirtiendo la reversión)
                            foreach ($originalEntry->journalEntryLines as $line) {
                                JournalEntryLine::create([
                                    'journal_entry_id' => $unreversingEntry->id,
                                    'accounting_account_id' => $line->accounting_account_id,
                                    'cost_center_id' => $line->cost_center_id,
                                    'branch_id' => $line->branch_id,
                                    'debit' => $line->debit,
                                    'credit' => $line->credit,
                                    'description' => $line->description,
                                ]);
                            }

                            // Marcar el original como NO reversado
                            $originalEntry->update(['status' => 'posted']);

                            Log::info('Invoice restored and accounting entries recreated', [
                                'invoice_id' => $invoice->id,
                                'original_entry_number' => $originalEntry->entry_number,
                                'unreversing_entry_number' => $unreversingEntry->entry_number,
                            ]);
                        }
                    }
                }

                // Restaurar CxC si existe
                if ($invoice->accounts_receivable_id) {
                    $receivable = DB::table('accounts_receivable')
                        ->where('id', $invoice->accounts_receivable_id)
                        ->first();

                    if ($receivable) {
                        DB::table('accounts_receivable')
                            ->where('id', $invoice->accounts_receivable_id)
                            ->update(['deleted_at' => null]);

                        Log::info('Accounts receivable restored on invoice restoration', [
                            'invoice_id' => $invoice->id,
                            'receivable_id' => $invoice->accounts_receivable_id,
                        ]);
                    }
                }
            });
        } catch (Exception $e) {
            Log::error('Error restoring accounting entries on invoice restoration', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);
        }

        $this->clearDashboardCache();
    }

    /**
     * Handle the Invoice "force deleted" event.
     * Nota: Soft-deleted invoices pueden ser restauradas, así que la reversión ocurre en deleted()
     * forceDeleted es para eliminación permanente (solo si se ejecuta manualmente)
     */
    public function forceDeleted(Invoice $invoice): void
    {
        // No hacer nada - la reversión ya ocurrió en deleted()
        $this->clearDashboardCache();
    }

    /**
     * Maneja factura de contado (paid)
     * Genera asiento: Débito Banco/Caja, Crédito Ingreso por Servicios
     */
    private function handleCashInvoice(Invoice $invoice): void
    {
        $eventCode = 'INVOICE_CASH';

        $eventData = [
            'payment_method' => $invoice->payment_method,
            'amount' => $invoice->total_amount,
        ];

        $journalEntry = $this->accountingEngine->processEvent($eventCode, $invoice, $eventData);

        if ($journalEntry) {
            $invoice->update(['journal_entry_id' => $journalEntry->id]);
            $this->logAccountingEvent($invoice, $eventCode, true);
        }
    }

    /**
     * Maneja factura a crédito
     * Genera asiento: Débito CxC, Crédito Ingreso por Servicios
     * Crea registro de AccountsReceivable
     */
    private function handleCreditInvoice(Invoice $invoice): void
    {
        $eventCode = 'INVOICE_CREDIT';

        $journalEntry = $this->accountingEngine->processEvent($eventCode, $invoice);

        if ($journalEntry) {
            $invoice->update(['journal_entry_id' => $journalEntry->id]);

            // Crear AccountsReceivable vinculado a la factura
            $this->accountsReceivable->createFromInvoice($invoice);

            $this->logAccountingEvent($invoice, $eventCode, true);
        }
    }

    /**
     * Maneja cambio de estado de pago (de pending/partial a paid)
     */
    private function handlePaymentStatusChange(Invoice $invoice): void
    {
        // Si ya tiene asiento contable de crédito, no generar otro
        if ($invoice->journal_entry_id) {
            return;
        }

        // Si pasó de crédito a contado, generar asiento de cobro
        $eventCode = 'PAYMENT_RECEIVED';

        $eventData = [
            'payment_method' => $invoice->payment_method,
            'amount' => $invoice->total_amount,
        ];

        $journalEntry = $this->accountingEngine->processEvent($eventCode, $invoice, $eventData);

        if ($journalEntry) {
            $invoice->update(['journal_entry_id' => $journalEntry->id]);
            $this->logAccountingEvent($invoice, $eventCode, true);
        }
    }

    /**
     * Validar si se debe procesar el evento contable
     */
    private function shouldProcess(Invoice $invoice): bool
    {
        // Solo procesar si client tiene accounting habilitado
        if (! $invoice->client?->accounting_enabled) {
            return false;
        }

        // Evitar procesar facturas incompletas (creadas sin líneas/totales)
        // Si total_amount es 0 y no tiene líneas, es probable que aún se esté creando
        if ($invoice->total_amount <= 0 && $invoice->lineItems()->count() === 0) {
            return false;
        }

        // Solo procesar si existen configuraciones de eventos
        // (Esta validación podría expandirse según necesidades)

        return true;
    }

    /**
     * Loguear eventos contables
     */
    private function logAccountingEvent(Invoice $model, string $event, bool $result): void
    {
        Log::info('Accounting event processed', [
            'model' => class_basename($model),
            'id' => $model->id,
            'event' => $event,
            'success' => $result,
            'client_id' => $model->client_id,
        ]);
    }

    /**
     * Clear dashboard cache for invoices
     */
    private function clearDashboardCache(): void
    {
        Cache::tags(['dashboard', 'invoices'])->flush();
    }
}
