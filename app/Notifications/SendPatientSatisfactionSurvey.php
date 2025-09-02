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
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $surveyUrl = route('survey.public', $this->surveyResponse->token);
        $practitionerName = $this->encounter->appointment->practitioner->full_name ?? 'Su médico';
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
