<?php

namespace Database\Seeders;

use App\Models\Encounter;
use Illuminate\Database\Seeder;

class EncounterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $completeEncounter = Encounter::factory()->count(1)
            ->withCompleteEncounterAndServices()
            ->create();
    }
}
