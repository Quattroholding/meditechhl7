<?php

namespace Database\Seeders;

use App\Models\Icd10Code;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiagnosticSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filename = public_path('icd10.csv');

        if (! file_exists($filename)) {
            $this->command->error('CSV file not found: '.$filename);

            return;
        }

        $this->command->info('Reading CSV file...');

        // Count total lines for progress bar
        $totalLines = $this->countLines($filename) - 1; // Exclude header

        $this->command->info("Found {$totalLines} rows. Processing diagnostics...");

        $handle = fopen($filename, 'r');

        if (! $handle) {
            $this->command->error('Error opening file.');

            return;
        }

        $bar = $this->command->getOutput()->createProgressBar($totalLines);
        $bar->start();

        $created = 0;
        $skipped = 0;

        DB::beginTransaction();

        try {
            $i = 0;
            while (($line = fgetcsv($handle, 4000, '*')) !== false) {
                if ($i > 0) {
                    $code = str_replace('"', '', $line[0]);

                    if (! Icd10Code::whereCode($code)->first()) {
                        Icd10Code::create([
                            'code' => $code,
                            'icd10_code' => $code,
                            'description' => $line[2],
                            'description_es' => $line[3],
                        ]);

                        $created++;
                    } else {
                        $skipped++;
                    }

                    $bar->advance();
                }

                $i++;
            }

            fclose($handle);

            DB::commit();

            $bar->finish();
            $this->command->newLine();
            $this->command->info("Created: {$created} diagnostics");
            $this->command->info("Skipped: {$skipped} rows (duplicates)");
        } catch (\Exception $e) {
            DB::rollBack();
            fclose($handle);
            $bar->finish();
            $this->command->newLine();
            $this->command->error('Error: '.$e->getMessage());

            throw $e;
        }
    }

    /**
     * Count total lines in the CSV file.
     */
    private function countLines(string $filename): int
    {
        $lineCount = 0;
        $handle = fopen($filename, 'r');

        if ($handle) {
            while (fgets($handle) !== false) {
                $lineCount++;
            }
            fclose($handle);
        }

        return $lineCount;
    }
}
