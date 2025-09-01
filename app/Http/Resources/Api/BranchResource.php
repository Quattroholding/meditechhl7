<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
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
            'phone' => $this->phone,
            'address' => $this->address,
            'type' => $this->type,
            'active' => $this->active,
            'client' => $this->whenLoaded('client', function () {
                return [
                    'id' => $this->client->id,
                    'name' => $this->client->name,
                ];
            }),
            'client_name' => $this->client_name,
            'consulting_rooms' => $this->whenLoaded('consultingRooms', function () {
                return $this->consultingRooms->map(function ($room) {
                    return [
                        'id' => $room->id,
                        'name' => $room->name,
                        'number' => $room->number,
                        'floor' => $room->floor,
                        'active' => $room->active,
                    ];
                });
            }),
            'consulting_rooms_count' => $this->whenCounted('consultingRooms'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
