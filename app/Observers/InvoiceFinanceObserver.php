<?php

namespace App\Observers;

use App\Enums\InvoivePatientStatus;
use App\Models\Invoice;
use App\Services\Finance\AccountsReceivableService;

/**
 * Observer para Invoice
 * Maneja la generación automática de CxC cuando se crean facturas a crédito
 */
class InvoiceFinanceObserver
{
    public function __construct(
        protected AccountsReceivableService $receivablesService,
    ) {}

    /**
     * Cuando se crea una factura
     * NOTE: CxC creation is now handled by ConsultationInvoiceService::triggerAccountingEvents()
     * This method is kept empty to avoid duplicate CxC records
     *
     * @deprecated Use ConsultationInvoiceService instead
     */
    public function created(Invoice $invoice): void
    {
        // CxC is created by ConsultationInvoiceService to ensure correct timing and avoid duplicates
        // This observer only handles updates to existing CxC
    }

    /**
     * Cuando se actualiza una factura
     * Actualiza CxC si cambió el status de pago
     */
    public function updated(Invoice $invoice): void
    {
        // Validar si contabilidad está habilitada
        if (! $this->shouldProcess($invoice)) {
            return;
        }

        // Si el pago cambió a PAID, actualizar CxC
        if ($invoice->isDirty('payment_status') && $invoice->accountsReceivable) {
            if ($invoice->payment_status === InvoivePatientStatus::PAID) {
                $invoice->accountsReceivable->update([
                    'status' => 'paid',
                    'paid_amount' => $invoice->total_amount,
                    'balance' => 0,
                ]);
            }
        }
    }

    /**
     * Validar si se debe procesar el evento contable
     */
    private function shouldProcess(Invoice $invoice): bool
    {
        // Load client relation if not already loaded
        if (! $invoice->relationLoaded('client')) {
            $invoice->load('client');
        }

        // Solo procesar si client tiene accounting habilitado
        if (! $invoice->client?->accounting_enabled) {
            return false;
        }

        return true;
    }
}
