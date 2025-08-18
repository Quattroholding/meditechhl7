<?php

namespace App\Http\Resources\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicalHistoryResource extends JsonResource
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
            'category' => $this->category,
            'title' => $this->title,
            'description' => $this->description,
            'recorded_date' => $this->recorded_date,
            'occurrence_date' => $this->occurrence_date,
            'clinical_status' => $this->clinical_status,
            'verification_status' => $this->verification_status,
            'created_at' => Carbon::parse($this->created_at)->format('d-m-Y'),
            'updated_at' => Carbon::parse($this->updated_at)->format('d-m-Y'),
        ];
    }
}
