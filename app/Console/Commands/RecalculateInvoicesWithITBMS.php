<?php

namespace App\Console\Commands;

use App\Models\ClientInvoice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RecalculateInvoicesWithITBMS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:recalculate-itbms
                            {--dry-run : Show what would be updated without making changes}
                            {--force : Skip confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate all existing invoices to add 7% ITBMS tax (Panama)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🧾 Recalculando facturas con ITBMS (7% Panamá)');
        $this->newLine();

        // Get all invoices without global scopes
        $invoices = ClientInvoice::withoutGlobalScopes()->get();

        if ($invoices->isEmpty()) {
            $this->warn('No se encontraron facturas para recalcular.');

            return self::SUCCESS;
        }

        $this->info("📊 Facturas encontradas: {$invoices->count()}");
        $this->newLine();

        // Show sample calculation
        $sampleInvoice = $invoices->first();
        $this->showSampleCalculation($sampleInvoice);

        // Confirm unless --force flag is used
        if (! $this->option('force') && ! $this->option('dry-run')) {
            if (! $this->confirm('¿Desea continuar con la actualización de todas las facturas?', true)) {
                $this->warn('Operación cancelada.');

                return self::FAILURE;
            }
        }

        $this->newLine();

        // Process invoices
        $updated = 0;
        $skipped = 0;
        $errors = 0;

        $progressBar = $this->output->createProgressBar($invoices->count());
        $progressBar->start();

        foreach ($invoices as $invoice) {
            try {
                $result = $this->recalculateInvoice($invoice);

                if ($result === 'updated') {
                    $updated++;
                } elseif ($result === 'skipped') {
                    $skipped++;
                }
            } catch (\Exception $e) {
                $errors++;
                $this->error("\nError en factura {$invoice->invoice_number}: {$e->getMessage()}");
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Show results
        $this->showResults($updated, $skipped, $errors, $invoices->count());

        return self::SUCCESS;
    }

    /**
     * Show sample calculation for the first invoice
     */
    protected function showSampleCalculation(ClientInvoice $invoice): void
    {
        $this->info('📝 Ejemplo de cálculo (Factura: '.$invoice->invoice_number.')');
        $this->line('─────────────────────────────────────────────────────');

        $oldTotal = $invoice->total;
        $taxableAmount = $invoice->subtotal - $invoice->discount_amount;
        $newTaxAmount = round($taxableAmount * 0.07, 2);
        $newTotal = $taxableAmount + $newTaxAmount;

        $this->table(
            ['Concepto', 'Valor Actual', 'Nuevo Valor'],
            [
                ['Subtotal', '$'.number_format($invoice->subtotal, 2), '$'.number_format($invoice->subtotal, 2)],
                ['Descuento', '$'.number_format($invoice->discount_amount, 2), '$'.number_format($invoice->discount_amount, 2)],
                ['Monto Imponible', '$'.number_format($taxableAmount, 2), '$'.number_format($taxableAmount, 2)],
                ['ITBMS (7%)', '$'.number_format($invoice->tax_amount ?? 0, 2), '$'.number_format($newTaxAmount, 2)],
                ['Total', '$'.number_format($oldTotal, 2), '$'.number_format($newTotal, 2)],
            ]
        );

        $this->newLine();
    }

    /**
     * Recalculate a single invoice
     */
    protected function recalculateInvoice(ClientInvoice $invoice): string
    {
        // Calculate taxable amount (subtotal - discount)
        $taxableAmount = $invoice->subtotal - $invoice->discount_amount;

        // Calculate new tax amount (7% ITBMS)
        $newTaxAmount = round($taxableAmount * 0.07, 2);

        // Calculate new total
        $newTotal = $taxableAmount + $newTaxAmount;

        // Check if update is needed
        $needsUpdate = abs($invoice->total - $newTotal) > 0.01
            || ($invoice->tax_amount ?? 0) == 0
            || ($invoice->tax_rate ?? 0) == 0;

        if (! $needsUpdate) {
            return 'skipped';
        }

        // Dry run mode - just show what would be done
        if ($this->option('dry-run')) {
            return 'updated';
        }

        // Update invoice
        DB::transaction(function () use ($invoice, $newTaxAmount, $newTotal) {
            $invoice->tax_rate = 0.07;
            $invoice->tax_amount = $newTaxAmount;
            $invoice->total = $newTotal;
            $invoice->save();
        });

        return 'updated';
    }

    /**
     * Show final results
     */
    protected function showResults(int $updated, int $skipped, int $errors, int $total): void
    {
        if ($this->option('dry-run')) {
            $this->info('🔍 Modo DRY RUN - No se realizaron cambios');
            $this->newLine();
        }

        $this->info('✅ Proceso completado');
        $this->line('─────────────────────────────────────────────────────');

        $this->table(
            ['Resultado', 'Cantidad'],
            [
                ['Total de facturas', $total],
                ['Actualizadas', $updated],
                ['Sin cambios necesarios', $skipped],
                ['Errores', $errors],
            ]
        );

        if ($this->option('dry-run')) {
            $this->newLine();
            $this->comment('💡 Para ejecutar los cambios realmente, ejecute el comando sin --dry-run:');
            $this->line('   php artisan invoices:recalculate-itbms');
        }
    }
}
