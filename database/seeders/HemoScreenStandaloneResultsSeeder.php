<?php

namespace Database\Seeders;

use App\Models\HemoScreenStandaloneResult;
use App\Models\Practitioner;
use Illuminate\Database\Seeder;

class HemoScreenStandaloneResultsSeeder extends Seeder
{
    /**
     * Seed HemoScreen standalone results for the past 6 months
     */
    public function run(): void
    {
        $this->command->info('Starting HemoScreen Standalone Results seeder...');

        // Get all standalone practitioners
        $practitioners = Practitioner::where('is_standalone', true)->with('user')->get();

        if ($practitioners->isEmpty()) {
            $this->command->warn('No standalone practitioners found. Run: php artisan hemoscreen:create-standalone-user first');
            return;
        }

        $this->command->info("Found {$practitioners->count()} standalone practitioner(s)");

        foreach ($practitioners as $practitioner) {
            $this->command->info("Generating results for: {$practitioner->name}");
            $this->generateResultsForPractitioner($practitioner);
        }

        $this->command->info('✓ HemoScreen Standalone Results seeding completed!');
    }

    /**
     * Generate results for a specific practitioner
     */
    private function generateResultsForPractitioner(Practitioner $practitioner): void
    {
        // Number of results to generate (3-5 per month for 6 months = 18-30 results)
        $totalResults = rand(18, 30);

        // Device serials to randomize
        $devices = ['HS12345', 'HS67890', 'HS11111'];

        // Patient identifiers (simulate different patients)
        $patients = [
            '8-123-4567',
            '8-234-5678',
            '8-345-6789',
            'PE-123456',
            null, // Some without identifier
        ];

        for ($i = 0; $i < $totalResults; $i++) {
            // Random date within last 6 months
            $daysAgo = rand(0, 180);
            $testDate = now()->subDays($daysAgo)->subHours(rand(0, 23))->subMinutes(rand(0, 59));

            // Generate CBC observations
            $observations = $this->generateCBCObservations();

            // Create result
            HemoScreenStandaloneResult::create([
                'user_id' => $practitioner->user_id,
                'practitioner_id' => $practitioner->id,
                'scb_id' => $practitioner->user->default_client_id,
                'device_serial' => $devices[array_rand($devices)],
                'patient_identifier' => $patients[array_rand($patients)],
                'message_control_id' => 'SEED-' . $testDate->format('YmdHis') . '-' . $i,
                'message_type' => 'OBS.R01',
                'panel_code' => rand(0, 100) > 20 ? '85025' : '85027', // 80% CBC, 20% CBC with diff
                'panel_name' => 'Complete Blood Count',
                'observations' => $observations,
                'raw_message' => [
                    'seeded' => true,
                    'generated_at' => now()->toIso8601String(),
                ],
                'test_performed_at' => $testDate,
            ]);
        }

        $this->command->info("  ✓ Generated {$totalResults} results");
    }

    /**
     * Generate realistic CBC observations
     * Mix of normal and abnormal values
     */
    private function generateCBCObservations(): array
    {
        // 70% chance of all normal values, 30% chance of some abnormal
        $isNormal = rand(1, 100) <= 70;

        $observations = [];

        // Reference ranges
        $params = [
            '718-7' => ['name' => 'Hemoglobin', 'min' => 13.5, 'max' => 17.5, 'unit' => 'g/dL'],
            '789-8' => ['name' => 'RBC', 'min' => 4.5, 'max' => 5.9, 'unit' => '10^12/L'],
            '6690-2' => ['name' => 'WBC', 'min' => 4.5, 'max' => 11.0, 'unit' => '10^9/L'],
            '20570-8' => ['name' => 'Hematocrit', 'min' => 38.8, 'max' => 50.0, 'unit' => '%'],
            '787-2' => ['name' => 'MCV', 'min' => 80.0, 'max' => 100.0, 'unit' => 'fL'],
            '785-6' => ['name' => 'MCH', 'min' => 27.0, 'max' => 32.0, 'unit' => 'pg'],
            '786-4' => ['name' => 'MCHC', 'min' => 32.0, 'max' => 36.0, 'unit' => 'g/dL'],
            '777-3' => ['name' => 'Platelets', 'min' => 150.0, 'max' => 400.0, 'unit' => '10^9/L'],
            '751-8' => ['name' => 'Neutrophils', 'min' => 40.0, 'max' => 70.0, 'unit' => '%'],
            '736-9' => ['name' => 'Lymphocytes', 'min' => 20.0, 'max' => 40.0, 'unit' => '%'],
            '5905-5' => ['name' => 'Monocytes', 'min' => 4.0, 'max' => 8.0, 'unit' => '%'],
            '713-8' => ['name' => 'Eosinophils', 'min' => 1.0, 'max' => 4.0, 'unit' => '%'],
            '706-2' => ['name' => 'Basophils', 'min' => 0.0, 'max' => 1.0, 'unit' => '%'],
        ];

        foreach ($params as $code => $param) {
            if ($isNormal) {
                // Generate normal value within range
                $value = $this->randomFloat($param['min'], $param['max']);
            } else {
                // 30% chance to be abnormal (either high or low)
                $makeAbnormal = rand(1, 100) <= 30;

                if ($makeAbnormal) {
                    // 50/50 chance of being too high or too low
                    if (rand(0, 1) === 0) {
                        // Too high
                        $value = $this->randomFloat($param['max'], $param['max'] * 1.3);
                    } else {
                        // Too low
                        $value = $this->randomFloat($param['min'] * 0.7, $param['min']);
                    }
                } else {
                    // Normal
                    $value = $this->randomFloat($param['min'], $param['max']);
                }
            }

            $observations[] = [
                'code' => $code,
                'name' => $param['name'],
                'value' => round($value, 2),
                'unit' => $param['unit'],
            ];
        }

        return $observations;
    }

    /**
     * Generate random float between min and max
     */
    private function randomFloat(float $min, float $max): float
    {
        return $min + mt_rand() / mt_getrandmax() * ($max - $min);
    }
}
