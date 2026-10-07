<?php

namespace App\Observers;

use App\Models\Finance\AccountsReceivable;
use App\Models\Payment;
use App\Models\Treasury\Bank;
use App\Models\Treasury\CashRegister;
use App\Services\Accounting\AccountingEngineService;
use App\Services\Finance\AccountsReceivableService;
use App\Services\Treasury\TreasuryService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Observer para Payment
 * Automatiza la generación de asientos contables y movimientos de tesorería
 * cuando se registran pagos de facturas
 */
class PaymentObserver
{
    public function __construct(
        protected AccountingEngineService $accountingEngine,
        protected AccountsReceivableService $accountsReceivable,
        protected TreasuryService $treasury,
    ) {}

    /**
     * Handle the Payment "created" event.
     * Registra movimiento de tesorería y genera asiento contable
     */
    public function created(Payment $payment): void
    {
        try {
            // Validar precondiciones
            if (! $this->shouldProcess($payment)) {
                return;
            }

            DB::transaction(function () use ($payment) {
                // Si el pago es para una factura a crédito
                $invoice = $payment->invoice;
                if ($invoice) {
                    // Buscar si existe AccountsReceivable
                    $receivable = $invoice->accountsReceivable ??
                                  AccountsReceivable::where('invoice_id', $invoice->id)->first();

                    if ($receivable) {
                        // Aplicar pago a AccountsReceivable
                        $this->accountsReceivable->applyPayment($receivable, $payment->amount);
                    }
                }

                // Crear movimiento de tesorería
                $this->handleTreasuryMovement($payment);

                // Generar asiento contable
                $this->handleAccountingEntry($payment);
            });

            Log::info('Payment accounting event processed', [
                'model' => 'Payment',
                'id' => $payment->id,
                'invoice_id' => $payment->invoice_id,
                'amount' => $payment->amount,
                'client_id' => $payment->client_id,
            ]);
        } catch (Exception $e) {
            Log::error('Failed to process payment accounting event', [
                'model' => 'Payment',
                'id' => $payment->id,
                'error' => $e->getMessage(),
                'client_id' => $payment->client_id,
            ]);
        }
    }

    /**
     * Crea movimiento de tesorería (ingreso)
     * movement_type: 'deposit'
     * source_type: Payment::class
     * source_id: payment.id
     * bank_id/cash_register_id: según payment_method
     */
    private function handleTreasuryMovement(Payment $payment): void
    {
        // Obtener el destino del método de pago (bank_id o cash_register_id)
        $destination = $this->getPaymentMethodDestination($payment->payment_method);

        if (! $destination) {
            Log::warning('No treasury destination found for payment method', [
                'payment_id' => $payment->id,
                'method' => $payment->payment_method,
            ]);

            return;
        }

        $invoiceNumber = $payment->invoice?->invoice_number ?? 'N/A';
        $movementData = [
            'movement_type' => 'deposit',
            'amount' => $payment->amount,
            'movement_date' => $payment->payment_date ?? now()->toDateString(),
            'description' => "Pago de factura {$invoiceNumber}",
            'reference_number' => $payment->reference_number,
            'source_type' => Payment::class,
            'source_id' => $payment->id,
        ];

        // Añadir bank_id o cash_register_id según corresponda
        if (isset($destination['bank_id'])) {
            $movementData['bank_id'] = $destination['bank_id'];
        } elseif (isset($destination['cash_register_id'])) {
            $movementData['cash_register_id'] = $destination['cash_register_id'];
        }

        $treasuryMovement = $this->treasury->recordMovement($payment->client_id, $movementData);

        if ($treasuryMovement) {
            $payment->update(['treasury_movement_id' => $treasuryMovement->id]);
        }
    }

    /**
     * Genera asiento contable del pago
     * Débito: Banco/Caja
     * Crédito: Cuentas por Cobrar
     */
    private function handleAccountingEntry(Payment $payment): void
    {
        $eventCode = 'PAYMENT_RECEIVED';

        $eventData = [
            'payment_method' => $payment->payment_method,
            'amount' => $payment->amount,
            'reference' => $payment->reference_number,
        ];

        $journalEntry = $this->accountingEngine->processEvent($eventCode, $payment, $eventData);

        if ($journalEntry) {
            $payment->update(['journal_entry_id' => $journalEntry->id]);
            $this->logAccountingEvent($payment, $eventCode, true);
        }
    }

    /**
     * Obtiene el destino del pago (bank_id o cash_register_id) según el método
     * Retorna array con 'bank_id' o 'cash_register_id'
     */
    private function getPaymentMethodDestination(string $paymentMethod): ?array
    {
        return match ($paymentMethod) {
            'cash' => [
                'cash_register_id' => CashRegister::query()
                    ->where('client_id', auth()->user()?->getCurrentClient()?->id)
                    ->first()?->id,
            ],
            'credit_card', 'debit_card', 'bank_transfer', 'online' => [
                'bank_id' => Bank::query()
                    ->where('client_id', auth()->user()?->getCurrentClient()?->id)
                    ->first()?->id,
            ],
            'check' => [
                'bank_id' => Bank::query()
                    ->where('client_id', auth()->user()?->getCurrentClient()?->id)
                    ->first()?->id,
            ],
            default => null,
        };
    }

    /**
     * Validar si se debe procesar el evento contable
     */
    private function shouldProcess(Payment $payment): bool
    {
        // Solo procesar si client tiene accounting habilitado
        if (! $payment->client?->accounting_enabled) {
            return false;
        }

        // Solo procesar si el pago está completado
        if ($payment->status !== 'completed') {
            return false;
        }

        return true;
    }

    /**
     * Loguear eventos contables
     */
    private function logAccountingEvent($model, string $event, bool $result): void
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
