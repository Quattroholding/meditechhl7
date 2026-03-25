<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\MedicalSpeciality;
use App\Models\Practitioner;
use App\Models\User;
use App\Models\UserClient;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Practitioner>
 */
class PractitionerFactory extends Factory
{
    protected $model = Practitioner::class;

    public function definition()
    {
        $gender = $this->faker->randomElement(['male', 'female']);
        $givenName = $gender === 'male' ? $this->faker->firstNameMale : $this->faker->firstNameFemale;

        $id_type = $this->faker->randomElement(['PA', 'CC', 'SS', 'CE', 'PT']);
        $identifier = $this->faker->unique()->regexify($this->getIdPattern($id_type));

        $specialty = MedicalSpeciality::inRandomOrder()->limit(1)->first();
        $id_type = $this->faker->randomElement(['PA', 'CC', 'SS', 'CE', 'PT']);

        return [
            'fhir_id' => 'practitioner-'.Str::uuid(),
            'registry' => $this->faker->randomNumber(8),
            'identifier' => $identifier,
            'identifier_type' => $id_type,
            'name' => 'Dr. '.$givenName.' '.$this->faker->lastName,
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
                    'display' => $specialty->name,
                    'period_start' => $this->faker->dateTimeBetween('-20 years', '-5 years')->format('Y-m-d'),
                    'medical_speciality_id' => $specialty->id,
                ],
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

    public function specialist(string $specialty, int $specialty_id)
    {
        return $this->afterCreating(function (Practitioner $practitioner) use ($specialty, $specialty_id) {
            $period_start = $this->faker->dateTimeBetween('-20 years', '-5 years')->format('Y-m-d');
            $period_end = $this->faker->dateTimeBetween($period_start, '+8 years');
            $medical_speciality = MedicalSpeciality::find($specialty_id);

            return $practitioner->qualifications()->create([
                'code' => $medical_speciality->id,
                'system' => 'http://terminology.hl7.org/CodeSystem/v2-0360',
                'display' => $specialty,
                'period_start' => $period_start,
                'period_end' => $period_end,
                'medical_speciality_id' => $specialty_id,
                'default' => 1,
            ]);
        });
    }

    public function configure()
    {
        return $this->afterCreating(function (Practitioner $practitioner) {
            // Crear usuario asociado si no existe
            if (! $practitioner->user) {

                $user = User::factory()
                    ->asDoctor()
                    ->create([
                        'first_name' => $practitioner->given_name,
                        'last_name' => $practitioner->family_name,
                        'email' => $practitioner->email,
                    ]);

                $practitioner->user()->associate($user);
                if ($practitioner->save()) {
                    $client = Client::where('id', '>', 1)->inRandomOrder()->take(1)->first();
                    UserClient::create([
                        'user_id' => $user->id,
                        'client_id' => $client->id,
                    ]);
                }
            }
        });
    }

    private function getIdPattern($id_type)
    {
        switch ($id_type) {
            case 'CC': // Cédula de Ciudadanía (Panamá): 8-123-456 o PE-123-456
                return '/^[A-Z]*[0-9]+-[0-9]+-[0-9]+$/';
            case 'CE': // Cédula Extranjera: Similar a CC
                return '/^[A-Z]*[0-9]+-[0-9]+-[0-9]+$/';
            case 'PA': // Pasaporte: N1234567
                return '/^[A-Z0-9-]{5,20}$/';
            case 'PT': // Permiso Temporal: Formato flexible
                return '/^[A-Z0-9-]{8,15}$/';
            case 'SS': // Seguro Social: XXX-XX-XXXX
                return '/^\d{3}-?\d{2}-?\d{4}$/';
            default:
                return '/^[A-Z0-9-]{5,20}$/'; // Universal para cualquier tipo
        }
    }
}
