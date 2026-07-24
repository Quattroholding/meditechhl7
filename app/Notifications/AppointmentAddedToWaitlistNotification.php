<?php

namespace App\Notifications;

use App\Channels\WhatsAppMetaChannel;
use App\Models\AppointmentWaitlistEntry;
use App\Notifications\Concerns\ValidatesEmailChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentAddedToWaitlistNotification extends Notification implements ShouldQueue
{
    use Queueable, ValidatesEmailChannel;

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

    public function toMail($notifiable)
    {
        $appointment = $this->waitlistEntry->appointment;
        $practitioner = $appointment->practitioner;
        $clinicName = $appointment->client->name ?? config('app.name');
        $position = $this->waitlistEntry->position ?? 'N/A';

        return (new MailMessage)
            ->subject('Agregado a Lista de Espera - '.$clinicName)
            ->view('emails.waitlist-added', [
                'patientName' => $notifiable->name,
                'practitionerName' => $practitioner->name,
                'requestedDate' => $appointment->start->format('d/m/Y'),
                'requestedTime' => $appointment->start->format('H:i'),
                'speciality' => $appointment->medicalSpeciality->name ?? 'Medicina General',
                'clinicName' => $clinicName,
                'urgencyLevel' => $this->waitlistEntry->urgency_level->label(),
                'maxWaitDays' => $this->waitlistEntry->max_wait_days,
                'position' => $position,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        $appointment = $this->waitlistEntry->appointment;

        return [
            'title' => '¡Agregado a Lista de Espera!',
            'message' => 'Te has agregado a la lista de espera. Te notificaremos cuando haya disponibilidad.',
            'steps' => array_filter([
                '👨‍⚕️ Doctor: '.$appointment->practitioner->name,
                '🏥 Especialidad: '.($appointment->medicalSpeciality->name ?? 'N/A'),
                '⏱️ Urgencia: '.$this->waitlistEntry->urgency_level->label(),
                '📅 Fecha solicitada: '.$appointment->start->format('d/m/Y'),
                '🕐 Hora solicitada: '.$appointment->start->format('H:i'),
                '⏳ Máximo de espera: '.$this->waitlistEntry->max_wait_days.' días',
            ]),
            'action' => [
                'text' => 'Ver Lista de Espera',
                'url' => route('appointment.waitlist'),
            ],
            'priority' => 'medium',
            'icon' => 'fas fa-hourglass-half',

            'type' => 'waitlist_added',
            'waitlist_entry_id' => $this->waitlistEntry->id,
            'appointment_id' => $appointment->id,
            'practitioner_name' => $appointment->practitioner->name,
            'urgency_level' => $this->waitlistEntry->urgency_level->value,
            'requested_date' => $appointment->start->format('Y-m-d'),
            'requested_time' => $appointment->start->format('H:i'),
            'max_wait_days' => $this->waitlistEntry->max_wait_days,
            'sent_at' => now()->toDateTimeString(),
        ];
    }

    public function toWhatsApp(object $notifiable): string
    {
        $appointment = $this->waitlistEntry->appointment;
        $practitioner = $appointment->practitioner;
        $clinicName = $appointment->client->name ?? config('app.name');

        $message = "⏳ *¡Agregado a Lista de Espera!*\n\n";
        $message .= "Hola {$notifiable->name},\n\n";
        $message .= "Te has agregado a la lista de espera con éxito.\n\n";

        $message .= "👨‍⚕️ *Doctor:* {$practitioner->name}\n";
        $message .= "🏥 *Especialidad:* ".($appointment->medicalSpeciality->name ?? 'Medicina General')."\n";
        $message .= "⏱️ *Nivel de Urgencia:* ".$this->waitlistEntry->urgency_level->label()."\n";
        $message .= "📅 *Fecha Solicitada:* ".$appointment->start->format('d/m/Y')."\n";
        $message .= "🕐 *Hora Solicitada:* ".$appointment->start->format('H:i a')."\n";
        $message .= "⏳ *Máximo de Espera:* ".$this->waitlistEntry->max_wait_days." días\n";

        $message .= "\n💬 Te notificaremos por WhatsApp cuando se libere un espacio disponible.\n";
        $message .= "¡Gracias por elegir ".$clinicName."! 😊";

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
            \Log::warning('Intento de envío a dirección reservada RFC 2606 en notificación de waitlist', $context);

            return;
        }

        \Log::error('Falló el envío de notificación de agregación a waitlist', $context);
    }
}
