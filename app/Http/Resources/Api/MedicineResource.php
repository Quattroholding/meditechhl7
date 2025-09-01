<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicineResource extends JsonResource
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
            'ndc_code' => $this->ndc_code,
            'home_name' => $this->home_name,
            'generic_name' => $this->generic_name,
            'full_name' => $this->full_name,
            'type' => $this->type,
            'mgs' => $this->mgs,
            'mgs_type' => $this->mgs_type,
            'concentration' => $this->concentration,
            'active' => $this->active,
            'narcotic' => $this->narcotic,
            'price' => $this->price,
            'product_type' => $this->product_type,
            'usage_indications' => $this->usage_indications,
            'porpuse' => $this->porpuse,
            'indication_and_usage' => $this->indication_and_usage,
            'source' => $this->source,
            'client' => $this->whenLoaded('client', function () {
                return [
                    'id' => $this->client->id,
                    'name' => $this->client->name,
                ];
            }),
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                ];
            }),
            'components' => $this->whenLoaded('components', function () {
                return $this->components->map(function ($component) {
                    return [
                        'id' => $component->id,
                        'name' => $component->name,
                        'concentration' => $component->concentration,
                    ];
                });
            }),
            'medication_requests_count' => $this->whenCounted('medicationRequests'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
