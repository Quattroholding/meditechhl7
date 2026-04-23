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
            'referrals.referredTo',
            'referrals.speciality',
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
            'referrals' => $encounter->referrals->map(function ($referral) {
                $data = [
                    'id' => $referral->id,
                    'referred_to_id' => $referral->referred_to_id,
                    'status' => $referral->getRawOriginal('status'),
                    'intent' => $referral->getRawOriginal('intent'),
                    'priority' => $referral->priority,
                    'code' => $referral->code,
                    'reason' => $referral->reason,
                    'description' => $referral->description,
                    'occurrence_date' => $referral->occurrence_date,
                    // Campos de especialista externo
                    'external_specialist_name' => $referral->external_specialist_name,
                    'external_specialist_specialty' => $referral->external_specialist_specialty,
                    'external_specialist_license' => $referral->external_specialist_license,
                    'external_specialist_phone' => $referral->external_specialist_phone,
                    'external_specialist_clinic' => $referral->external_specialist_clinic,
                ];

                // Si es especialista interno, agregar info del practitioner
                if ($referral->referred_to_id && $referral->referredTo) {
                    $data['referred_to_practitioner'] = [
                        'id' => $referral->referredTo->id,
                        'name' => $referral->referredTo->name,
                        'licence_code' => $referral->referredTo->licence_code,
                        'identifier' => $referral->referredTo->identifier,
                        'email' => $referral->referredTo->email,
                        'phone' => $referral->referredTo->phone,
                    ];
                }

                // Agregar info de la especialidad
                if ($referral->speciality) {
                    $data['speciality'] = [
                        'id' => $referral->speciality->id,
                        'name' => $referral->speciality->name,
                        'code' => $referral->speciality->code,
                    ];
                }

                return $data;
            })->toArray(),
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

    /**
     * Compare two snapshots and identify changes.
     *
     * @return array Array with 'added', 'removed', and 'modified' keys for each section
     */
    public function compareSnapshots(?EncounterSnapshot $previousSnapshot, EncounterSnapshot $currentSnapshot): array
    {
        if (! $previousSnapshot) {
            return ['is_first_version' => true];
        }

        $previous = $previousSnapshot->snapshot_data;
        $current = $currentSnapshot->snapshot_data;

        $changes = [
            'is_first_version' => false,
            'encounter' => $this->compareEncounterData($previous['encounter'] ?? [], $current['encounter'] ?? []),
            'diagnoses' => $this->compareArrayById($previous['diagnoses'] ?? [], $current['diagnoses'] ?? []),
            'vital_signs' => $this->compareArrayById($previous['vital_signs'] ?? [], $current['vital_signs'] ?? []),
            'medication_requests' => $this->compareArrayById($previous['medication_requests'] ?? [], $current['medication_requests'] ?? []),
            'present_illness' => $this->comparePresentIllness($previous['present_illness'] ?? null, $current['present_illness'] ?? null),
            'physical_exams' => $this->compareArrayById($previous['physical_exams'] ?? [], $current['physical_exams'] ?? []),
            'procedures' => $this->compareArrayById($previous['procedures'] ?? [], $current['procedures'] ?? []),
            'referrals' => $this->compareArrayById($previous['referrals'] ?? [], $current['referrals'] ?? []),
            'observations' => $this->compareArrayById($previous['observations'] ?? [], $current['observations'] ?? []),
            'service_requests' => $this->compareArrayById($previous['service_requests'] ?? [], $current['service_requests'] ?? []),
            'skin_lesions' => $this->compareArrayById($previous['skin_lesions'] ?? [], $current['skin_lesions'] ?? []),
            'charge_items' => $this->compareArrayById($previous['charge_items'] ?? [], $current['charge_items'] ?? []),
            'medical_leaves' => $this->compareArrayById($previous['medical_leaves'] ?? [], $current['medical_leaves'] ?? []),
        ];

        return $changes;
    }

    /**
     * Compare encounter basic data.
     */
    protected function compareEncounterData(array $previous, array $current): array
    {
        $changes = [];
        $fieldsToCompare = ['status', 'reason', 'general_note', 'follow_up_date', 'diagnosis'];

        foreach ($fieldsToCompare as $field) {
            $prevValue = $previous[$field] ?? null;
            $currValue = $current[$field] ?? null;

            if ($prevValue !== $currValue) {
                $changes[$field] = [
                    'old' => $prevValue,
                    'new' => $currValue,
                ];
            }
        }

        return $changes;
    }

    /**
     * Compare arrays by ID and detect added, removed, and modified items.
     */
    protected function compareArrayById(array $previous, array $current): array
    {
        $previousById = collect($previous)->keyBy('id');
        $currentById = collect($current)->keyBy('id');

        $added = [];
        $removed = [];
        $modified = [];

        // Find added items
        foreach ($currentById as $id => $item) {
            if (! $previousById->has($id)) {
                $added[] = $item;
            }
        }

        // Find removed items
        foreach ($previousById as $id => $item) {
            if (! $currentById->has($id)) {
                $removed[] = $item;
            }
        }

        // Find modified items
        foreach ($currentById as $id => $currentItem) {
            if ($previousById->has($id)) {
                $previousItem = $previousById->get($id);
                if (json_encode($previousItem) !== json_encode($currentItem)) {
                    $modified[] = [
                        'id' => $id,
                        'old' => $previousItem,
                        'new' => $currentItem,
                        'changed_fields' => $this->getChangedFields($previousItem, $currentItem),
                    ];
                }
            }
        }

        return [
            'added' => $added,
            'removed' => $removed,
            'modified' => $modified,
        ];
    }

    /**
     * Compare present illness data.
     */
    protected function comparePresentIllness(?array $previous, ?array $current): array
    {
        if ($previous === null && $current === null) {
            return ['changed' => false];
        }

        if ($previous === null) {
            return ['changed' => true, 'type' => 'added', 'new' => $current];
        }

        if ($current === null) {
            return ['changed' => true, 'type' => 'removed', 'old' => $previous];
        }

        if (json_encode($previous) === json_encode($current)) {
            return ['changed' => false];
        }

        return [
            'changed' => true,
            'type' => 'modified',
            'old' => $previous,
            'new' => $current,
            'changed_fields' => $this->getChangedFields($previous, $current),
        ];
    }

    /**
     * Get list of changed fields between two arrays.
     */
    protected function getChangedFields(array $old, array $new): array
    {
        $changed = [];

        foreach ($new as $key => $value) {
            $oldValue = $old[$key] ?? null;
            if (json_encode($oldValue) !== json_encode($value)) {
                $changed[$key] = [
                    'old' => $oldValue,
                    'new' => $value,
                ];
            }
        }

        return $changed;
    }
}
