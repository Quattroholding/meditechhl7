<?php

namespace App\Events;

use App\Models\Appointment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AppointmentCheckedIn implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $appointment;

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('doctor.' . $this->appointment->practitioner_id);
    }

    public function broadcastWith()
    {
        // Log para debugging
        \Log::info('Broadcasting appointment checked-in event', [
            'appointment_id' => $this->appointment->id,
            'practitioner_id' => $this->appointment->practitioner_id,
            'channel' => 'doctor.' . $this->appointment->practitioner_id
        ]);
        
        return [
            'appointment' => [
                'id' => $this->appointment->id,
                'patient_name' => $this->appointment->patient->name ?? 'Paciente',
                'appointment_time' => $this->appointment->start->format('H:i'),
                'consulting_room' => $this->appointment->consultingRoom->name ?? 'Consultorio',
            ]
        ];
    }

    public function broadcastAs()
    {
        return 'appointment.checked.in';
    }
}