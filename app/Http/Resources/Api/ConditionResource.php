<?php

namespace App\Http\Resources\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConditionResource extends JsonResource
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
            'clinical_status' => $this->clinical_status,
            'verification_status' => $this->verification_status,
            'code' => $this->code,
            'description' => $this->icd10Code->description_es,
            'category' => $this->category,
            'severity' => $this->severity,
            'onset_date' => Carbon::parse($this->onset_date)->format('d-m-Y'),
            'abatement_date' => Carbon::parse($this->abatement_date)->format('d-m-Y'),
            'recorded_date' => Carbon::parse($this->recorded_date)->format('d-m-Y'),
            'note' => $this->note,
            'evidence' => $this->evidence,
            'icd10_code' => $this->whenLoaded('icd10Code', function () {
                return [
                    'code' => $this->icd10Code->code,
                    'description' => $this->icd10Code->description,
                ];
            }),
            'practitioner' => $this->whenLoaded('practitioner', function () {
                return [
                    'id' => $this->practitioner->id,
                    'name' => $this->practitioner->name,
                ];
            }),
            'created_at' => Carbon::parse($this->created_at)->format('d-m-Y'),
            'updated_at' => Carbon::parse($this->updated_at)->format('d-m-Y'),
        ];
    }
}
