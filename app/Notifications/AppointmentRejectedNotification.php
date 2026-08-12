<?php

namespace App\Notifications;

use App\Channels\WhatsAppMetaChannel;
use App\Models\Appointment;
use App\Notifications\Concerns\ValidatesEmailChannel;
use App\Notifications\Concerns\WithEmailMetadata;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable, ValidatesEmailChannel, WithEmailMetadata;

    public $tries = 3;

    public $backoff = [60, 300, 600];

    public $deleteWhenMissingModels = true;

    public function __construct(
        public Appointment $appointment,
        public ?string $rejectionReason = null
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

    /**
     * Define metadatos personalizados para tracking del correo
     */
    protected function emailMetadata(): array
    {
        return [
            'Type' => 'appointment-rejected',
            'Appointment-ID' => $this->appointment->id,
            'Patient-ID' => $this->appointment->patient_id,
            'Patient-Name' => $this->appointment->patient->full_name ?? 'N/A',
            'Doctor-ID' => $this->appointment->practitioner_id,
            'Doctor-Name' => $this->appointment->practitioner->name ?? 'N/A',
            'Requested-Date' => $this->appointment->original_requested_datetime->format('Y-m-d H:i'),
            'Branch-Name' => $this->appointment->consultingRoom->branch->name ?? 'N/A',
            'Rejection-Reason' => $this->rejectionReason ?? 'N/A',
            'Client-ID' => $this->appointment->client_id,
        ];
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
            ])
            ->withSwiftMessage(function ($message) {
                $this->applyEmailMetadata($message);
            });
    }

    public function toArray($notifiable)
    {
        return [
            // Standard notification fields
            'title' => 'Solicitud de Cita No Disponible',
            'message' => 'Su solicitud de cita con Dr. '.$this->appointment->practitioner->name.' no está disponible.',
            'steps' => array_filter([
                '📅 Fecha solicitada: '.$this->appointment->original_requested_datetime->format('d/m/Y H:i'),
                $this->rejectionReason ? '📝 Motivo: '.$this->rejectionReason : null,
            ]),
            'action' => [
                'text' => 'Buscar Horarios Disponibles',
                'url' => route('appointment.calendar'),
            ],
            'priority' => 'medium',
            'icon' => 'fas fa-calendar-xmark',

            // Legacy/specific fields (for backwards compatibility)
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
            'sent_at' => now()->toDateTimeString(),
        ];
    }

    /**
     * Get the WhatsApp representation of the notification.
     */
    public function toWhatsApp(object $notifiable): string
    {
        $practitioner = $this->appointment->practitioner;
        $requestedDate = $this->appointment->original_requested_datetime;
        $clinicName = $this->appointment->client->name ?? config('app.name');

        $message = "❌ *Solicitud de Cita No Disponible*\n\n";
        $message .= "Hola {$notifiable->name},\n\n";
        $message .= "Lamentamos informarle que su solicitud de cita no está disponible:\n\n";

        $message .= "👨‍⚕️ *Doctor:* {$practitioner->name}\n";

        if ($practitioner->specialty) {
            $message .= "🏥 *Especialidad:* {$practitioner->specialty}\n";
        }

        $message .= "📅 *Fecha solicitada:* {$requestedDate->format('d/m/Y')}\n";
        $message .= "🕐 *Hora solicitada:* {$requestedDate->format('H:i a')}\n";
        $message .= "🏢 *Clínica:* {$clinicName}\n";

        if ($this->rejectionReason) {
            $message .= "\n📝 *Motivo:*\n{$this->rejectionReason}\n";
        }

        $message .= "\n💡 *Alternativas:*\n";
        $message .= "• Puede buscar otros horarios disponibles\n";
        $message .= "• Puede solicitar cita con otro especialista\n";
        $message .= "• Puede contactarnos para más opciones\n";

        $message .= "\n📱 Le invitamos a revisar nuestros horarios disponibles en el calendario.\n";
        $message .= "\nDisculpe las molestias.\n";
        $message .= "Equipo de {$clinicName}";

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
            'rejection_reason' => $this->rejectionReason,
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
        \Log::error('Falló el envío de notificación de cita rechazada', $context);
    }
}
