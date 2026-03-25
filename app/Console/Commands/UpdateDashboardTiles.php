<?php

namespace App\Console\Commands;

use App\Dashboard\Tiles\AppointmentsByStatusTile;
use App\Dashboard\Tiles\RealtimeStatsTile;
use App\Dashboard\Tiles\RevenueChartTile;
use Illuminate\Console\Command;

class UpdateDashboardTiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dashboard:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all dashboard tiles with fresh data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating dashboard tiles...');

        $tiles = [
            RealtimeStatsTile::class => 'Realtime Stats',
            AppointmentsByStatusTile::class => 'Appointments by Status',
            RevenueChartTile::class => 'Revenue Chart',
        ];

        foreach ($tiles as $tileClass => $tileName) {
            try {
                $tile = app($tileClass);
                $tile->refresh();
                $this->info("✓ Updated: {$tileName}");
            } catch (\Exception $e) {
                $this->error("✗ Failed to update {$tileName}: {$e->getMessage()}");
            }
        }

        $this->newLine();
        $this->info('All tiles updated successfully!');

        return Command::SUCCESS;
    }
}
