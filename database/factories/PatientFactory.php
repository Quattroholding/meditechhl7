<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\InsuranceCompany;
use App\Models\Patient;
use App\Models\PatientClient;
use App\Models\PatientInsurancePolicy;
use App\Models\PatientRelationship;
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

    public function withoutDependents()
    {
        return $this->afterCreating(function (Patient $patient) {
            // Create user and insurance but skip dependents to prevent infinite recursion
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
                    if ($client) {
                        UserClient::create([
                            'user_id' => $user->id,
                            'client_id' => $client->id,
                        ]);
                        PatientClient::create([
                            'patient_id' => $patient->id,
                            'client_id' => $client->id,
                        ]);
                        $user->default_client_id = $client->id;
                        $user->save();
                    }
                }
            }

            // Only create insurance, skip dependents
            if ($this->faker->boolean(60)) {
                PatientInsurancePolicy::factory()
                    ->primary()
                    ->active()
                    ->forSelf()
                    ->create([
                        'patient_id' => $patient->id,
                    ]);

                if ($this->faker->boolean(10)) {
                    PatientInsurancePolicy::factory()
                        ->secondary()
                        ->active()
                        ->create([
                            'patient_id' => $patient->id,
                        ]);
                }
            }
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

            // Crear pólizas de seguro con 60% de probabilidad
            if ($this->faker->boolean(60)) {
                // Crear al menos una póliza primaria
                $insurace = InsuranceCompany::inRandomOrder()->limit(1)->first();
                PatientInsurancePolicy::factory()
                    ->primary()
                    ->active()
                    ->forSelf()
                    ->create([
                        'patient_id' => $patient->id,
                        'insurance_company_id' => $insurace->id,
                    ]);

                // 10% de probabilidad de tener una segunda póliza
                if ($this->faker->boolean(10)) {

                    $insurace = InsuranceCompany::inRandomOrder()->limit(1)->first();
                    PatientInsurancePolicy::factory()
                        ->secondary()
                        ->active()
                        ->create([
                            'patient_id' => $patient->id,
                            'insurance_company_id' => $insurace->id,
                        ]);
                }
            }

            // Crear dependientes con 25% de probabilidad
            if ($this->faker->boolean(25)) {
                $dependentCount = $this->faker->numberBetween(1, 3);

                for ($i = 0; $i < $dependentCount; $i++) {
                    // Crear un paciente dependiente sin relaciones para evitar recursión infinita
                    $dependentPatient = Patient::factory()->withoutDependents()->create();

                    // Crear la relación bidireccional
                    $relationshipType = $this->faker->randomElement(['child', 'spouse', 'parent']);

                    // Relación del paciente principal hacia el dependiente
                    PatientRelationship::factory()
                        ->{$relationshipType}()
                        ->create([
                            'patient_id' => $patient->id,
                            'related_patient_id' => $dependentPatient->id,
                        ]);

                    // Relación inversa del dependiente hacia el paciente principal
                    $inverseRelationship = $this->getInverseRelationship($relationshipType);
                    PatientRelationship::factory()
                        ->{$inverseRelationship}()
                        ->create([
                            'patient_id' => $dependentPatient->id,
                            'related_patient_id' => $patient->id,
                        ]);

                    // Si el paciente principal tiene seguro, copiar al dependiente con 70% probabilidad
                    if ($patient->insurancePolicies()->exists() && $this->faker->boolean(70)) {
                        $primaryInsurance = $patient->insurancePolicies()->where('priority', 'primary')->first();
                        if ($primaryInsurance) {
                            PatientInsurancePolicy::factory()
                                ->primary()
                                ->active()
                                ->create([
                                    'patient_id' => $dependentPatient->id,
                                    'insurance_company_id' => $primaryInsurance->insurance_company_id,
                                    'subscriber_patient_id' => $patient->id,
                                    'relationship_to_subscriber' => $relationshipType === 'child' ? 'child' :
                                                                   ($relationshipType === 'spouse' ? 'spouse' : 'other'),
                                ]);
                        }
                    }
                }
            }
        });
    }

    /**
     * Get the inverse relationship type for bidirectional relationships
     */
    private function getInverseRelationship($relationshipType)
    {
        switch ($relationshipType) {
            case 'child':
                return 'parent';
            case 'parent':
                return 'child';
            case 'spouse':
                return 'spouse';
            default:
                return 'spouse'; // fallback
        }
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
