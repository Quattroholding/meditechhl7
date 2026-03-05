<?php

namespace App\Events;

use App\Models\ServiceRequest;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LabResultsReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public ServiceRequest $serviceRequest
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('encounter.'.$this->serviceRequest->encounter_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'lab-results-received';
    }

    public function broadcastWith(): array
    {
        return [
            'service_request_id' => $this->serviceRequest->id,
            'encounter_id' => $this->serviceRequest->encounter_id,
            'observations_count' => $this->serviceRequest->observations()->count(),
        ];
    }
}
