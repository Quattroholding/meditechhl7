<?php

namespace Database\Factories;

use App\Models\InsuranceCompany;
use App\Models\Patient;
use App\Models\PatientInsurancePolicy;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PatientInsurancePolicy>
 */
class PatientInsurancePolicyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $effectiveDate = $this->faker->dateTimeBetween('-2 years', 'now');
        $expirationDate = $this->faker->dateTimeBetween($effectiveDate, '+1 year');

        return [
            'patient_id' => Patient::factory(),
            'insurance_company_id' => InsuranceCompany::factory(),
            'policy_number' => $this->faker->unique()->regexify('[A-Z]{3}[0-9]{6}'),
            'group_number' => $this->faker->optional()->regexify('[0-9]{4}[A-Z]{2}'),
            'subscriber_id' => $this->faker->regexify('[0-9]{9}'),
            'subscriber_name' => $this->faker->name(),
            'relationship_to_subscriber' => $this->faker->randomElement(['self', 'spouse', 'child', 'parent', 'other']),
            'effective_date' => $effectiveDate,
            'expiration_date' => $this->faker->boolean(80) ? $expirationDate : null,
            'priority' => $this->faker->randomElement(['primary', 'secondary', 'tertiary']),
            'coverage_percentage' => $this->faker->randomFloat(2, 70, 95),
            'copay_amount' => $this->faker->randomFloat(2, 500, 3000),
            'deductible_amount' => $this->faker->randomFloat(2, 5000, 50000),
            'deductible_remaining' => function (array $attributes) {
                return $this->faker->randomFloat(2, 0, $attributes['deductible_amount']);
            },
            'out_of_pocket_max' => $this->faker->randomFloat(2, 100000, 500000),
            'out_of_pocket_remaining' => function (array $attributes) {
                return $this->faker->randomFloat(2, 0, $attributes['out_of_pocket_max']);
            },
            'is_active' => $this->faker->boolean(85),
            'coverage_details' => [
                'preventive_care' => $this->faker->boolean(90),
                'specialist_visits' => $this->faker->boolean(80),
                'emergency_room' => $this->faker->boolean(95),
                'hospitalization' => $this->faker->boolean(85),
                'prescription_drugs' => $this->faker->boolean(75),
                'mental_health' => $this->faker->boolean(70),
                'physical_therapy' => $this->faker->boolean(65),
                'dental' => $this->faker->boolean(40),
                'vision' => $this->faker->boolean(35),
            ],
            'notes' => $this->faker->optional()->paragraph(),
        ];
    }

    public function primary(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'primary',
        ]);
    }

    public function secondary(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'secondary',
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
            'expiration_date' => $this->faker->dateTimeBetween('+1 month', '+1 year'),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
            'expiration_date' => $this->faker->dateTimeBetween('-1 year', '-1 day'),
        ]);
    }

    public function forSelf(): static
    {
        return $this->state(fn (array $attributes) => [
            'relationship_to_subscriber' => 'self',
        ]);
    }
}
