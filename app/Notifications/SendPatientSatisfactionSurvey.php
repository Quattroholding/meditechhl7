<?php

namespace App\Notifications;

use App\Models\Encounter;
use App\Models\Survey;
use App\Models\SurveyResponse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendPatientSatisfactionSurvey extends Notification implements ShouldQueue
{
    use Queueable;

    public $tries = 3;

    public $backoff = [60, 300, 600];

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public SurveyResponse $surveyResponse,
        public Encounter $encounter,
        public Survey $survey
    ) {
        $this->onQueue('emails');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['mail', 'database'];

        // Add WhatsApp channel if user has WhatsApp phone number
        if ($notifiable->whatsapp_phone || $notifiable->phone) {
            // Use N8N channel instead of Twilio
            $channels[] = \App\Channels\WhatsAppN8NChannel::class;
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $surveyUrl = route('survey.public', $this->surveyResponse->token);
        $practitionerName = $this->encounter->appointment->practitioner->name ?? 'Su médico';
        $encounterDate = $this->encounter->start->format('d/m/Y');
        $clinicName = $this->encounter->appointment->client->name;

        return (new MailMessage)
            ->subject('Encuesta de Satisfacción - '.$clinicName)
            ->view('emails.patient-satisfaction-survey', [
                'patientName' => $notifiable->name,
                'surveyTitle' => $this->survey->title,
                'surveyUrl' => $surveyUrl,
                'practitionerName' => $practitionerName,
                'encounterDate' => $encounterDate,
                'clinicName' => $clinicName,
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
            'survey_id' => $this->survey->id,
            'survey_title' => $this->survey->title,
            'survey_response_id' => $this->surveyResponse->id,
            'encounter_id' => $this->encounter->id,
            'encounter_date' => $this->encounter->start->format('Y-m-d'),
            'practitioner_name' => $this->encounter->practitioner->full_name ?? null,
            'survey_url' => route('survey.public', $this->surveyResponse->token),
            'sent_at' => now()->toDateTimeString(),
        ];
    }

    /**
     * Get the WhatsApp representation of the notification.
     */
    public function toWhatsApp(object $notifiable): string
    {
        $surveyUrl = route('survey.public', $this->surveyResponse->token);
        $practitionerName = $this->encounter->practitioner->name ?? 'su médico';
        $encounterDate = $this->encounter->start->format('d/m/Y');
        $clinicName = $this->encounter->appointment->client->name ?? config('app.name');

        $message = "📋 *Encuesta de Satisfacción*\n\n";
        $message .= "Hola {$notifiable->name},\n\n";
        $message .= "Gracias por su visita con *{$practitionerName}* el {$encounterDate}.\n\n";
        $message .= "Nos gustaría conocer su opinión sobre la atención recibida en *{$clinicName}*.\n\n";
        $message .= "Su experiencia es muy importante para nosotros y nos ayuda a mejorar continuamente nuestros servicios.\n\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━\n";
        $message .= "👉 *Por favor complete nuestra encuesta aquí:*\n\n";
        $message .= "{$surveyUrl}\n\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━\n\n";
        $message .= "⏱️ Solo tomará unos minutos de su tiempo.\n\n";
        $message .= '¡Gracias por ayudarnos a mejorar! 😊';

        return $message;
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        \Log::error('Falló el envío de notificación de encuesta de satisfacción', [
            'survey_response_id' => $this->surveyResponse->id,
            'encounter_id' => $this->encounter->id,
            'patient_id' => $this->surveyResponse->patient_id,
            'error' => $exception->getMessage(),
        ]);
    }
}
