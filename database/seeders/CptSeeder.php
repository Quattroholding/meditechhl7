<?php

namespace Database\Seeders;

use App\Models\CptCode;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filename = public_path('cpts2.csv');

        if (! file_exists($filename)) {
            $this->command->error('CSV file not found: '.$filename);

            return;
        }

        $this->command->info('Reading CSV file...');

        // Count total lines for progress bar
        $totalLines = $this->countLines($filename) - 1; // Exclude header

        $this->command->info("Found {$totalLines} rows. Processing CPT codes...");

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
                    $cpt_area_id = null;
                    if (! empty($line[4])) {
                        $cpt_area_id = str_replace('"', '', $line[4]);
                    }
                    $code = str_replace('"', '', $line[0]);

                    if (! CptCode::whereCode($code)->first()) {
                        CptCode::create([
                            'code' => $code,
                            'description' => $line[1],
                            'description_es' => $line[2],
                            'type' => $line[3],
                            'cpt_area_id' => $cpt_area_id,
                            'duplicity' => str_replace('"', '', $line[5]),
                            'is_body' => str_replace('"', '', $line[6]),
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
            $this->command->info("Created: {$created} CPT codes");
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
