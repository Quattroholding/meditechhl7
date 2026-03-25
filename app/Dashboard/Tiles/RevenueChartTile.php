<?php

namespace App\Dashboard\Tiles;

use App\Models\Invoice;
use Carbon\Carbon;
use Spatie\Dashboard\Models\Tile;

class RevenueChartTile extends Tile
{
    /**
     * Refresh interval in seconds (5 minutes)
     */
    public int $refreshIntervalInSeconds = 300;

    /**
     * Get the data for the tile
     */
    public function getData(): array
    {
        $last7Days = collect();

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $revenue = Invoice::whereDate('created_at', $date)->sum('total_net');

            $last7Days->push([
                'date' => $date->format('d/m'),
                'revenue' => round($revenue, 2),
            ]);
        }

        return [
            'labels' => $last7Days->pluck('date')->toArray(),
            'data' => $last7Days->pluck('revenue')->toArray(),
            'total_week' => round($last7Days->sum('revenue'), 2),
        ];
    }
}
