<?php

namespace App\Http\Resources\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceRequestResource extends JsonResource
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
            'code' => $this->code,
            'display' => $this->service_type,
            'description' => $this->cpt->description_es,
            'requesterName' => $this->practitioner->name,
            'authoredOn' => Carbon::parse($this->authored_on)->format('d-m-Y'),
            'category' => $this->category,
            'quantity' => $this->quantity,
            'subject' => $this->subject,
            'occurrence_date' => Carbon::parse($this->occurrence_date)->format('d-m-Y'),
            'requester_reference' => $this->requester_reference,
            'performer_type' => $this->performer_type,
            'reason_code' => $this->reason_code,
            'note' => $this->note,
            'practitioner' => $this->whenLoaded('practitioner', function () {
                return [
                    'id' => $this->practitioner->id,
                    'name' => $this->practitioner->name,
                ];
            }),
            'encounter_id' => $this->encounter_id,
            'created_at' => Carbon::parse($this->created_at)->format('d-m-Y'),
            'updated_at' => Carbon::parse($this->updated_at)->format('d-m-Y'),
        ];
    }
}
