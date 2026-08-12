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

class AppointmentCancelledNotification extends Notification implements ShouldQueue
{
    use Queueable, ValidatesEmailChannel, WithEmailMetadata;

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

        // Obtener el teléfono y email que realmente se usarán (respetando testing mode)
        $whatsappPhone = $notifiable->routeNotificationFor('whatsapp', $this);
        $email = $notifiable->routeNotificationFor('mail', $this);

        // Priorizar WhatsApp si está disponible
        if ($whatsappPhone) {
            $channels[] = WhatsAppMetaChannel::class;
        }

        // Si no tiene WhatsApp, usar email
        if ($this->isValidEmail($email)) {
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
            'Type' => 'appointment-cancelled',
            'Appointment-ID' => $this->appointment->id,
            'Patient-ID' => $this->appointment->patient_id,
            'Patient-Name' => $this->appointment->patient->full_name ?? 'N/A',
            'Doctor-ID' => $this->appointment->practitioner_id,
            'Doctor-Name' => $this->appointment->practitioner->name ?? 'N/A',
            'Appointment-Date' => $this->appointment->start->format('Y-m-d H:i'),
            'Branch-Name' => $this->appointment->consultingRoom->branch->name ?? 'N/A',
            'Cancelled-By' => $this->cancelledBy,
            'Cancellation-Reason' => $this->cancellationReason ?? 'N/A',
            'Client-ID' => $this->appointment->client_id,
        ];
    }

    public function toMail($notifiable)
    {
        $practitioner = $this->appointment->practitioner;
        $appointmentDate = $this->appointment->start;
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
                'rescheduleUrl' => route('patients.landing'),
            ])
            ->withSwiftMessage(function ($message) {
                $this->applyEmailMetadata($message);
            });
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
     * Uses WhatsApp template: cancelar_cita
     * Template: "Hola {{1}}, Tu próxima cita con {{2}} el {{3}} a las {{4}} ha sida cancelada.
     *            Haganos saber si tiene una pregunta o necesecita reprogramarla."
     */
    public function toWhatsApp(object $notifiable): array
    {
        $practitioner = $this->appointment->practitioner;
        $appointmentDate = $this->appointment->start;

        // Build components for template
        $components = [];

        // Body component with variables
        // {{1}} = Nombre del paciente
        // {{2}} = Nombre del doctor
        // {{3}} = Fecha de la cita (formato: 15/01/2026)
        // {{4}} = Hora de la cita (formato: 10:30)
        $components[] = [
            'type' => 'body',
            'parameters' => [
                ['type' => 'text', 'text' => $notifiable->name],
                ['type' => 'text', 'text' => $practitioner->name],
                ['type' => 'text', 'text' => $appointmentDate->format('d/m/Y')],
                ['type' => 'text', 'text' => $appointmentDate->format('H:i')],
            ],
        ];

        return [
            'use_template' => true,
            'template_name' => 'cancelar_cita',
            'language_code' => 'es',
            'components' => $components,
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
