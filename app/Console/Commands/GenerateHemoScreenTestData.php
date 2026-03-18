<?php

namespace App\Console\Commands;

use App\Models\HemoScreenStandaloneResult;
use App\Models\Practitioner;
use Illuminate\Console\Command;

class GenerateHemoScreenTestData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hemoscreen:generate-test-data
                            {--practitioner= : Specific practitioner ID to generate data for}
                            {--results=25 : Number of results to generate per practitioner}
                            {--days=180 : Number of days in the past to generate data for}
                            {--fresh : Delete existing test data before generating new data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate test data for HemoScreen standalone results';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🧪 HemoScreen Test Data Generator');
        $this->newLine();

        // Get options
        $practitionerId = $this->option('practitioner');
        $resultsCount = (int) $this->option('results');
        $days = (int) $this->option('days');
        $fresh = $this->option('fresh');

        // Get practitioners
        $practitioners = $this->getPractitioners($practitionerId);

        if ($practitioners->isEmpty()) {
            $this->warn('⚠ No standalone practitioners found.');
            $this->info('💡 Run: php artisan hemoscreen:create-standalone-user');

            return self::FAILURE;
        }

        $this->info("Found {$practitioners->count()} standalone practitioner(s)");
        $this->newLine();

        // Confirm if fresh
        if ($fresh) {
            if (! $this->confirm('⚠ This will delete ALL existing test data. Continue?', false)) {
                $this->info('Cancelled.');

                return self::SUCCESS;
            }

            $deleted = HemoScreenStandaloneResult::query()->delete();
            $this->info("🗑 Deleted {$deleted} existing results");
            $this->newLine();
        }

        // Generate data for each practitioner
        $totalGenerated = 0;
        $progressBar = $this->output->createProgressBar($practitioners->count());
        $progressBar->start();

        foreach ($practitioners as $practitioner) {
            $generated = $this->generateResultsForPractitioner($practitioner, $resultsCount, $days);
            $totalGenerated += $generated;
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Summary
        $this->info("✅ Generated {$totalGenerated} test results successfully!");
        $this->newLine();

        // Show summary per practitioner
        $this->table(
            ['Practitioner', 'Email', 'Results Count'],
            $practitioners->map(function ($practitioner) {
                return [
                    $practitioner->name,
                    $practitioner->user->email,
                    HemoScreenStandaloneResult::where('practitioner_id', $practitioner->id)->count(),
                ];
            })->toArray()
        );

        return self::SUCCESS;
    }

    /**
     * Get practitioners based on options
     */
    private function getPractitioners(?string $practitionerId): \Illuminate\Support\Collection
    {
        $query = Practitioner::where('is_standalone', true)->with('user');

        if ($practitionerId) {
            $query->where('id', $practitionerId);
        }

        return $query->get();
    }

    /**
     * Generate results for a specific practitioner
     */
    private function generateResultsForPractitioner(Practitioner $practitioner, int $count, int $days): int
    {
        // Device serials to randomize
        $devices = ['HS12345', 'HS67890', 'HS11111', 'HS22222', 'HS99999'];

        // Patient identifiers (simulate different patients)
        $patients = [
            '8-123-4567',
            '8-234-5678',
            '8-345-6789',
            '8-456-7890',
            'PE-123456',
            'PE-234567',
            null, // Some without identifier
        ];

        $generated = 0;

        for ($i = 0; $i < $count; $i++) {
            // Random date within specified days
            $daysAgo = rand(0, $days);
            $testDate = now()->subDays($daysAgo)->subHours(rand(0, 23))->subMinutes(rand(0, 59));

            // Generate CBC observations
            $observations = $this->generateCBCObservations();

            // Create result
            HemoScreenStandaloneResult::create([
                'user_id' => $practitioner->user_id,
                'practitioner_id' => $practitioner->id,
                'scb_id' => $practitioner->scb_id,
                'device_serial' => $devices[array_rand($devices)],
                'patient_identifier' => $patients[array_rand($patients)],
                'message_control_id' => 'TEST-'.$testDate->format('YmdHis').'-'.$i.'-'.$practitioner->id,
                'message_type' => 'OBS.R01',
                'panel_code' => rand(0, 100) > 20 ? '85025' : '85027', // 80% CBC, 20% CBC with diff
                'panel_name' => 'Complete Blood Count',
                'observations' => $observations,
                'raw_message' => [
                    'test_data' => true,
                    'generated_at' => now()->toIso8601String(),
                    'generated_by' => 'hemoscreen:generate-test-data',
                ],
                'test_performed_at' => $testDate,
            ]);

            $generated++;
        }

        return $generated;
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
            // Differential - Absolute counts (as sent by HemoScreen device)
            '751-8' => ['name' => 'Neutrophils Absolute', 'min' => 2.0, 'max' => 7.0, 'unit' => '10^9/L'],
            '731-0' => ['name' => 'Lymphocytes Absolute', 'min' => 1.0, 'max' => 4.0, 'unit' => '10^9/L'],
            '742-7' => ['name' => 'Monocytes Absolute', 'min' => 0.2, 'max' => 1.0, 'unit' => '10^9/L'],
            '711-2' => ['name' => 'Eosinophils Absolute', 'min' => 0.0, 'max' => 0.5, 'unit' => '10^9/L'],
            '704-7' => ['name' => 'Basophils Absolute', 'min' => 0.0, 'max' => 0.2, 'unit' => '10^9/L'],
            // Differential - Percentages (as sent by HemoScreen device)
            '770-8' => ['name' => 'Neutrophils Percent', 'min' => 40.0, 'max' => 70.0, 'unit' => '%'],
            '736-9' => ['name' => 'Lymphocytes Percent', 'min' => 20.0, 'max' => 40.0, 'unit' => '%'],
            '5905-5' => ['name' => 'Monocytes Percent', 'min' => 4.0, 'max' => 8.0, 'unit' => '%'],
            '713-8' => ['name' => 'Eosinophils Percent', 'min' => 1.0, 'max' => 4.0, 'unit' => '%'],
            '706-2' => ['name' => 'Basophils Percent', 'min' => 0.0, 'max' => 1.0, 'unit' => '%'],
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
