<?php

namespace Database\Seeders;

use App\Models\ClinicalObservationType;
use Illuminate\Database\Seeder;

class ClinicalObservationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vitalSigns = [
            [
                'code' => '8310-5',
                'name' => 'Temperatura Corporal',
                'category' => 'vital_sign',
                'system' => 'general',
                'default_unit' => '°C',
                'min_normal_value' => 36.0,
                'max_normal_value' => 37.5,
                'description' => 'Temperatura corporal central',
                'procedure' => 'Termómetro oral, axilar o timpánico',
                'possible_abnormalities' => [
                    'Fiebre' => '>37.5°C',
                    'Hipotermia' => '<36.0°C',
                ],
            ],
            [
                'code' => '8867-4',
                'name' => 'Frecuencia Cardíaca',
                'category' => 'vital_sign',
                'system' => 'cardiovascular',
                'default_unit' => 'lpm',
                'min_normal_value' => 60,
                'max_normal_value' => 100,
                'description' => 'Pulsaciones por minuto',
                'procedure' => 'Palpación de pulso radial durante 60 segundos',
                'possible_abnormalities' => [
                    'Taquicardia' => '>100 lpm',
                    'Bradicardia' => '<60 lpm',
                ],
            ],
            [
                'code' => '8480-6',
                'name' => 'Presión Arterial Sistólica',
                'category' => 'vital_sign',
                'system' => 'cardiovascular',
                'default_unit' => 'mmHg',
                'min_normal_value' => 90,
                'max_normal_value' => 120,
                'description' => 'Presión arterial máxima durante el ciclo cardíaco',
                'procedure' => 'Esfigmomanómetro braquial',
                'possible_abnormalities' => [
                    'Hipertensión' => '>140 mmHg',
                    'Hipotensión' => '<90 mmHg',
                ],
            ],
            [
                'code' => '8462-4',
                'name' => 'Presión Arterial Diastólica',
                'category' => 'vital_sign',
                'system' => 'cardiovascular',
                'default_unit' => 'mmHg',
                'min_normal_value' => 60,
                'max_normal_value' => 80,
                'description' => 'Presión arterial mínima durante el ciclo cardíaco',
                'procedure' => 'Esfigmomanómetro braquial',
                'possible_abnormalities' => [
                    'Hipertensión' => '>90 mmHg',
                    'Hipotensión' => '<60 mmHg',
                ],
            ],
            [
                'code' => '9279-1',
                'name' => 'Frecuencia Respiratoria',
                'category' => 'vital_sign',
                'system' => 'respiratorio',
                'default_unit' => 'rpm',
                'min_normal_value' => 12,
                'max_normal_value' => 20,
                'description' => 'Respiraciones por minuto',
                'procedure' => 'Observación visual durante 60 segundos',
                'possible_abnormalities' => [
                    'Taquipnea' => '>20 rpm',
                    'Bradipnea' => '<12 rpm',
                    'Apnea' => '0 rpm',
                ],
            ],
            [
                'code' => '2708-6',
                'name' => 'Saturación de Oxígeno',
                'category' => 'vital_sign',
                'system' => 'respiratorio',
                'default_unit' => '%',
                'min_normal_value' => 95,
                'max_normal_value' => 100,
                'description' => 'Saturación de oxígeno en sangre medida por pulsioximetría',
                'procedure' => 'Pulsioxímetro en dedo',
                'possible_abnormalities' => [
                    'Hipoxemia' => '<90%',
                    'Hipoxia severa' => '<80%',
                ],
            ],
            [
                'code' => '29463-7',
                'name' => 'Peso Corporal',
                'category' => 'vital_sign',
                'system' => 'general',
                'default_unit' => 'kg',
                'min_normal_value' => null,
                'max_normal_value' => null,
                'description' => 'Peso corporal total',
                'procedure' => 'Báscula calibrada',
                'possible_abnormalities' => [
                    'Bajo peso' => 'IMC <18.5',
                    'Sobrepeso' => 'IMC >25',
                    'Obesidad' => 'IMC >30',
                ],
            ],
            [
                'code' => '8302-2',
                'name' => 'Talla',
                'category' => 'vital_sign',
                'system' => 'general',
                'default_unit' => 'cm',
                'min_normal_value' => null,
                'max_normal_value' => null,
                'description' => 'Estatura del paciente',
                'procedure' => 'Estadiómetro',
                'possible_abnormalities' => [],
            ],
            [
                'code' => '39156-5',
                'name' => 'Índice de Masa Corporal',
                'category' => 'vital_sign',
                'system' => 'general',
                'default_unit' => 'kg/m²',
                'min_normal_value' => 18.5,
                'max_normal_value' => 24.9,
                'description' => 'Relación entre peso y talla',
                'procedure' => 'Cálculo: peso(kg)/talla(m)²',
                'possible_abnormalities' => [
                    'Bajo peso' => '<18.5',
                    'Normal' => '18.5-24.9',
                    'Sobrepeso' => '25-29.9',
                    'Obesidad' => '≥30',
                ],
            ],
            [
                'code' => '85354-9',
                'name' => 'Glucemia Capilar',
                'category' => 'vital_sign',
                'system' => 'metabólico',
                'default_unit' => 'mg/dL',
                'min_normal_value' => 70,
                'max_normal_value' => 100,
                'description' => 'Nivel de glucosa en sangre capilar',
                'procedure' => 'Glucometría con punción digital',
                'possible_abnormalities' => [
                    'Hipoglucemia' => '<70 mg/dL',
                    'Hiperglucemia' => '>126 mg/dL en ayunas',
                ],
            ],
        ];

        $physicalExams = [
            // CONSITUCIONAL
            [
                'code' => '24825-0',
                'name' => 'Examen Constitucional',
                'category' => 'physical_exam',
                'system' => 'general',
                'default_unit' => null,
                'min_normal_value' => null,
                'max_normal_value' => null,
                'description' => 'Evaluación general del estado del paciente',
                'procedure' => 'Inspección general, estado nutricional, desarrollo',
                'possible_abnormalities' => [
                    'Malnutrición',
                    'Alteración del desarrollo',
                    'Facies patológicas',
                    'Posturas anormales',
                ],
                'default_answer' => 'The patient is alert, cooperative and oriented and in no acute distress.',
                'default_answer_es' => 'El paciente está alerta, cooperativo y orientado y no sufre una angustia aguda.',
            ],
            // HEENT (Cabeza, Ojos, Oídos, Nariz y Garganta)
            [
                'code' => '24821-9',
                'name' => 'Examen HEENT',
                'category' => 'physical_exam',
                'system' => 'cabeza_y_cuello',
                'default_unit' => null,
                'min_normal_value' => null,
                'max_normal_value' => null,
                'description' => 'Examen de cabeza, ojos, oídos, nariz y garganta',
                'procedure' => 'Inspección, palpación, evaluación de pares craneales',
                'possible_abnormalities' => [
                    'Cabeza' => ['Hidrocefalia', 'Microcefalia', 'Trauma'],
                    'Ojos' => ['Ictericia', 'Anisocoria', 'Conjuntivitis'],
                    'Oídos' => ['Otitis', 'Hipoacusia', 'Secreción'],
                    'Nariz' => ['Desviación septal', 'Epistaxis', 'Pólipos'],
                    'Garganta' => ['Faringitis', 'Amigdalitis', 'Masas'],
                ],
                'default_answer' => 'Head is normocephalic.  No evidence of trauma.  Ears:  No acute purulent discharge.  Eyes:  PER: 4mm, conjunctivae with no scleral jaundice.  Nose:  Normal mucosa and septum. EOMI, no hyperemia exudates or hemorrhages',
                'default_answer_es' => 'La cabeza es normocefálica. Sin evidencia de trauma. Oídos: Sin secreción purulenta aguda. Ojos: PER: 4 mm, conjuntiva sin ictericia escleral. Trufa: Mucosa y septo normales. EOMI, sin hiperemia exudados ni hemorragias',
            ],
            // Examen de Ojos (Detallado)
            [
                'code' => '24818-5',
                'name' => 'Examen Oftalmológico',
                'category' => 'physical_exam',
                'system' => 'oftalmológico',
                'default_unit' => null,
                'min_normal_value' => null,
                'max_normal_value' => null,
                'description' => 'Evaluación detallada de ojos y visión',
                'procedure' => 'Agudeza visual, fondo de ojo, motilidad ocular',
                'possible_abnormalities' => [
                    'Cataratas',
                    'Glaucoma',
                    'Retinopatía',
                    'Estrabismo',
                ],
            ],
            // PULMONAR
            [
                'code' => '24627-0',
                'name' => 'Examen Respiratorio',
                'category' => 'physical_exam',
                'system' => 'respiratorio',
                'default_unit' => null,
                'min_normal_value' => null,
                'max_normal_value' => null,
                'description' => 'Evaluación del sistema respiratorio',
                'procedure' => 'Auscultación pulmonar, percusión, etc.',
                'possible_abnormalities' => [
                    'Crepitaciones',
                    'Sibilancias',
                    'Roncos',
                    'Disminución del murmullo vesicular',
                ],
                'default_answer' => 'No weight loss, fever, chills, weakness or fatigue.',
                'default_answer_es' => 'Sin dificultad para respirar. Sin cianosis, tos ni esputo.',

            ],
            // CARDIOVASCULAR
            [
                'code' => '24636-1',
                'name' => 'Examen Cardiovascular',
                'category' => 'physical_exam',
                'system' => 'cardiovascular',
                'default_unit' => null,
                'min_normal_value' => null,
                'max_normal_value' => null,
                'description' => 'Evaluación del sistema cardiovascular',
                'procedure' => 'Auscultación cardiaca, palpación de pulsos, etc.',
                'possible_abnormalities' => [
                    'Soplos cardíacos',
                    'Arritmias',
                    'Edemas',
                    'Pulsos disminuidos',
                ],
                'default_answer' => 'Regular rate and rhythm, no murmurs to auscultation, instant capillary filling, radial and femoral pulses are intact.',
                'default_answer_es' => 'Frecuencia y ritmo regulares, sin soplos a la auscultación, llenado capilar instantáneo, pulsos radiales y femorales intactos.',
            ],
            // Examen de Pecho/Senos
            [
                'code' => '24826-8',
                'name' => 'Examen de Pecho/Senos',
                'category' => 'physical_exam',
                'system' => 'torácico',
                'default_unit' => null,
                'min_normal_value' => null,
                'max_normal_value' => null,
                'description' => 'Evaluación de mamas y pared torácica',
                'procedure' => 'Inspección, palpación en cuadrantes',
                'possible_abnormalities' => [
                    'Mamas' => ['Nódulos', 'Secreción', 'Retracción'],
                    'Pared torácica' => ['Deformidades', 'Tumefacciones'],
                ],
                'default_answer' => 'Symmetrical bilaterally to expiration and inspiration movement.',
                'default_answer_es' => 'Simétrico bilateralmente al movimiento de inspiración y espiración.',
            ],
            // GASTROINTESTINAL
            [
                'code' => '10230-1',
                'name' => 'Examen Abdominal',
                'category' => 'physical_exam',
                'system' => 'gastrointestinal',
                'default_unit' => null,
                'min_normal_value' => null,
                'max_normal_value' => null,
                'description' => 'Evaluación del abdomen y órganos internos',
                'procedure' => 'Palpación, percusión, auscultación',
                'possible_abnormalities' => [
                    'Dolor a la palpación',
                    'Masas palpables',
                    'Hepatomegalia',
                    'Esplenomegalia',
                ],
                'default_answer' => 'Soft, nontender, nondistended, nonpalpable masses with good bowel sounds heard.  Inguinal area is normal.',
                'default_answer_es' => 'Se escuchan masas blandas, no dolorosas, no distendidas y no palpables con buenos ruidos intestinales. El área inguinal es normal.',
            ],
            // Examen Linfático
            [
                'code' => '24828-4',
                'name' => 'Examen Linfático',
                'category' => 'physical_exam',
                'system' => 'linfático',
                'default_unit' => null,
                'min_normal_value' => null,
                'max_normal_value' => null,
                'description' => 'Evaluación de ganglios linfáticos',
                'procedure' => 'Palpación de cadenas ganglionares',
                'possible_abnormalities' => [
                    'Adenopatías' => [
                        'Cervicales',
                        'Axilares',
                        'Inguinales',
                        'Generalizadas',
                    ],
                    'Características' => [
                        'Dolorosas',
                        'Fijas',
                        'Blandas/Duras',
                    ],
                ],
                'default_answer' => 'No palpable lymph nodes.',
                'default_answer_es' => 'No hay ganglios linfáticos palpables.',
            ],
            // NEUROLOGICO
            [
                'code' => '24822-7',
                'name' => 'Examen Neurológico',
                'category' => 'physical_exam',
                'system' => 'neurológico',
                'default_unit' => null,
                'min_normal_value' => null,
                'max_normal_value' => null,
                'description' => 'Evaluación del sistema nervioso',
                'procedure' => 'Reflejos, fuerza muscular, sensibilidad, etc.',
                'possible_abnormalities' => [
                    'Reflejos anormales',
                    'Déficit motor',
                    'Déficit sensitivo',
                    'Alteración de pares craneales',
                ],
                'default_answer' => 'The patient is oriented to person, place and time.',
                'default_answer_es' => 'El paciente está orientado a la persona, el lugar y el tiempo.',
            ],
            // MUSCOESQUELETICO
            [
                'code' => '24820-1',
                'name' => 'Examen Osteomuscular',
                'category' => 'physical_exam',
                'system' => 'musculoesquelético',
                'default_unit' => null,
                'min_normal_value' => null,
                'max_normal_value' => null,
                'description' => 'Evaluación del sistema musculoesquelético',
                'procedure' => 'Inspección, palpación, rangos de movimiento',
                'possible_abnormalities' => [
                    'Limitación de movimiento',
                    'Dolor articular',
                    'Deformidades',
                    'Hipersensibilidad',
                ],
                'default_answer' => 'Symmetrical extremities, mobile, without edema, symmetrical, regular and of good amplitude; muscle tone and trophism preserved, muscle strength 5/5. Active and passive motility preserved.',
                'default_answer_es' => 'Extremidades simétricas, móviles, sin edema, simétricas, regulares y de buena amplitud; tono muscular y trofismo conservados, fuerza muscular 5/5. Motilidad activa y pasiva conservada.',
            ],
            // DERMATOLOGICO
            [
                'code' => '24823-5',
                'name' => 'Examen de Piel y Faneras',
                'category' => 'physical_exam',
                'system' => 'dermatológico',
                'default_unit' => null,
                'min_normal_value' => null,
                'max_normal_value' => null,
                'description' => 'Evaluación de piel, pelo y uñas',
                'procedure' => 'Inspección visual',
                'possible_abnormalities' => [
                    'Lesiones cutáneas',
                    'Erupciones',
                    'Cambios en la pigmentación',
                    'Alteraciones en uñas',
                ],
                'default_answer' => 'Normal skin temperature. No edema, or superficial varicosities.',
                'default_answer_es' => 'Lesión circular en la cabeza, sin descamativo, sin prurito, enrojecida con bordes definidos.',
            ],
            // Examen Psiquiátrico
            [
                'code' => '24831-8',
                'name' => 'Examen del Estado Mental',
                'category' => 'physical_exam',
                'system' => 'psiquiátrico',
                'default_unit' => null,
                'min_normal_value' => null,
                'max_normal_value' => null,
                'description' => 'Evaluación del estado mental y funciones cognitivas',
                'procedure' => 'Entrevista, pruebas cognitivas, evaluación conductual',
                'possible_abnormalities' => [
                    'Orientación' => ['Desorientación temporo-espacial'],
                    'Memoria' => ['Amnesia', 'Déficit de memoria reciente'],
                    'Ánimo' => ['Depresión', 'Euforia', 'Labilidad emocional'],
                    'Pensamiento' => ['Delirios', 'Ideas obsesivas'],
                    'Percepción' => ['Alucinaciones', 'Ilusiones'],
                ],
                'default_answer' => 'Oriented 3X, denies suicidal or homicidal ideations. Insomina and anxiety.',
                'default_answer_es' => 'Orientado 3X, niega ideas suicidas u homicidas. Insomnio y ansiedad.',
            ],
        ];

        foreach ($vitalSigns as $vs) {
            ClinicalObservationType::updateOrCreate(
                ['code' => $vs['code']], // Buscar por code
                $vs // Actualizar/crear con todos los datos
            );
        }

        foreach ($physicalExams as $pe) {
            ClinicalObservationType::updateOrCreate(
                ['code' => $pe['code']], // Buscar por code
                $pe // Actualizar/crear con todos los datos
            );
        }

    }
}
