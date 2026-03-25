<?php

namespace Database\Factories;

use App\Models\Package;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Package>
 */
class PackageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true).' Plan',
            'slug' => fake()->unique()->slug(),
            'description' => fake()->sentence(),
            'base_price' => fake()->randomFloat(2, 50, 500),
            'max_doctors_included' => fake()->numberBetween(1, 10),
            'max_users' => fake()->numberBetween(5, 100),
            'price_per_extra_doctor' => fake()->randomFloat(2, 10, 50),
            'features' => ['feature1', 'feature2'],
            'is_active' => true,
            'billing_period' => 'monthly',
            'billing_period_days' => 30,
            'agent_available' => fake()->boolean(),
            'appointments_limit' => fake()->numberBetween(50, 500),
        ];
    }
}
