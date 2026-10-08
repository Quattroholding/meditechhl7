<?php

namespace App\Console\Commands;

use App\Models\Finance\SupplierInvoice;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('supplier-invoices:calculate-aging-report {--client-id= : Cliente específico}')]
#[Description('Calcula y muestra reporte de antigüedad de facturas de proveedores')]
class CalculateAgingReportCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Generando reporte de antigüedad de facturas...');

        $query = SupplierInvoice::where('status', 'approved')
            ->where('balance', '>', 0)
            ->with('supplier');

        if ($clientId = $this->option('client-id')) {
            $query->where('client_id', $clientId);
            $this->info("Filtrando por cliente: {$clientId}");
        }

        $invoices = $query->orderBy('invoice_date', 'asc')->get();

        if ($invoices->isEmpty()) {
            $this->info('No hay facturas pendientes para generar reporte.');

            return;
        }

        $this->newLine();
        $this->info("Total de facturas: {$invoices->count()}");
        $this->newLine();

        // Agrupar por antigüedad
        $current = [];
        $days30 = [];
        $days60 = [];
        $days90 = [];
        $daysOver90 = [];

        foreach ($invoices as $invoice) {
            $daysOld = now()->diffInDays($invoice->invoice_date);

            $agingBucket = [
                'invoice_number' => $invoice->invoice_number,
                'supplier' => $invoice->supplier->legal_name,
                'date' => $invoice->invoice_date->format('d/m/Y'),
                'total' => $invoice->total_amount,
                'paid' => $invoice->paid_amount,
                'balance' => $invoice->balance,
                'days' => $daysOld,
            ];

            if ($daysOld <= 30) {
                $current[] = $agingBucket;
            } elseif ($daysOld <= 60) {
                $days30[] = $agingBucket;
            } elseif ($daysOld <= 90) {
                $days60[] = $agingBucket;
            } elseif ($daysOld <= 120) {
                $days90[] = $agingBucket;
            } else {
                $daysOver90[] = $agingBucket;
            }
        }

        // Mostrar reportes por rango
        $this->showAgingBucket('0-30 días (Actual)', $current);
        $this->showAgingBucket('31-60 días', $days30);
        $this->showAgingBucket('61-90 días', $days60);
        $this->showAgingBucket('91-120 días', $days90);
        $this->showAgingBucket('Más de 120 días', $daysOver90);

        // Resumen
        $this->newLine();
        $this->info('=== RESUMEN ===');
        $this->line('Actual (0-30):   '.$this->formatBucket($current));
        $this->line('31-60:           '.$this->formatBucket($days30));
        $this->line('61-90:           '.$this->formatBucket($days60));
        $this->line('91-120:          '.$this->formatBucket($days90));
        $this->line('Más de 120:      '.$this->formatBucket($daysOver90));

        $totalBalance = $invoices->sum('balance');
        $this->newLine();
        $this->info('Total pendiente: B/. '.number_format($totalBalance, 2));
    }

    private function showAgingBucket(string $label, array $invoices): void
    {
        if (empty($invoices)) {
            return;
        }

        $this->newLine();
        $this->line("--- {$label} ---");

        $total = 0;
        foreach ($invoices as $inv) {
            $total += $inv['balance'];
            $this->line(sprintf(
                '  %s | %s | %s | B/. %s',
                $inv['invoice_number'],
                substr($inv['supplier'], 0, 30),
                $inv['date'],
                number_format($inv['balance'], 2)
            ));
        }

        $this->line('Subtotal: B/. '.number_format($total, 2));
    }

    private function formatBucket(array $invoices): string
    {
        $count = count($invoices);
        $total = collect($invoices)->sum('balance');

        return "{$count} facturas | B/. ".number_format($total, 2);
    }
}
