<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentCancelledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $tries = 3;

    public $backoff = [60, 300, 600];

    public function __construct(
        public Appointment $appointment,
        public ?string $cancellationReason = null,
        public string $cancelledBy = 'practitioner'
    ) {
        $this->onQueue('emails');
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $practitioner = $this->appointment->practitioner;
        $appointmentDate = $this->appointment->start_datetime;
        $clinicName = $this->appointment->client->name ?? config('app.name');

        return (new MailMessage)
            ->subject('Cita Médica Cancelada - '.$clinicName)
            ->view('emails.appointment-cancelled', [
                'patientName' => $notifiable->name,
                'practitionerName' => $practitioner->name,
                'appointmentDate' => $appointmentDate->format('d/m/Y'),
                'appointmentTime' => $appointmentDate->format('H:i'),
                'specialty' => $practitioner->specialty ?? 'Medicina General',
                'clinicName' => $clinicName,
                'cancellationReason' => $this->cancellationReason,
                'rescheduleUrl' => route('appointment.calendar'),
            ]);
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'appointment_cancelled',
            'appointment_id' => $this->appointment->id,
            'practitioner_name' => $this->appointment->practitioner->name,
            'practitioner_id' => $this->appointment->practitioner->id,
            'appointment_datetime' => $this->appointment->start_datetime->format('Y-m-d H:i:s'),
            'appointment_date' => $this->appointment->start_datetime->format('Y-m-d'),
            'appointment_time' => $this->appointment->start_datetime->format('H:i'),
            'cancellation_reason' => $this->cancellationReason,
            'cancelled_by' => $this->cancelledBy,
            'clinic_name' => $this->appointment->client->name ?? null,
            'branch_name' => $this->appointment->consultingRoom->branch->name ?? null,
            'consulting_room' => $this->appointment->consultingRoom->name ?? null,
            'message' => 'Su cita con Dr. '.$this->appointment->practitioner->name.' ha sido cancelada.',
            'sent_at' => now()->toDateTimeString(),
        ];
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        \Log::error('Falló el envío de notificación de cita cancelada', [
            'appointment_id' => $this->appointment->id,
            'patient_id' => $this->appointment->patient_id,
            'practitioner_id' => $this->appointment->practitioner_id,
            'cancellation_reason' => $this->cancellationReason,
            'cancelled_by' => $this->cancelledBy,
            'error' => $exception->getMessage(),
        ]);
    }
}
