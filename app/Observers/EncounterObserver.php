<?php

namespace App\Observers;

use App\Jobs\SendSurveyEmailJob;
use App\Models\Encounter;
use App\Models\Survey;
use App\Models\SurveyResponse;
use Illuminate\Support\Facades\Log;

class EncounterObserver
{
    /**
     * Handle the Encounter "updated" event.
     */
    public function updated(Encounter $encounter): void
    {
        if ($encounter->isDirty('status') && $encounter->getRawOriginal('status') == 'in-progress') {

            $this->sendSatisfactionSurvey($encounter);
        }
    }

    private function sendSatisfactionSurvey(Encounter $encounter): void
    {
        try {
            $activeSurvey = Survey::where('status', 'active')
                ->where('is_active', true)
                ->with('questions')
                ->first();

            if (! $activeSurvey || $activeSurvey->questions->isEmpty()) {
                Log::info('No hay encuesta activa disponible para enviar', [
                    'encounter_id' => $encounter->id,
                ]);

                return;
            }

            $patient = $encounter->patient;
            if (! $patient || ! $patient->email) {
                Log::info('Paciente sin email para envío de encuesta', [
                    'encounter_id' => $encounter->id,
                    'patient_id' => $patient?->id,
                ]);

                return;
            }

            $existingResponse = SurveyResponse::where('survey_id', $activeSurvey->id)
                ->where('patient_id', $patient->id)
                ->whereHas('survey', function ($query) use ($encounter) {
                    $query->where('created_at', '>=', $encounter->created_at);
                })
                ->first();

            if ($existingResponse) {
                Log::info('Ya existe una respuesta de encuesta para este paciente', [
                    'encounter_id' => $encounter->id,
                    'patient_id' => $patient->id,
                    'survey_response_id' => $existingResponse->id,
                ]);

                return;
            }

            $surveyResponse = SurveyResponse::create([
                'survey_id' => $activeSurvey->id,
                'patient_id' => $patient->id,
                'status' => 'pending',
            ]);

            SendSurveyEmailJob::dispatch($surveyResponse, $encounter);

            Log::info('Encuesta de satisfacción enviada', [
                'encounter_id' => $encounter->id,
                'patient_id' => $patient->id,
                'survey_id' => $activeSurvey->id,
                'survey_response_id' => $surveyResponse->id,
            ]);

        } catch (\Exception $e) {
            Log::error('Error al enviar encuesta de satisfacción', [
                'encounter_id' => $encounter->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
