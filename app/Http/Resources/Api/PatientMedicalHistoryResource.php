<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientMedicalHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'patient_info' => [
                'id' => $this->id,
                'fhir_id' => $this->fhir_id,
                'identifier' => $this->identifier,
                'identifier_type' => $this->identifier_type,
                'name' => $this->name,
                'given_name' => $this->given_name,
                'family_name' => $this->family_name,
                'gender' => $this->gender,
                'birth_date' => $this->birth_date,
                'age' => $this->age,
                'blood_type' => $this->blood_type,
                'marital_status' => $this->marital_status,
                'phone' => $this->phone,
                'email' => $this->email,
            ],
            'medical_history' => MedicalHistoryResource::collection($this->medicalHistories),
            'encounters' => EncounterResource::collection($this->encounters()->with(['practitioner', 'serviceRequests', 'vitalSigns'])->get()),
            'conditions' => ConditionResource::collection($this->conditions),
            'vital_signs' => VitalSignResource::collection($this->vitalSigns),
            'physical_exams' => PhysicalExamResource::collection($this->physicalExams),
            'medications' => MedicationRequestResource::collection($this->medicationRequests),
            'services' => ServiceRequestResource::collection($this->serviceRequests),
            'procedures' => ProcedureResource::collection($this->procedures),
            'allergies' => MedicalHistoryResource::collection($this->allergies),
            'last_updated' => $this->updated_at,
        ];
    }
}
