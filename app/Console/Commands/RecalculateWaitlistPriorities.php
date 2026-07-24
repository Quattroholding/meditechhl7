<?php

namespace App\Console\Commands;

use App\Services\WaitlistService;
use Illuminate\Console\Command;

class RecalculateWaitlistPriorities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'waitlist:recalculate-priorities';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate priority scores for all active waitlist entries';

    /**
     * Execute the console command.
     */
    public function handle(WaitlistService $waitlistService): int
    {
        $this->info('Recalculating waitlist priorities...');

        $updated = $waitlistService->recalculatePriorities();

        $this->info("✓ Updated $updated waitlist entries");

        return Command::SUCCESS;
    }
}
