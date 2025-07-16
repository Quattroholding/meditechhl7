<?php

namespace App\Notifications;

use App\Models\SurveyResponse;
use App\Models\Encounter;
use App\Models\Survey;
use App\Mail\SurveyInvitationMail;
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
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $surveyUrl = route('survey.public', $this->surveyResponse->token);
        $practitionerName = $this->encounter->practitioner->name ?? 'Su médico';
        $encounterDate = $this->encounter->start->format('d/m/Y');
        $clinicName = $this->encounter->appointment->client->name;

        return (new MailMessage)
            ->subject('Encuesta de Satisfacción - ' . $clinicName)
            ->greeting('Estimado/a ' . $notifiable->name . ',')
            ->line('Esperamos que se encuentre bien. Su opinión es muy importante para nosotros y nos ayuda a mejorar continuamente la calidad de nuestros servicios.')
            ->line('**Detalles de su consulta:**')
            ->line('• Fecha: ' . $encounterDate)
            ->line('• Médico: ' . $practitionerName)
            ->line('• Centro médico: ' . $clinicName)
            ->line('Le invitamos a completar una breve encuesta de satisfacción sobre la atención recibida.')
            ->action('Completar Encuesta', $surveyUrl)
            ->line('Esta encuesta tomará aproximadamente 2-3 minutos en completarse.')
            ->line('Gracias por confiar en nosotros para su cuidado médico.')
            ->salutation('Equipo de ' . $clinicName);
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
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        \Log::error('Falló el envío de notificación de encuesta de satisfacción', [
            'survey_response_id' => $this->surveyResponse->id,
            'encounter_id' => $this->encounter->id,
            'patient_id' => $this->surveyResponse->patient_id,
            'error' => $exception->getMessage()
        ]);
    }
}
