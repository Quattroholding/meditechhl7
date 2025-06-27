<?php

namespace Database\Seeders;

use App\Models\Practitioner;
use App\Models\ServiceCatalog;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceCatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🏥 Seeding Service Catalogs for practitioners...');

        // Get all practitioners with their users and clients
        $practitioners = Practitioner::with(['user.clients'])
            ->whereHas('user.clients')
            ->get();

        if ($practitioners->count() === 0) {
            $this->command->warn('⚠️  No practitioners with clients found. Please seed practitioners and associate them with clients first.');

            return;
        }

        $this->command->info("📋 Found {$practitioners->count()} practitioners");

        DB::beginTransaction();

        try {
            foreach ($practitioners as $practitioner) {
                $this->command->info("👨‍⚕️ Creating service catalog for: {$practitioner->first_name} {$practitioner->last_name}");

                $this->createBasicServicesForPractitioner($practitioner);
                $this->createSpecialtyServicesForPractitioner($practitioner);
            }

            // Create some shared/common services for all practitioners
            $this->createCommonServices();

            DB::commit();
            $this->command->info('✅ Service catalogs seeded successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Error seeding service catalogs: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Create basic services that every practitioner should have
     */
    private function createBasicServicesForPractitioner(Practitioner $practitioner): void
    {
        // Basic consultation services
        ServiceCatalog::factory()
            ->forPractitioner($practitioner)
            ->consultation()
            ->count(3)
            ->create();

        // Basic procedures
        ServiceCatalog::factory()
            ->forPractitioner($practitioner)
            ->procedure()
            ->count(2)
            ->create();

        // Basic diagnostic services
        ServiceCatalog::factory()
            ->forPractitioner($practitioner)
            ->diagnostic()
            ->count(2)
            ->create();

        // Some affordable services
        ServiceCatalog::factory()
            ->forPractitioner($practitioner)
            ->affordable()
            ->count(2)
            ->create();
    }

    /**
     * Create specialty-specific services based on practitioner's specialty
     */
    private function createSpecialtyServicesForPractitioner(Practitioner $practitioner): void
    {
        // Get practitioner's specialty from medical_speciality_id or other fields
        $specialty = $this->determinePractitionerSpecialty($practitioner);

        switch ($specialty) {
            case 'cardiologia':
            case 'cardiology':
                $this->createCardiologyServices($practitioner);
                break;

            case 'pediatria':
            case 'pediatrics':
                $this->createPediatricsServices($practitioner);
                break;

            case 'ginecologia':
            case 'gynecology':
                $this->createGynecologyServices($practitioner);
                break;

            case 'dermatologia':
            case 'dermatology':
                $this->createDermatologyServices($practitioner);
                break;

            case 'medicina_general':
            case 'general_medicine':
            default:
                $this->createGeneralMedicineServices($practitioner);
                break;
        }
    }

    /**
     * Determine practitioner's specialty
     */
    private function determinePractitionerSpecialty(Practitioner $practitioner): string
    {
        // You can customize this logic based on your database structure
        $specialtyField = $practitioner->specialty ?? $practitioner->medical_speciality_id ?? null;

        if (! $specialtyField) {
            return 'medicina_general';
        }

        // Map specialty IDs or names to our categories
        $specialtyMap = [
            '1' => 'medicina_general',
            '2' => 'cardiologia',
            '3' => 'pediatria',
            '4' => 'ginecologia',
            '5' => 'dermatologia',
            'medicina general' => 'medicina_general',
            'cardiología' => 'cardiologia',
            'pediatría' => 'pediatria',
            'ginecología' => 'ginecologia',
            'dermatología' => 'dermatologia',
        ];

        return $specialtyMap[strtolower($specialtyField)] ?? 'medicina_general';
    }

    /**
     * Create cardiology-specific services
     */
    private function createCardiologyServices(Practitioner $practitioner): void
    {
        $services = [
            [
                'name' => 'Consulta de Cardiología',
                'cpt_code' => '99213',
                'base_price' => 150.00,
                'duration_minutes' => 45,
                'service_type' => 'consultation',
                'specialty' => 'Cardiología',
            ],
            [
                'name' => 'Electrocardiograma',
                'cpt_code' => '93000',
                'base_price' => 75.00,
                'duration_minutes' => 15,
                'service_type' => 'procedure',
                'specialty' => 'Cardiología',
            ],
            [
                'name' => 'Ecocardiograma',
                'cpt_code' => '93306',
                'base_price' => 300.00,
                'duration_minutes' => 45,
                'service_type' => 'diagnostic',
                'specialty' => 'Cardiología',
            ],
            [
                'name' => 'Prueba de Esfuerzo',
                'cpt_code' => '93015',
                'base_price' => 400.00,
                'duration_minutes' => 60,
                'service_type' => 'diagnostic',
                'specialty' => 'Cardiología',
            ],
            [
                'name' => 'Holter 24 horas',
                'cpt_code' => '93224',
                'base_price' => 250.00,
                'duration_minutes' => 30,
                'service_type' => 'diagnostic',
                'specialty' => 'Cardiología',
            ],
        ];

        $this->createServicesFromArray($practitioner, $services);
    }

    /**
     * Create pediatrics-specific services
     */
    private function createPediatricsServices(Practitioner $practitioner): void
    {
        $services = [
            [
                'name' => 'Consulta Pediátrica',
                'cpt_code' => '99213',
                'base_price' => 120.00,
                'duration_minutes' => 30,
                'service_type' => 'consultation',
                'specialty' => 'Pediatría',
            ],
            [
                'name' => 'Control de Niño Sano',
                'cpt_code' => '99391',
                'base_price' => 100.00,
                'duration_minutes' => 30,
                'service_type' => 'consultation',
                'specialty' => 'Pediatría',
            ],
            [
                'name' => 'Vacunación',
                'cpt_code' => '90471',
                'base_price' => 50.00,
                'duration_minutes' => 15,
                'service_type' => 'procedure',
                'specialty' => 'Pediatría',
            ],
            [
                'name' => 'Evaluación del Desarrollo',
                'cpt_code' => '96110',
                'base_price' => 200.00,
                'duration_minutes' => 45,
                'service_type' => 'diagnostic',
                'specialty' => 'Pediatría',
            ],
        ];

        $this->createServicesFromArray($practitioner, $services);
    }

    /**
     * Create gynecology-specific services
     */
    private function createGynecologyServices(Practitioner $practitioner): void
    {
        $services = [
            [
                'name' => 'Consulta Ginecológica',
                'cpt_code' => '99213',
                'base_price' => 140.00,
                'duration_minutes' => 30,
                'service_type' => 'consultation',
                'specialty' => 'Ginecología',
            ],
            [
                'name' => 'Papanicolaou',
                'cpt_code' => '88150',
                'base_price' => 80.00,
                'duration_minutes' => 20,
                'service_type' => 'procedure',
                'specialty' => 'Ginecología',
            ],
            [
                'name' => 'Colposcopia',
                'cpt_code' => '57452',
                'base_price' => 250.00,
                'duration_minutes' => 30,
                'service_type' => 'procedure',
                'specialty' => 'Ginecología',
            ],
            [
                'name' => 'Ultrasonido Ginecológico',
                'cpt_code' => '76856',
                'base_price' => 200.00,
                'duration_minutes' => 30,
                'service_type' => 'diagnostic',
                'specialty' => 'Ginecología',
            ],
            [
                'name' => 'Control Prenatal',
                'cpt_code' => '59400',
                'base_price' => 120.00,
                'duration_minutes' => 30,
                'service_type' => 'consultation',
                'specialty' => 'Ginecología',
            ],
        ];

        $this->createServicesFromArray($practitioner, $services);
    }

    /**
     * Create dermatology-specific services
     */
    private function createDermatologyServices(Practitioner $practitioner): void
    {
        $services = [
            [
                'name' => 'Consulta Dermatológica',
                'cpt_code' => '99213',
                'base_price' => 130.00,
                'duration_minutes' => 30,
                'service_type' => 'consultation',
                'specialty' => 'Dermatología',
            ],
            [
                'name' => 'Biopsia de Piel',
                'cpt_code' => '11100',
                'base_price' => 200.00,
                'duration_minutes' => 30,
                'service_type' => 'procedure',
                'specialty' => 'Dermatología',
            ],
            [
                'name' => 'Crioterapia',
                'cpt_code' => '17110',
                'base_price' => 150.00,
                'duration_minutes' => 20,
                'service_type' => 'procedure',
                'specialty' => 'Dermatología',
            ],
            [
                'name' => 'Dermatoscopia',
                'cpt_code' => '96999',
                'base_price' => 100.00,
                'duration_minutes' => 15,
                'service_type' => 'diagnostic',
                'specialty' => 'Dermatología',
            ],
        ];

        $this->createServicesFromArray($practitioner, $services);
    }

    /**
     * Create general medicine services
     */
    private function createGeneralMedicineServices(Practitioner $practitioner): void
    {
        $services = [
            [
                'name' => 'Consulta de Medicina General',
                'cpt_code' => '99213',
                'base_price' => 100.00,
                'duration_minutes' => 30,
                'service_type' => 'consultation',
                'specialty' => 'Medicina General',
            ],
            [
                'name' => 'Chequeo Médico General',
                'cpt_code' => '99395',
                'base_price' => 150.00,
                'duration_minutes' => 45,
                'service_type' => 'consultation',
                'specialty' => 'Medicina General',
            ],
            [
                'name' => 'Sutura de Herida Menor',
                'cpt_code' => '12001',
                'base_price' => 120.00,
                'duration_minutes' => 30,
                'service_type' => 'procedure',
                'specialty' => 'Medicina General',
            ],
            [
                'name' => 'Certificado Médico',
                'cpt_code' => '99499',
                'base_price' => 25.00,
                'duration_minutes' => 10,
                'service_type' => 'consultation',
                'specialty' => 'Medicina General',
            ],
        ];

        $this->createServicesFromArray($practitioner, $services);
    }

    /**
     * Create services from array of service data
     */
    private function createServicesFromArray(Practitioner $practitioner, array $services): void
    {
        foreach ($services as $serviceData) {
            ServiceCatalog::factory()
                ->forPractitioner($practitioner)
                ->create(array_merge($serviceData, [
                    'description' => $this->generateDescription($serviceData['service_type']),
                    'currency' => 'USD',
                    'billing_unit' => 'each',
                    'is_active' => true,
                    'effective_date' => now(),
                    'covered_by_insurance' => true,
                    'patient_copay' => $serviceData['patient_copay'] ?? 10.00,
                ]));
        }
    }

    /**
     * Create common services available to all practitioners
     */
    private function createCommonServices(): void
    {
        $this->command->info('🏥 Creating common services...');

        // Get the first client to associate common services
        $client = \App\Models\Client::first();
        if (! $client) {
            $this->command->warn('⚠️  No clients found. Skipping common services.');

            return;
        }

        $commonServices = [
            [
                'name' => 'Teleconsulta',
                'cpt_code' => '99421',
                'base_price' => 75.00,
                'duration_minutes' => 20,
                'service_type' => 'consultation',
                'specialty' => 'Telemedicina',
            ],
            [
                'name' => 'Consulta de Urgencia',
                'cpt_code' => '99281',
                'base_price' => 200.00,
                'duration_minutes' => 30,
                'service_type' => 'consultation',
                'specialty' => 'Urgencias',
            ],
            [
                'name' => 'Certificado de Salud',
                'cpt_code' => '99499',
                'base_price' => 30.00,
                'duration_minutes' => 10,
                'service_type' => 'consultation',
                'specialty' => 'Medicina General',
            ],
        ];

        foreach ($commonServices as $serviceData) {
            ServiceCatalog::factory()->create(array_merge($serviceData, [
                'description' => $this->generateDescription($serviceData['service_type']),
                'currency' => 'USD',
                'billing_unit' => 'each',
                'is_active' => true,
                'effective_date' => now(),
                'covered_by_insurance' => true,
                'patient_copay' => 5.00,
                'client_id' => $client->id,
                'practitioner_id' => null, // Available to all practitioners
                'created_by' => 1, // System user
            ]));
        }
    }

    /**
     * Generate description based on service type
     */
    private function generateDescription(string $serviceType): string
    {
        $descriptions = [
            'consultation' => 'Evaluación médica especializada con historia clínica completa, examen físico y plan de tratamiento.',
            'procedure' => 'Procedimiento médico ambulatorio realizado con técnicas estándares y medidas de bioseguridad.',
            'diagnostic' => 'Estudio diagnóstico para evaluación de condiciones médicas específicas con interpretación especializada.',
            'therapeutic' => 'Tratamiento terapéutico personalizado según protocolo médico establecido.',
        ];

        return $descriptions[$serviceType] ?? $descriptions['consultation'];
    }
}
