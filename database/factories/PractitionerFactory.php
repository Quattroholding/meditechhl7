<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Practitioner;
use App\Models\User;
use App\Models\UserClient;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Practitioner>
 */
class PractitionerFactory extends Factory
{
    protected $model = Practitioner::class;

    public function definition()
    {
        $gender = $this->faker->randomElement(['male', 'female']);
        $givenName = $gender === 'male' ? $this->faker->firstNameMale : $this->faker->firstNameFemale;

        $specialties = [
            'Cardiología', 'Pediatría', 'Dermatología', 'Ginecología',
            'Traumatología', 'Neurología', 'Oncología', 'Medicina General'
        ];

        return [
            'fhir_id' => 'practitioner-' . Str::uuid(),
            'identifier' => $this->faker->unique()->numerify('DOC#######'),
            'name' => 'Dr. ' . $givenName . ' ' . $this->faker->lastName,
            'given_name' => $givenName,
            'family_name' => $this->faker->lastName,
            'gender' => $gender,
            'birth_date' => $this->faker->dateTimeBetween('-60 years', '-30 years')->format('Y-m-d'),
            'address' => $this->faker->streetAddress,
            'phone' => $this->faker->phoneNumber,
            'email' => $this->faker->unique()->safeEmail,
            'qualification' => json_encode([
                [
                    'code' => $this->faker->randomElement(['MD', 'DO']),
                    'system' => 'http://terminology.hl7.org/CodeSystem/v2-0360',
                    'display' => $this->faker->randomElement($specialties),
                    'period_start' => $this->faker->dateTimeBetween('-20 years', '-5 years')->format('Y-m-d'),
                ]
            ]),
            'active' => true,
        ];
    }

    // States adicionales
    public function inactive()
    {
        return $this->state(function (array $attributes) {
            return [
                'active' => false,
            ];
        });
    }

    public function specialist(string $specialty)
    {
        return  $this->afterCreating(function (Practitioner $practitioner) use($specialty) {
            $period_start = $this->faker->dateTimeBetween('-20 years', '-5 years')->format('Y-m-d');
            $period_end = $this->faker->dateTimeBetween($period_start, '+8 years');
            return  $practitioner->qualifications()->create([
                    'code' => 'MD',
                    'system' => 'http://terminology.hl7.org/CodeSystem/v2-0360',
                    'display' => $specialty,
                    'period_start' => $period_start,
                    'period_end'=>$period_end
                ]);
        });
    }

    public function configure()
    {
        return $this->afterCreating(function (Practitioner $practitioner) {
            // Crear usuario asociado si no existe
            if (!$practitioner->user) {

                $user = User::factory()
                    ->asDoctor()
                    ->create([
                        'first_name' =>$practitioner->given_name,
                        'last_name' => $practitioner->family_name,
                        'email' => Str::slug(str_replace('Dr. ', '', $practitioner->name)) . '@clinica.com'
                    ]);

                $practitioner->user()->associate($user);
                if($practitioner->save()){
                    $client = Client::inRandomOrder()->take(1)->first();
                    UserClient::create([
                        'user_id'=>$user->id,
                        'client_id'=>$client->id,
                    ]);
                }
            }
        });
    }
}
