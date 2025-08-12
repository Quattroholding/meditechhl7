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
            $allMedications = $this->fetchAllMedications();
            
            if (empty($allMedications)) {
                $this->command->error('No se encontraron medicamentos en la API de la FDA');
                $this->fallbackSeeder();
                return;
            }

            $this->command->info('Procesando ' . count($allMedications) . ' medicamentos...');

            $bar = $this->command->getOutput()->createProgressBar(count($allMedications));
            $bar->start();

            foreach ($allMedications as $drug) {
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

    protected function fetchAllMedications(): array
    {
        $allMedications = [];
        $skip = 0;
        $limit = 100; // Usar lotes más pequeños para mejor estabilidad
        $maxTotal = 2000; // Reducir límite para pruebas iniciales

        // Primero probar sin API key (la FDA API permite uso sin key con límites)
        do {
            $this->command->info("Obteniendo lote de medicamentos (skip: $skip)...");
            
            // Simplificar la búsqueda - obtener cualquier medicamento con información openFDA
            $queryParams = [
                'limit' => $limit,
                'skip' => $skip,
                'search' => '_exists_:openfda'
            ];

            // Solo agregar API key si está disponible
            $apiKey = config('services.fda.api_key') ?? '8ZdSuS1DI0Wzk4ayILGrV6X6YtTybeUHkflSkCbf';
            if (!empty($apiKey)) {
                $queryParams['api_key'] = $apiKey;
            }

            $url = $this->apiUrl . '?' . http_build_query($queryParams);
            
            $this->command->info("URL: " . $url);
            
            $response = Http::timeout(30)->get($url);
            
            if (!$response->successful()) {
                $this->command->warn("Error en la petición (skip: $skip): " . $response->status());
                
                // Si es el primer intento y falla, intentar una búsqueda más simple
                if ($skip === 0) {
                    $this->command->info("Intentando con búsqueda más simple...");
                    $simpleParams = ['limit' => $limit];
                    if (!empty($apiKey)) {
                        $simpleParams['api_key'] = $apiKey;
                    }
                    
                    $simpleUrl = $this->apiUrl . '?' . http_build_query($simpleParams);
                    $response = Http::timeout(30)->get($simpleUrl);
                    
                    if (!$response->successful()) {
                        $this->command->error("Fallo también con búsqueda simple. Status: " . $response->status());
                        $this->command->error("Response: " . $response->body());
                        break;
                    }
                }
            }

            $data = $response->json();

            if (!isset($data['results']) || empty($data['results'])) {
                $this->command->info("No hay más medicamentos disponibles (skip: $skip)");
                break;
            }

            $allMedications = array_merge($allMedications, $data['results']);
            $skip += $limit;

            $this->command->info("Obtenidos " . count($data['results']) . " medicamentos. Total acumulado: " . count($allMedications));

            // Rate limiting - esperar un poco entre peticiones
            usleep(500000); // 0.5 segundos para ser más conservadores

        } while (count($allMedications) < $maxTotal && isset($data['results']) && count($data['results']) === $limit);

        return $allMedications;
    }

    protected function processDrug(array $drug)
    {
        try {
            // Validar que el medicamento tenga información mínima requerida
            if (!$this->isValidDrug($drug)) {
                return;
            }

            $openFda = $drug['openfda'] ?? [];
            $productName = $this->extractProductName($drug, $openFda);
            
            // Evitar medicamentos con nombres genéricos
            if (empty($productName) || $productName === 'Medicamento desconocido') {
                return;
            }

            $code = $this->extractCode($openFda);
            
            // Verificar si ya existe para evitar duplicados
            if (Medication::where('code', $code)->exists()) {
                return;
            }

            $medicationData = [
                'fhir_id' => 'medication-' . Str::uuid(),
                'code' => $code,
                'name' => $this->sanitizeString($productName, 255),
                'form' => $this->getDrugForm($drug),
                'dose' => $this->sanitizeString($this->getDose($drug), 255),
                'strength' => $this->sanitizeString($this->getStrength($drug), 255),
                'ingredient' => $this->getIngredients($openFda),
                'product_type' => $this->sanitizeString($openFda['product_type'][0] ?? 'HUMAN_PRESCRIPTION_DRUG', 100),
            ];

            Medication::create($medicationData);

        } catch (\Exception $e) {
            $this->command->warn('Error procesando medicamento: ' . $e->getMessage());
        }
    }

    protected function isValidDrug(array $drug): bool
    {
        $openFda = $drug['openfda'] ?? [];
        
        // Debe tener al menos nombre de marca, nombre genérico o descripción
        $hasName = !empty($openFda['brand_name']) || 
                   !empty($openFda['generic_name']) || 
                   !empty($drug['description']);
        
        // Debe ser un medicamento para humanos
        $isHumanDrug = isset($openFda['product_type']) && 
                       (str_contains($openFda['product_type'][0] ?? '', 'HUMAN'));
        
        return $hasName && $isHumanDrug;
    }

    protected function extractProductName(array $drug, array $openFda): string
    {
        // Preferir nombre de marca, luego genérico, luego descripción
        if (!empty($openFda['brand_name'])) {
            return trim($openFda['brand_name'][0]);
        }
        
        if (!empty($openFda['generic_name'])) {
            return trim($openFda['generic_name'][0]);
        }
        
        if (!empty($drug['description'])) {
            // Limpiar descripciones muy largas
            $description = trim($drug['description']);
            if (is_array($description)) {
                $description = $description[0];
            }
            return strlen($description) > 100 ? substr($description, 0, 100) . '...' : $description;
        }
        
        return 'Medicamento desconocido';
    }

    protected function extractCode(array $openFda): string
    {
        // Preferir RxCUI, luego NDC, luego generar uno único
        if (!empty($openFda['rxcui'])) {
            return 'RXCUI-' . $openFda['rxcui'][0];
        }
        
        if (!empty($openFda['ndc'])) {
            return 'NDC-' . $openFda['ndc'][0];
        }
        
        return 'FDA-' . Str::random(8) . '-' . time();
    }

    protected function sanitizeString(?string $value, int $maxLength): string
    {
        if (empty($value)) {
            return '';
        }
        
        $value = trim($value);
        return strlen($value) > $maxLength ? substr($value, 0, $maxLength - 3) . '...' : $value;
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

    protected function fallbackSeeder(): void
    {
        $this->command->info('Cargando medicamentos de respaldo...');
        
        // Datos de respaldo con medicamentos comunes
        $commonDrugs = [
            // Analgésicos y antiinflamatorios
            ['name' => 'Acetaminophen', 'form' => 'Tableta', 'dose' => '500 mg', 'type' => 'HUMAN_OTC_DRUG'],
            ['name' => 'Ibuprofen', 'form' => 'Tableta', 'dose' => '400 mg', 'type' => 'HUMAN_OTC_DRUG'],
            ['name' => 'Aspirin', 'form' => 'Tableta', 'dose' => '325 mg', 'type' => 'HUMAN_OTC_DRUG'],
            ['name' => 'Naproxen', 'form' => 'Tableta', 'dose' => '220 mg', 'type' => 'HUMAN_OTC_DRUG'],
            
            // Antibióticos comunes
            ['name' => 'Amoxicillin', 'form' => 'Cápsula', 'dose' => '500 mg', 'type' => 'HUMAN_PRESCRIPTION_DRUG'],
            ['name' => 'Azithromycin', 'form' => 'Tableta', 'dose' => '250 mg', 'type' => 'HUMAN_PRESCRIPTION_DRUG'],
            ['name' => 'Cephalexin', 'form' => 'Cápsula', 'dose' => '500 mg', 'type' => 'HUMAN_PRESCRIPTION_DRUG'],
            ['name' => 'Doxycycline', 'form' => 'Tableta', 'dose' => '100 mg', 'type' => 'HUMAN_PRESCRIPTION_DRUG'],
            
            // Cardiovasculares
            ['name' => 'Lisinopril', 'form' => 'Tableta', 'dose' => '10 mg', 'type' => 'HUMAN_PRESCRIPTION_DRUG'],
            ['name' => 'Metoprolol', 'form' => 'Tableta', 'dose' => '50 mg', 'type' => 'HUMAN_PRESCRIPTION_DRUG'],
            ['name' => 'Amlodipine', 'form' => 'Tableta', 'dose' => '5 mg', 'type' => 'HUMAN_PRESCRIPTION_DRUG'],
            ['name' => 'Atorvastatin', 'form' => 'Tableta', 'dose' => '20 mg', 'type' => 'HUMAN_PRESCRIPTION_DRUG'],
            
            // Diabetes
            ['name' => 'Metformin', 'form' => 'Tableta', 'dose' => '500 mg', 'type' => 'HUMAN_PRESCRIPTION_DRUG'],
            ['name' => 'Glipizide', 'form' => 'Tableta', 'dose' => '5 mg', 'type' => 'HUMAN_PRESCRIPTION_DRUG'],
            
            // Respiratorios
            ['name' => 'Albuterol', 'form' => 'Inhalador', 'dose' => '90 mcg', 'type' => 'HUMAN_PRESCRIPTION_DRUG'],
            ['name' => 'Fluticasone', 'form' => 'Spray nasal', 'dose' => '50 mcg', 'type' => 'HUMAN_PRESCRIPTION_DRUG'],
            
            // Gastrointestinales
            ['name' => 'Omeprazole', 'form' => 'Cápsula', 'dose' => '20 mg', 'type' => 'HUMAN_OTC_DRUG'],
            ['name' => 'Ranitidine', 'form' => 'Tableta', 'dose' => '150 mg', 'type' => 'HUMAN_OTC_DRUG'],
            
            // Antihistamínicos
            ['name' => 'Cetirizine', 'form' => 'Tableta', 'dose' => '10 mg', 'type' => 'HUMAN_OTC_DRUG'],
            ['name' => 'Loratadine', 'form' => 'Tableta', 'dose' => '10 mg', 'type' => 'HUMAN_OTC_DRUG'],
            
            // Vitaminas y suplementos
            ['name' => 'Vitamin D3', 'form' => 'Tableta', 'dose' => '1000 IU', 'type' => 'HUMAN_OTC_DRUG'],
            ['name' => 'Multivitamin', 'form' => 'Tableta', 'dose' => '1 daily', 'type' => 'HUMAN_OTC_DRUG'],
            
            // Tópicos
            ['name' => 'Hydrocortisone', 'form' => 'Crema', 'dose' => '1%', 'type' => 'HUMAN_OTC_DRUG'],
            ['name' => 'Bacitracin', 'form' => 'Pomada', 'dose' => '500 units/g', 'type' => 'HUMAN_OTC_DRUG'],
        ];

        $bar = $this->command->getOutput()->createProgressBar(count($commonDrugs));
        $bar->start();

        foreach ($commonDrugs as $drug) {
            try {
                Medication::firstOrCreate(
                    ['name' => $drug['name']],
                    [
                        'fhir_id' => 'medication-' . Str::uuid(),
                        'code' => 'FALLBACK-' . Str::upper(Str::slug($drug['name'], '')),
                        'name' => $drug['name'],
                        'form' => $drug['form'],
                        'dose' => $drug['dose'],
                        'strength' => $drug['dose'],
                        'ingredient' => [['item' => $drug['name'], 'strength' => $drug['dose']]],
                        'product_type' => $drug['type']
                    ]
                );
            } catch (\Exception $e) {
                $this->command->warn("Error creando medicamento {$drug['name']}: " . $e->getMessage());
            }
            
            $bar->advance();
        }

        $bar->finish();
        $this->command->info("\n" . count($commonDrugs) . " medicamentos de respaldo cargados!");
    }
}
