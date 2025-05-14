<?php

namespace Database\Factories;

use App\Models\Condition;
use App\Models\Icd10Code;
use App\Models\Patient;
use App\Models\Practitioner;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ConditionFactory extends Factory
{
    protected $model = Condition::class;

    public function definition()
    {
        $condition = Icd10Code::inRandomOrder()->first();


        $onsetDate = $this->faker->dateTimeBetween('-5 years', 'now');
        $abatementDate = $this->faker->boolean(30)
            ? $this->faker->dateTimeBetween($onsetDate, 'now')
            : null;

        return [
            'fhir_id' => 'condition-' . Str::uuid(),
            'patient_id' => Patient::factory(),
            'practitioner_id' => Practitioner::factory(),
            'identifier' => 'DX-' . $this->faker->unique()->numerify('#######'),
            'clinical_status' => $abatementDate ? 'resolved' : $this->faker->randomElement(['active', 'recurrence', 'relapse']),
            'verification_status' => $this->faker->randomElement(['confirmed', 'provisional', 'differential']),
            'code' => $condition->code,
            'category' =>null,
            'severity' => $this->faker->randomElement(['mild', 'moderate', 'severe', 'critical']),
            'onset_date' => $onsetDate,
            'abatement_date' => $abatementDate,
            'recorded_date' => $this->faker->dateTimeBetween($onsetDate, 'now'),
            'note' => $this->faker->paragraph(),
        ];
    }

    // States para diferentes tipos de condiciones
    public function active()
    {
        return $this->state(function (array $attributes) {
            return [
                'clinical_status' => 'active',
                'abatement_date' => null,
            ];
        });
    }

    public function resolved()
    {
        return $this->state(function (array $attributes) {
            return [
                'clinical_status' => 'resolved',
                'abatement_date' => $this->faker->dateTimeBetween($attributes['onset_date'], 'now'),
            ];
        });
    }

    public function chronic()
    {
        return $this->state(function (array $attributes) {
            return [
                'clinical_status' => 'active',
                'abatement_date' => null,
                'note' => 'Condición crónica. ' . $this->faker->sentence(),
            ];
        });
    }

    public function withCategory(string $category)
    {
        $conditions = array_filter($this->getCommonConditions(), function($cond) use ($category) {
            return $cond['category'] === $category;
        });

        if (empty($conditions)) {
            return $this;
        }

        $condition = $this->faker->randomElement($conditions);

        return $this->state(function (array $attributes) use ($condition) {
            return [
                'code' => $condition['code'],
                'category' => $condition['category'],
            ];
        });
    }

    // Método auxiliar para condiciones comunes
    protected function getCommonConditions(): array
    {
        return [
            // Enfermedades infecciosas
            [
                'code' => 'A09',
                'category' => 'infectious',
                'description' => 'Diarrea y gastroenteritis de presunto origen infeccioso'
            ],
            [
                'code' => 'J18.9',
                'category' => 'infectious',
                'description' => 'Neumonía, no especificada'
            ],

            // Enfermedades crónicas
            [
                'code' => 'E11.65',
                'category' => 'chronic',
                'description' => 'Diabetes mellitus tipo 2 con hiperglucemia'
            ],
            [
                'code' => 'I10',
                'category' => 'chronic',
                'description' => 'Hipertensión esencial (primaria)'
            ],

            // Enfermedades cardiovasculares
            [
                'code' => 'I25.10',
                'category' => 'cardiovascular',
                'description' => 'Enfermedad aterosclerótica del corazón sin angina de pecho'
            ],

            // Trastornos musculoesqueléticos
            [
                'code' => 'M54.5',
                'category' => 'musculoskeletal',
                'description' => 'Lumbalgia baja'
            ],

            // Trastornos mentales
            [
                'code' => 'F32.9',
                'category' => 'mental',
                'description' => 'Episodio depresivo, no especificado'
            ],

            // Lesiones
            [
                'code' => 'S72.001A',
                'category' => 'injury',
                'description' => 'Fractura de fémur no especificada, inicial'
            ],

            // Condiciones respiratorias
            [
                'code' => 'J45.909',
                'category' => 'respiratory',
                'description' => 'Asma no especificada, no controlada'
            ]
        ];
    }

    // Configuración para asegurar relaciones
    public function configure()
    {
        return $this->afterMaking(function (Condition $condition) {
            if (!$condition->patient_id) {
                $condition->patient()->associate(Patient::factory()->create());
            }
            if (!$condition->practitioner_id) {
                $condition->practitioner()->associate(Practitioner::factory()->create());
            }
        });
    }
}
