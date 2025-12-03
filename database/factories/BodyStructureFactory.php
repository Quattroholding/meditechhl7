<?php

namespace Database\Factories;

use App\Models\BodyStructure;
use App\Models\Encounter;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BodyStructure>
 */
class BodyStructureFactory extends Factory
{
    protected $model = BodyStructure::class;

    private $bodyLocations = [
        ['code' => '39937001', 'display' => 'Piel (estructura)'],
        ['code' => '113305005', 'display' => 'Piel de la cara'],
        ['code' => '48075008', 'display' => 'Piel del brazo'],
        ['code' => '30021000', 'display' => 'Piel de la pierna'],
        ['code' => '53120007', 'display' => 'Piel del tronco'],
        ['code' => '119235005', 'display' => 'Piel del cuero cabelludo'],
        ['code' => '181476001', 'display' => 'Piel del cuello'],
        ['code' => '32696007', 'display' => 'Piel de la mano'],
        ['code' => '56459004', 'display' => 'Piel del pie'],
    ];

    private $morphologies = [
        ['code' => '400210000', 'display' => 'Nevus de la piel'],
        ['code' => '254701007', 'display' => 'Carcinoma basocelular'],
        ['code' => '372244006', 'display' => 'Melanoma maligno'],
        ['code' => '35917007', 'display' => 'Carcinoma de células escamosas'],
        ['code' => '201101007', 'display' => 'Nevus melanocítico benigno'],
        ['code' => '400158001', 'display' => 'Queratosis seborreica'],
    ];

    private $laterality = [
        ['code' => '7771000', 'display' => 'Izquierdo'],
        ['code' => '24028007', 'display' => 'Derecho'],
        ['code' => '51440002', 'display' => 'Bilateral'],
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $location = fake()->randomElement($this->bodyLocations);
        $morphology = fake()->randomElement($this->morphologies);
        $qualifier = fake()->optional(0.6)->randomElement($this->laterality);

        return [
            'fhir_id' => 'body-structure-'.uniqid(),
            'patient_id' => Patient::factory(),
            'encounter_id' => fake()->optional(0.8)->randomElement(Encounter::pluck('id')->toArray()),
            'identifier' => 'BS-'.strtoupper(fake()->bothify('???###')),
            'active' => fake()->boolean(90),
            'morphology_code' => $morphology['code'],
            'morphology_display' => $morphology['display'],
            'location_code' => $location['code'],
            'location_display' => $location['display'],
            'location_qualifier_code' => $qualifier['code'] ?? null,
            'location_qualifier_display' => $qualifier['display'] ?? null,
            'description' => fake()->optional(0.7)->sentence(),
            'image_references' => null,
            'extension' => null,
            'included_structures' => null,
        ];
    }

    /**
     * Indicate that the body structure is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'active' => false,
        ]);
    }

    /**
     * Indicate that the body structure is a nevus.
     */
    public function nevus(): static
    {
        return $this->state(fn (array $attributes) => [
            'morphology_code' => '400210000',
            'morphology_display' => 'Nevus de la piel',
        ]);
    }

    /**
     * Indicate that the body structure is on the face.
     */
    public function onFace(): static
    {
        return $this->state(fn (array $attributes) => [
            'location_code' => '113305005',
            'location_display' => 'Piel de la cara',
        ]);
    }
}
