<?php

namespace App\Observers;

use App\Models\SupplierInvoice;
use App\Services\Accounting\AccountingEngineService;

/**
 * Observer para SupplierInvoice
 * Genera asientos contables automáticamente cuando se crean o aprueban facturas
 */
class SupplierInvoiceObserver
{
    public function __construct(protected AccountingEngineService $accountingEngine) {}

    /**
     * Cuando se aprueba una factura de proveedor
     * Genera asiento contable automático
     */
    public function updated(SupplierInvoice $invoice): void
    {
        // Si la factura cambió a status 'approved' y no tiene asiento contable
        if (
            $invoice->isDirty('status')
            && $invoice->status === 'approved'
            && ! $invoice->journal_entry_id
        ) {
            $journalEntry = $this->accountingEngine->processSupplierInvoiceCreated($invoice);
            $invoice->update(['journal_entry_id' => $journalEntry->id]);
        }

        // Si la factura fue revertida y tiene asiento, revertir asiento contable
        if (
            $invoice->isDirty('status')
            && $invoice->status === 'cancelled'
            && $invoice->journal_entry_id
        ) {
            $invoice->journalEntry->reverse('Cancelación de factura de proveedor');
        }
    }
}
