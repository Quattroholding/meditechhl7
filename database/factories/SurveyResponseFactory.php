<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Patient;
use App\Models\Practitioner;
use App\Models\Survey;
use App\Models\SurveyResponse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SurveyResponse>
 */
class SurveyResponseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'survey_id' => Survey::factory(),
            'patient_id' => Patient::factory(),
            'practitioner_id' => Practitioner::factory(),
            'client_id' => Client::factory(),
            'token' => fake()->sha256(),
            'submitted_at' => fake()->dateTime(),
            'responses' => [],
            'status' => fake()->randomElement(['pending', 'completed']),
        ];
    }
}
