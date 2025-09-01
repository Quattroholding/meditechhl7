<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConsultingRoomResource extends JsonResource
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
            'name' => $this->name,
            'number' => $this->number,
            'floor' => $this->floor,
            'active' => $this->active,
            'full_name_branch' => $this->full_name_branch,
            'branch' => $this->whenLoaded('branch', function () {
                return [
                    'id' => $this->branch->id,
                    'name' => $this->branch->name,
                    'phone' => $this->branch->phone,
                    'address' => $this->branch->address,
                    'type' => $this->branch->type,
                    'client_id' => $this->branch->client_id,
                ];
            }),
            'branch_name' => $this->branch_name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
