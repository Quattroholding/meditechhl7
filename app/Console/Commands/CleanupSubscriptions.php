<?php

namespace App\Console\Commands;

use App\Models\ReferralCode;
use App\Services\SubscriptionService;
use Illuminate\Console\Command;

class CleanupSubscriptions extends Command
{
    protected $signature = 'subscriptions:cleanup';

    protected $description = 'Clean up expired subscriptions and referral codes';

    public function handle(SubscriptionService $subscriptionService): int
    {
        $this->info('Cleaning up subscriptions...');

        $expiredCount = $subscriptionService->processExpired();
        $this->info("Expired {$expiredCount} subscriptions.");

        $this->info('Cleaning up expired referral codes...');

        $expiredCodes = ReferralCode::where('is_active', true)
            ->whereNotNull('valid_until')
            ->whereDate('valid_until', '<', today())
            ->get();

        foreach ($expiredCodes as $code) {
            $code->is_active = false;
            $code->save();
        }

        $this->info("Deactivated {$expiredCodes->count()} expired referral codes.");

        return Command::SUCCESS;
    }
}
