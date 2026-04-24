<?php

namespace App\Notifications;

use App\Channels\WhatsAppMetaChannel;
use App\Models\Appointment;
use App\Notifications\Concerns\ValidatesEmailChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentCancelledNotification extends Notification implements ShouldQueue
{
    use Queueable, ValidatesEmailChannel;

    public $tries = 3;

    public $backoff = [60, 300, 600];

    public $deleteWhenMissingModels = true;

    public function __construct(
        public Appointment $appointment,
        public ?string $cancellationReason = null,
        public string $cancelledBy = 'practitioner'
    ) {
        $this->onQueue('emails');
    }

    public function via($notifiable)
    {
        $channels = ['database'];

        // Priorizar WhatsApp si está disponible
        if ($notifiable->whatsapp_phone || $notifiable->phone) {
            $channels[] = WhatsAppMetaChannel::class;
        }
        // Si no tiene WhatsApp, usar email
        elseif ($this->isValidEmail($notifiable->email)) {
            $channels[] = 'mail';
        }

        return $channels;
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
            // Standard notification fields
            'title' => 'Cita Médica Cancelada',
            'message' => 'Su cita con '.$this->appointment->practitioner->name.' ha sido cancelada.',
            'steps' => array_filter([
                '📅 Fecha cancelada: '.$this->appointment->start->format('d/m/Y H:i'),
                $this->cancellationReason ? '📝 Motivo: '.$this->cancellationReason : null,
            ]),
            'action' => [
                'text' => 'Reagendar Cita',
                'url' => route('appointment.calendar'),
            ],
            'priority' => 'medium',
            'icon' => 'fas fa-calendar-times',

            // Legacy/specific fields (for backwards compatibility)
            'type' => 'appointment_cancelled',
            'appointment_id' => $this->appointment->id,
            'practitioner_name' => $this->appointment->practitioner->name,
            'practitioner_id' => $this->appointment->practitioner->id,
            'appointment_datetime' => $this->appointment->start->format('Y-m-d H:i:s'),
            'appointment_date' => $this->appointment->start->format('Y-m-d'),
            'appointment_time' => $this->appointment->start->format('H:i'),
            'cancellation_reason' => $this->cancellationReason,
            'cancelled_by' => $this->cancelledBy,
            'clinic_name' => $this->appointment->client->name ?? null,
            'branch_name' => $this->appointment->consultingRoom->branch->name ?? null,
            'consulting_room' => $this->appointment->consultingRoom->name ?? null,
            'sent_at' => now()->toDateTimeString(),
        ];
    }

    /**
     * Get the WhatsApp representation of the notification.
     */
    public function toWhatsApp(object $notifiable): string
    {
        $practitioner = $this->appointment->practitioner;
        $appointmentDate = $this->appointment->start;
        $clinicName = $this->appointment->client->name ?? config('app.name');

        $message = "❌ *Cita Médica Cancelada*\n\n";
        $message .= "Hola {$notifiable->name},\n\n";
        $message .= "Lamentamos informarle que su cita médica ha sido cancelada:\n\n";

        $message .= "👨‍⚕️ *Doctor:* {$practitioner->name}\n";
        $message .= "📅 *Fecha cancelada:* {$appointmentDate->format('d/m/Y')}\n";
        $message .= "🕐 *Hora:* {$appointmentDate->format('H:i a')}\n";
        $message .= "🏢 *Clínica:* {$clinicName}\n";

        if ($this->appointment->consultingRoom->branch->name ?? null) {
            $message .= "🏪 *Sede:* {$this->appointment->consultingRoom->branch->name}\n";
        }

        if ($this->cancellationReason) {
            $message .= "\n📝 *Motivo de la cancelación:*\n{$this->cancellationReason}\n";
        }

        $message .= "\n💡 Si desea reagendar su cita, puede hacerlo a través del chat de WhatsApp +507 831-6172.\n";
        $message .= "\nDisculpe las molestias ocasionadas.\n";
        $message .= "\nAtentamente,\n";
        $message .= "Equipo de {$clinicName} 🏥";

        return $message;
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
            'cancellation_reason' => $this->cancellationReason,
            'cancelled_by' => $this->cancelledBy,
            'error' => $errorMessage,
        ];

        // Check if it's an RFC 2606 reserved domain error
        if (str_contains($errorMessage, 'Recipient address reserved by RFC 2606') ||
            str_contains($errorMessage, 'code "501"')) {
            \Log::warning('Intento de envío a dirección reservada RFC 2606', array_merge($context, [
                'patient_email' => $this->appointment->patient->email ?? 'N/A',
                'note' => 'El email del paciente usa un dominio reservado (example.com, test.com, etc.)',
            ]));

            return;
        }

        // Log other errors as errors
        \Log::error('Falló el envío de notificación de cita cancelada', $context);
    }
}
