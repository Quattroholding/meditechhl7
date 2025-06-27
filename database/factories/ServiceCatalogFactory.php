<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Practitioner;
use App\Models\ServiceCatalog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServiceCatalog>
 */
class ServiceCatalogFactory extends Factory
{
    protected $model = ServiceCatalog::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $serviceTypes = [
            'consultation' => 'Consulta',
            'procedure' => 'Procedimiento',
            'diagnostic' => 'Diagnóstico',
            'therapeutic' => 'Terapéutico',
            'surgical' => 'Quirúrgico',
            'laboratory' => 'Laboratorio',
            'imaging' => 'Imagenología',
        ];

        $complexities = ['low', 'medium', 'high'];
        $serviceType = $this->faker->randomElement(array_keys($serviceTypes));

        return [
            'name' => $this->generateServiceName($serviceType),
            'description' => $this->generateServiceDescription($serviceType),
            'service_type' => $serviceType,
            'base_price' => $this->faker->randomFloat(2, 25, 500),
            'currency' => 'USD',
            'billing_unit' => 'each',
            'duration_minutes' => $this->generateDuration($serviceType),
            'complexity' => $this->faker->randomElement($complexities),
            'specialty' => $this->generateSpecialty($serviceType),
            'body_system' => $this->faker->optional()->randomElement([
                'cardiovascular', 'respiratory', 'digestive', 'nervous', 'musculoskeletal',
                'endocrine', 'genitourinary', 'integumentary', 'immune', 'reproductive',
            ]),
            'requires_authorization' => $this->faker->boolean(20),
            'covered_by_insurance' => $this->faker->boolean(80),
            'patient_copay' => $this->faker->randomFloat(2, 0, 50),
            'insurance_allowable' => $this->faker->optional(60)->randomFloat(2, 20, 400),
            'is_active' => true,
            'effective_date' => now(),
            'expiration_date' => $this->faker->optional(10)->dateTimeBetween('+1 year', '+5 years'),
            'cpt_code' => $this->generateCptCode($serviceType),
            'revenue_code' => $this->faker->optional()->numerify('###'),
            'cost_center' => $this->faker->optional()->numerify('CC###'),
            'gl_account' => $this->faker->optional()->numerify('####'),
            'cost_estimate' => $this->faker->optional()->randomFloat(2, 10, 200),
            'markup_percentage' => $this->faker->randomFloat(2, 0, 100),
            'clinical_notes' => $this->faker->optional()->sentence(),
            'prerequisites' => $this->faker->optional()->randomElement([
                ['preparation' => 'Ayuno de 8 horas'],
                ['equipment' => 'Equipo especializado requerido'],
                ['consent' => 'Consentimiento informado necesario'],
            ]),
        ];
    }

    /**
     * Generate appropriate service names based on type
     */
    private function generateServiceName(string $serviceType): string
    {
        $names = [
            'consultation' => [
                'Consulta de Medicina General',
                'Consulta de Cardiología',
                'Consulta de Pediatría',
                'Consulta de Ginecología',
                'Consulta de Dermatología',
                'Consulta de Neurología',
                'Consulta de Psiquiatría',
                'Consulta de Endocrinología',
                'Consulta de Oftalmología',
                'Consulta de Otorrinolaringología',
            ],
            'procedure' => [
                'Biopsia de piel',
                'Extracción de cerumen',
                'Sutura de herida menor',
                'Infiltración articular',
                'Crioterapia',
                'Electrocardiograma',
                'Espirometría',
                'Curva de glucosa',
                'Holter 24 horas',
                'Prueba de esfuerzo',
            ],
            'diagnostic' => [
                'Radiografía de tórax',
                'Ecografía abdominal',
                'Ecocardiograma',
                'Mamografía',
                'Tomografía computarizada',
                'Resonancia magnética',
                'Endoscopia digestiva',
                'Colonoscopia',
                'Cistoscopia',
                'Broncoscopia',
            ],
            'therapeutic' => [
                'Fisioterapia respiratoria',
                'Terapia ocupacional',
                'Sesión de psicoterapia',
                'Infiltración epidural',
                'Bloqueo nervioso',
                'Oxigenoterapia',
                'Nebulización',
                'Dialíterapia',
                'Quimioterapia',
                'Radioterapia',
            ],
            'surgical' => [
                'Apendicectomía',
                'Colecistectomía',
                'Hernioplastia inguinal',
                'Cesárea',
                'Histerectomía',
                'Artroscopia de rodilla',
                'Cataratas',
                'Amigdalectomía',
                'Vasectomía',
                'Circuncisión',
            ],
            'laboratory' => [
                'Hemograma completo',
                'Química sanguínea',
                'Perfil lipídico',
                'Función hepática',
                'Función renal',
                'Marcadores tumorales',
                'Hormonas tiroideas',
                'Examen de orina',
                'Coprocultivo',
                'Hemocultivo',
            ],
            'imaging' => [
                'Rayos X de extremidades',
                'Ultrasonido pélvico',
                'TAC de abdomen',
                'RM de columna',
                'Angiografía',
                'Densitometría ósea',
                'Gammografía',
                'PET scan',
                'Fluoroscopia',
                'Doppler vascular',
            ],
        ];

        return $this->faker->randomElement($names[$serviceType] ?? $names['consultation']);
    }

    /**
     * Generate service descriptions
     */
    private function generateServiceDescription(string $serviceType): string
    {
        $descriptions = [
            'consultation' => 'Evaluación médica especializada con historia clínica completa, examen físico y plan de tratamiento.',
            'procedure' => 'Procedimiento médico ambulatorio realizado con técnicas estándares y medidas de bioseguridad.',
            'diagnostic' => 'Estudio diagnóstico para evaluación de condiciones médicas específicas con interpretación especializada.',
            'therapeutic' => 'Tratamiento terapéutico personalizado según protocolo médico establecido.',
            'surgical' => 'Procedimiento quirúrgico realizado en quirófano con anestesia y cuidados post-operatorios.',
            'laboratory' => 'Análisis de laboratorio clínico con procesamiento especializado y control de calidad.',
            'imaging' => 'Estudio de imagen diagnóstica con equipo de alta resolución e interpretación radiológica.',
        ];

        return $descriptions[$serviceType] ?? $descriptions['consultation'];
    }

    /**
     * Generate duration based on service type
     */
    private function generateDuration(string $serviceType): int
    {
        $durations = [
            'consultation' => [15, 20, 30, 45, 60],
            'procedure' => [30, 45, 60, 90, 120],
            'diagnostic' => [15, 30, 45, 60],
            'therapeutic' => [30, 45, 60, 90],
            'surgical' => [60, 90, 120, 180, 240],
            'laboratory' => [5, 10, 15],
            'imaging' => [20, 30, 45, 60],
        ];

        return $this->faker->randomElement($durations[$serviceType] ?? $durations['consultation']);
    }

    /**
     * Generate specialty based on service type
     */
    private function generateSpecialty(string $serviceType): string
    {
        $specialties = [
            'consultation' => ['Medicina General', 'Medicina Interna', 'Pediatría', 'Cardiología', 'Ginecología'],
            'procedure' => ['Medicina General', 'Cardiología', 'Dermatología', 'Cirugía General'],
            'diagnostic' => ['Radiología', 'Cardiología', 'Laboratorio Clínico', 'Patología'],
            'therapeutic' => ['Fisiatría', 'Psiquiatría', 'Oncología', 'Medicina del Dolor'],
            'surgical' => ['Cirugía General', 'Ginecología', 'Urología', 'Ortopedia', 'Oftalmología'],
            'laboratory' => ['Laboratorio Clínico', 'Microbiología', 'Hematología', 'Bioquímica'],
            'imaging' => ['Radiología', 'Medicina Nuclear', 'Cardiología', 'Neurología'],
        ];

        return $this->faker->randomElement($specialties[$serviceType] ?? $specialties['consultation']);
    }

    /**
     * Generate realistic CPT codes based on service type
     */
    private function generateCptCode(string $serviceType): string
    {
        $cptRanges = [
            'consultation' => ['99201', '99202', '99203', '99204', '99205', '99211', '99212', '99213', '99214', '99215'],
            'procedure' => ['11100', '11101', '12001', '12002', '20610', '93000', '94010', '82947', '93224', '93015'],
            'diagnostic' => ['71010', '76700', '93306', '77057', '74150', '72148', '43239', '45330', '52000', '31622'],
            'therapeutic' => ['94640', '97110', '90834', '62311', '64483', '94002', '94640', '90935', '96413', '77301'],
            'surgical' => ['44970', '47562', '49505', '59510', '58150', '29881', '66984', '42826', '55250', '54161'],
            'laboratory' => ['85025', '80053', '80061', '80076', '80069', '83036', '84443', '81001', '87086', '87040'],
            'imaging' => ['73610', '76856', '74177', '72148', '75625', '77080', '78306', '78811', '76000', '93922'],
        ];

        return $this->faker->randomElement($cptRanges[$serviceType] ?? $cptRanges['consultation']);
    }

    /**
     * Create service catalog for specific practitioner
     */
    public function forPractitioner(Practitioner $practitioner): static
    {
        // Get client_id from practitioner's user first client
        $clientId = $practitioner->user?->clients()?->first()?->id;

        return $this->state(fn (array $attributes) => [
            'practitioner_id' => $practitioner->id,
            'client_id' => $clientId,
            'created_by' => $practitioner->user_id,
        ]);
    }

    /**
     * Create consultation services
     */
    public function consultation(): static
    {
        return $this->state(fn (array $attributes) => [
            'service_type' => 'consultation',
            'name' => $this->generateServiceName('consultation'),
            'description' => $this->generateServiceDescription('consultation'),
            'duration_minutes' => $this->faker->randomElement([15, 20, 30, 45, 60]),
            'cpt_code' => $this->faker->randomElement(['99201', '99202', '99203', '99204', '99205']),
            'base_price' => $this->faker->randomFloat(2, 50, 200),
        ]);
    }

    /**
     * Create procedure services
     */
    public function procedure(): static
    {
        return $this->state(fn (array $attributes) => [
            'service_type' => 'procedure',
            'name' => $this->generateServiceName('procedure'),
            'description' => $this->generateServiceDescription('procedure'),
            'duration_minutes' => $this->faker->randomElement([30, 45, 60, 90]),
            'cpt_code' => $this->faker->randomElement(['11100', '12001', '20610', '93000']),
            'base_price' => $this->faker->randomFloat(2, 75, 350),
        ]);
    }

    /**
     * Create diagnostic services
     */
    public function diagnostic(): static
    {
        return $this->state(fn (array $attributes) => [
            'service_type' => 'diagnostic',
            'name' => $this->generateServiceName('diagnostic'),
            'description' => $this->generateServiceDescription('diagnostic'),
            'duration_minutes' => $this->faker->randomElement([15, 30, 45, 60]),
            'cpt_code' => $this->faker->randomElement(['71010', '76700', '93306', '77057']),
            'base_price' => $this->faker->randomFloat(2, 80, 400),
        ]);
    }

    /**
     * Create affordable services
     */
    public function affordable(): static
    {
        return $this->state(fn (array $attributes) => [
            'base_price' => $this->faker->randomFloat(2, 25, 100),
            'patient_copay' => $this->faker->randomFloat(2, 0, 25),
            'covered_by_insurance' => true,
        ]);
    }

    /**
     * Create premium services
     */
    public function premium(): static
    {
        return $this->state(fn (array $attributes) => [
            'base_price' => $this->faker->randomFloat(2, 200, 800),
            'complexity' => 'high',
            'requires_authorization' => true,
            'duration_minutes' => $this->faker->numberBetween(60, 240),
        ]);
    }
}
