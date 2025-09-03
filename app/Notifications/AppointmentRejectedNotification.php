<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $tries = 3;

    public $backoff = [60, 300, 600];

    public function __construct(
        public Appointment $appointment,
        public ?string $rejectionReason = null
    ) {
        $this->onQueue('emails');
    }

    public function via($notifiable)
    {
        return ['mail', 'database']; // También guardamos en BD para historial
    }

    public function toMail($notifiable)
    {
        $practitioner = $this->appointment->practitioner;
        $requestedDate = $this->appointment->original_requested_datetime;
        $clinicName = $this->appointment->client->name ?? config('app.name');

        return (new MailMessage)
            ->subject('Cita Médica No Disponible - '.$clinicName)
            ->view('emails.appointment-rejected', [
                'patientName' => $notifiable->name,
                'practitionerName' => $practitioner->name,
                'requestedDate' => $requestedDate->format('d/m/Y'),
                'requestedTime' => $requestedDate->format('H:i'),
                'specialty' => $practitioner->specialty ?? 'Medicina General',
                'clinicName' => $clinicName,
                'rejectionReason' => $this->rejectionReason,
                'availableDoctorsUrl' => url('/doctors/available'),
                'rescheduleUrl' => route('appointments.calendar'),
                'contactPhone' => null, // Can be configured per clinic
                'contactEmail' => config('mail.from.address'),
            ]);
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'appointment_rejected',
            'appointment_id' => $this->appointment->id,
            'practitioner_name' => $this->appointment->practitioner->name,
            'practitioner_id' => $this->appointment->practitioner->id,
            'requested_datetime' => $this->appointment->original_requested_datetime->format('Y-m-d H:i:s'),
            'requested_date' => $this->appointment->original_requested_datetime->format('Y-m-d'),
            'requested_time' => $this->appointment->original_requested_datetime->format('H:i'),
            'rejection_reason' => $this->rejectionReason,
            'specialty' => $this->appointment->practitioner->specialty,
            'clinic_name' => $this->appointment->client->name ?? null,
            'branch_name' => $this->appointment->consultingRoom->branch->name ?? null,
            'consulting_room' => $this->appointment->consultingRoom->name ?? null,
            'service_type' => $this->appointment->service_type,
            'duration_minutes' => $this->appointment->minutes_duration,
            'message' => 'Su solicitud de cita con Dr. '.$this->appointment->practitioner->name.' ha sido rechazada.',
            'sent_at' => now()->toDateTimeString(),
        ];
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        \Log::error('Falló el envío de notificación de cita rechazada', [
            'appointment_id' => $this->appointment->id,
            'patient_id' => $this->appointment->patient_id,
            'practitioner_id' => $this->appointment->practitioner_id,
            'rejection_reason' => $this->rejectionReason,
            'error' => $exception->getMessage(),
        ]);
    }
}
