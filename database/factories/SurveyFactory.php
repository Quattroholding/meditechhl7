<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Survey;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Survey>
 */
class SurveyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'is_active' => fake()->boolean(),
            'status' => fake()->randomElement(['draft', 'active', 'inactive']),
            'client_id' => Client::factory(),
            'created_by' => User::factory(),
            'trigger_point' => fake()->randomElement(['post_appointment', 'post_consultation', 'post_payment']),
        ];
    }
}
