<?php

namespace App\Notifications;

use App\Models\Appointment;
use App\Notifications\Concerns\ValidatesEmailChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentProposedNotification extends Notification implements ShouldQueue
{
    use Queueable, ValidatesEmailChannel;

    public $tries = 3;

    public $backoff = [60, 300, 600];

    public function __construct(
        public Appointment $appointment
    ) {
        $this->onQueue('emails');
    }

    public function via($notifiable)
    {
        return array_filter([
            'database',
            $this->getMailChannelIfValid($notifiable->email),
        ]);
    }

    public function toMail($notifiable)
    {
        $patient = $this->appointment->patient;
        $requestedDate = $this->appointment->original_requested_datetime;
        $clinicName = $this->appointment->client->name ?? config('app.name');

        return (new MailMessage)
            ->subject('Nueva Solicitud de Cita Médica - '.$clinicName)
            ->view('emails.appointment-proposed', [
                'practitionerName' => $notifiable->name,
                'patientName' => $patient->name,
                'requestedDate' => $requestedDate->format('d/m/Y'),
                'requestedTime' => $requestedDate->format('H:i'),
                'durationMinutes' => $this->appointment->minutes_duration,
                'serviceType' => $this->appointment->service_type ?? 'Consulta',
                'branchName' => $this->appointment->consultingRoom->branch->name ?? 'N/A',
                'consultingRoom' => $this->appointment->consultingRoom->name ?? 'N/A',
                'clinicName' => $clinicName,
                'description' => $this->appointment->description,
                'comment' => $this->appointment->comment,
                'reviewUrl' => url('/appointments?status=proposed'), // . $this->appointment->id
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            // Standard notification fields
            'title' => 'Nueva Solicitud de Cita',
            'message' => 'Nueva solicitud de cita de '.$this->appointment->patient->name,
            'steps' => array_filter([
                '📅 Fecha solicitada: '.$this->appointment->original_requested_datetime->format('d/m/Y H:i'),
                '⏱️ Duración: '.$this->appointment->minutes_duration.' minutos',
                $this->appointment->service_type ? '🩺 Tipo: '.$this->appointment->service_type : null,
                $this->appointment->consultingRoom->branch->name ? '🏪 Sede: '.$this->appointment->consultingRoom->branch->name : null,
                $this->appointment->comment ? '💬 Comentario: '.$this->appointment->comment : null,
            ]),
            'action' => [
                'text' => 'Revisar Solicitud',
                'url' => url('/appointments?status=proposed'),
            ],
            'priority' => 'high',
            'icon' => 'fas fa-calendar-plus',

            // Legacy/specific fields (for backwards compatibility)
            'type' => 'appointment_proposed',
            'appointment_id' => $this->appointment->id,
            'patient_name' => $this->appointment->patient->name,
            'patient_id' => $this->appointment->patient->id,
            'requested_datetime' => $this->appointment->original_requested_datetime->format('Y-m-d H:i:s'),
            'requested_date' => $this->appointment->original_requested_datetime->format('Y-m-d'),
            'requested_time' => $this->appointment->original_requested_datetime->format('H:i'),
            'duration_minutes' => $this->appointment->minutes_duration,
            'service_type' => $this->appointment->service_type,
            'clinic_name' => $this->appointment->client->name ?? null,
            'branch_name' => $this->appointment->consultingRoom->branch->name ?? null,
            'consulting_room' => $this->appointment->consultingRoom->name ?? null,
            'description' => $this->appointment->description,
            'comment' => $this->appointment->comment,
            'sent_at' => now()->toDateTimeString(),
        ];
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $errorMessage = $exception->getMessage();
        $context = [
            'appointment_id' => $this->appointment->id,
            'patient_id' => $this->appointment->patient_id,
            'practitioner_id' => $this->appointment->practitioner_id,
            'error' => $errorMessage,
        ];

        // Check if it's an RFC 2606 reserved domain error
        if (str_contains($errorMessage, 'Recipient address reserved by RFC 2606') ||
            str_contains($errorMessage, 'code "501"')) {
            \Log::warning('Intento de envío a dirección reservada RFC 2606', array_merge($context, [
                'practitioner_email' => $this->appointment->practitioner->email ?? 'N/A',
                'note' => 'El email del médico usa un dominio reservado (example.com, test.com, etc.)',
            ]));

            return;
        }

        // Log other errors as errors
        \Log::error('Falló el envío de notificación de cita propuesta', $context);
    }
}
