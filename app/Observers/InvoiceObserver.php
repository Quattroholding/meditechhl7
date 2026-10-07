<?php

namespace App\Observers;

use App\Models\Invoice;
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
     */
    public function deleted(Invoice $invoice): void
    {
        $this->clearDashboardCache();
    }

    /**
     * Handle the Invoice "restored" event.
     */
    public function restored(Invoice $invoice): void
    {
        $this->clearDashboardCache();
    }

    /**
     * Handle the Invoice "force deleted" event.
     */
    public function forceDeleted(Invoice $invoice): void
    {
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
