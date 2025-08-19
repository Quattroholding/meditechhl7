<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Patient;
use App\Models\PatientClient;
use App\Models\User;
use App\Models\UserClient;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Patient>
 */
class PatientFactory extends Factory
{
    protected $model = Patient::class;

    public function definition()
    {
        $gender = $this->faker->randomElement(['male', 'female']);
        $givenName = $gender === 'male' ? $this->faker->firstNameMale : $this->faker->firstNameFemale;
        $id_type = $this->faker->randomElement(['PA', 'CC', 'SS', 'CE', 'PT']);
        $identifier = $this->faker->unique()->regexify($this->getIdPattern($id_type));

        return [
            'fhir_id' => 'patient-'.Str::uuid(),
            'identifier' => $identifier,
            'identifier_type' => $id_type,
            'name' => $givenName.' '.$this->faker->lastName,
            'given_name' => $givenName,
            'family_name' => $this->faker->lastName,
            'gender' => $gender,
            'birth_date' => $this->faker->dateTimeBetween('-80 years', '-18 years')->format('Y-m-d'),
            'deceased' => false,
            'deceased_date' => null,
            'address' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'state' => $this->faker->state,
            'postal_code' => $this->faker->postcode,
            'country' => $this->faker->country,
            'phone' => $this->faker->phoneNumber,
            'whatsapp_phone' => $this->faker->phoneNumber,
            'email' => $this->faker->unique()->safeEmail,
            'marital_status' => $this->faker->randomElement(['Soltero/a', 'Casado/a', 'Divorciado/a', 'Viudo/a']),
            'multiple_birth' => $this->faker->boolean(20),
            'multiple_birth_count' => function (array $attributes) {
                return $attributes['multiple_birth'] ? $this->faker->numberBetween(2, 4) : null;
            },
            'communication' => json_encode([
                'language' => 'es',
                'preferred' => true,
            ]),
        ];
    }

    // States adicionales para casos específicos
    public function deceased()
    {
        return $this->state(function (array $attributes) {
            return [
                'deceased' => true,
                'deceased_date' => $this->faker->dateTimeBetween($attributes['birth_date'], 'now')->format('Y-m-d'),
            ];
        });
    }

    public function minor()
    {
        return $this->state(function (array $attributes) {
            return [
                'birth_date' => $this->faker->dateTimeBetween('-17 years', '-1 year')->format('Y-m-d'),
            ];
        });
    }

    public function configure()
    {
        return $this->afterCreating(function (Patient $patient) {
            // Crear usuario asociado si no existe
            if (! $patient->user) {
                $user = User::factory()
                    ->asPatient()
                    ->create([
                        'first_name' => $patient->given_name,
                        'last_name' => $patient->family_name,
                        'email' => $patient->email,
                    ]);

                $patient->user()->associate($user);
                if ($patient->save()) {
                    $client = Client::where('id', '>', 1)->inRandomOrder()->take(1)->first();
                    UserClient::create([
                        'user_id' => $user->id,
                        'client_id' => $client->id,
                    ]);
                    PatientClient::create([
                        'patient_id' => $patient->id,
                        'client_id' => $client->id,
                    ]);
                }

                $user->default_client_id = $client->id;
                $user->save();
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
