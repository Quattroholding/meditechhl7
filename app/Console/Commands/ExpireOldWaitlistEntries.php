<?php

namespace App\Console\Commands;

use App\Services\WaitlistService;
use Illuminate\Console\Command;

class ExpireOldWaitlistEntries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'waitlist:expire-old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire old waitlist entries and freed slots';

    /**
     * Execute the console command.
     */
    public function handle(WaitlistService $waitlistService): int
    {
        $this->info('Expiring old waitlist entries...');

        $expiredEntries = $waitlistService->expireOldEntries();
        $expiredSlots = $waitlistService->expireOldFreedSlots();

        $this->info("✓ Expired $expiredEntries waitlist entries");
        $this->info("✓ Expired $expiredSlots freed slots");

        return Command::SUCCESS;
    }
}
