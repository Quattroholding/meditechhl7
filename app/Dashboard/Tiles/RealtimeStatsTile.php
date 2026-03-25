<?php

namespace App\Dashboard\Tiles;

use App\Models\Appointment;
use App\Models\Encounter;
use App\Models\Patient;
use Carbon\Carbon;
use Spatie\Dashboard\Models\Tile;

class RealtimeStatsTile extends Tile
{
    /**
     * Refresh interval in seconds (60 seconds = 1 minute)
     */
    public int $refreshIntervalInSeconds = 60;

    /**
     * Get the data for the tile
     */
    public function getData(): array
    {
        $today = Carbon::today();

        return [
            'appointments_today' => Appointment::whereDate('start', $today)->count(),
            'new_patients_today' => Patient::whereDate('created_at', $today)->count(),
            'active_encounters' => Encounter::whereNull('end')
                ->whereDate('start', '>=', $today)
                ->count(),
            'pending_appointments' => Appointment::where('status', 'pending')
                ->whereDate('start', '>=', $today)
                ->count(),
        ];
    }
}
