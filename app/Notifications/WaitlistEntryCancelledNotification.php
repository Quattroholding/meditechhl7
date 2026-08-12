<?php

namespace App\Notifications;

use App\Channels\WhatsAppMetaChannel;
use App\Models\AppointmentWaitlistEntry;
use App\Notifications\Concerns\ValidatesEmailChannel;
use App\Notifications\Concerns\WithEmailMetadata;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WaitlistEntryCancelledNotification extends Notification implements ShouldQueue
{
    use Queueable, ValidatesEmailChannel, WithEmailMetadata;

    public $tries = 3;

    public $backoff = [60, 300, 600];

    public $deleteWhenMissingModels = true;

    public function __construct(
        public AppointmentWaitlistEntry $waitlistEntry
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
        $appointment = $this->waitlistEntry->appointment;

        return [
            'Type' => 'waitlist-cancelled',
            'Waitlist-Entry-ID' => $this->waitlistEntry->id,
            'Appointment-ID' => $appointment->id,
            'Patient-ID' => $this->waitlistEntry->patient_id,
            'Patient-Name' => $this->waitlistEntry->patient->full_name ?? 'N/A',
            'Doctor-ID' => $appointment->practitioner_id,
            'Doctor-Name' => $appointment->practitioner->name ?? 'N/A',
            'Requested-Date' => $appointment->start->format('Y-m-d H:i'),
            'Cancellation-Reason' => $this->waitlistEntry->cancellation_reason ?? 'N/A',
            'Client-ID' => $appointment->client_id,
        ];
    }

    public function toMail($notifiable)
    {
        $appointment = $this->waitlistEntry->appointment;
        $practitioner = $appointment->practitioner;
        $clinicName = $appointment->client->name ?? config('app.name');

        return (new MailMessage)
            ->subject('Entrada en Lista de Espera Cancelada - '.$clinicName)
            ->view('emails.waitlist-cancelled', [
                'patientName' => $notifiable->name,
                'practitionerName' => $practitioner->name,
                'requestedDate' => $appointment->start->format('d/m/Y'),
                'requestedTime' => $appointment->start->format('H:i'),
                'speciality' => $appointment->medicalSpeciality->name ?? 'Medicina General',
                'clinicName' => $clinicName,
                'cancellationReason' => $this->waitlistEntry->cancellation_reason,
            ])
            ->withSymfonyMessage(function ($message) {
                $this->applyEmailMetadata($message);
            });
    }

    public function toArray(object $notifiable): array
    {
        $appointment = $this->waitlistEntry->appointment;

        return [
            'title' => 'Entrada en Lista de Espera Cancelada',
            'message' => 'Tu entrada en la lista de espera ha sido cancelada.',
            'steps' => array_filter([
                '👨‍⚕️ Doctor: '.$appointment->practitioner->name,
                '🏥 Especialidad: '.($appointment->medicalSpeciality->name ?? 'N/A'),
                '📅 Fecha Solicitada: '.$appointment->start->format('d/m/Y'),
                '🕐 Hora Solicitada: '.$appointment->start->format('H:i'),
                $this->waitlistEntry->cancellation_reason ? '📝 Razón: '.$this->waitlistEntry->cancellation_reason : null,
            ]),
            'action' => [
                'text' => 'Ver Mis Citas',
                'url' => route('appointment.index'),
            ],
            'priority' => 'medium',
            'icon' => 'fas fa-times-circle',

            'type' => 'waitlist_cancelled',
            'waitlist_entry_id' => $this->waitlistEntry->id,
            'appointment_id' => $appointment->id,
            'practitioner_name' => $appointment->practitioner->name,
            'requested_date' => $appointment->start->format('Y-m-d'),
            'requested_time' => $appointment->start->format('H:i'),
            'cancellation_reason' => $this->waitlistEntry->cancellation_reason,
            'sent_at' => now()->toDateTimeString(),
        ];
    }

    public function toWhatsApp(object $notifiable): string
    {
        $appointment = $this->waitlistEntry->appointment;
        $practitioner = $appointment->practitioner;
        $clinicName = $appointment->client->name ?? config('app.name');

        $message = "❌ *Entrada Cancelada*\n\n";
        $message .= "Hola {$notifiable->name},\n\n";
        $message .= "Tu entrada en la lista de espera ha sido cancelada.\n\n";

        $message .= "👨‍⚕️ *Doctor:* {$practitioner->name}\n";
        $message .= '🏥 *Especialidad:* '.($appointment->medicalSpeciality->name ?? 'Medicina General')."\n";
        $message .= '📅 *Fecha Solicitada:* '.$appointment->start->format('d/m/Y')."\n";
        $message .= '🕐 *Hora Solicitada:* '.$appointment->start->format('H:i')."\n";

        if ($this->waitlistEntry->cancellation_reason) {
            $message .= '📝 *Razón:* '.$this->waitlistEntry->cancellation_reason."\n";
        }

        $message .= "\n💬 Si deseas reagendarte, puedes solicitar una nueva cita a través de nuestra plataforma.\n";
        $message .= "¡Gracias por confiar en {$clinicName}!";

        return $message;
    }

    public function failed(\Throwable $exception): void
    {
        $errorMessage = $exception->getMessage();
        $context = [
            'waitlist_entry_id' => $this->waitlistEntry->id,
            'appointment_id' => $this->waitlistEntry->appointment_id,
            'patient_id' => $this->waitlistEntry->patient_id,
            'error' => $errorMessage,
        ];

        if (str_contains($errorMessage, 'Recipient address reserved by RFC 2606') ||
            str_contains($errorMessage, 'code "501"')) {
            \Log::warning('Intento de envío a dirección reservada RFC 2606 en notificación de cancelación de waitlist', $context);

            return;
        }

        \Log::error('Falló el envío de notificación de cancelación de waitlist', $context);
    }
}
