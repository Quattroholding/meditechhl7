<?php

namespace App\Services;

use App\Models\ClinicalObservationType;
use App\Models\Encounter;
use App\Models\EncounterSnapshot;
use App\Models\Icd10Code;
use Illuminate\Support\Facades\Auth;

class EncounterSnapshotService
{
    /**
     * Create a snapshot of the encounter with all related data.
     */
    public function createSnapshot(Encounter $encounter, string $snapshotType = 'initial_finish', ?string $changeSummary = null): EncounterSnapshot
    {
        $snapshotData = $this->captureEncounterData($encounter);
        $version = $this->getNextVersion($encounter);

        return EncounterSnapshot::create([
            'encounter_id' => $encounter->id,
            'version' => $version,
            'snapshot_type' => $snapshotType,
            'snapshot_data' => $snapshotData,
            'change_summary' => $changeSummary,
            'created_by' => Auth::id() ?? $encounter->practitioner->user_id ?? 1,
        ]);
    }

    /**
     * Capture all encounter data including relationships.
     */
    protected function captureEncounterData(Encounter $encounter): array
    {
        // Cargar todas las relaciones necesarias
        $encounter->load([
            'patient',
            'practitioner',
            'appointment',
            'medicalSpeciality',
            'diagnoses.condition',
            'vitalSigns',
            'physicalExams',
            'medicationRequests',
            'presentIllnesses',
            'procedures',
            'referrals',
            'observations',
            'serviceRequests.results',
            'skinLesions.bodyStructure',
            'chargeItems',
            'medicalLeaves',
        ]);

        return [
            'encounter' => [
                'id' => $encounter->id,
                'fhir_id' => $encounter->fhir_id,
                'patient_id' => $encounter->patient_id,
                'practitioner_id' => $encounter->practitioner_id,
                'appointment_id' => $encounter->appointment_id,
                'identifier' => $encounter->identifier,
                'status' => $encounter->getRawOriginal('status'),
                'class' => $encounter->class,
                'type' => $encounter->type,
                'priority' => $encounter->priority,
                'reason' => $encounter->reason,
                'general_note' => $encounter->general_note,
                'follow_up_date' => $encounter->follow_up_date,
                'start' => $encounter->start,
                'end' => $encounter->end,
                'medical_speciality_id' => $encounter->medical_speciality_id,
                'scb_id' => $encounter->scb_id,
                'diagnosis' => $encounter->diagnosis,
                'hospitalization' => $encounter->hospitalization,
                'location' => $encounter->location,
                'extension' => $encounter->extension,
            ],
            'patient' => $encounter->patient ? [
                'id' => $encounter->patient->id,
                'name' => $encounter->patient->name,
                'identifier' => $encounter->patient->identifier,
                'birth_date' => $encounter->patient->birth_date,
            ] : null,
            'practitioner' => $encounter->practitioner ? [
                'id' => $encounter->practitioner->id,
                'name' => $encounter->practitioner->name,
                'licence_code' => $encounter->practitioner->licence_code,
            ] : null,
            'diagnoses' => $encounter->diagnoses->map(function ($diagnosis) {
                $icd10 = null;
                if ($diagnosis->condition && $diagnosis->condition->code) {
                    $icd10 = Icd10Code::where('code', $diagnosis->condition->code)->first();
                }

                return [
                    'id' => $diagnosis->id,
                    'condition_id' => $diagnosis->condition_id,
                    'rank' => $diagnosis->rank,
                    'use' => $diagnosis->use,
                    'note' => $diagnosis->note,
                    'condition' => $diagnosis->condition ? [
                        'code' => $diagnosis->condition->code,
                        'description_es' => $icd10 ? $icd10->description_es : null,
                        'category' => $diagnosis->condition->category,
                        'clinical_status' => $diagnosis->condition->clinical_status,
                        'verification_status' => $diagnosis->condition->verification_status,
                        'severity' => $diagnosis->condition->severity,
                        'onset_date' => $diagnosis->condition->onset_date,
                        'note' => $diagnosis->condition->note,
                    ] : null,
                ];
            })->toArray(),
            'vital_signs' => $encounter->vitalSigns->map(function ($vital) {
                $observationType = ClinicalObservationType::where('code', $vital->code)->first();

                // Use unit from vital sign record, or fallback to default_unit from observation type
                $unit = $vital->unit ?? ($observationType ? $observationType->default_unit : null);

                return [
                    'id' => $vital->id,
                    'code' => $vital->code,
                    'name' => $observationType ? $observationType->name : null,
                    'category' => $vital->category,
                    'value' => $vital->value,
                    'unit' => $unit,
                    'interpretation' => $vital->interpretation,
                    'note' => $vital->note,
                    'effective_date' => $vital->effective_date,
                    'body_site' => $vital->body_site,
                ];
            })->toArray(),
            'physical_exams' => $encounter->physicalExams->map(fn ($exam) => [
                'id' => $exam->id,
                'code' => $exam->code,
                'category' => $exam->category,
                'description' => $exam->description,
                'body_site' => $exam->body_site,
                'conclusion' => $exam->conclusion,
                'effective_date' => $exam->effective_date,
            ])->toArray(),
            'medication_requests' => $encounter->medicationRequests->map(function ($med) {
                // Try to get medication name from different sources
                $medicationName = $med->medication;

                // If medication field is null, try medication2 relationship
                if (! $medicationName && $med->medication_id2) {
                    $medication2 = $med->medication2;
                    if ($medication2) {
                        $medicationName = $medication2->display ?? $medication2->home_name ?? $medication2->generic_name;
                    }
                }

                // If still null, try medicine relationship
                if (! $medicationName && $med->medication_id) {
                    $medicine = $med->medicine;
                    if ($medicine) {
                        $medicationName = $medicine->home_name ?? $medicine->generic_name;
                    }
                }

                return [
                    'id' => $med->id,
                    'medication' => $medicationName,
                    'medication_id' => $med->medication_id,
                    'medication_id2' => $med->medication_id2,
                    'status' => $med->getRawOriginal('status'),
                    'intent' => $med->getRawOriginal('intent'),
                    'priority' => $med->getRawOriginal('priority'),
                    'reason' => $med->reason,
                    'dosage_instruction' => $med->dosage_instruction,
                    'dosage_text' => $med->dosage_text,
                    'route' => $med->route,
                    'frequency' => $med->frequency,
                    'quantity' => $med->quantity,
                    'refills' => $med->refills,
                    'duration' => $med->duration,
                    'valid_from' => $med->valid_from,
                    'valid_to' => $med->valid_to,
                    'note' => $med->note,
                ];
            })->toArray(),
            'present_illness' => $encounter->presentIllnesses ? [
                'id' => $encounter->presentIllnesses->id,
                'description' => $encounter->presentIllnesses->description,
                'location' => $encounter->presentIllnesses->location,
                'locations' => $encounter->presentIllnesses->locations,
                'severity' => $encounter->presentIllnesses->severity,
                'duration' => $encounter->presentIllnesses->duration,
                'timing' => $encounter->presentIllnesses->timing,
                'onset' => $encounter->presentIllnesses->onset,
                'onset_date' => $encounter->presentIllnesses->onset_date,
                'aggravating_factors' => $encounter->presentIllnesses->aggravating_factors,
                'alleviating_factors' => $encounter->presentIllnesses->alleviating_factors,
                'associated_symptoms' => $encounter->presentIllnesses->associated_symptoms,
            ] : null,
            'procedures' => $encounter->procedures->map(fn ($procedure) => [
                'id' => $procedure->id,
                'code' => $procedure->code,
                'status' => $procedure->getRawOriginal('status'),
                'reason' => $procedure->reason,
                'performed_date' => $procedure->performed_date,
                'body_site' => $procedure->body_site,
            ])->toArray(),
            'referrals' => $encounter->referrals->map(fn ($referral) => [
                'id' => $referral->id,
                'referred_to_id' => $referral->referred_to_id,
                'status' => $referral->getRawOriginal('status'),
                'intent' => $referral->getRawOriginal('intent'),
                'priority' => $referral->priority,
                'code' => $referral->code,
                'reason' => $referral->reason,
                'description' => $referral->description,
                'occurrence_date' => $referral->occurrence_date,
            ])->toArray(),
            'observations' => $encounter->observations->map(fn ($obs) => [
                'id' => $obs->id,
                'code' => $obs->code,
                'category' => $obs->category,
                'value' => $obs->value,
                'value_string' => $obs->value_string,
                'unit' => $obs->unit,
                'interpretation' => $obs->interpretation,
                'note' => $obs->note,
                'effective_date' => $obs->effective_date,
            ])->toArray(),
            'service_requests' => $encounter->serviceRequests->map(fn ($service) => [
                'id' => $service->id,
                'code' => $service->code,
                'service_type' => $service->service_type,
                'code_display' => $service->code_display,
                'status' => $service->getRawOriginal('status'),
                'intent' => $service->getRawOriginal('intent'),
                'priority' => $service->getRawOriginal('priority'),
                'quantity' => $service->quantity,
                'note' => $service->note,
                'patient_instruction' => $service->patient_instruction,
                'results' => $service->results->map(fn ($result) => [
                    'id' => $result->id,
                    'result_type' => $result->result_type,
                    'code' => $result->code,
                    'file_path' => $result->file_path,
                    'file_name' => $result->file_name,
                    'notes' => $result->notes,
                    'interpretation' => $result->interpretation,
                    'result_date' => $result->result_date,
                ])->toArray(),
            ])->toArray(),
            'skin_lesions' => $encounter->skinLesions->map(fn ($lesion) => [
                'id' => $lesion->id,
                'lesion_type' => $lesion->lesion_type,
                'color' => $lesion->color,
                'size_length_mm' => $lesion->size_length_mm,
                'size_width_mm' => $lesion->size_width_mm,
                'risk_level' => $lesion->risk_level,
                'clinical_notes' => $lesion->clinical_notes,
                'status' => $lesion->status,
            ])->toArray(),
            'charge_items' => $encounter->chargeItems->map(fn ($charge) => [
                'id' => $charge->id,
                'status' => $charge->getRawOriginal('status'),
                'code' => $charge->code,
                'quantity' => $charge->quantity,
                'unit_price_value' => $charge->unit_price_value,
                'cpt_code' => $charge->cpt_code,
                'note' => $charge->note,
            ])->toArray(),
            'medical_leaves' => $encounter->medicalLeaves->map(fn ($leave) => [
                'id' => $leave->id,
                'start_datetime' => $leave->start_datetime,
                'end_datetime' => $leave->end_datetime,
                'total_days' => $leave->total_days,
                'diagnosis' => $leave->diagnosis,
                'status' => $leave->getRawOriginal('status'),
                'notes' => $leave->notes,
            ])->toArray(),
            'snapshot_metadata' => [
                'captured_at' => now(),
                'total_diagnoses' => $encounter->diagnoses->count(),
                'total_vital_signs' => $encounter->vitalSigns->count(),
                'total_medications' => $encounter->medicationRequests->count(),
                'total_service_requests' => $encounter->serviceRequests->count(),
            ],
        ];
    }

    /**
     * Get the next version number for the encounter.
     */
    protected function getNextVersion(Encounter $encounter): int
    {
        $lastSnapshot = $encounter->snapshots()->latest('version')->first();

        return $lastSnapshot ? $lastSnapshot->version + 1 : 1;
    }

    /**
     * Check if encounter has been modified since last snapshot.
     */
    public function hasChangedSinceLastSnapshot(Encounter $encounter): bool
    {
        $lastSnapshot = $encounter->snapshots()->latest('version')->first();

        if (! $lastSnapshot) {
            return false;
        }

        $currentData = $this->captureEncounterData($encounter);
        $lastData = $lastSnapshot->snapshot_data;

        return $this->arraysAreDifferent($currentData, $lastData);
    }

    /**
     * Compare two arrays for differences (recursive).
     */
    protected function arraysAreDifferent(array $array1, array $array2): bool
    {
        return json_encode($array1) !== json_encode($array2);
    }

    /**
     * Get the latest snapshot for an encounter.
     */
    public function getLatestSnapshot(Encounter $encounter): ?EncounterSnapshot
    {
        return $encounter->snapshots()->latest('version')->first();
    }

    /**
     * Get all snapshots for an encounter.
     */
    public function getAllSnapshots(Encounter $encounter)
    {
        return $encounter->snapshots()->orderBy('version', 'desc')->get();
    }
}
