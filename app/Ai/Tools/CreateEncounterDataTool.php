<?php

namespace App\Ai\Tools;

use App\Models\Condition;
use App\Models\Encounter;
use App\Models\EncounterDiagnosis;
use App\Models\MedicationRequest;
use App\Models\PhysicalExam;
use App\Models\PresentIllness;
use App\Models\ServiceRequest;
use App\Models\VitalSign;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;

class CreateEncounterDataTool implements Tool
{
    public function name(): string
    {
        return 'create_encounter_data';
    }

    public function description(): string
    {
        return 'Create or update encounter data for different SOAP sections (subjective, objective, assessment, plan).';
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'encounter_id' => $schema->integer()->required()->description('The ID of the encounter to update'),
            'section' => $schema->string()->required()->description('Which SOAP section to update: subjective, objective, assessment, or plan'),
            'data' => $schema->object()->required()->description('Section-specific data structure'),
        ];
    }

    public function handle(Request $request): string
    {
        $input = $request->all();
        try {
            $encounterId = $input['encounter_id'] ?? null;
            $section = $input['section'] ?? null;
            $data = $input['data'] ?? [];

            if (! $encounterId || ! $section) {
                return json_encode([
                    'success' => false,
                    'error' => 'encounter_id and section are required',
                ]);
            }

            $encounter = Encounter::findOrFail($encounterId);

            $created = [];

            switch ($section) {
                case 'subjective':
                    $created = $this->processSubjective($encounter, $data);
                    break;

                case 'objective':
                    $created = $this->processObjective($encounter, $data);
                    break;

                case 'assessment':
                    $created = $this->processAssessment($encounter, $data);
                    break;

                case 'plan':
                    $created = $this->processPlan($encounter, $data);
                    break;
            }

            Log::info("Encounter data processed for section: {$section}", [
                'encounter_id' => $encounterId,
                'section' => $section,
                'items_created' => $created,
            ]);

            return json_encode([
                'success' => true,
                'section' => $section,
                'encounter_id' => $encounterId,
                'items_created' => $created,
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating encounter data: '.$e->getMessage(), [
                'encounter_id' => $input['encounter_id'] ?? null,
                'section' => $input['section'] ?? null,
                'trace' => $e->getTraceAsString(),
            ]);

            return json_encode([
                'success' => false,
                'error' => 'Error creating encounter data: '.$e->getMessage(),
            ]);
        }
    }

    private function processSubjective(Encounter $encounter, array $data): array
    {
        $created = [
            'reason' => 0,
            'present_illness' => 0,
            'general_notes' => 0,
        ];

        // Update reason
        if (isset($data['reason']) && ! empty($data['reason'])) {
            $encounter->reason = $data['reason'];
            $encounter->save();
            $created['reason'] = 1;
        }

        // Create present illness
        if (isset($data['present_illness']) && is_array($data['present_illness'])) {
            foreach ($data['present_illness'] as $illness) {
                try {
                    $description = $illness['description'] ?? $illness['chief_complaint'] ?? 'Reason for visit';

                    PresentIllness::create([
                        'encounter_id' => $encounter->id,
                        'patient_id' => $encounter->patient_id,
                        'practitioner_id' => $encounter->practitioner_id,
                        'description' => $description,
                        'locations' => $illness['locations'] ?? null,
                        'severity' => $illness['severity'] ?? null,
                        'duration' => $illness['duration'] ?? null,
                        'onset_date' => $illness['onset_date'] ?? null,
                        'onset' => $illness['onset'] ?? 'gradual',
                        'progression' => $illness['progression'] ?? null,
                        'associated_symptoms' => $illness['associated_symptoms'] ?? null,
                        'fhir_id' => 'Observation/'.Str::uuid(),
                        'source_system' => 'AI',
                    ]);
                    $created['present_illness']++;
                } catch (\Exception $e) {
                    Log::warning('Failed to create present illness', ['error' => $e->getMessage()]);
                }
            }
        }

        // Update general notes
        if (isset($data['general_notes']) && ! empty($data['general_notes'])) {
            $encounter->general_note = $data['general_notes'];
            $encounter->save();
            $created['general_notes'] = 1;
        }

        return $created;
    }

    private function processObjective(Encounter $encounter, array $data): array
    {
        $created = [
            'vital_signs' => 0,
            'physical_exams' => 0,
        ];

        // Create vital signs
        if (isset($data['vital_signs']) && is_array($data['vital_signs'])) {
            foreach ($data['vital_signs'] as $vital) {
                try {
                    $value = $vital['value'] ?? null;
                    $unit = $vital['unit'] ?? null;
                    $code = $vital['code'] ?? $vital['loinc_code'] ?? 'VITAL-'.Str::random(6);

                    // Handle blood pressure format (e.g., "150/95" -> separate systolic/diastolic)
                    if (is_string($value) && strpos($value, '/') !== false) {
                        $parts = explode('/', $value);
                        if (count($parts) === 2) {
                            $systolic = (float) trim($parts[0]);
                            $diastolic = (float) trim($parts[1]);

                            // Create systolic BP reading
                            VitalSign::create([
                                'encounter_id' => $encounter->id,
                                'patient_id' => $encounter->patient_id,
                                'practitioner_id' => $encounter->practitioner_id,
                                'value' => $systolic,
                                'unit' => $unit ?? 'mmHg',
                                'note' => $vital['notes'] ?? null,
                                'code' => '8480-6', // LOINC for systolic BP
                                'status' => 'final',
                                'category' => 'vital-signs',
                                'effective_date' => $vital['measured_at'] ?? now(),
                                'issued_date' => now(),
                                'fhir_id' => 'Observation/'.Str::uuid(),
                                'source_system' => 'AI',
                            ]);
                            $created['vital_signs']++;

                            // Create diastolic BP reading
                            VitalSign::create([
                                'encounter_id' => $encounter->id,
                                'patient_id' => $encounter->patient_id,
                                'practitioner_id' => $encounter->practitioner_id,
                                'value' => $diastolic,
                                'unit' => $unit ?? 'mmHg',
                                'note' => $vital['notes'] ?? null,
                                'code' => '8462-4', // LOINC for diastolic BP
                                'status' => 'final',
                                'category' => 'vital-signs',
                                'effective_date' => $vital['measured_at'] ?? now(),
                                'issued_date' => now(),
                                'fhir_id' => 'Observation/'.Str::uuid(),
                                'source_system' => 'AI',
                            ]);
                            $created['vital_signs']++;

                            continue;
                        }
                    }

                    // Regular vital sign (not blood pressure)
                    VitalSign::create([
                        'encounter_id' => $encounter->id,
                        'patient_id' => $encounter->patient_id,
                        'practitioner_id' => $encounter->practitioner_id,
                        'value' => $value,
                        'unit' => $unit,
                        'note' => $vital['notes'] ?? null,
                        'code' => $code,
                        'status' => 'final',
                        'category' => 'vital-signs',
                        'effective_date' => $vital['measured_at'] ?? now(),
                        'issued_date' => now(),
                        'fhir_id' => 'Observation/'.Str::uuid(),
                        'source_system' => 'AI',
                    ]);
                    $created['vital_signs']++;
                } catch (\Exception $e) {
                    Log::warning('Failed to create vital sign', ['error' => $e->getMessage(), 'vital' => $vital]);
                }
            }
        }

        // Create physical exams
        if (isset($data['physical_exams']) && is_array($data['physical_exams'])) {
            foreach ($data['physical_exams'] as $exam) {
                try {
                    PhysicalExam::create([
                        'encounter_id' => $encounter->id,
                        'clinical_observation_type_id' => $exam['type_id'] ?? null,
                        'findings' => $exam['findings'] ?? null,
                        'notes' => $exam['notes'] ?? null,
                    ]);
                    $created['physical_exams']++;
                } catch (\Exception $e) {
                    Log::warning('Failed to create physical exam', ['error' => $e->getMessage()]);
                }
            }
        }

        return $created;
    }

    private function processAssessment(Encounter $encounter, array $data): array
    {
        $created = [
            'diagnoses' => 0,
        ];

        // Create diagnoses
        if (isset($data['diagnoses']) && is_array($data['diagnoses'])) {
            foreach ($data['diagnoses'] as $diagnosis) {
                $icd10Code = $diagnosis['icd10_code'] ?? null;
                $note = $diagnosis['note'] ?? null;

                try {
                    if (! $icd10Code) {
                        continue;
                    }

                    // Find or create condition
                    $condition = Condition::where('code', '=', $icd10Code)->first();

                    if (! $condition) {
                        // Create a new condition if it doesn't exist
                        $condition = Condition::create([
                            'code' => $icd10Code,
                            'patient_id' => $encounter->patient_id,
                            'encounter_id' => $encounter->id,
                            'practitioner_id' => $encounter->practitioner_id,
                            'clinical_status' => 'active',
                            'verification_status' => 'provisional',
                            'category' => $diagnosis['category'] ?? 'problem',
                            'note' => $note,
                            'fhir_id' => 'Condition/'.Str::uuid(),
                            'source_system' => 'AI',
                        ]);
                    }

                    if ($condition) {
                        EncounterDiagnosis::create([
                            'encounter_id' => $encounter->id,
                            'condition_id' => $condition->id,
                            'diagnosis_type' => 'primary',
                            'notes' => $note,
                        ]);
                        $created['diagnoses']++;
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to create diagnosis', [
                        'icd10_code' => $icd10Code,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        return $created;
    }

    private function processPlan(Encounter $encounter, array $data): array
    {
        $created = [
            'medications' => 0,
            'service_requests' => 0,
        ];

        // Create medication requests
        if (isset($data['medications']) && is_array($data['medications'])) {
            foreach ($data['medications'] as $med) {
                try {
                    // Combine dosage_text with frequency if both exist
                    $fullDosageText = $med['dosage_text'] ?? null;
                    if ($fullDosageText && ! empty($med['frequency'])) {
                        // Normalize frequency to lowercase for better readability
                        $frequency = strtolower(trim($med['frequency']));
                        $fullDosageText = trim($fullDosageText).' '.$frequency;
                    } elseif (! $fullDosageText && ! empty($med['frequency'])) {
                        $fullDosageText = trim($med['frequency']);
                    }

                    MedicationRequest::create([
                        'encounter_id' => $encounter->id,
                        'patient_id' => $encounter->patient_id,
                        'practitioner_id' => $encounter->practitioner_id,
                        'medication_id2' => $med['medication_id'] ?? null,
                        'medication' => $med['medication_name'] ?? null,
                        'dosage_text' => $fullDosageText,
                        'dosage_instruction' => $med['dosage_instruction'] ?? null,
                        'frequency' => $med['frequency'] ?? null,
                        'quantity' => $med['quantity'] ?? null,
                        'duration' => $med['duration'] ?? null,
                        'duration_type' => $med['duration_type'] ?? 'dias',
                        'note' => $med['notes'] ?? null,
                        'reason' => $med['reason'] ?? null,
                        'status' => 'draft',
                        'intent' => 'order',
                        'priority' => 'routine',
                        'fhir_id' => 'MedicationRequest/'.Str::uuid(),
                        'identifier' => 'MR-'.$encounter->id.'-'.Str::random(8),
                        'valid_from' => now()->toDateString(),
                        'source_system' => 'AI',
                    ]);
                    $created['medications']++;
                } catch (\Exception $e) {
                    Log::warning('Failed to create medication request', ['error' => $e->getMessage()]);
                }
            }
        }

        // Create service requests
        if (isset($data['service_requests']) && is_array($data['service_requests'])) {
            foreach ($data['service_requests'] as $service) {
                try {
                    $code = $service['cpt_code'] ?? $service['code'] ?? 'PROC-'.Str::random(6);

                    ServiceRequest::create([
                        'encounter_id' => $encounter->id,
                        'patient_id' => $encounter->patient_id,
                        'practitioner_id' => $encounter->practitioner_id,
                        'service_type' => $service['service_type'] ?? null,
                        'code' => $code,
                        'code_display' => $service['description'] ?? null,
                        'note' => $service['reason'] ?? $service['notes'] ?? null,
                        'quantity' => $service['quantity'] ?? 1,
                        'status' => 'draft',
                        'intent' => 'order',
                        'priority' => strtolower($service['urgency'] ?? 'routine'),
                        'authored_on' => now(),
                        'last_updated' => now(),
                        'fhir_id' => 'ServiceRequest/'.Str::uuid(),
                        'source_system' => 'AI',
                    ]);
                    $created['service_requests']++;
                } catch (\Exception $e) {
                    Log::warning('Failed to create service request', ['error' => $e->getMessage()]);
                }
            }
        }

        return $created;
    }
}
