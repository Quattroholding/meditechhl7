<?php

namespace App\Console\Commands;

use App\Models\Medicine;
use App\Models\MedicineActiveComponent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SyncFdaMedicines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'medicines:sync-fda
                            {--limit=1000 : Maximum number of medicines to sync}
                            {--force : Force sync even if last sync was recent}
                            {--dry-run : Show what would be done without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize medicines with FDA database - creates, activates, and deactivates medicines monthly';

    protected $apiUrl = 'https://api.fda.gov/drug/ndc.json';
    protected $stats = [
        'created' => 0,
        'updated' => 0,
        'activated' => 0,
        'deactivated' => 0,
        'skipped' => 0,
        'errors' => 0
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🏥 Iniciando sincronización mensual de medicamentos con FDA...');

        // Verificar si es necesario hacer sync
        if (!$this->shouldSync()) {
            return 0;
        }

        try {
            $startTime = microtime(true);

            // Marcar todas las medicinas FDA como inactivas temporalmente
            $this->markFdaMedicinesAsInactive();

            // Obtener medicamentos de FDA
            $fdaMedicines = $this->fetchFdaMedicines();

            if (empty($fdaMedicines)) {
                $this->error('❌ No se pudieron obtener medicamentos de la FDA');
                return 1;
            }

            $this->info("📊 Procesando " . count($fdaMedicines) . " medicamentos de la FDA...");

            $bar = $this->output->createProgressBar(count($fdaMedicines));
            $bar->start();

            foreach ($fdaMedicines as $fdaMedicine) {

                $this->processFdaMedicine($fdaMedicine);
                $bar->advance();
            }

            $bar->finish();
            $this->newLine();

            // Desactivar medicamentos que ya no están en FDA
            $this->deactivateObsoleteMedicines();

            $endTime = microtime(true);
            $duration = round($endTime - $startTime, 2);

            $this->displaySyncResults($duration);

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Error durante la sincronización: ' . $e->getMessage());
            return 1;
        }
    }

    protected function shouldSync(): bool
    {
        if ($this->option('force')) {
            $this->info('🔄 Sincronización forzada...');
            return true;
        }

        // Verificar última sincronización
        $lastSync = Medicine::where('source', 'FDA')
            ->orderBy('updated_at', 'desc')
            ->first();

        if (!$lastSync) {
            $this->info('🆕 Primera sincronización...');
            return true;
        }

        $daysSinceLastSync = Carbon::parse($lastSync->updated_at)->diffInDays(now());

        if ($daysSinceLastSync < 30) {
            $this->info("⏭️ Última sincronización fue hace {$daysSinceLastSync} días. Saltando sincronización (usar --force para forzar).");
            return false;
        }

        $this->info("📅 Última sincronización: {$daysSinceLastSync} días atrás. Procediendo...");
        return true;
    }

    protected function markFdaMedicinesAsInactive(): void
    {
        if ($this->option('dry-run')) {
            $count = Medicine::where('source', 'FDA')->where('active', true)->count();
            $this->info("🔍 [DRY-RUN] Marcaría {$count} medicamentos FDA como inactivos");
            return;
        }

        $updated = Medicine::where('source', 'FDA')
            ->where('active', true)
            ->update(['active' => false]);

        $this->info("📝 Marcados {$updated} medicamentos FDA como inactivos temporalmente");
    }

    protected function fetchFdaMedicines(): array
    {
        $allMedicines = [];
        $skip = 0;
        $limit = 100;
        $maxTotal = $this->option('limit');

        do {
            $this->info("🌐 Obteniendo lote (skip: {$skip})...");

            $queryParams = [
                'limit' => $limit,
                'skip' => $skip,
                'search' => '_exists_:openfda'
            ];

            $apiKey = config('services.fda.api_key') ?? '8ZdSuS1DI0Wzk4ayILGrV6X6YtTybeUHkflSkCbf';
            if (!empty($apiKey)) {
                $queryParams['api_key'] = $apiKey;
            }

            $response = Http::timeout(30)->get($this->apiUrl, $queryParams);

            if (!$response->successful()) {
                $this->warn("⚠️ Error en petición (skip: {$skip}): " . $response->status());
                break;
            }

            $data = $response->json();


            if (!isset($data['results']) || empty($data['results'])) {
                $this->info("✅ No hay más medicamentos (skip: {$skip})");
                break;
            }

            $allMedicines = array_merge($allMedicines, $data['results']);
            $skip += $limit;

            // Rate limiting
            usleep(300000); // 0.3 segundos



        } while (count($allMedicines) < $maxTotal && count($data['results']) === $limit);

        return $allMedicines;
    }


    protected function isValidFdaMedicine(array $fdaMedicine): bool
    {
        // Para NDC API, la estructura es diferente - datos están directamente en el root
        $productNdc = $fdaMedicine['product_ndc'] ?? null;
        $genericName = $fdaMedicine['generic_name'] ?? '';
        $brandName = $fdaMedicine['brand_name'] ?? '';
        $activeIngredients = $fdaMedicine['active_ingredients'] ?? [];

        // Debe tener NDC y al menos un nombre
        $hasNdc = !empty($productNdc);
        $hasName = !empty($genericName) || !empty($brandName);
        $hasIngredients = !empty($activeIngredients);

        return $hasNdc && $hasName && $hasIngredients;
    }

    protected function extractMedicineData(array $fdaMedicine): array
    {
        // Para NDC API, los datos están directamente en el root
        $productNdc = $fdaMedicine['product_ndc'] ?? '';
        $genericName = $fdaMedicine['generic_name'] ?? '';
        $brandName = $fdaMedicine['brand_name'] ?? '';
        $dosageForm = $fdaMedicine['dosage_form'] ?? 'TABLET';
        $activeIngredients = $fdaMedicine['active_ingredients'] ?? [];

        // Determinar nombre principal
        $homeName = !empty($brandName) ? $brandName : $genericName;
        if (empty($genericName)) {
            $genericName = $homeName;
        }

        // Extraer dosificación del primer ingrediente activo
        $mgsData = $this->extractDosageFromIngredients($activeIngredients);

        return [
            'source' => 'FDA',
            'ndc_code' => $this->sanitizeString($productNdc, 15),
            'home_name' => $this->sanitizeString($homeName, 100),
            'generic_name' => $this->sanitizeString($genericName, 100),
            'mgs' => $mgsData['value'],
            'mgs_type' => $mgsData['unit'],
            'type' => $this->mapDosageForm($dosageForm),
            'product_type' => $fdaMedicine['product_type'] ?? 'HUMAN_PRESCRIPTION_DRUG',
            'usage_indications' => null, // NDC API no tiene indicaciones
            'porpuse' => null,
            'indication_and_usage' => null,
            'narcotic' => $this->isNarcotic($fdaMedicine),
            'active' => true,
            'client_id' => null,
            'user_id' => null,
        ];
    }

    protected function extractDosage(string $strength): array
    {
        if (empty($strength)) {
            return ['value' => '0', 'unit' => 'MG'];
        }

        // Intentar extraer número y unidad
        if (preg_match('/(\d+(?:\.\d+)?)\s*([A-Za-z]+)/', $strength, $matches)) {
            $value = $matches[1];
            $unit = strtoupper($matches[2]);

            // Mapear unidades comunes
            $unitMap = [
                'MILLIGRAM' => 'MG',
                'MILLIGRAMS' => 'MG',
                'MILLILITER' => 'ML',
                'MILLILITERS' => 'ML',
                'MICROGRAM' => 'MCG',
                'MICROGRAMS' => 'MCG',
                'GRAM' => 'GM',
                'GRAMS' => 'GM',
                'UNIT' => 'UNITS',
                'IU' => 'UNITS',
            ];

            $mappedUnit = $unitMap[$unit] ?? $unit;

            return ['value' => $value, 'unit' => $mappedUnit];
        }

        return ['value' => '0', 'unit' => 'MG'];
    }

    protected function mapDosageForm(string $form): string
    {
        $formMap = [
            'TABLET' => 'TABLETS',
            'CAPSULE' => 'CAPSULES',
            'INJECTION' => 'VIAL',
            'SOLUTION' => 'SOLUTION',
            'CREAM' => 'TUBE OF CREAM',
            'OINTMENT' => 'TUBE OF CREAM',
            'DROPS' => 'BOTTLE OF DROPS',
            'SYRUP' => 'BOTTLE OF SYRUP',
        ];

        return $formMap[strtoupper($form)] ?? 'TABLETS';
    }

    protected function extractUsageIndications(array $fdaMedicine): ?string
    {
        return $this->sanitizeString(
            $fdaMedicine['indications_and_usage'][0] ??
            $fdaMedicine['usage'][0] ??
            null,
            4000
        );
    }

    protected function extractPurpose(array $fdaMedicine): ?string
    {
        return $this->sanitizeString(
            $fdaMedicine['purpose'][0] ?? null,
            4000
        );
    }

    protected function extractIndicationAndUsage(array $fdaMedicine): ?string
    {
        return $this->sanitizeString(
            $fdaMedicine['indications_and_usage'][0] ?? null,
            4000
        );
    }

    protected function isNarcotic(array $fdaMedicine): bool
    {
        $narcotic_terms = ['narcotic', 'controlled', 'schedule', 'opioid', 'morphine', 'codeine'];
        $content = json_encode($fdaMedicine);

        foreach ($narcotic_terms as $term) {
            if (stripos($content, $term) !== false) {
                return true;
            }
        }

        return false;
    }


    protected function createNewMedicine(array $data): void
    {
        $medicine = Medicine::create($data);
        $this->stats['created']++;

        // Extraer ingredientes activos del arreglo original
        if (!empty($data['active_ingredients'])) {
            $this->createActiveComponents($medicine, $data['active_ingredients']);
        }
    }

    protected function deactivateObsoleteMedicines(): void
    {
        if ($this->option('dry-run')) {
            $count = Medicine::where('source', 'FDA')->where('active', false)->count();
            $this->info("🔍 [DRY-RUN] Desactivaría {$count} medicamentos obsoletos");
            return;
        }

        $deactivated = Medicine::where('source', 'FDA')
            ->where('active', false)
            ->update(['active' => false]);

        $this->stats['deactivated'] = $deactivated;
    }

    protected function sanitizeString(?string $value, int $maxLength): ?string
    {
        if (empty($value)) {
            return null;
        }

        $value = trim($value);
        return strlen($value) > $maxLength ? substr($value, 0, $maxLength - 3) . '...' : $value;
    }

    protected function extractDosageFromIngredients(array $activeIngredients): array
    {
        if (empty($activeIngredients)) {
            return ['value' => '0', 'unit' => 'MG'];
        }

        // Tomar el primer ingrediente activo para la dosificación principal
        $firstIngredient = $activeIngredients[0];
        $strength = $firstIngredient['strength'] ?? '';

        return $this->extractDosage($strength);
    }

    protected function createActiveComponents(Medicine $medicine, array $activeIngredients): void
    {
        // Limpiar componentes existentes si es una actualización
        $medicine->components()->delete();

        foreach ($activeIngredients as $ingredient) {
            $name = $ingredient['name'] ?? '';
            $strength = $ingredient['strength'] ?? '';

            if (!empty($name)) {
                MedicineActiveComponent::create([
                    'medicine_id' => $medicine->id,
                    'name' => $this->sanitizeString($name, 100),
                    'mgs' => $this->sanitizeString($strength, 25) ?: '0'
                ]);
            }
        }
    }

    protected function updateExistingMedicine(Medicine $medicine, array $data): void
    {
        $wasInactive = !$medicine->active;

        // Actualizar datos del medicamento
        $medicineData = $data;
        unset($medicineData['active_ingredients']); // Remover antes de actualizar
        $medicine->update($medicineData);

        // Actualizar componentes activos si existen
        if (!empty($data['active_ingredients'])) {
            $this->createActiveComponents($medicine, $data['active_ingredients']);
        }

        if ($wasInactive) {
            $this->stats['activated']++;
        } else {
            $this->stats['updated']++;
        }
    }

    protected function processFdaMedicine(array $fdaMedicine): void
    {
        try {
            if (!$this->isValidFdaMedicine($fdaMedicine)) {
                $this->stats['skipped']++;
                return;
            }

            $medicineData = $this->extractMedicineData($fdaMedicine);

            if (empty($medicineData)) {
                $this->stats['skipped']++;
                return;
            }

            // Agregar ingredientes activos al arreglo de datos
            $medicineData['active_ingredients'] = $fdaMedicine['active_ingredients'] ?? [];

            if ($this->option('dry-run')) {
                $activeCount = count($medicineData['active_ingredients']);
                $this->info("🔍 [DRY-RUN] Procesaría: {$medicineData['generic_name']} ({$activeCount} ingredientes)");
                return;
            }

            // Verificar duplicados basado en generic_name, mgs y type
            $existingMedicine = Medicine::where('generic_name', $medicineData['generic_name'])
                ->where('mgs', $medicineData['mgs'])
                ->where('type', $medicineData['type'])
                ->where('source', 'FDA')
                ->first();

            if ($existingMedicine) {
                $this->updateExistingMedicine($existingMedicine, $medicineData);
            } else {
                $this->createNewMedicine($medicineData);
            }

        } catch (\Exception $e) {
            $this->stats['errors']++;
            $this->warn("⚠️ Error procesando medicamento: " . $e->getMessage());
        }
    }

    protected function displaySyncResults(float $duration): void
    {
        $this->newLine();
        $this->info('📊 Resultados de la sincronización:');
        $this->line("⏱️  Duración: {$duration}s");
        $this->line("✅ Creados: {$this->stats['created']}");
        $this->line("📝 Actualizados: {$this->stats['updated']}");
        $this->line("🔄 Reactivados: {$this->stats['activated']}");
        $this->line("❌ Desactivados: {$this->stats['deactivated']}");
        $this->line("⏭️  Omitidos: {$this->stats['skipped']}");
        $this->line("💥 Errores: {$this->stats['errors']}");

        $total = $this->stats['created'] + $this->stats['updated'] + $this->stats['activated'];
        $this->info("🎉 Sincronización completada! Total procesados: {$total}");
    }
}
