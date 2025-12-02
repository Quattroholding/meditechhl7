<?php

namespace Database\Factories;

use App\Models\BodyStructure;
use App\Models\Encounter;
use App\Models\Patient;
use App\Models\Practitioner;
use App\Models\SkinLesion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SkinLesion>
 */
class SkinLesionFactory extends Factory
{
    protected $model = SkinLesion::class;

    private $lesionTypes = [
        'nevus', 'melanoma', 'basal_cell_carcinoma', 'squamous_cell_carcinoma',
        'keratosis', 'wart', 'cyst', 'lipoma', 'hemangioma', 'other',
    ];

    private $colors = [
        'brown', 'black', 'red', 'pink', 'tan', 'blue', 'purple', 'white', 'mixed',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $sizeLength = fake()->randomFloat(2, 1, 15);
        $diameter = $sizeLength > 6 ? 'greater_than_6mm' : 'less_than_6mm';

        return [
            'body_structure_id' => BodyStructure::factory(),
            'patient_id' => Patient::factory(),
            'encounter_id' => fake()->optional(0.9)->randomElement(Encounter::pluck('id')->toArray()),
            'practitioner_id' => fake()->randomElement(Practitioner::pluck('id')->toArray()),
            'lesion_type' => fake()->randomElement($this->lesionTypes),
            'color' => fake()->randomElement($this->colors),
            'size_length_mm' => $sizeLength,
            'size_width_mm' => fake()->randomFloat(2, 1, 15),
            'size_height_mm' => fake()->optional(0.5)->randomFloat(2, 0.5, 5),
            'asymmetry' => fake()->randomElement(['symmetric', 'asymmetric']),
            'border' => fake()->randomElement(['regular', 'irregular']),
            'color_variation' => fake()->optional(0.4)->randomElement(['uniform', 'varied', 'multiple_colors']),
            'diameter' => $diameter,
            'evolving' => fake()->randomElement(['stable', 'changing']),
            'risk_level' => fake()->randomElement(['low', 'moderate', 'high', 'suspicious']),
            'clinical_notes' => fake()->optional(0.6)->sentence(),
            'first_observed_date' => fake()->dateTimeBetween('-2 years', '-6 months'),
            'last_examination_date' => fake()->dateTimeBetween('-6 months', 'now'),
            'requires_biopsy' => fake()->boolean(20),
            'requires_removal' => fake()->boolean(15),
            'next_follow_up_date' => fake()->optional(0.8)->dateTimeBetween('now', '+1 year'),
            'status' => fake()->randomElement(['active', 'removed', 'resolved', 'under_treatment']),
            'treatment_plan' => fake()->optional(0.3)->sentence(),
        ];
    }

    /**
     * Indicate that the lesion is low risk.
     */
    public function lowRisk(): static
    {
        return $this->state(fn (array $attributes) => [
            'risk_level' => 'low',
            'asymmetry' => 'symmetric',
            'border' => 'regular',
            'evolving' => 'stable',
            'requires_biopsy' => false,
            'requires_removal' => false,
        ]);
    }

    /**
     * Indicate that the lesion is high risk.
     */
    public function highRisk(): static
    {
        return $this->state(fn (array $attributes) => [
            'risk_level' => 'high',
            'asymmetry' => 'asymmetric',
            'border' => 'irregular',
            'evolving' => 'changing',
            'diameter' => 'greater_than_6mm',
            'size_length_mm' => fake()->randomFloat(2, 7, 12),
            'requires_biopsy' => fake()->boolean(70),
            'requires_removal' => fake()->boolean(50),
        ]);
    }

    /**
     * Indicate that the lesion is suspicious for melanoma.
     */
    public function suspicious(): static
    {
        return $this->state(fn (array $attributes) => [
            'lesion_type' => 'melanoma',
            'risk_level' => 'suspicious',
            'asymmetry' => 'asymmetric',
            'border' => 'irregular',
            'color_variation' => 'multiple_colors',
            'diameter' => 'greater_than_6mm',
            'evolving' => 'changing',
            'size_length_mm' => fake()->randomFloat(2, 8, 15),
            'requires_biopsy' => true,
            'requires_removal' => true,
            'status' => 'under_treatment',
        ]);
    }

    /**
     * Indicate that the lesion is a simple nevus.
     */
    public function nevus(): static
    {
        return $this->state(fn (array $attributes) => [
            'lesion_type' => 'nevus',
            'risk_level' => 'low',
            'color' => 'brown',
            'asymmetry' => 'symmetric',
            'border' => 'regular',
            'diameter' => 'less_than_6mm',
            'evolving' => 'stable',
            'size_length_mm' => fake()->randomFloat(2, 2, 5),
            'size_width_mm' => fake()->randomFloat(2, 2, 5),
        ]);
    }

    /**
     * Indicate that the lesion has been removed.
     */
    public function removed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'removed',
            'next_follow_up_date' => null,
        ]);
    }

    /**
     * Indicate that the lesion is active and requires follow-up.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'next_follow_up_date' => fake()->dateTimeBetween('now', '+6 months'),
        ]);
    }
}
