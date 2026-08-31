<?php

namespace App\Notifications;

use App\Channels\WhatsAppMetaChannel;
use App\Models\AppointmentWaitlistEntry;
use App\Notifications\Concerns\ValidatesEmailChannel;
use App\Notifications\Concerns\WithEmailMetadata;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WaitlistEntryExpiredNotification extends Notification implements ShouldQueue
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
            'Type' => 'waitlist-expired',
            'Waitlist-Entry-ID' => $this->waitlistEntry->id,
            'Appointment-ID' => $appointment->id,
            'Patient-ID' => $this->waitlistEntry->patient_id,
            'Patient-Name' => $this->waitlistEntry->patient->full_name ?? 'N/A',
            'Doctor-ID' => $appointment->practitioner_id,
            'Doctor-Name' => $appointment->practitioner->name ?? 'N/A',
            'Requested-Date' => $appointment->start->format('Y-m-d H:i'),
            'Days-Waited' => Carbon::parse($this->waitlistEntry->getRawOriginal('created_at'))->diffInDays(now()),
            'Expires-At' => $this->waitlistEntry->expires_at->format('Y-m-d H:i'),
            'Client-ID' => $appointment->client_id,
        ];
    }

    public function toMail($notifiable)
    {
        $appointment = $this->waitlistEntry->appointment;
        $practitioner = $appointment->practitioner;
        $clinicName = $appointment->client->name ?? config('app.name');

        return (new MailMessage)
            ->subject('Entrada en Lista de Espera Expirada - '.$clinicName)
            ->view('emails.waitlist-expired', [
                'patientName' => $notifiable->name,
                'practitionerName' => $practitioner->name,
                'requestedDate' => $appointment->start->format('d/m/Y'),
                'requestedTime' => $appointment->start->format('H:i'),
                'speciality' => $appointment->medicalSpeciality->name ?? 'Medicina General',
                'clinicName' => $clinicName,
                'daysWaited' => Carbon::parse($this->waitlistEntry->getRawOriginal('created_at'))->diffInDays(now()),
            ])
            ->withSymfonyMessage(function ($message) {
                $this->applyEmailMetadata($message);
            });
    }

    public function toArray(object $notifiable): array
    {
        $appointment = $this->waitlistEntry->appointment;

        return [
            'title' => 'Entrada en Lista de Espera Expirada',
            'message' => 'Tu entrada en la lista de espera ha expirado. Puedes solicitar una nueva cita.',
            'steps' => array_filter([
                '👨‍⚕️ Doctor: '.$appointment->practitioner->name,
                '🏥 Especialidad: '.($appointment->medicalSpeciality->name ?? 'N/A'),
                '📅 Fecha Solicitada: '.$appointment->start->format('d/m/Y'),
                '🕐 Hora Solicitada: '.$appointment->start->format('H:i'),
                '⏳ Días de Espera: '.$this->waitlistEntry->created_at->diffInDays(now()),
            ]),
            'action' => [
                'text' => 'Agendar Nueva Cita',
                'url' => route('appointment.create'),
            ],
            'priority' => 'medium',
            'icon' => 'fas fa-calendar-times',

            'type' => 'waitlist_expired',
            'waitlist_entry_id' => $this->waitlistEntry->id,
            'appointment_id' => $appointment->id,
            'practitioner_name' => $appointment->practitioner->name,
            'requested_date' => $appointment->start->format('Y-m-d'),
            'requested_time' => $appointment->start->format('H:i'),
            'days_waited' => Carbon::parse($this->waitlistEntry->getRawOriginal('created_at'))->diffInDays(now()),
            'expires_at' => $this->waitlistEntry->expires_at->toDateTimeString(),
            'sent_at' => now()->toDateTimeString(),
        ];
    }

    public function toWhatsApp(object $notifiable): string
    {
        $appointment = $this->waitlistEntry->appointment;
        $practitioner = $appointment->practitioner;
        $clinicName = $appointment->client->name ?? config('app.name');
        $daysWaited = Carbon::parse($this->waitlistEntry->getRawOriginal('created_at'))->diffInDays(now());

        $message = "⏳ *Entrada Expirada*\n\n";
        $message .= "Hola {$notifiable->name},\n\n";
        $message .= "Tu entrada en la lista de espera ha expirado después de {$daysWaited} días.\n\n";

        $message .= "👨‍⚕️ *Doctor:* {$practitioner->name}\n";
        $message .= '🏥 *Especialidad:* '.($appointment->medicalSpeciality->name ?? 'Medicina General')."\n";
        $message .= '📅 *Fecha Solicitada:* '.$appointment->start->format('d/m/Y')."\n";
        $message .= '🕐 *Hora Solicitada:* '.$appointment->start->format('H:i')."\n";

        $message .= "\n📋 Si aún deseas agendar con este doctor, por favor solicita una nueva cita.\n";
        $message .= "¡Estamos aquí para ayudarte! 👋\n";
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
            \Log::warning('Intento de envío a dirección reservada RFC 2606 en notificación de expiración de waitlist', $context);

            return;
        }

        \Log::error('Falló el envío de notificación de expiración de waitlist', $context);
    }
}
