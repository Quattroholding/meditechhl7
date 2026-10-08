<?php

namespace Database\Seeders;

use App\Models\Accounting\AccountingPeriod;
use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AccountingPeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = Client::all();

        foreach ($clients as $client) {
            // Create periods for current year and next year
            for ($year = now()->year; $year <= now()->year + 1; $year++) {
                for ($month = 1; $month <= 12; $month++) {
                    $date = Carbon::createFromDate($year, $month, 1);

                    // Skip future months for current year
                    if ($year === now()->year && $month > now()->month) {
                        continue;
                    }

                    AccountingPeriod::firstOrCreate(
                        [
                            'client_id' => $client->id,
                            'fiscal_year' => $year,
                            'period_number' => $month,
                        ],
                        [
                            'uuid' => Str::uuid(),
                            'name' => $date->locale('es')->translatedFormat('F Y'),
                            'start_date' => $date->startOfMonth()->toDateString(),
                            'end_date' => $date->endOfMonth()->toDateString(),
                            'status' => $date->endOfMonth()->isPast() ? 'closed' : 'open',
                            'created_by' => 1,
                        ]
                    );
                }
            }
        }

        $this->command->info('Accounting periods seeded successfully!');
    }
}
