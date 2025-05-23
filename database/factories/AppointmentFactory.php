<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Encounter;
use App\Models\Patient;
use App\Models\Practitioner;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition()
    {
        // Fechas aleatorias en el futuro (próximos 30 días)
        $startDate = $this->faker->dateTimeBetween('now', '+30 days');
        // Duración de la cita entre 15 y 60 minutos
        $duration = $this->faker->numberBetween(15, 60);
        $endDate = (clone $startDate)->modify("+{$duration} minutes");

        $patient = Patient::inRandomOrder()->limit(1)->first();

        $practitioner = Practitioner::inRandomOrder()->limit(1)->first();

        $specility_id=null;

        if($practitioner->qualifications()->first()){
            $specility_id = $practitioner->qualifications()->first()->medical_speciality_id;
        }

        $serviceTypes = [
            'Consulta general',
            'Control de rutina',
            'Examen médico',
            'Consulta especializada',
            'Seguimiento',
            'Urgencia',
            'Vacunación',
            'Chequeo anual',
            'Botox'
        ];

        return [
            'fhir_id' => 'appointment-' . Str::uuid(),
            'patient_id' =>$patient->id,
            'practitioner_id' =>$practitioner->id,
            'identifier' => 'APT-' . $this->faker->unique()->numerify('#######'),
            'status' => $this->faker->randomElement([
                'proposed',
                'pending',
                'booked',
                'arrived',
                'fulfilled',
                'cancelled',
                'noshow'
            ]),
            'service_type' => $this->faker->randomElement($serviceTypes),
            'description' => $this->faker->sentence,
            'start' => $startDate,
            'end' => $endDate,
            'minutes_duration' => $duration,
            'medical_speciality_id'=>$specility_id,
            'participant' => json_encode([
                [
                    'actor' => 'Patient/' . $patient->fhir_id,
                    'required' => 'required',
                    'status' => 'accepted'
                ],
                [
                    'actor' => 'Practitioner/' . $practitioner->fhir_id,
                    'required' => 'required',
                    'status' => 'accepted'
                ]
            ]),
        ];
    }

    // States adicionales para diferentes estados de cita
    public function booked()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'booked',
            ];
        });
    }

    public function cancelled()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'cancelled',
                'description' => 'Cita cancelada: ' . $this->faker->sentence,
            ];
        });
    }

    public function fulfilled()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'fulfilled',
                'description' => 'Consulta realizada: ' . $this->faker->sentence,
            ];
        });
    }

    public function noShow()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'noshow',
                'description' => 'Paciente no se presentó',
            ];
        });
    }

    // State para citas pasadas
    public function past()
    {
        return $this->state(function (array $attributes) {
            $startDate = $this->faker->dateTimeBetween('-6 months', '-1 day');
            $duration = $this->faker->numberBetween(15, 60);
            $endDate = (clone $startDate)->modify("+{$duration} minutes");

            return [
                'start' => $startDate,
                'end' => $endDate,
                'minutes_duration' => $duration,
                'status' => $this->faker->randomElement(['fulfilled', 'cancelled', 'noshow']),
            ];
        });
    }

    // State para citas con especialista
    public function withSpecialist(string $specialty)
    {
        return $this->state(function (array $attributes) use ($specialty) {
            $practitioner = Practitioner::factory()->specialist($specialty)->create();

            return [
                'practitioner_id' => $practitioner->id,
                'service_type' => 'Consulta de ' . $specialty,
                'description' => 'Consulta especializada en ' . $specialty,
            ];
        });
    }

    public function withEncounter()
    {
        return $this->afterCreating(function (Appointment $appointment) {
            if(!Encounter::whereAppointmentId($appointment->id)->first()){
                $encounter = Encounter::factory()
                    ->withVitalSigns()
                    ->withPresentIllness()
                    ->withPhysicalExams()
                    ->withDiagnoses()
                    ->withServiceRequests()
                    ->withMedicationRequests()
                    ->withReferral()
                    ->create([
                        'patient_id' => $appointment->patient_id,
                        'practitioner_id' => $appointment->practitioner_id,
                        'appointment_id'=>$appointment->id,
                        'status' => 'finished',
                        'class' => 'AMB',
                        'type' => 'AMB',
                        'priority'=>'routine']);
            }
        });
    }
}
