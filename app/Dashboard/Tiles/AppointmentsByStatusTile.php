<?php

namespace App\Dashboard\Tiles;

use App\Models\Appointment;
use Carbon\Carbon;
use Spatie\Dashboard\Models\Tile;

class AppointmentsByStatusTile extends Tile
{
    /**
     * Refresh interval in seconds (2 minutes)
     */
    public int $refreshIntervalInSeconds = 120;

    /**
     * Get the data for the tile
     */
    public function getData(): array
    {
        $today = Carbon::today();

        return [
            'pending' => Appointment::where('status', 'pending')
                ->whereDate('start', $today)
                ->count(),
            'booked' => Appointment::where('status', 'booked')
                ->whereDate('start', $today)
                ->count(),
            'confirmed' => Appointment::where('status', 'confirmed')
                ->whereDate('start', $today)
                ->count(),
            'arrived' => Appointment::where('status', 'arrived')
                ->whereDate('start', $today)
                ->count(),
            'fulfilled' => Appointment::where('status', 'fulfilled')
                ->whereDate('start', $today)
                ->count(),
            'cancelled' => Appointment::where('status', 'cancelled')
                ->whereDate('start', $today)
                ->count(),
            'total' => Appointment::whereDate('start', $today)->count(),
        ];
    }
}
