<?php

namespace App\Notifications;

use App\Channels\WhatsAppMetaChannel;
use App\Models\AppointmentFreedSlot;
use App\Notifications\Concerns\ValidatesEmailChannel;
use App\Notifications\Concerns\WithEmailMetadata;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WaitlistSlotAvailableNotification extends Notification implements ShouldQueue
{
    use Queueable, ValidatesEmailChannel, WithEmailMetadata;

    public $tries = 3;

    public $backoff = [60, 300, 600];

    public $deleteWhenMissingModels = true;

    public function __construct(
        public AppointmentFreedSlot $freedSlot
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
        $appointment = $this->freedSlot->cancelledAppointment;

        return [
            'Type' => 'waitlist-slot-available',
            'Freed-Slot-ID' => $this->freedSlot->id,
            'Cancelled-Appointment-ID' => $this->freedSlot->cancelled_appointment_id,
            'Doctor-ID' => $appointment->practitioner_id,
            'Doctor-Name' => $appointment->practitioner->name ?? 'N/A',
            'Available-Date' => $this->freedSlot->slot_date,
            'Available-Time' => substr($this->freedSlot->slot_start_time, 0, 5),
            'Duration-Minutes' => $this->freedSlot->duration_minutes,
            'Specialty' => $appointment->medicalSpeciality->name ?? 'N/A',
            'Client-ID' => $appointment->client_id,
        ];
    }

    public function toMail($notifiable)
    {
        $appointment = $this->freedSlot->cancelledAppointment;
        $practitioner = $appointment->practitioner;
        $clinicName = $appointment->client->name ?? config('app.name');
        $speciality = $appointment->medicalSpeciality->name ?? 'Medicina General';

        return (new MailMessage)
            ->subject('¡Espacio Disponible! - '.$clinicName)
            ->view('emails.waitlist-slot-available', [
                'patientName' => $notifiable->name,
                'practitionerName' => $practitioner->name,
                'availableDate' => Carbon::parse($this->freedSlot->slot_date)->format('d/m/Y'),
                'availableTime' => substr($this->freedSlot->slot_start_time, 0, 5),
                'speciality' => $speciality,
                'clinicName' => $clinicName,
                'durationMinutes' => $this->freedSlot->duration_minutes,
            ])
            ->withSwiftMessage(function ($message) {
                $this->applyEmailMetadata($message);
            });
    }

    public function toArray(object $notifiable): array
    {
        $appointment = $this->freedSlot->cancelledAppointment;

        return [
            'title' => '¡Espacio Disponible!',
            'message' => 'Se ha liberado un espacio en tu horario preferido. ¡Réclámalo ahora!',
            'steps' => array_filter([
                '👨‍⚕️ Doctor: '.$appointment->practitioner->name,
                '🏥 Especialidad: '.($appointment->medicalSpeciality->name ?? 'N/A'),
                '📅 Fecha Disponible: '.Carbon::parse($this->freedSlot->slot_date)->format('d/m/Y'),
                '🕐 Hora Disponible: '.substr($this->freedSlot->slot_start_time, 0, 5),
                '⏱️ Duración: '.$this->freedSlot->duration_minutes.' minutos',
            ]),
            'action' => [
                'text' => 'Aceptar Espacio',
                'url' => route('appointment.waitlist'),
            ],
            'priority' => 'high',
            'icon' => 'fas fa-check-circle',

            'type' => 'slot_available',
            'freed_slot_id' => $this->freedSlot->id,
            'appointment_id' => $appointment->id,
            'practitioner_name' => $appointment->practitioner->name,
            'available_date' => $this->freedSlot->slot_date,
            'available_time' => substr($this->freedSlot->slot_start_time, 0, 5),
            'duration_minutes' => $this->freedSlot->duration_minutes,
            'sent_at' => now()->toDateTimeString(),
        ];
    }

    public function toWhatsApp(object $notifiable): string
    {
        $appointment = $this->freedSlot->cancelledAppointment;
        $practitioner = $appointment->practitioner;
        $clinicName = $appointment->client->name ?? config('app.name');

        $message = "✅ *¡Espacio Disponible!*\n\n";
        $message .= "Hola {$notifiable->name},\n\n";
        $message .= "¡Excelente noticia! Se ha liberado un espacio en tu horario preferido.\n\n";

        $message .= "👨‍⚕️ *Doctor:* {$practitioner->name}\n";
        $message .= '🏥 *Especialidad:* '.($appointment->medicalSpeciality->name ?? 'Medicina General')."\n";
        $message .= '📅 *Fecha:* '.Carbon::parse($this->freedSlot->slot_date)->format('d/m/Y')."\n";
        $message .= '🕐 *Hora:* '.substr($this->freedSlot->slot_start_time, 0, 5)."\n";
        $message .= '⏱️ *Duración:* '.$this->freedSlot->duration_minutes." minutos\n";

        $message .= "\n⚡ Este espacio está disponible por tiempo limitado.\n";
        $message .= 'Accede a tu lista de espera para aceptarlo: '.route('appointment.waitlist')."\n";
        $message .= "¡Gracias por confiar en {$clinicName}! 😊";

        return $message;
    }

    public function failed(\Throwable $exception): void
    {
        $errorMessage = $exception->getMessage();
        $context = [
            'freed_slot_id' => $this->freedSlot->id,
            'appointment_id' => $this->freedSlot->cancelled_appointment_id,
            'error' => $errorMessage,
        ];

        if (str_contains($errorMessage, 'Recipient address reserved by RFC 2606') ||
            str_contains($errorMessage, 'code "501"')) {
            \Log::warning('Intento de envío a dirección reservada RFC 2606 en notificación de slot disponible', $context);

            return;
        }

        \Log::error('Falló el envío de notificación de slot disponible', $context);
    }
}
