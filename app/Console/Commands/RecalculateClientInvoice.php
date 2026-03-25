<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Models\ClientInvoice;
use App\Services\ClientInvoiceService;
use Illuminate\Console\Command;

class RecalculateClientInvoice extends Command
{
    protected $signature = 'invoices:recalculate {client_id : The client ID}';

    protected $description = 'Recalculate pending invoices for a client to apply updated discounts';

    public function __construct(
        protected ClientInvoiceService $invoiceService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $clientId = $this->argument('client_id');

        $client = Client::find($clientId);

        if (! $client) {
            $this->error("Client #{$clientId} not found");

            return self::FAILURE;
        }

        $this->info("Recalculating invoices for client #{$clientId} ({$client->name})...");

        $pendingInvoices = ClientInvoice::where('client_id', $clientId)
            ->where('status', 'pending')
            ->get();

        if ($pendingInvoices->isEmpty()) {
            $this->warn('No pending invoices found for this client');

            return self::SUCCESS;
        }

        $this->info("Found {$pendingInvoices->count()} pending invoice(s)");

        foreach ($pendingInvoices as $invoice) {
            $oldTotal = $invoice->total;

            // Recalcular items y aplicar descuentos
            $this->invoiceService->calculateItems($invoice, $invoice->subscription);
            $this->invoiceService->applyAvailableDiscounts($invoice);

            $invoice->refresh();

            $this->info("  Invoice #{$invoice->id}: \${$oldTotal} → \${$invoice->total} (saved: \$".($oldTotal - $invoice->total).')');
        }

        $this->newLine();
        $this->info('✓ Invoices recalculated successfully!');

        return self::SUCCESS;
    }
}
