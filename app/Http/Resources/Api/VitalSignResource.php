<?php

namespace App\Http\Resources\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VitalSignResource extends JsonResource
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
            'status' => $this->status,
            'code' => $this->code,
            'category' => $this->category,
            'description' => $this->observationType->name,
            'value' => $this->value,
            'unit' => $this->observationType->default_unit,
            'min_normal_value'=>$this->observationType->min_normal_value,
            'max_normal_value'=>$this->observationType->max_normal_value,
            'interpretation' => $this->interpretation,
            'note' => $this->note,
            'effective_date' => Carbon::parse($this->effective_date)->format('d-m-Y'),
            'issued_date' => Carbon::parse($this->issued_date)->format('d-m-Y'),
            'body_site' => $this->body_site,
            'method' => $this->method,
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
