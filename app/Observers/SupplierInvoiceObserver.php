<?php

namespace App\Observers;

use App\Models\SupplierInvoice;
use App\Services\Accounting\AccountingEngineService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
                && $invoice->status === 'approved'
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
                && $invoice->status === 'cancelled'
                && $invoice->journal_entry_id
            ) {
                DB::transaction(function () use ($invoice) {
                    try {
                        $invoice->journalEntry->reverse('Cancelación de factura de proveedor');

                        Log::info('Supplier invoice journal entry reversed', [
                            'model' => 'SupplierInvoice',
                            'id' => $invoice->id,
                            'journal_entry_id' => $invoice->journal_entry_id,
                            'client_id' => $invoice->client_id,
                        ]);
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
     * Validar si se debe procesar el evento contable
     */
    private function shouldProcess(SupplierInvoice $invoice): bool
    {
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
}
