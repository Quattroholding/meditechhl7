<?php

namespace App\Console\Commands;

use App\Ai\Agents\EncounterProcessingAgent;
use App\Models\Encounter;
use App\Models\Patient;
use Illuminate\Console\Command;

class TestEncounterAgent extends Command
{
    protected $signature = 'encounter:test-agent {test-number? : Test case number (1-10)}';

    protected $description = 'Run EncounterProcessingAgent test cases';

    private $testCases = [
        1 => [
            'name' => 'Routine Consultation - Hypertension and Diabetes',
            'patient' => ['name' => 'María García', 'gender' => 'female', 'age' => 52],
            'transcription' => 'Paciente femenino de 52 años, obesa, con hipertensión arterial en tratamiento desde hace 8 años y diabetes mellitus tipo 2 desde hace 5 años. Refiere fatiga leve desde hace 2 semanas. Presión arterial hoy 138/88. Frecuencia cardíaca 76 por minuto. Peso 89 kg. Se solicita panel metabólico completo y hemoglobina glicosilada. Se mantiene losartán 50mg cada 12 horas y se agrega metformina 1000mg dos veces al día. Paciente refiere alergía a penicilina.',
        ],
        2 => [
            'name' => 'Acute Case - Respiratory Infection',
            'patient' => ['name' => 'Carlos López', 'gender' => 'male', 'age' => 34],
            'transcription' => 'Paciente masculino de 34 años, sin antecedentes conocidos, presenta tos productiva con esputo verde desde hace 5 días, fiebre de 38.5 grados centígrados, dolor torácico leve a la palpación, disnea leve. Ausculta: estertores en base derecha. Presenta hipoxemia leve con saturación de oxígeno 94%. Se sospecha neumonía bacteriana. Se solicita radiografía de tórax y hemocultivo. Se prescribe amoxicilina-clavulánico 875/125 mg cada 8 horas por 10 días.',
        ],
        3 => [
            'name' => 'Pediatric - Otitis Media',
            'patient' => ['name' => 'Juan Pérez', 'gender' => 'male', 'age' => 4],
            'transcription' => 'Paciente masculino de 4 años traído por su madre debido a dolor de oído derecho intenso desde hace 3 días, fiebre intermitente máxima de 39 grados, irritabilidad. Otoscopia: membrana timpánica eritematosa, abombada. Se diagnostica otitis media aguda. Se prescribe amoxicilina 250mg suspensión cada 8 horas por 10 días. Madre refiere que el niño es alérgico a eritromicina.',
        ],
        4 => [
            'name' => 'Geriatric Complex - Polypharmacy',
            'patient' => ['name' => 'Roberto Fernández', 'gender' => 'male', 'age' => 78],
            'transcription' => 'Paciente masculino de 78 años con antecedentes de insuficiencia cardíaca congestiva, fibrilación auricular en anticoagulación, EPOC, osteoporosis. Viene por disnea progresiva desde hace una semana. Presión arterial 145/92, frecuencia cardíaca 92 y irregular, edema en miembros inferiores ++. Se solicita ecocardiograma, radiografía de tórax, BNP. Se aumenta furosemida a 40mg diarios, se verifica INR.',
        ],
        5 => [
            'name' => 'Obstetric - Prenatal Control',
            'patient' => ['name' => 'Ana Rodríguez', 'gender' => 'female', 'age' => 28],
            'transcription' => 'Paciente femenina de 28 años, primigesta, en semana 24 de gestación. Refiere molestias lumbares leves y ocasional edema de miembros inferiores. Presión arterial 120/80, peso 68kg. Se solicita ecografía obstétrica, prueba tolerancia glucosa. Se prescriben hierro 325mg, calcio 1000mg y vitamina prenatal.',
        ],
        6 => [
            'name' => 'Dermatologic - Allergic Dermatitis',
            'patient' => ['name' => 'Patricia Sánchez', 'gender' => 'female', 'age' => 31],
            'transcription' => 'Paciente femenina de 31 años con erupción cutánea pruriginosa generalizada que comenzó hace 4 días después de cambiar detergente. Lesiones eritematosas, edematosas en tronco y brazos. Sin fiebre. Se diagnostica dermatitis alérgica. Se prescribe hidrocortisona cream 1% c/12h, cetirizina 10mg cada noche.',
        ],
        7 => [
            'name' => 'Psychiatric - Major Depression',
            'patient' => ['name' => 'Diego Martínez', 'gender' => 'male', 'age' => 45],
            'transcription' => 'Paciente masculino de 45 años con tristeza profunda, anhedonia, insomnio severo, fatiga desde hace 3 meses tras pérdida de empleo. Pensamientos suicidas pasivos. Se diagnostica depresión mayor moderada. Se prescribe sertralina 50mg cada mañana, psicoterapia semanal.',
        ],
        8 => [
            'name' => 'Traumatologic - Fracture',
            'patient' => ['name' => 'Fernando Acosta', 'gender' => 'male', 'age' => 52],
            'transcription' => 'Paciente masculino de 52 años con caída desde altura 2m. Dolor intenso antebrazo derecho, tumefacción importante. Radiografía evidencia fractura radio-cúbito tercio medio. Se coloca férula yeso. Se prescribe paracetamol 500mg c/6h, ibuprofeno 400mg c/8h.',
        ],
        9 => [
            'name' => 'Endocrinologic - Thyrotoxicosis',
            'patient' => ['name' => 'Laura Gutiérrez', 'gender' => 'female', 'age' => 38],
            'transcription' => 'Paciente femenina de 38 años con enfermedad de Graves, exacerbación: taquicardia 120 lpm, temblor fino, ansiedad, pérdida peso 5kg/mes. Se ordena TSH, T3, T4. Se inicia propranolol 40mg c/8h, metimazol 30mg diarios.',
        ],
        10 => [
            'name' => 'Nephrology - Chronic Kidney Disease',
            'patient' => ['name' => 'Héctor Ramírez', 'gender' => 'male', 'age' => 64],
            'transcription' => 'Paciente masculino de 64 años con IRC estadío 3b, TFG 35, Cr 2.1, BUN 28, K+ 5.8, fósforo 4.2. PA 142/90. Fatiga y náuseas. Se ordena ECO renal, proteinuria 24h. Quelante fósforo, dieta baja Na/K.',
        ],
    ];

    public function handle()
    {
        $testNumber = $this->argument('test-number');

        if ($testNumber) {
            $this->runSingleTest((int) $testNumber);
        } else {
            $this->runAllTests();
        }
    }

    private function runSingleTest(int $testNumber)
    {
        if (! isset($this->testCases[$testNumber])) {
            $this->error("Test case $testNumber not found. Available: 1-10");

            return 1;
        }

        $this->info("\n🏥 Running Test $testNumber: {$this->testCases[$testNumber]['name']}\n");

        $testCase = $this->testCases[$testNumber];
        $patientData = $testCase['patient'];
        $transcription = $testCase['transcription'];

        // Create patient with proper name parsing to avoid UTF-8 encoding issues
        $nameParts = explode(' ', $patientData['name'], 2);
        $patient = Patient::factory()->create([
            'name' => $patientData['name'],
            'given_name' => $nameParts[0],
            'family_name' => $nameParts[1] ?? $nameParts[0],
            'gender' => $patientData['gender'],
            'birth_date' => now()->subYears($patientData['age']),
        ]);

        // Create encounter without appointment_id to avoid duplicates
        $encounter = Encounter::factory()->create([
            'patient_id' => $patient->id,
            'appointment_id' => null,
        ]);

        $this->info("Patient: {$patient->name} ({$patientData['age']} years, {$patientData['gender']})");
        $this->info("Encounter ID: {$encounter->id}\n");

        $this->info('📝 Transcription:');
        $this->line($transcription);
        $this->info("\n🔄 Processing...\n");

        try {
            $agent = new EncounterProcessingAgent($encounter->id, $transcription);
            $result = $agent->process();

            if ($result['success'] ?? false) {
                $this->info('✅ Processing successful!');

                $created = $result['items_created'] ?? [];
                foreach ($created as $section => $items) {
                    $this->line("  📊 $section:");
                    foreach ($items as $key => $count) {
                        if ($count > 0) {
                            $this->line("     - $key: $count");
                        }
                    }
                }

                if (! empty($result['warnings'])) {
                    $this->warn("\n⚠️  Warnings:");
                    foreach ($result['warnings'] as $warning) {
                        $this->line("   - $warning");
                    }
                }
            } else {
                $this->error('❌ Processing failed: '.($result['error'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
            $this->error("❌ Exception: {$e->getMessage()}");
        }

        return 0;
    }

    private function runAllTests()
    {
        $this->info("\n🏥 Running all 10 test cases...\n");

        $results = [];

        foreach ($this->testCases as $num => $testCase) {
            $this->info("Test $num: {$testCase['name']}");

            try {
                // Parse patient name to avoid UTF-8 encoding issues
                $nameParts = explode(' ', $testCase['patient']['name'], 2);
                $patient = Patient::factory()->create([
                    'name' => $testCase['patient']['name'],
                    'given_name' => $nameParts[0],
                    'family_name' => $nameParts[1] ?? $nameParts[0],
                    'gender' => $testCase['patient']['gender'],
                    'birth_date' => now()->subYears($testCase['patient']['age']),
                ]);

                // Create encounter without appointment_id to avoid duplicates
                $encounter = Encounter::factory()->create([
                    'patient_id' => $patient->id,
                    'appointment_id' => null,
                ]);

                $agent = new EncounterProcessingAgent($encounter->id, $testCase['transcription']);
                $result = $agent->process();

                if ($result['success'] ?? false) {
                    $this->line("  ✅ Passed\n");
                    $results[$num] = 'PASS';
                } else {
                    $this->line("  ❌ Failed\n");
                    $results[$num] = 'FAIL';
                }
            } catch (\Exception $e) {
                $this->line("  ❌ Exception: {$e->getMessage()}\n");
                $results[$num] = 'ERROR';
            }
        }

        $this->info("\n".str_repeat('=', 60));
        $this->info('Summary:');
        $this->info(str_repeat('=', 60));

        foreach ($results as $num => $status) {
            $icon = $status === 'PASS' ? '✅' : '❌';
            $this->line("$icon Test $num: $status");
        }

        $passed = count(array_filter($results, fn ($s) => $s === 'PASS'));
        $total = count($results);

        $this->info("\n📈 Result: $passed/$total tests passed");

        return $passed === $total ? 0 : 1;
    }
}
