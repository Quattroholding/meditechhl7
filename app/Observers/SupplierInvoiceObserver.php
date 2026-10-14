<?php

namespace App\Observers;

use App\Enums\SupplierInvoiceStatus;
use App\Models\Accounting\JournalEntry;
use App\Models\Accounting\JournalEntryLine;
use App\Models\Finance\SupplierInvoice;
use App\Models\User;
use App\Services\Accounting\AccountingEngineService;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Observer para SupplierInvoice
 * Genera asientos contables automáticamente cuando se crean o aprueban facturas
 * Maneja distribuciones por centro de costo y validaciones contables
 */
class SupplierInvoiceObserver
{
    public function __construct(protected AccountingEngineService $accountingEngine) {}

    /**
     * Handle the SupplierInvoice "created" event.
     * No genera asiento aún, solo registra la factura
     */
    public function created(SupplierInvoice $invoice): void
    {
        // Por ahora, solo loguear creación
        // El asiento se genera cuando se aprueba
        Log::info('Supplier invoice created', [
            'model' => 'SupplierInvoice',
            'id' => $invoice->id,
            'invoice_number' => $invoice->invoice_number,
            'client_id' => $invoice->client_id,
        ]);
    }

    /**
     * Handle the SupplierInvoice "updated" event.
     * Genera asiento contable cuando status cambia a 'approved'
     * Revierte asiento si se cancela
     */
    public function updated(SupplierInvoice $invoice): void
    {
        try {
            // Si la factura cambió a status 'approved' y no tiene asiento contable
            if (
                $invoice->isDirty('status')
                && $invoice->status === SupplierInvoiceStatus::APPROVED
                && ! $invoice->journal_entry_id
            ) {
                if (! $this->shouldProcess($invoice)) {
                    return;
                }

                DB::transaction(function () use ($invoice) {
                    $journalEntry = $this->accountingEngine->processSupplierInvoiceCreated($invoice);

                    if ($journalEntry) {
                        // Registrar campos de aprobación
                        $invoice->update([
                            'journal_entry_id' => $journalEntry->id,
                            'approved_at' => now(),
                            'approved_by' => auth()->id(),
                        ]);

                        $this->logAccountingEvent($invoice, 'SUPPLIER_INVOICE_APPROVED', true);
                    }
                });
            }

            // Si la factura fue revertida y tiene asiento, revertir asiento contable
            if (
                $invoice->isDirty('status')
                && $invoice->status === SupplierInvoiceStatus::CANCELLED
                && $invoice->journal_entry_id
            ) {
                DB::transaction(function () use ($invoice) {
                    try {
                        $journalEntry = $invoice->journalEntry;
                        if ($journalEntry && $journalEntry->isPosted()) {
                            $journalEntry->reverse(auth()->user() ?? User::find(1));

                            Log::info('Supplier invoice journal entry reversed', [
                                'model' => 'SupplierInvoice',
                                'id' => $invoice->id,
                                'journal_entry_id' => $invoice->journal_entry_id,
                                'client_id' => $invoice->client_id,
                            ]);
                        }
                    } catch (Exception $e) {
                        Log::error('Failed to reverse journal entry', [
                            'journal_entry_id' => $invoice->journal_entry_id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                });
            }
        } catch (Exception $e) {
            Log::error('Failed to process supplier invoice accounting event', [
                'model' => 'SupplierInvoice',
                'id' => $invoice->id,
                'error' => $e->getMessage(),
                'client_id' => $invoice->client_id,
            ]);
        }
    }

    /**
     * Handle the SupplierInvoice "deleted" event.
     * Revierte asientos contables cuando se borra una factura
     */
    public function deleted(SupplierInvoice $invoice): void
    {
        try {
            DB::transaction(function () use ($invoice) {
                // Si la factura tiene un asiento contable, revertirlo
                if ($invoice->journal_entry_id) {
                    $journalEntry = $invoice->journalEntry;
                    if ($journalEntry && $journalEntry->isPosted()) {
                        $journalEntry->reverse(auth()->user() ?? User::find(1));

                        Log::info('Supplier invoice journal entry reversed on deletion', [
                            'supplier_invoice_id' => $invoice->id,
                            'journal_entry_id' => $invoice->journal_entry_id,
                            'entry_number' => $journalEntry->entry_number,
                        ]);
                    }
                }
            });
        } catch (Exception $e) {
            Log::error('Error reverting accounting entries on supplier invoice deletion', [
                'supplier_invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);
        }

        $this->clearDashboardCache();
    }

    /**
     * Handle the SupplierInvoice "restored" event.
     * Cuando se restaura una factura eliminada, se debe restaurar su asiento contable
     */
    public function restored(SupplierInvoice $invoice): void
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
                                'uuid' => Str::uuid(),
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

                            Log::info('Supplier invoice restored and accounting entries recreated', [
                                'supplier_invoice_id' => $invoice->id,
                                'original_entry_number' => $originalEntry->entry_number,
                                'unreversing_entry_number' => $unreversingEntry->entry_number,
                            ]);
                        }
                    }
                }
            });
        } catch (Exception $e) {
            Log::error('Error restoring accounting entries on supplier invoice restoration', [
                'supplier_invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);
        }

        $this->clearDashboardCache();
    }

    /**
     * Validar si se debe procesar el evento contable
     */
    private function shouldProcess(SupplierInvoice $invoice): bool
    {
        // Load client relation if not already loaded
        if (! $invoice->relationLoaded('client')) {
            $invoice->load('client');
        }

        // Validar que client existe y tiene accounting habilitado
        if (! $invoice->client || ! $invoice->client->accounting_enabled) {
            Log::warning('Accounting not enabled for client', [
                'client_id' => $invoice->client_id,
                'supplier_invoice_id' => $invoice->id,
            ]);

            return false;
        }

        // Validar que existen configuraciones de eventos
        // (Podría expandirse según necesidades del negocio)

        // Validar que período contable está abierto
        try {
            // Esta validación depende del servicio de contabilidad
            // Por ahora, permitir el procesamiento
        } catch (Exception $e) {
            Log::warning('Period validation failed', [
                'supplier_invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }

        return true;
    }

    /**
     * Loguear eventos contables
     */
    private function logAccountingEvent(SupplierInvoice $model, string $event, bool $result): void
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
     * Clear dashboard cache for supplier invoices
     */
    private function clearDashboardCache(): void
    {
        Cache::tags(['dashboard', 'invoices', 'payables'])->flush();
    }
}
