<?php

namespace App\Console\Commands;

use App\Services\SubscriptionService;
use Illuminate\Console\Command;

class GenerateSubscriptionInvoices extends Command
{
    protected $signature = 'subscriptions:generate-invoices';

    protected $description = 'Generate invoices for subscriptions that are due for renewal';

    public function handle(SubscriptionService $subscriptionService): int
    {
        $this->info('Starting invoice generation process...');

        $count = $subscriptionService->processRenewals();

        $this->info("Generated {$count} invoices successfully.");

        return Command::SUCCESS;
    }
}
