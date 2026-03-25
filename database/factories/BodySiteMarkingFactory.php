<?php

namespace Database\Factories;

use App\Models\BodySiteMarking;
use App\Models\BodyStructure;
use App\Models\SkinLesion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BodySiteMarking>
 */
class BodySiteMarkingFactory extends Factory
{
    protected $model = BodySiteMarking::class;

    private $views = ['anterior', 'posterior', 'left_lateral', 'right_lateral'];

    private $shapes = ['circle', 'square', 'triangle', 'star'];

    private $riskColors = [
        'low' => '#22C55E',      // Green
        'moderate' => '#FBBF24', // Yellow
        'high' => '#F97316',     // Orange
        'suspicious' => '#EF4444', // Red
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'body_structure_id' => BodyStructure::factory(),
            'skin_lesion_id' => SkinLesion::factory(),
            'body_view' => fake()->randomElement($this->views),
            'position_x' => fake()->randomFloat(4, 10, 90),
            'position_y' => fake()->randomFloat(4, 10, 90),
            'marker_color' => fake()->randomElement($this->riskColors),
            'marker_shape' => fake()->randomElement($this->shapes),
            'marker_size' => fake()->numberBetween(8, 16),
            'label' => fake()->optional(0.7)->bothify('L-###'),
            'notes' => fake()->optional(0.4)->sentence(),
            'is_visible' => true,
        ];
    }

    /**
     * Indicate that the marking is on the anterior view.
     */
    public function anterior(): static
    {
        return $this->state(fn (array $attributes) => [
            'body_view' => 'anterior',
        ]);
    }

    /**
     * Indicate that the marking is on the posterior view.
     */
    public function posterior(): static
    {
        return $this->state(fn (array $attributes) => [
            'body_view' => 'posterior',
        ]);
    }

    /**
     * Indicate that the marking is hidden.
     */
    public function hidden(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_visible' => false,
        ]);
    }
}
