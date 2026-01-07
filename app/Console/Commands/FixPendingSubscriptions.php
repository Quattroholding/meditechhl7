<?php

namespace App\Console\Commands;

use App\Enums\SubscriptionStatus;
use App\Models\ClientSubscription;
use App\Services\ClientInvoiceService;
use Illuminate\Console\Command;

class FixPendingSubscriptions extends Command
{
    protected $signature = 'subscriptions:fix-pending {--dry-run : Display what would be done without making changes}';

    protected $description = 'Fix pending activation subscriptions without invoices by generating missing invoices';

    public function handle(ClientInvoiceService $invoiceService): int
    {
        $this->info('Checking for pending activation subscriptions without invoices...');

        $subscriptions = ClientSubscription::where('status', SubscriptionStatus::PENDING_ACTIVATION)
            ->whereDoesntHave('invoices')
            ->with(['client', 'package'])
            ->get();

        if ($subscriptions->isEmpty()) {
            $this->info('No inconsistent subscriptions found.');

            return Command::SUCCESS;
        }

        $this->warn("Found {$subscriptions->count()} subscription(s) in pending_activation without invoices:");
        $this->newLine();

        $table = [];
        foreach ($subscriptions as $subscription) {
            $table[] = [
                'ID' => $subscription->id,
                'Client' => $subscription->client->name,
                'Package' => $subscription->package->name,
                'Created' => $subscription->created_at,
            ];
        }

        $this->table(['ID', 'Client', 'Package', 'Created'], $table);
        $this->newLine();

        if ($this->option('dry-run')) {
            $this->info('DRY RUN - No changes made. Remove --dry-run to generate invoices.');

            return Command::SUCCESS;
        }

        if (! $this->confirm('Do you want to generate invoices for these subscriptions?')) {
            $this->info('Operation cancelled.');

            return Command::SUCCESS;
        }

        $generated = 0;
        $failed = 0;

        foreach ($subscriptions as $subscription) {
            try {
                $invoice = $invoiceService->generate($subscription);
                $this->info("✓ Generated invoice {$invoice->invoice_number} for subscription #{$subscription->id} ({$subscription->client->name})");
                $generated++;
            } catch (\Exception $e) {
                $this->error("✗ Failed to generate invoice for subscription #{$subscription->id}: {$e->getMessage()}");
                $failed++;
            }
        }

        $this->newLine();
        $this->info("Summary: {$generated} invoice(s) generated, {$failed} failed.");

        return Command::SUCCESS;
    }
}
