<?php

namespace App\Jobs;

use App\Models\SurveyResponse;
use App\Models\Encounter;
use App\Notifications\SendPatientSatisfactionSurvey;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendSurveyEmailJob implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $backoff = [60, 300, 600];

    /**
     * Create a new job instance.
     */
    public function __construct(
        public SurveyResponse $surveyResponse,
        public Encounter $encounter
    ) {
        $this->onQueue('emails');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $patient = $this->surveyResponse->patient;
            $survey = $this->surveyResponse->survey;

            if (!$patient->email) {
                Log::warning('No se puede enviar encuesta: paciente sin email', [
                    'patient_id' => $patient->id,
                    'survey_response_id' => $this->surveyResponse->id
                ]);
                return;
            }

            $patient->notify(new SendPatientSatisfactionSurvey(
                $this->surveyResponse,
                $this->encounter,
                $survey
            ));

            Log::info('Notificación de encuesta enviada exitosamente', [
                'patient_id' => $patient->id,
                'patient_email' => $patient->email,
                'survey_id' => $survey->id,
                'survey_response_id' => $this->surveyResponse->id,
                'encounter_id' => $this->encounter->id
            ]);

        } catch (\Exception $e) {
            Log::error('Error al enviar notificación de encuesta', [
                'survey_response_id' => $this->surveyResponse->id,
                'encounter_id' => $this->encounter->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Falló el envío de notificación de encuesta después de todos los intentos', [
            'survey_response_id' => $this->surveyResponse->id,
            'encounter_id' => $this->encounter->id,
            'error' => $exception->getMessage()
        ]);
    }
}
