<?php

namespace App\Http\Resources\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicationRequestResource extends JsonResource
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
            'intent' => $this->intent,
            'priority' => $this->priority,
            'reason' => $this->reason,
            'medication_name' => $this->medicine->full_name,
            'dosage_instruction' => $this->dosage_instruction,
            'requester_name' => $this->practitioner->name,
            'authored_on' => Carbon::parse($this->created_at)->format('d-m-Y'),
            'dosage' => $this->dosage_text,
            'route' => $this->route,
            'frequency' => $this->frequency,
            'quantity' => $this->quantity,
            'refills' => $this->refills,
            'valid_from' => $this->valid_from,
            'valid_to' => $this->valid_to,
            'substitution_allowed' => $this->substitution_allowed,
            'note' => $this->note,
            'medicine' => $this->whenLoaded('medicine', function () {
                return [
                    'id' => $this->medicine->id,
                    'name' => $this->medicine->name,
                    'active_ingredient' => $this->medicine->active_ingredient,
                ];
            }),
            'practitioner' => $this->whenLoaded('practitioner', function () {
                return [
                    'id' => $this->practitioner->id,
                    'name' => $this->practitioner->name,
                ];
            }),
            'encounter_id' => $this->encounter_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
