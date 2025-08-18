<?php

namespace App\Http\Resources\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProcedureResource extends JsonResource
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
            'category' => $this->category,
            'code' => $this->code,
            'performed_date' => Carbon::parse($this->performed_date)->format('d-m-Y'),
            'location' => $this->location,
            'outcome' => $this->outcome,
            'description' => $this->cpt->description_es,
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
