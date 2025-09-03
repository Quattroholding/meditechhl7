<?php

namespace Database\Factories;

use App\Models\PatientRelationship;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PatientRelationship>
 */
class PatientRelationshipFactory extends Factory
{
    protected $model = PatientRelationship::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $relationships = [
            ['code' => 'CHILD', 'display' => 'Child'],
            ['code' => 'DAUC', 'display' => 'Daughter'],
            ['code' => 'SON', 'display' => 'Son'],
            ['code' => 'SPO', 'display' => 'Spouse'],
            ['code' => 'HUSB', 'display' => 'Husband'],
            ['code' => 'WIFE', 'display' => 'Wife'],
            ['code' => 'PRN', 'display' => 'Parent'],
            ['code' => 'FTH', 'display' => 'Father'],
            ['code' => 'MTH', 'display' => 'Mother'],
            ['code' => 'SIB', 'display' => 'Sibling'],
            ['code' => 'BRO', 'display' => 'Brother'],
            ['code' => 'SIS', 'display' => 'Sister'],
        ];

        $relationship = $this->faker->randomElement($relationships);
        $gender = $this->faker->randomElement(['male', 'female']);
        $givenName = $gender === 'male' ? $this->faker->firstNameMale : $this->faker->firstNameFemale;
        $familyName = $this->faker->lastName;

        return [
            'fhir_id' => 'related-person-'.Str::uuid(),
            'patient_id' => \App\Models\Patient::factory(),
            'related_patient_id' => null, // Will be set when creating actual patient relationships
            'identifier' => $this->faker->unique()->regexify('[0-9]{8}-[0-9]{4}'),
            'identifier_type' => $this->faker->randomElement(['CC', 'CE', 'PA']),
            'name' => $givenName.' '.$familyName,
            'given_name' => $givenName,
            'family_name' => $familyName,
            'gender' => $gender,
            'birth_date' => $this->faker->dateTimeBetween('-80 years', '-18 years'),
            'phone' => $this->faker->phoneNumber,
            'email' => $this->faker->optional(70)->safeEmail,
            'address' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'state' => $this->faker->state,
            'postal_code' => $this->faker->postcode,
            'country' => $this->faker->country,
            'relationship_code' => $relationship['code'],
            'relationship_display' => $relationship['display'],
            'relationship_system' => 'http://terminology.hl7.org/CodeSystem/v3-RoleCode',
            'is_emergency_contact' => $this->faker->boolean(30),
            'is_insurance_subscriber' => $this->faker->boolean(15),
            'contact_preferences' => json_encode([
                'preferred_contact_method' => $this->faker->randomElement(['phone', 'email', 'sms']),
                'language' => 'es',
                'preferred_time' => $this->faker->randomElement(['morning', 'afternoon', 'evening']),
            ]),
            'effective_date' => $this->faker->dateTimeBetween('-5 years', 'now'),
            'end_date' => null,
            'is_active' => true,
            'notes' => $this->faker->optional(40)->sentence(),
            'extension' => null,
        ];
    }

    /**
     * Create a child relationship
     */
    public function child(): static
    {
        return $this->state(fn (array $attributes) => [
            'relationship_code' => 'CHILD',
            'relationship_display' => 'Child',
            'birth_date' => $this->faker->dateTimeBetween('-17 years', '-1 year'),
            'is_emergency_contact' => false,
            'is_insurance_subscriber' => false,
        ]);
    }

    /**
     * Create a spouse relationship
     */
    public function spouse(): static
    {
        return $this->state(fn (array $attributes) => [
            'relationship_code' => $this->faker->randomElement(['SPO', 'HUSB', 'WIFE']),
            'relationship_display' => $this->faker->randomElement(['Spouse', 'Husband', 'Wife']),
            'birth_date' => $this->faker->dateTimeBetween('-70 years', '-20 years'),
            'is_emergency_contact' => $this->faker->boolean(80),
            'is_insurance_subscriber' => $this->faker->boolean(40),
        ]);
    }

    /**
     * Create a parent relationship
     */
    public function parent(): static
    {
        return $this->state(fn (array $attributes) => [
            'relationship_code' => $this->faker->randomElement(['PRN', 'FTH', 'MTH']),
            'relationship_display' => $this->faker->randomElement(['Parent', 'Father', 'Mother']),
            'birth_date' => $this->faker->dateTimeBetween('-80 years', '-40 years'),
            'is_emergency_contact' => $this->faker->boolean(60),
            'is_insurance_subscriber' => $this->faker->boolean(70),
        ]);
    }

    /**
     * Create an emergency contact
     */
    public function emergencyContact(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_emergency_contact' => true,
        ]);
    }

    /**
     * Create an insurance subscriber
     */
    public function insuranceSubscriber(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_insurance_subscriber' => true,
        ]);
    }
}
