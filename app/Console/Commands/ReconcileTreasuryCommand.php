<?php

namespace App\Console\Commands;

use App\Models\Finance\PaymentSchedule;
use App\Models\Treasury\TreasuryMovement;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('payments:reconcile-treasury {--fix : Corregir inconsistencias encontradas}')]
#[Description('Valida que PaymentSchedule y TreasuryMovement estén sincronizados')]
class ReconcileTreasuryCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Iniciando reconciliación de tesorería...');

        $fix = $this->option('fix');

        $this->newLine();
        $this->line('=== Validación 1: Pagos sin movimiento de tesorería ===');
        $this->validatePaymentsWithoutMovement($fix);

        $this->newLine();
        $this->line('=== Validación 2: Movimientos sin pago programado ===');
        $this->validateMovementsWithoutPayment($fix);

        $this->newLine();
        $this->line('=== Validación 3: Montos no coinciden ===');
        $this->validateAmountMismatch($fix);

        $this->newLine();
        $this->info('Reconciliación completada.');
    }

    private function validatePaymentsWithoutMovement(bool $fix): void
    {
        $orphanedPayments = PaymentSchedule::where('paid', true)
            ->whereNull('treasury_movement_id')
            ->get();

        if ($orphanedPayments->isEmpty()) {
            $this->info('✓ No hay pagos sin movimiento de tesorería.');

            return;
        }

        $this->error("✗ Encontrados {$orphanedPayments->count()} pagos sin movimiento");

        foreach ($orphanedPayments as $payment) {
            $this->line("  - PaymentSchedule #{$payment->id} (Factura: {$payment->supplierInvoice->invoice_number})");
        }

        if ($fix) {
            $this->warn('Nota: Estos pagos probablemente fueron marcados manualmente como pagados.');
            $this->info('Se recomienda crear movimientos de tesorería para estos pagos manualmente en el UI.');
        }
    }

    private function validateMovementsWithoutPayment(bool $fix): void
    {
        $orphanedMovements = TreasuryMovement::where('source_type', PaymentSchedule::class)
            ->whereNotNull('source_id')
            ->whereDoesntHave('sourceModel')
            ->get();

        if ($orphanedMovements->isEmpty()) {
            $this->info('✓ No hay movimientos huérfanos.');

            return;
        }

        $this->error("✗ Encontrados {$orphanedMovements->count()} movimientos sin pago programado");

        foreach ($orphanedMovements as $movement) {
            $this->line("  - TreasuryMovement #{$movement->id} (B/. {$movement->amount})");
        }

        if ($fix) {
            $this->warn('Limpiando referencias de source_id en movimientos huérfanos...');
            TreasuryMovement::where('source_type', PaymentSchedule::class)
                ->whereNotNull('source_id')
                ->whereDoesntHave('sourceModel')
                ->update(['source_id' => null]);
            $this->info('✓ Referencias limpidas.');
        }
    }

    private function validateAmountMismatch(bool $fix): void
    {
        $mismatches = [];

        $payments = PaymentSchedule::where('paid', true)
            ->whereNotNull('treasury_movement_id')
            ->with('treasuryMovement')
            ->get();

        foreach ($payments as $payment) {
            if (abs($payment->amount - $payment->treasuryMovement->amount) > 0.01) {
                $mismatches[] = [
                    'payment_id' => $payment->id,
                    'payment_amount' => $payment->amount,
                    'movement_amount' => $payment->treasuryMovement->amount,
                    'invoice' => $payment->supplierInvoice->invoice_number,
                ];
            }
        }

        if (empty($mismatches)) {
            $this->info('✓ No hay desajustes de montos.');

            return;
        }

        $this->error("✗ Encontrados {count($mismatches)} pagos con montos desajustados");

        foreach ($mismatches as $mismatch) {
            $this->line(sprintf(
                '  - Factura %s: PaymentSchedule B/. %s vs TreasuryMovement B/. %s',
                $mismatch['invoice'],
                number_format($mismatch['payment_amount'], 2),
                number_format($mismatch['movement_amount'], 2)
            ));
        }

        if ($fix) {
            $this->warn('No se pueden corregir montos automáticamente. Requiere revisión manual.');
        }
    }
}
