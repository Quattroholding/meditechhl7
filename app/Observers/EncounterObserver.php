<?php

namespace App\Observers;

use App\Models\Encounter;
use App\Models\Survey;
use App\Models\SurveyResponse;
use App\Models\User;
use App\Notifications\EncounterPrescriptionNotification;
use App\Notifications\SendPatientSatisfactionSurvey;
use App\Services\EncounterPrescriptionPdfService;
use App\Services\EncounterSnapshotService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class EncounterObserver
{
    /**
     * Safe logging that doesn't throw exceptions
     */
    private function safeLog(string $level, string $message, array $context = []): void
    {
        try {
            Log::$level($message, $context);
        } catch (\Exception $e) {
            // Silently fail - logging should never stop execution
        }
    }

    /**
     * Handle the Encounter "created" event.
     */
    public function created(Encounter $encounter): void
    {
        $this->clearDashboardCache();
    }

    /**
     * Handle the Encounter "updated" event.
     */
    public function updated(Encounter $encounter): void
    {
        // Handle snapshots first (critical functionality)
        try {
            $this->handleEncounterSnapshots($encounter);
        } catch (\Exception $e) {
            $this->safeLog('error', 'Error crítico al crear snapshot', [
                'encounter_id' => $encounter->id,
                'error' => $e->getMessage(),
            ]);
        }

        // Handle satisfaction survey
        if ($encounter->isDirty('status') && $encounter->getRawOriginal('status') == 'in-progress') {
            try {
                $this->sendSatisfactionSurvey($encounter);
            } catch (\Exception $e) {
                // Log error but don't stop execution
                $this->safeLog('error', 'Error al enviar encuesta (no crítico)', [
                    'encounter_id' => $encounter->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Handle prescription notification
        try {
            $this->sendPrescriptionNotification($encounter);
        } catch (\Exception $e) {
            $this->safeLog('error', 'Error al enviar notificación de prescripción', [
                'encounter_id' => $encounter->id,
                'error' => $e->getMessage(),
            ]);
        }

        $this->clearDashboardCache();
    }

    /**
     * Handle the Encounter "deleted" event.
     */
    public function deleted(Encounter $encounter): void
    {
        $this->clearDashboardCache();
    }

    /**
     * Handle encounter snapshot creation
     */
    private function handleEncounterSnapshots(Encounter $encounter): void
    {
        try {
            $snapshotService = app(EncounterSnapshotService::class);

            // Get RAW status values (without accessors)
            $currentStatus = $encounter->getRawOriginal('status') ?? $encounter->getAttributes()['status'] ?? null;
            $previousStatus = $encounter->getOriginal('status');

            // Case 1: Status changed to "finished" - Create initial snapshot

            if ($encounter->wasChanged('status') && $currentStatus === 'finished') {
                $snapshotService->createSnapshot(
                    $encounter,
                    'initial_finish',
                    'Encounter marked as finished'
                );

                $this->safeLog('info', 'Snapshot inicial creado al finalizar encounter', [
                    'encounter_id' => $encounter->id,
                    'previous_status' => $previousStatus,
                    'current_status' => $currentStatus,
                ]);

                return;
            }

            // Case 2: Encounter already finished but was modified - Create post-modification snapshot
            if ($currentStatus === 'finished' && $encounter->wasChanged()) {
                // Check if there are actual changes worth snapshotting
                if ($snapshotService->hasChangedSinceLastSnapshot($encounter)) {
                    $changedFields = array_keys($encounter->getChanges());
                    $changeSummary = 'Modified fields: '.implode(', ', $changedFields);

                    $snapshotService->createSnapshot(
                        $encounter,
                        'post_modification',
                        $changeSummary
                    );

                    $this->safeLog('info', 'Snapshot post-modificación creado', [
                        'encounter_id' => $encounter->id,
                        'changed_fields' => $changedFields,
                    ]);
                }
            }

        } catch (\Exception $e) {
            $this->safeLog('error', 'Error al crear snapshot de encounter', [
                'encounter_id' => $encounter->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Send prescription notification to patient with attached PDFs
     * Only sends if practitioner has signature and seal, and there are unsent prescriptions
     * Also sends to all receptionists (asistente role) associated with the same client
     */
    private function sendPrescriptionNotification(Encounter $encounter): void
    {
        try {
            $pdfService = new EncounterPrescriptionPdfService;
            $practitioner = $encounter->practitioner;
            $patient = $encounter->patient;

            // Validate practitioner has signature and seal
            if (! $pdfService->practitionerHasSignatureAndSeal($practitioner)) {
                $this->safeLog('info', 'Practitioner no tiene firma y sello configurados, no se envian recetas', [
                    'encounter_id' => $encounter->id,
                    'practitioner_id' => $practitioner->id,
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
                $this->safeLog('info', 'No hay recetas pendientes de envio para este encounter', [
                    'encounter_id' => $encounter->id,
                    'patient_id' => $patient->id,
                ]);

                return;
            }

            // Send notification to patient
            if ($patient && $patient->email) {
                $patient->notify(new EncounterPrescriptionNotification(
                    $encounter,
                    $hasUnsentMedications,
                    $hasUnsentServiceRequests
                ));

                $this->safeLog('info', 'Notificacion de recetas medicas enviada al paciente', [
                    'encounter_id' => $encounter->id,
                    'patient_id' => $patient->id,
                    'patient_email' => $patient->email,
                ]);
            } else {
                $this->safeLog('info', 'Paciente sin email para envio de recetas', [
                    'encounter_id' => $encounter->id,
                    'patient_id' => $patient?->id,
                ]);
            }

            // Send notification to all receptionists (asistente role) associated with the same client
            $client = $encounter->appointment->client;
            if ($client) {
                $receptionists = User::role('asistente')
                    ->whereHas('clients', function ($query) use ($client) {
                        $query->where('clients.id', $client->id);
                    })
                    ->whereNotNull('email')
                    ->get();

                $notifiedCount = 0;
                foreach ($receptionists as $receptionist) {
                    try {
                        $receptionist->notify(new EncounterPrescriptionNotification(
                            $encounter,
                            $hasUnsentMedications,
                            $hasUnsentServiceRequests
                        ));
                        $notifiedCount++;
                    } catch (\Exception $e) {
                        $this->safeLog('error', 'Error al enviar notificacion a recepcionista', [
                            'encounter_id' => $encounter->id,
                            'user_id' => $receptionist->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }

                $this->safeLog('info', 'Notificaciones de recetas enviadas a recepcionistas del cliente', [
                    'encounter_id' => $encounter->id,
                    'client_id' => $client->id,
                    'receptionists_notified' => $notifiedCount,
                    'total_receptionists' => $receptionists->count(),
                ]);
            }

            // Mark prescriptions as sent
            if ($hasUnsentMedications) {
                $pdfService->markMedicationsAsSent($encounter);
            }

            if ($hasUnsentServiceRequests) {
                $pdfService->markServiceRequestsAsSent($encounter);
            }

            $this->safeLog('info', 'Notificacion de recetas medicas completada', [
                'encounter_id' => $encounter->id,
                'patient_id' => $patient->id,
                'practitioner_id' => $practitioner->id,
                'has_medications' => $hasUnsentMedications,
                'has_service_requests' => $hasUnsentServiceRequests,
            ]);

        } catch (\Exception $e) {
            $this->safeLog('error', 'Error al enviar notificacion de recetas medicas', [
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
                $this->safeLog('info', 'No hay encuesta activa disponible para enviar', [
                    'encounter_id' => $encounter->id,
                ]);

                return;
            }

            $patient = $encounter->patient;
            if (! $patient || ! $patient->email) {
                $this->safeLog('info', 'Paciente sin email para envío de encuesta', [
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
                $this->safeLog('info', 'Ya existe una respuesta de encuesta para este paciente', [
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

            // Send survey notification via email, WhatsApp, and database (delayed 15 minutes)
            $patient->notify((new SendPatientSatisfactionSurvey($surveyResponse, $encounter, $activeSurvey))->delay(now()->addMinutes(15)));

            $this->safeLog('info', 'Encuesta de satisfacción enviada', [
                'encounter_id' => $encounter->id,
                'patient_id' => $patient->id,
                'survey_id' => $activeSurvey->id,
                'survey_response_id' => $surveyResponse->id,
                'has_email' => (bool) $patient->email,
                'has_phone' => (bool) $patient->phone,
            ]);

        } catch (\Exception $e) {
            $this->safeLog('error', 'Error al enviar encuesta de satisfacción', [
                'encounter_id' => $encounter->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Clear dashboard cache for encounters
     */
    private function clearDashboardCache(): void
    {
        Cache::tags(['dashboard', 'encounters'])->flush();
    }
}
