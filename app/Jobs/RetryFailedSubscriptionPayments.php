<?php

namespace App\Jobs;

use App\Models\ClientSubscription;
use App\Services\SubscriptionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RetryFailedSubscriptionPayments implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(SubscriptionService $subscriptionService): void
    {
        $subscriptions = ClientSubscription::query()
            ->whereNotNull('next_retry_at')
            ->where('next_retry_at', '<=', now())
            ->whereIn('status', ['past_due', 'suspended'])
            ->get();

        foreach ($subscriptions as $subscription) {

            DB::beginTransaction();

            try {

                $invoice = $subscription->latestInvoice;

                if (! $invoice || $invoice->isPaid()) {
                    DB::rollBack();

                    continue;
                }

                $charged = $subscriptionService->chargeInvoice($subscription, $invoice);

                if ($charged) {

                    $subscription->updateBillingPeriod();

                    Log::info('Retry payment success', [
                        'subscription_id' => $subscription->id,
                    ]);

                }

                DB::commit();

            } catch (\Exception $e) {

                DB::rollBack();

                Log::error('Retry payment failed', [
                    'subscription_id' => $subscription->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
