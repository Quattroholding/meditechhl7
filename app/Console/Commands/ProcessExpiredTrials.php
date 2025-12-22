<?php

namespace App\Console\Commands;

use App\Enums\SubscriptionStatus;
use App\Models\ClientSubscription;
use App\Services\ClientInvoiceService;
use Illuminate\Console\Command;

class ProcessExpiredTrials extends Command
{
    protected $signature = 'subscriptions:process-trials';

    protected $description = 'Process trial subscriptions that have ended';

    public function handle(ClientInvoiceService $invoiceService): int
    {
        $this->info('Processing expired trials...');

        $expiredTrials = ClientSubscription::where('status', SubscriptionStatus::TRIAL->value)
            ->where('trial_ends_at', '<=', now())
            ->get();

        $count = 0;

        foreach ($expiredTrials as $subscription) {
            try {
                $this->info("Processing trial for subscription #{$subscription->id}");

                $invoice = $invoiceService->generate($subscription);

                $subscription->status = SubscriptionStatus::PENDING_ACTIVATION;
                $subscription->save();

                $this->info("Generated invoice #{$invoice->id} for subscription #{$subscription->id}");

                $count++;
            } catch (\Exception $e) {
                $this->error("Failed to process subscription #{$subscription->id}: {$e->getMessage()}");
            }
        }

        $this->info("Processed {$count} trial subscriptions.");

        return Command::SUCCESS;
    }
}
