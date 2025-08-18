<?php

namespace App\Http\Resources\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PhysicalExamResource extends JsonResource
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
            'category' => $this->category,
            'code' => $this->code,
            'description' => $this->observationType->name,
            'method' => $this->method,
            'conclusion' => $this->conclusion,
            'effective_date' => Carbon::parse($this->effective_date)->format('d-m-Y'),
            'body_site' => $this->body_site,
            'finding' => $this->finding,
            'media' => $this->media,
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
