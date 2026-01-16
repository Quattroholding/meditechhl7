<?php

namespace App\Observers;

use App\Models\Encounter;
use App\Models\Survey;
use App\Models\SurveyResponse;
use App\Notifications\EncounterPrescriptionNotification;
use App\Notifications\SendPatientSatisfactionSurvey;
use App\Services\EncounterPrescriptionPdfService;
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

        $this->sendPrescriptionNotification($encounter);
    }

    /**
     * Send prescription notification to patient with attached PDFs
     * Only sends if practitioner has signature and seal, and there are unsent prescriptions
     */
    private function sendPrescriptionNotification(Encounter $encounter): void
    {
        try {
            $pdfService = new EncounterPrescriptionPdfService;
            $practitioner = $encounter->practitioner;
            $patient = $encounter->patient;


            // Validate practitioner has signature and seal
            if (! $pdfService->practitionerHasSignatureAndSeal($practitioner)) {
                Log::info('Practitioner no tiene firma y sello configurados, no se envian recetas', [
                    'encounter_id' => $encounter->id,
                    'practitioner_id' => $practitioner->id,
                ]);

                return;
            }

            // Validate patient has email
            if (! $patient || ! $patient->email) {
                Log::info('Paciente sin email para envio de recetas', [
                    'encounter_id' => $encounter->id,
                    'patient_id' => $patient?->id,
                ]);

                return;
            }

            // Check for unsent prescriptions
            $hasUnsentMedications = $encounter->medicationRequests()
                ->whereNull('notification_sent_at')
                ->exists();

            $hasUnsentServiceRequests = $encounter->serviceRequests()
                ->whereNull('notification_sent_at')
                ->exists();

            // If no unsent prescriptions, skip
            if (! $hasUnsentMedications && ! $hasUnsentServiceRequests) {
                Log::info('No hay recetas pendientes de envio para este encounter', [
                    'encounter_id' => $encounter->id,
                    'patient_id' => $patient->id,
                ]);

                return;
            }

            // Send notification with PDFs attached
            $patient->notify(new EncounterPrescriptionNotification(
                $encounter,
                $hasUnsentMedications,
                $hasUnsentServiceRequests
            ));

            // Mark prescriptions as sent
            if ($hasUnsentMedications) {
                $pdfService->markMedicationsAsSent($encounter);
            }

            if ($hasUnsentServiceRequests) {
                $pdfService->markServiceRequestsAsSent($encounter);
            }

            Log::info('Notificacion de recetas medicas enviada', [
                'encounter_id' => $encounter->id,
                'patient_id' => $patient->id,
                'practitioner_id' => $practitioner->id,
                'has_medications' => $hasUnsentMedications,
                'has_service_requests' => $hasUnsentServiceRequests,
                'patient_email' => $patient->email,
            ]);

        } catch (\Exception $e) {
            Log::error('Error al enviar notificacion de recetas medicas', [
                'encounter_id' => $encounter->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    private function sendSatisfactionSurvey(Encounter $encounter): void
    {
        try {
            $activeSurvey = Survey::where('status', 'active')
                ->where('trigger_point', 'after_encounter')
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
                'encounter_id' => $encounter->id,
                'practitioner_id' => $encounter->practitioner_id,
                'client_id' => $encounter->appointment->client_id,
                'medical_speciality_id' => $encounter->appointment->medical_speciality_id,
                'status' => 'pending',
            ]);

            // Send survey notification via email, WhatsApp, and database
            $patient->notify(new SendPatientSatisfactionSurvey($surveyResponse, $encounter, $activeSurvey));

            Log::info('Encuesta de satisfacción enviada', [
                'encounter_id' => $encounter->id,
                'patient_id' => $patient->id,
                'survey_id' => $activeSurvey->id,
                'survey_response_id' => $surveyResponse->id,
                'has_email' => (bool) $patient->email,
                'has_phone' => (bool) $patient->phone,
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
