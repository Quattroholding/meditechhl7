<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\CptCode;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServiceRequest>
 */
class ServiceRequestFactory extends Factory
{
    protected $model = ServiceRequest::class;

    public function definition()
    {

        $cpt = CptCode::inRandomOrder()->limit(1)->first();

        return [
            'fhir_id' => $this->faker->unique()->uuid,
            'encounter_id' => Encounter::factory(),
            'patient_id' => Patient::factory(),
            'practitioner_id' => Practitioner::factory(),
            'status' => $this->faker->randomElement(['draft', 'active', 'on-hold', 'completed']),
            'intent' => $this->faker->randomElement(['order', 'original-order', 'reflex-order']),
            'priority' => $this->faker->randomElement(['routine', 'urgent', 'asap', 'stat']),
            'do_not_perform' => $this->faker->boolean(10),
            'code' =>$cpt->code,
            'service_type'=>$cpt->type,
            'code_system' => 'https://www.ama-assn.org/practice-management/cpt',
            'code_display' => $this->faker->sentence(3),
            'quantity' => $this->faker->numberBetween(1, 3),
            'occurrence_start' => $this->faker->dateTimeBetween('now', '+1 week'),
            'occurrence_end' => $this->faker->dateTimeBetween('+1 week', '+2 weeks'),
            'body_site' => [
                'code' => $this->faker->randomElement(['HEAD', 'CHEST', 'ABDOMEN']),
                'display' => $this->faker->word
            ],
            'note' => $this->faker->optional()->sentence,
            'authored_on' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'last_updated' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
