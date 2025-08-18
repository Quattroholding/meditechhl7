<?php

namespace App\Http\Resources\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EncounterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'fhir_id' => $this->fhir_id,
            'identifier' => $this->identifier,
            'status' => $this->status,
            'class' => $this->class,
            'type' => $this->type,
            'priority' => $this->priority,
            'reason' => $this->reason,
            'start' => Carbon::parse($this->start)->format('d-m-Y H:i'),
            'end' => Carbon::parse($this->end)->format('d-m-Y H:i'),
            'service_type' => $this->appointment->service_type,
            'speciality' => $this->appointment->medicalSpeciality->name,
            'practitioner_name' => $this->practitioner->name,
            'practitioner' => $this->whenLoaded('practitioner', function () {
                return [
                    'id' => $this->practitioner->id,
                    'name' => $this->practitioner->name,
                ];
            }),
            'appointment_id' => $this->appointment_id,
            'medical_speciality_id' => $this->medical_speciality_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
