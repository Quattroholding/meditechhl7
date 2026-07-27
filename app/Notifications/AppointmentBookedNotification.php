<?php

namespace App\Notifications;

use App\Channels\WhatsAppMetaChannel;
use App\Models\Appointment;
use App\Notifications\Concerns\ValidatesEmailChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentBookedNotification extends Notification implements ShouldQueue
{
    use Queueable, ValidatesEmailChannel;

    public $tries = 3;

    public $backoff = [60, 300, 600];

    public $deleteWhenMissingModels = true;

    public function __construct(
        public Appointment $appointment
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
        $appointmentDate = $this->appointment->start;
        $clinicName = $this->appointment->client->name ?? config('app.name');

        return (new MailMessage)
            ->subject('Cita Médica Agendada - '.$clinicName)
            ->view('emails.appointment-booked', [
                'patientName' => $notifiable->name,
                'practitionerName' => $practitioner->name,
                'appointmentDate' => $appointmentDate->format('d/m/Y'),
                'appointmentTime' => $appointmentDate->format('H:i'),
                'durationMinutes' => $this->appointment->minutes_duration,
                'serviceType' => $this->appointment->service_type ?? 'Consulta general',
                'specialty' => $this->appointment->medicalSpeciality->name ?? 'Medicina General',
                'clinicName' => $clinicName,
                'branchName' => $this->appointment->consultingRoom->branch->name ?? null,
                'consultingRoom' => $this->appointment->consultingRoom->name ?? null,
                'comment' => $this->appointment->comment,
                'patientInstruction' => $this->appointment->patient_instruction,
                'appointmentUrl' => route('appointment.calendar'),
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
            'title' => 'Cita Médica Agendada',
            'message' => 'Su cita con '.$this->appointment->practitioner->name.' ha sido agendada exitosamente.',
            'steps' => array_filter([
                '📅 Fecha: '.$this->appointment->start->format('d/m/Y'),
                '🕐 Hora: '.$this->appointment->start->format('H:i'),
                '⏱️ Duración: '.$this->appointment->minutes_duration.' minutos',
                $this->appointment->consultingRoom->branch->name ? '🏪 Sede: '.$this->appointment->consultingRoom->branch->name : null,
                $this->appointment->consultingRoom->name ? '🚪 Consultorio: '.$this->appointment->consultingRoom->name : null,
                $this->appointment->patient_instruction ? '📋 Instrucciones: '.$this->appointment->patient_instruction : null,
                '⏰ Recibirá un recordatorio 2 horas antes de su cita',
            ]),
            'action' => [
                'text' => 'Ver Calendario',
                'url' => route('appointment.calendar'),
            ],
            'priority' => 'high',
            'icon' => 'fas fa-calendar-plus',

            // Legacy/specific fields
            'type' => 'appointment_booked',
            'appointment_id' => $this->appointment->id,
            'practitioner_name' => $this->appointment->practitioner->name,
            'practitioner_id' => $this->appointment->practitioner->id,
            'appointment_datetime' => $this->appointment->start->format('Y-m-d H:i:s'),
            'appointment_date' => $this->appointment->start->format('Y-m-d'),
            'appointment_time' => $this->appointment->start->format('H:i'),
            'duration_minutes' => $this->appointment->minutes_duration,
            'service_type' => $this->appointment->service_type,
            'clinic_name' => $this->appointment->client->name ?? null,
            'branch_name' => $this->appointment->consultingRoom->branch->name ?? null,
            'consulting_room' => $this->appointment->consultingRoom->name ?? null,
            'comment' => $this->appointment->comment,
            'patient_instruction' => $this->appointment->patient_instruction,
            'sent_at' => now()->toDateTimeString(),
        ];
    }

    /**
     * Get the WhatsApp representation of the notification.
     * Uses WhatsApp template: cita_reservada
     * Template: "Hola {{1}}, Tiene una cita reservada con {{2}}. Se confirma su cita para {{3}} el {{4}} a las {{5}}.
     *            Gracias por su preferirnos."
     * Button: "Ver detalles" -> https://sami.meditecpty.com/cita/{{1}}
     */
    public function toWhatsApp(object $notifiable): array
    {
        $practitioner = $this->appointment->practitioner;
        $appointmentDate = $this->appointment->start;
        $specialty = $this->appointment->medicalSpeciality->name ?? 'Medicina General';

        // Build components for template
        $components = [];

        // Body component with variables
        // {{1}} = Nombre del paciente
        // {{2}} = Nombre del doctor
        // {{3}} = Especialidad
        // {{4}} = Fecha de la cita (formato: 15/01/2026)
        // {{5}} = Hora de la cita (formato: 10:30)
        $components[] = [
            'type' => 'body',
            'parameters' => [
                ['type' => 'text', 'text' => trim($notifiable->name ?? '') ?: 'Paciente'],
                ['type' => 'text', 'text' => trim($practitioner->name ?? '') ?: 'Doctor'],
                ['type' => 'text', 'text' => trim($specialty) ?: 'Medicina General'],
                ['type' => 'text', 'text' => $appointmentDate->format('d/m/Y')],
                ['type' => 'text', 'text' => $appointmentDate->format('H:i')],
            ],
        ];

        // Button component (Ver detalles)
        // Parameter should be the appointment ID to form a valid URL
        /*$components[] = [
            'type' => 'button',
            'sub_type' => 'url',
            'index' => 0,
            'parameters' => [
                ['type' => 'text', 'text' => (string) $this->appointment->id],
            ],
        ];*/

        return [
            'use_template' => true,
            'template_name' => 'cita_reservada',
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
        \Log::error('Falló el envío de notificación de cita agendada', $context);
    }
}
