<?php

namespace App\Console\Commands;

use App\Services\ClientInvoiceService;
use Illuminate\Console\Command;

class ProcessOverdueInvoices extends Command
{
    protected $signature = 'subscriptions:process-overdue';

    protected $description = 'Mark overdue invoices and suspend subscriptions if needed';

    public function handle(ClientInvoiceService $invoiceService): int
    {
        $this->info('Processing overdue invoices...');

        $count = $invoiceService->processOverdue();

        $this->info("Processed {$count} overdue invoices.");

        return Command::SUCCESS;
    }
}
