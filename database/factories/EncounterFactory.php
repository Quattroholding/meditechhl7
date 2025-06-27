<?php
namespace Database\Factories;

use App\Models\{Condition,
    CptCode,
    Encounter,
    MedicalSpeciality,
    Medicine,
    Patient,
    Practitioner,
    Appointment,
    ClinicalObservationType,
    Medication,
    PresentIllness,
    PresentIllnesType,
    Procedure,
    MedicationRequest,
    Referral,
    ServiceCatalog,
    ChargeItem};
use Illuminate\Database\Eloquent\Factories\Factory;

class EncounterFactory extends Factory
{
    protected $model = Encounter::class;

    public function definition()
    {
        $appointment = Appointment::whereIn('status',['fulfilled'])->inRandomOrder()->limit(1)->first();

        $startDate = $this->faker->dateTimeBetween($appointment->end, '+30 days');
        // Duración de la cita entre 15 y 60 minutos
        $duration = $this->faker->numberBetween(15, 60);
        $endDate = (clone $startDate)->modify("+{$duration} minutes");

        $type ='4525004'; // consulta de medicina general
        if($appointment->medical_speciality_id <> '50')   $type ='26172008'; // consulta de especialidad

        return [
            'fhir_id' => 'encounter-' . $this->faker->uuid(),
            'patient_id' => $appointment->patient_id,
            'practitioner_id' => $appointment->practitioner_id,
            'appointment_id' => $appointment->id,
            'identifier' => 'ENC-' . $this->faker->unique()->numerify('#######'),
            'status' => $this->faker->randomElement(['planned', 'arrived', 'in-progress', 'finished', 'cancelled']),
            'class' => $this->faker->randomElement(['AMB', 'EMER', 'FLD', 'HH', 'IMP']),
            'type' => $type,
            'priority' => $this->faker->randomElement(['routine', 'urgent', 'asap', 'stat']),
            'reason' => $this->faker->sentence(), //CHIEF COMPLAINT
            'start' => $startDate,
            'end' => $endDate,
            'medical_speciality_id'=>$appointment->medical_speciality_id,
        ];
    }

    // States para tipos de encuentros
    public function ambulatory()
    {
        return $this->state(function (array $attributes) {
            return [
                'class' => 'AMB',
                'type' => 'AMB',
            ];
        });
    }

    public function emergency()
    {
        return $this->state(function (array $attributes) {
            return [
                'class' => 'EMER',
                'type' => 'EMER',
                'priority' => 'stat',
            ];
        });
    }

    public function inpatient()
    {
        return $this->state(function (array $attributes) {
            return [
                'class' => 'IMP',
                'type' => 'IMP',
            ];
        });
    }

    public function finished()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'finished',
            ];
        });
    }

    // Métodos para relaciones

    public function withDiagnoses($count = 1, $useExistingConditions = true)
    {
        return $this->afterCreating(function (Encounter $encounter) use ($count, $useExistingConditions) {
            for ($i = 0; $i < $count; $i++) {
                // Usar condición existente o crear una nueva
                if ($useExistingConditions && Condition::exists()) {
                    $condition = Condition::inRandomOrder()->first();
                } else {
                    $condition = Condition::factory()->create([
                        'patient_id' => $encounter->patient_id,
                        'practitioner_id' => $encounter->practitioner_id,
                    ]);
                }

                // Crear el diagnóstico asociado al encounter
                $encounter->diagnoses()->create([
                    'encounter_id' => $encounter->id,
                    'condition_id' => $condition->id,
                    'rank' => $i + 1,
                    'use' => $this->faker->randomElement(['principal', 'secundario', 'admision']),
                    'note' => $this->faker->sentence(),
                ]);
            }
        });
    }
    public function withVitalSigns($count = 3)
    {
        return $this->afterCreating(function (Encounter $encounter) use ($count) {
            $vitalSignTypes = ClinicalObservationType::vitalSigns()->get();

            foreach ($vitalSignTypes as $type) {
                $value = $this->generateVitalSignValue($type);

                $encounter->vitalSigns()->create([
                    'fhir_id' => 'observation-' . $this->faker->uuid(),
                    'code' => $type->code,
                    'status' => 'final',
                    'category' => 'vital-signs',
                    'value' => $value,
                    'unit' => $type->default_unit,
                    'effective_date' => $this->faker->dateTimeBetween($encounter->start, $encounter->end),
                    'issued_date' => $this->faker->dateTimeBetween($encounter->start, $encounter->end),
                    'patient_id' => $encounter->patient_id,
                    'practitioner_id' => $encounter->practitioner_id,
                ]);
            }
        });
    }

    public function withPhysicalExams($count = 2)
    {
        return $this->afterCreating(function (Encounter $encounter) use ($count) {
            $examTypes = ClinicalObservationType::physicalExams()->inRandomOrder()->limit($count)->get();

            foreach ($examTypes as $type) {
                $findings = $this->generateExamFindings($type);

                $encounter->physicalExams()->create([
                    'fhir_id' => 'observation-' . $this->faker->uuid(),
                    'code' => $type->code,
                    'status' => 'final',
                    'category' => 'exam',
                    'description' => $type->name . ' realizado durante la consulta',
                    'finding' => $findings,
                    'effective_date' => $this->faker->dateTimeBetween($encounter->start, $encounter->end),
                    'patient_id' => $encounter->patient_id,
                    'practitioner_id' => $encounter->practitioner_id,
                ]);
            }
        });
    }

    public function withPresentIllness()
    {
        return $this->afterCreating(function (Encounter $encounter) {

            $location = PresentIllnesType::whereType('location')->inRandomOrder()->limit(1)->first();
            $severity = PresentIllnesType::whereType('severity')->inRandomOrder()->limit(1)->first();
            $timing = PresentIllnesType::whereType('timing')->inRandomOrder()->limit(1)->first();
            $duration= PresentIllnesType::whereType('duration')->inRandomOrder()->limit(1)->first();

            $encounter->presentIllnesses()->create([
                'fhir_id' => 'condition-' . $this->faker->uuid(),
                'description' => $this->faker->paragraph(1),
                'location' =>$location->value,
                'severity' => $severity->value,
                'duration' => $duration->value,
                'timing' =>$timing->value,
                'onset' => $this->faker->randomElement(['sudden', 'acute', 'gradual']),
                'aggravating_factors' =>  $this->faker->paragraph(3),
                'alleviating_factors' =>  $this->faker->paragraph(3),
                'associated_symptoms' =>  $this->faker->paragraph(3),
                'patient_id' => $encounter->patient_id,
                'practitioner_id' => $encounter->practitioner_id,
            ]);
        });
    }

    public function withProcedures($count = 1)
    {
        return $this->afterCreating(function (Encounter $encounter) use ($count) {

            for ($i = 0; $i < $count; $i++) {
                $examTypes = CptCode::inRandomOrder()->limit(1)->first();

                $encounter->procedures()->create([
                    'fhir_id' => 'procedure-' . $this->faker->uuid(),
                    'code' => $examTypes->code,
                    'identifier'=>$examTypes->type,
                    'status' => $this->faker->randomElement(['preparation', 'in-progress', 'not-done', 'completed']),
                    'performed_date' => $this->faker->dateTimeBetween($encounter->start, $encounter->end),
                    'reason' => $this->faker->sentence(),
                    'patient_id' => $encounter->patient_id,
                    'practitioner_id' => $encounter->practitioner_id,
                ]);
            }
        });
    }

    public function withServiceRequests($count = 1)
    {
        return $this->afterCreating(function (Encounter $encounter) use ($count) {

            for ($i = 0; $i < $count; $i++) {
                $examTypes = CptCode::inRandomOrder()->limit(1)->first();

                $encounter->serviceRequests()->create([
                    'fhir_id' => 'servicerequest-' . $this->faker->uuid(),
                    'patient_id' => $encounter->patient_id,
                    'practitioner_id' => $encounter->practitioner_id,
                    'status' => $this->faker->randomElement(['draft', 'active', 'on-hold', 'completed']),
                    'intent' => $this->faker->randomElement(['order', 'original-order', 'reflex-order']),
                    'priority' => $this->faker->randomElement(['routine', 'urgent', 'asap', 'stat']),
                    'do_not_perform' => $this->faker->boolean(10),
                    'code' =>$examTypes->code,
                    'service_type'=>$examTypes->type,
                    'code_system' => 'https://www.ama-assn.org/practice-management/cpt',
                    'code_display' => $this->faker->sentence(3),
                    'quantity' => $this->faker->numberBetween(1, 3),
                    'occurrence_start' =>$encounter->start,
                    'body_site' => [
                        'code' => $this->faker->randomElement(['HEAD', 'CHEST', 'ABDOMEN']),
                        'display' => $this->faker->word
                    ],
                    'note' => $this->faker->optional()->sentence,
                    'authored_on' =>$encounter->start,
                    'last_updated' =>$encounter->start,
                ]);
            }
        });
    }

    public function withMedicationRequests($count = 2)
    {
        return $this->afterCreating(function (Encounter $encounter) use ($count) {

            $medications = Medicine::inRandomOrder()->limit($count)->get();

            /*if ($medications->isEmpty()) {
                $medications = Medicine::factory()->count($count)->create();
            }*/

            foreach ($medications as $medication) {

                $dosage_instruction = $this->generateDosageInstruction();

                $medArr = [
                    'fhir_id' => 'medicationrequest-' . $this->faker->uuid(),
                    'identifier' => 'RX-' . $this->faker->unique()->numerify('#######'),
                    'status' => 'active',
                    'intent' => 'order',
                    'medication_id' => $medication->id,
                    'dosage_instruction' =>$dosage_instruction,
                    'dosage_text'=>$dosage_instruction['text'],
                    'quantity'=> $this->faker->numberBetween(1, 30),
                    'refills'=> $this->faker->numberBetween(1, 5),
                    'valid_from' => now(),
                    'valid_to' => now()->addDays($this->faker->numberBetween(7, 30)),
                    'patient_id' => $encounter->patient_id,
                    'practitioner_id' => $encounter->practitioner_id,
                ];
                $encounter->medicationRequests()->create($medArr);
            }
        });
    }

    public function withReferral()
    {
        return $this->afterCreating(function (Encounter $encounter) {

            $specialty = MedicalSpeciality::inRandomOrder()->limit(1)->first();
            $referredTo = Practitioner::factory()->specialist($specialty->name,$specialty->id);

            $encounter->referrals()->create([
                'fhir_id' => 'servicerequest-' . $this->faker->uuid(),
                'identifier' => 'REF-' . $this->faker->unique()->numerify('#######'),
                'status' => 'active',
                'intent' => 'order',
                'code' => $specialty->id,
                'reason' => $this->faker->sentence(),
                'description' => "Referencia a especialista en $specialty->name",
                'occurrence_date' => $this->faker->dateTimeBetween($encounter->start, '+30 days'),
                'referred_to_id' => $referredTo->id,
                'patient_id' => $encounter->patient_id,
                'practitioner_id' => $encounter->practitioner_id,
            ]);
        });
    }

    public function withCompleteEncounter()
    {
        return $this->afterCreating(function (Encounter $encounter) {

            // Signos vitales (3-5)
            $this->withVitalSigns(rand(3, 5))->create([], $encounter);

            // Exámenes físicos (1-3)
            $this->withPhysicalExams(rand(1, 3))->create([], $encounter);

            // Diagnósticos (1-3)
            $this->withDiagnoses(rand(1, 3))->create([], $encounter);

            // Historia de la enfermedad actual
            $this->withPresentIllness()->create([], $encounter);

            // Solicitud de servicios (0-2)
            if ($this->faker->boolean(80)) {
                $this->withServiceRequests(rand(0, 3))->create([], $encounter);
            }

            // Procedimientos (0-2)
            if ($this->faker->boolean(50)) {
                $this->withProcedures(rand(0, 2))->create([], $encounter);
            }

            // Medicamentos (0-3)
            if ($this->faker->boolean(60)) {
                $this->withMedicationRequests(rand(0, 3))->create([], $encounter);
            }

            // Referencia a especialista (20% de probabilidad)
            if ($this->faker->boolean(20)) {
                $this->withReferral()->create([], $encounter);
            }
        });
    }

    // Métodos auxiliares
    protected function generateVitalSignValue(ClinicalObservationType $type)
    {
        $isNormal = $this->faker->boolean(80);

        if ($isNormal) {
            return $this->faker->randomFloat(2, $type->min_normal_value, $type->max_normal_value);
        }

        if ($this->faker->boolean()) {
            return $this->faker->randomFloat(2, $type->max_normal_value + 1, $type->max_normal_value * 1.5);
        }

        return $this->faker->randomFloat(2, $type->min_normal_value * 0.5, $type->min_normal_value - 1);
    }

    protected function generateExamFindings(ClinicalObservationType $type)
    {
        if ($this->faker->boolean(70) || empty($type->possible_abnormalities)) {
            return ['text' => 'Normal'];
        }

        $selectedAbnormalities = $this->faker->randomElements(
            array_keys($type->possible_abnormalities),
            rand(1, min(3, count($type->possible_abnormalities))));

        $findings = [];
        foreach ($selectedAbnormalities as $abnormality) {
            $detail = is_array($type->possible_abnormalities[$abnormality])
                ? $this->faker->randomElement($type->possible_abnormalities[$abnormality])
                : $type->possible_abnormalities[$abnormality];

            $findings[$abnormality] = $detail;
        }

        return $findings;
    }

    protected function generateDosageInstruction()
    {
        $frequencies = ['cada 8 horas', 'cada 12 horas', 'diario', 'en la mañana', 'en la noche'];
        $routes = ['oral', 'sublingual', 'tópico', 'intramuscular', 'subcutáneo'];
        $durations = ['por 7 días', 'por 10 días', 'por 2 semanas', 'hasta terminarlo', 'según sea necesario'];

        return [
            'text' => $this->faker->randomElement([1, 2]) . ' ' . $this->faker->randomElement(['tableta', 'cápsula', 'aplicación']) .
                ' ' . $this->faker->randomElement($frequencies) .
                ' ' . $this->faker->randomElement($routes) .
                ' ' . $this->faker->randomElement($durations),
            'route' => $this->faker->randomElement($routes),
            'frequency' => $this->faker->randomElement($frequencies),
            'duration' => $this->faker->randomElement($durations)
        ];
    }

    /**
     * Create services (ChargeItems) for the encounter from the ServiceCatalog
     */
    public function withServices($count = null)
    {
        return $this->afterCreating(function (Encounter $encounter) use ($count) {
            // Get available services for this practitioner
            $availableServices = ServiceCatalog::where('practitioner_id', $encounter->practitioner_id)
                ->where('is_active', true)
                ->get();

            // If no practitioner-specific services, get common services for this client
            if ($availableServices->isEmpty()) {
                $availableServices = ServiceCatalog::where('client_id', $encounter->client_id)
                    ->whereNull('practitioner_id')
                    ->where('is_active', true)
                    ->get();
            }

            // If still no services, skip
            if ($availableServices->isEmpty()) {
                return;
            }

            // Determine how many services to add
            $serviceCount = $count ?? $this->faker->numberBetween(1, min(4, $availableServices->count()));

            // Randomly select services
            $selectedServices = $availableServices->random(min($serviceCount, $availableServices->count()));

            foreach ($selectedServices as $service) {
                $quantity = $this->faker->numberBetween(1, 2);
                $unitPrice = $service->base_price;
                $totalPrice = $quantity * $unitPrice;

                // Get client_id from practitioner's user clients
                $clientId = $encounter->practitioner->user->clients()->first()->id ?? $service->client_id;

                ChargeItem::create([
                    'fhir_id' => 'chargeitem-' . $this->faker->uuid(),
                    'identifier' => 'CHG-' . $this->faker->unique()->numerify('#######'),
                    'status' => $this->faker->randomElement(['billable', 'not-billable', 'aborted']),
                    'code' => [
                        'text' => $service->name,
                        'coding' => [[
                            'code' => $service->cpt_code,
                            'system' => 'http://www.ama-assn.org/go/cpt',
                            'display' => $service->name
                        ]]
                    ],
                    'patient_id' => $encounter->patient_id,
                    'encounter_id' => $encounter->id,
                    'performer_practitioner_id' => $encounter->practitioner_id,
                    'service_catalog_id' => $service->id,
                    'quantity' => $quantity,
                    'unit_price_value' => $unitPrice,
                    'unit_price_currency' => $service->currency,
                    'occurrence_date_time' => $this->faker->dateTimeBetween($encounter->start, $encounter->end),
                    'note' => $this->faker->optional(30)->sentence(),
                    'client_id' => $clientId,
                    'created_by' => $encounter->practitioner->user_id ?? 1,
                ]);
            }
        });
    }

    /**
     * Create services with specific types
     */
    public function withConsultationServices($count = 1)
    {
        return $this->afterCreating(function (Encounter $encounter) use ($count) {
            $consultationServices = ServiceCatalog::where('practitioner_id', $encounter->practitioner_id)
                ->where('service_type', 'consultation')
                ->where('is_active', true)
                ->get();

            if ($consultationServices->isEmpty()) {
                $consultationServices = ServiceCatalog::where('client_id', $encounter->client_id)
                    ->whereNull('practitioner_id')
                    ->where('service_type', 'consultation')
                    ->where('is_active', true)
                    ->get();
            }

            if ($consultationServices->isNotEmpty()) {
                $selectedServices = $consultationServices->random(min($count, $consultationServices->count()));

                foreach ($selectedServices as $service) {
                    // Get client_id from practitioner's user clients
                    $clientId = $encounter->practitioner->user->clients()->first()->id ?? $service->client_id;

                    ChargeItem::create([
                        'fhir_id' => 'chargeitem-' . $this->faker->uuid(),
                        'identifier' => 'CHG-' . $this->faker->unique()->numerify('#######'),
                        'status' => 'billable',
                        'code' => [
                            'text' => $service->name,
                            'coding' => [[
                                'code' => $service->cpt_code,
                                'system' => 'http://www.ama-assn.org/go/cpt',
                                'display' => $service->name
                            ]]
                        ],
                        'patient_id' => $encounter->patient_id,
                        'encounter_id' => $encounter->id,
                        'performer_practitioner_id' => $encounter->practitioner_id,
                        'service_catalog_id' => $service->id,
                        'quantity' => 1,
                        'unit_price_value' => $service->base_price,
                        'unit_price_currency' => $service->currency,
                        'occurrence_date_time' => $encounter->start,
                        'client_id' => $clientId,
                        'created_by' => $encounter->practitioner->user_id ?? 1,
                    ]);
                }
            }
        });
    }

    /**
     * Create procedure services
     */
    public function withProcedureServices($count = null)
    {
        return $this->afterCreating(function (Encounter $encounter) use ($count) {
            $procedureServices = ServiceCatalog::where('practitioner_id', $encounter->practitioner_id)
                ->where('service_type', 'procedure')
                ->where('is_active', true)
                ->get();

            if ($procedureServices->isEmpty()) {
                $procedureServices = ServiceCatalog::where('client_id', $encounter->client_id)
                    ->whereNull('practitioner_id')
                    ->where('service_type', 'procedure')
                    ->where('is_active', true)
                    ->get();
            }

            if ($procedureServices->isNotEmpty()) {
                $serviceCount = $count ?? $this->faker->numberBetween(0, 2);
                if ($serviceCount > 0) {
                    $selectedServices = $procedureServices->random(min($serviceCount, $procedureServices->count()));

                    foreach ($selectedServices as $service) {
                        $quantity = $this->faker->numberBetween(1, 2);
                        $totalPrice = $quantity * $service->base_price;

                        // Get client_id from practitioner's user clients
                        $clientId = $encounter->practitioner->user->clients()->first()->id ?? $service->client_id;

                        ChargeItem::create([
                            'fhir_id' => 'chargeitem-' . $this->faker->uuid(),
                            'identifier' => 'CHG-' . $this->faker->unique()->numerify('#######'),
                            'status' => 'billable',
                            'code' => [
                                'text' => $service->name,
                                'coding' => [[
                                    'code' => $service->cpt_code,
                                    'system' => 'http://www.ama-assn.org/go/cpt',
                                    'display' => $service->name
                                ]]
                            ],
                            'patient_id' => $encounter->patient_id,
                            'encounter_id' => $encounter->id,
                            'performer_practitioner_id' => $encounter->practitioner_id,
                            'service_catalog_id' => $service->id,
                            'quantity' => $quantity,
                            'unit_price_value' => $service->base_price,
                            'unit_price_currency' => $service->currency,
                            'occurrence_date_time' => $this->faker->dateTimeBetween($encounter->start, $encounter->end),
                            'client_id' => $clientId,
                            'created_by' => $encounter->practitioner->user_id ?? 1,
                        ]);
                    }
                }
            }
        });
    }

    /**
     * Update withCompleteEncounter to include services
     */
    public function withCompleteEncounterAndServices()
    {
        return $this->afterCreating(function (Encounter $encounter) {
            // Add all existing encounter elements
            $this->withVitalSigns(rand(3, 5))->create([], $encounter);
            $this->withPhysicalExams(rand(1, 3))->create([], $encounter);
            $this->withDiagnoses(rand(1, 3))->create([], $encounter);
            $this->withPresentIllness()->create([], $encounter);

            if ($this->faker->boolean(80)) {
                $this->withServiceRequests(rand(0, 3))->create([], $encounter);
            }

            if ($this->faker->boolean(50)) {
                $this->withProcedures(rand(0, 2))->create([], $encounter);
            }

            if ($this->faker->boolean(60)) {
                $this->withMedicationRequests(rand(0, 3))->create([], $encounter);
            }

            if ($this->faker->boolean(20)) {
                $this->withReferral()->create([], $encounter);
            }

            // Add services (ChargeItems) for billing
            $this->withConsultationServices(1)->create([], $encounter);

            if ($this->faker->boolean(60)) {
                $this->withProcedureServices()->create([], $encounter);
            }

            if ($this->faker->boolean(40)) {
                $this->withServices(rand(1, 2))->create([], $encounter);
            }
        });
    }
}
