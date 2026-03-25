<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\InsuranceCompany;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InsuranceCompany>
 */
class InsuranceCompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $insuranceCompanies = [
            'Seguros Universales S.A.',
            'Mapfre Seguros',
            'Humano Seguros',
            'ARS Palic',
            'Seguro Nacional de Salud (SENASA)',
            'MetaSalud',
            'ARS Simag',
            'Plan de Servicios Médicos (PSM)',
            'SEMMA (Seguro Médico Magisterial)',
            'Seguros Pepín',
            'ARS Futuro',
            'Primera ARS',
            'ARS BMI',
            'Seguros La Colonial',
        ];

        $companyName = $this->faker->randomElement($insuranceCompanies);
        $code = strtoupper(substr(str_replace([' ', '.', '(', ')'], '', $companyName), 0, 6));

        return [
            'client_id' => Client::factory(),
            'name' => $companyName,
            'code' => $code.'-'.$this->faker->unique()->numberBetween(100, 999),
            'email' => $this->faker->unique()->companyEmail(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'contact_person' => $this->faker->name(),
            'contact_email' => $this->faker->unique()->safeEmail(),
            'contact_phone' => $this->faker->phoneNumber(),
            'default_coverage_percentage' => $this->faker->randomFloat(2, 70, 90),
            'default_copay_amount' => $this->faker->randomFloat(2, 500, 2000),
            'is_active' => $this->faker->boolean(85),
            'coverage_types' => [
                'consultation' => $this->faker->boolean(90),
                'emergency' => $this->faker->boolean(95),
                'hospitalization' => $this->faker->boolean(80),
                'surgery' => $this->faker->boolean(75),
                'laboratory' => $this->faker->boolean(85),
                'imaging' => $this->faker->boolean(80),
                'pharmacy' => $this->faker->boolean(70),
                'dental' => $this->faker->boolean(60),
                'vision' => $this->faker->boolean(50),
                'maternity' => $this->faker->boolean(85),
            ],
            'notes' => $this->faker->optional()->paragraph(),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
