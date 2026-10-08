<?php

namespace App\Console\Commands;

use App\Models\Finance\PaymentSchedule;
use App\Services\Treasury\TreasuryService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

#[Signature('supplier-invoices:process-scheduled-payments {--client-id=}')]
#[Description('Procesa pagos programados con fecha <= hoy y crea movimientos de tesorería')]
class ProcessScheduledPaymentsCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(TreasuryService $treasuryService): void
    {
        $this->info('Iniciando procesamiento de pagos programados...');

        $query = PaymentSchedule::where('paid', false)
            ->where('payment_date', '<=', now()->toDateString())
            ->with(['supplierInvoice' => fn ($q) => $q->with('supplier')]);

        // Filtrar por cliente si se especifica
        if ($clientId = $this->option('client-id')) {
            $query->whereHas('supplierInvoice', fn ($q) => $q->where('client_id', $clientId));
            $this->info("Filtrando por cliente: {$clientId}");
        }

        $pendingPayments = $query->get();

        if ($pendingPayments->isEmpty()) {
            $this->info('No hay pagos programados pendientes para procesar.');

            return;
        }

        $this->info("Se encontraron {$pendingPayments->count()} pagos programados pendientes");

        $processed = 0;
        $failed = 0;

        foreach ($pendingPayments as $schedule) {
            try {
                // Validar que al menos se haya seleccionado banco o caja
                if (! $schedule->bank_id && ! $schedule->cash_register_id) {
                    throw new \Exception('PaymentSchedule no tiene banco o caja especificada');
                }

                // Crear TreasuryMovement para el pago
                $movement = $treasuryService->recordMovement(
                    $schedule->supplierInvoice->client_id,
                    [
                        'movement_type' => 'withdrawal',
                        'source_type' => PaymentSchedule::class,
                        'source_id' => $schedule->id,
                        'bank_id' => $schedule->bank_id,
                        'cash_register_id' => $schedule->cash_register_id,
                        'amount' => $schedule->amount,
                        'description' => "Pago factura {$schedule->supplierInvoice->invoice_number} - {$schedule->supplierInvoice->supplier->legal_name}",
                        'reference_number' => $schedule->supplierInvoice->invoice_number,
                    ]
                );

                // Marcar como pagado
                $schedule->update([
                    'paid' => true,
                    'paid_at' => now(),
                    'treasury_movement_id' => $movement->id,
                ]);

                // Actualizar monto pagado en factura
                $invoicePaidAmount = $schedule->supplierInvoice->paymentSchedules()
                    ->where('paid', true)
                    ->sum('amount');

                $schedule->supplierInvoice->update([
                    'paid_amount' => $invoicePaidAmount,
                    'balance' => $schedule->supplierInvoice->total_amount - $invoicePaidAmount,
                ]);

                $this->line("✓ Pago procesado: {$schedule->supplierInvoice->invoice_number} - B/. ".number_format($schedule->amount, 2));
                $processed++;
            } catch (\Exception $e) {
                $this->error("✗ Error procesando pago {$schedule->id}: {$e->getMessage()}");
                Log::error('ProcessScheduledPayments: Error', [
                    'schedule_id' => $schedule->id,
                    'invoice_number' => $schedule->supplierInvoice->invoice_number,
                    'error' => $e->getMessage(),
                ]);
                $failed++;
            }
        }

        $this->newLine();
        $this->info('Procesamiento completado:');
        $this->info("  Pagos procesados: {$processed}");
        $this->info("  Errores: {$failed}");
    }
}
