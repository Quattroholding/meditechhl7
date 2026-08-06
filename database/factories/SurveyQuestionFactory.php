<?php

namespace Database\Factories;

use App\Models\Survey;
use App\Models\SurveyQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SurveyQuestion>
 */
class SurveyQuestionFactory extends Factory
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
            'question_text' => fake()->sentence(),
            'question_type' => fake()->randomElement(['text', 'textarea', 'select', 'radio', 'checkbox', 'rating', 'number']),
            'options' => ['Option 1', 'Option 2', 'Option 3'],
            'is_required' => fake()->boolean(),
            'order' => fake()->numberBetween(1, 10),
        ];
    }
}
