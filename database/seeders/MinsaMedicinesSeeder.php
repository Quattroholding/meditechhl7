<?php

namespace Database\Seeders;

use App\Models\Medication;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class MinsaMedicinesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filePath = public_path('minsa_meds_2026.xlsx');

        if (! file_exists($filePath)) {
            $this->command->error('Excel file not found: '.$filePath);

            return;
        }

        $this->command->info('Reading Excel file...');

        $reader = new Xlsx;
        $spreadsheet = $reader->load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow();

        $this->command->info("Found {$highestRow} rows. Processing medications...");

        $bar = $this->command->getOutput()->createProgressBar($highestRow - 1);
        $bar->start();

        $created = 0;
        $skipped = 0;

        DB::beginTransaction();

        try {
            foreach ($worksheet->getRowIterator(2) as $row) {
                $rowData = [];
                foreach ($row->getCellIterator('A', 'J') as $cell) {
                    $rowData[] = trim((string) $cell->getValue());
                }

                // Skip empty rows
                if (empty($rowData[1]) && empty($rowData[2])) {
                    $bar->advance();
                    $skipped++;

                    continue;
                }

                $ndcCode = $rowData[1];
                $homeName = strtoupper($rowData[2]);
                $genericName = strtoupper($rowData[3]);
                $otherActive = strtoupper($rowData[4]);
                $type = $rowData[5];
                $mgs = $rowData[6];
                $mgsType = $rowData[7];
                $labelerName = $rowData[8];
                $estado = $rowData[9] ?? '';

                // Check if medication already exists by code
                $existingMedication = Medication::where('code', $ndcCode)->first();
                if ($existingMedication) {
                    $bar->advance();
                    $skipped++;

                    continue;
                }

                // Map status
                $status = $this->mapStatus($estado);

                // Determine if it's a brand
                $isBrand = ! empty($homeName) && ! empty($genericName) &&
                    Str::lower($homeName) !== Str::lower($genericName);

                // Create medication (truncate fields to fit DB column sizes)
                $medication = Medication::create([
                    'fhir_id' => (string) Str::uuid(),
                    'status' => $status,
                    'code' => Str::limit($ndcCode, 100, ''),
                    'code_system' => 'MINSA',
                    'display' => Str::limit($homeName ?: $genericName, 255, ''),
                    'home_name' => Str::limit($homeName, 255, ''),
                    'generic_name' => Str::limit($genericName, 255, ''),
                    'form' => Str::limit($type, 100, ''),
                    'manufacturer' => Str::limit($labelerName, 255, ''),
                    'is_brand' => $isBrand,
                ]);

                // Parse and create ingredients
                $this->createIngredients($medication, $genericName, $otherActive, $mgs, $mgsType);

                $created++;
                $bar->advance();
            }

            DB::commit();

            $bar->finish();
            $this->command->newLine();
            $this->command->info("Created: {$created} medications");
            $this->command->info("Skipped: {$skipped} rows (empty or duplicates)");
        } catch (\Exception $e) {
            DB::rollBack();
            $bar->finish();
            $this->command->newLine();
            $this->command->error('Error: '.$e->getMessage());

            throw $e;
        }
    }

    /**
     * Map Excel status to medication status.
     */
    private function mapStatus(string $estado): string
    {
        return match (Str::lower(trim($estado))) {
            'trámite finalizado', 'tramite finalizado', 'activo' => 'active',
            'inactivo', 'suspendido', 'cancelado' => 'inactive',
            default => 'active',
        };
    }

    /**
     * Create medication ingredients from parsed data.
     */
    private function createIngredients(
        Medication $medication,
        string $genericName,
        string $otherActive,
        string $mgs,
        string $mgsType
    ): void {
        // Build list of ingredients
        $ingredients = [];

        // First ingredient is the generic name
        if (! empty($genericName)) {
            $ingredients[] = trim($genericName);
        }

        // Parse other active ingredients
        if (! empty($otherActive)) {
            // Some values have concentrations mixed in, clean them
            $cleaned = preg_replace('/\d+\.?\d*\s*\/\s*\d+\.?\d*/u', '', $otherActive);
            $cleaned = preg_replace('/[\d.,]+\s*(mg|g|ml|mcg|%)/iu', '', $cleaned);

            // Split by common separators
            $others = preg_split('/[,;\/]+/u', $cleaned);
            foreach ($others as $other) {
                $other = trim($other);
                if (! empty($other) && strlen($other) > 2) {
                    $ingredients[] = $other;
                }
            }
        }

        // Parse concentrations (separated by /)
        $strengths = $this->parseStrengths($mgs);

        // Parse units (separated by /)
        $units = $this->parseUnits($mgsType);

        // Create ingredient records
        foreach ($ingredients as $index => $ingredientName) {
            $strengthValue = $strengths[$index] ?? ($strengths[0] ?? null);
            $strengthUnit = $units[$index] ?? ($units[0] ?? null);

            DB::table('medication_ingredients')->insert([
                'medication_id' => $medication->id,
                'substance_code' => null,
                'substance_display' => Str::limit($ingredientName, 255, ''),
                'strength_value' => $strengthValue,
                'strength_unit' => Str::limit($strengthUnit, 50, ''),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Parse strength values from the mgs column.
     */
    private function parseStrengths(string $mgs): array
    {
        if (empty($mgs)) {
            return [];
        }

        $strengths = [];
        $parts = preg_split('/\s*\/\s*/', $mgs);

        foreach ($parts as $part) {
            $part = trim($part);
            // Extract numeric value
            if (preg_match('/^([\d.,]+)/', $part, $matches)) {
                // Handle comma as decimal separator
                $value = str_replace(',', '.', $matches[1]);
                $strengths[] = (float) $value;
            }
        }

        return $strengths;
    }

    /**
     * Parse units from the mgs_type column.
     */
    private function parseUnits(string $mgsType): array
    {
        if (empty($mgsType)) {
            return [];
        }

        $units = [];
        $parts = preg_split('/\s*\/\s*/', $mgsType);

        foreach ($parts as $part) {
            $part = trim($part);
            if (! empty($part)) {
                $units[] = $part;
            }
        }

        return $units;
    }
}
