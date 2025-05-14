<?php

namespace Database\Seeders;

use App\Models\Medication;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class MedicationSeeder extends Seeder
{
    protected $client;
    protected $apiUrl = 'https://api.fda.gov/drug/label.json';

    public function __construct()
    {

    }

    public function run()
    {
        $this->command->info('Obteniendo datos de medicamentos de la FDA...');

        try {

            $path = $this->apiUrl.'?api_key=8ZdSuS1DI0Wzk4ayILGrV6X6YtTybeUHkflSkCbf&limit=1000';
            $response = Http::get($path);

            $data = json_decode($response->getBody(), true);

            if (!isset($data['results'])) {
                $this->command->error('No se encontraron resultados en la API de la FDA');
                return;
            }

            $this->command->info('Procesando ' . count($data['results']) . ' medicamentos...');

            $bar = $this->command->getOutput()->createProgressBar(count($data['results']));
            $bar->start();

            foreach ($data['results'] as $drug) {
                $this->processDrug($drug);
                $bar->advance();
            }

            $bar->finish();
            $this->command->info("\nMedicamentos cargados exitosamente!");

        } catch (\Exception $e) {
            $this->command->error('Error al conectarse a la API de la FDA: ' . $e->getMessage());
            $this->command->info('Usando datos de respaldo...');
            $this->fallbackSeeder();
        }
    }

    protected function processDrug(array $drug)
    {
        try {
            $openFda = $drug['openfda'] ?? [];
            $productName = $openFda['brand_name'][0] ?? $openFda['generic_name'][0] ?? $drug['description'] ?? 'Medicamento desconocido';

            $medicationData = [
                'fhir_id' => 'medication-' . Str::uuid(),
                'code' => $openFda['rxcui'][0] ?? 'RXCUI-' . Str::random(6),
                'name' => $productName,
                'form' => $this->getDrugForm($drug),
                'dose' => $this->getDose($drug),
                'strength' => $this->getStrength($drug),
                'ingredient' => $this->getIngredients($openFda),
                'manufacturer' => $openFda['manufacturer_name'][0] ?? 'Fabricante desconocido',
                'is_brand' => isset($openFda['brand_name']),
                'is_over_the_counter' => str_contains($drug['openfda']['product_type'][0] ?? '', 'OTC'),
                'status' => 'active',
                'product_type' =>isset($openFda['product_type'][0]),
            ];


            Medication::firstOrCreate(
                ['code' => $medicationData['code']],
                $medicationData
            );

        } catch (\Exception $e) {
            $this->command->warn('Error procesando medicamento: ' . $e->getMessage());
        }
    }

    protected function getDrugForm(array $drug): string
    {
        $form = $drug['openfda']['dosage_form'][0] ?? $drug['dosage_form'][0] ?? 'Tableta';

        // Mapeo de formas comunes
        $formsMap = [
            'TABLET' => 'Tableta',
            'CAPSULE' => 'Cápsula',
            'INJECTION' => 'Inyección',
            'CREAM' => 'Crema',
            'SOLUTION' => 'Solución',
            'SUSPENSION' => 'Suspensión',
            'GEL' => 'Gel',
            'OINTMENT' => 'Pomada',
            'POWDER' => 'Polvo',
            'AEROSOL' => 'Aerosol',
        ];

        return $formsMap[strtoupper($form)] ?? $form;
    }

    protected function getDose(array $drug): string
    {
        $active = $drug['active_ingredient'] ?? [];
        $strength = $drug['active_ingredient_strength'] ?? [];

        if (count($active) > 0 && count($strength) > 0) {
            return $strength[0] . ' de ' . $active[0];
        }

        return $drug['dosage_and_administration'][0] ?? 'Dosis no especificada';
    }

    protected function getStrength(array $drug): string
    {
        $strength = $drug['active_ingredient_strength'][0] ?? null;

        if ($strength) {
            // Extraer solo la parte numérica si es posible
            if (preg_match('/([\d.]+)\s*(\w+)/', $strength, $matches)) {
                return $matches[1] . ' ' . $matches[2];
            }
            return $strength;
        }

        return 'Fuerza no especificada';
    }

    protected function getIngredients(array $openFda): array
    {
        $ingredients = [];

        $genericNames = $openFda['generic_name'] ?? [];
        $substances = $openFda['substance_name'] ?? [];
        $strengths = $openFda['strength'] ?? [];

        // Preferir sustancias específicas si están disponibles
        if (count($substances) > 0) {
            foreach ($substances as $index => $substance) {
                $strength = $strengths[$index] ?? 'No especificada';
                $ingredients[] = [
                    'item' => $substance,
                    'strength' => $strength
                ];
            }
        }
        // Si no, usar nombres genéricos
        elseif (count($genericNames) > 0) {
            foreach ($genericNames as $name) {
                $ingredients[] = [
                    'item' => $name,
                    'strength' => 'No especificada'
                ];
            }
        }

        return count($ingredients) > 0 ? $ingredients : [
            [
                'item' => 'Ingrediente no especificado',
                'strength' => 'No especificada'
            ]
        ];
    }

    protected function fallbackSeeder()
    {
        // Datos de respaldo en caso de que falle la API
        $commonDrugs = [
            [
                'name' => 'Acetaminophen',
                'form' => 'Tableta',
                'dose' => '500 mg',
                'is_brand' => false,
                'is_over_the_counter' => true
            ],
            [
                'name' => 'Ibuprofen',
                'form' => 'Tableta',
                'dose' => '400 mg',
                'is_brand' => false,
                'is_over_the_counter' => true
            ],
            // ... agregar más medicamentos comunes
        ];

        foreach ($commonDrugs as $drug) {
            Medication::firstOrCreate(
                ['name' => $drug['name']],
                array_merge($drug, [
                    'fhir_id' => 'medication-' . Str::uuid(),
                    'code' => 'RXCUI-' . Str::random(6),
                    'strength' => $drug['dose'],
                    'ingredient' => json_encode([['item' => $drug['name'], 'strength' => $drug['dose']]]),
                    'manufacturer' => 'Varios fabricantes',
                    'status' => 'active'
                ])
            );
        }
    }
}
