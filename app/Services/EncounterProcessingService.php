<?php

namespace App\Services;

use App\Ai\Agents\EncounterProcessingAgent;
use App\Models\Encounter;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EncounterProcessingService
{
    /**
     * Process a medical transcription and populate encounter with SOAP data.
     *
     * @param  int  $encounterId  ID of the encounter to populate
     * @param  string  $transcription  Medical transcription text or audio content
     * @return array Processing result with status and summary
     */
    public function processVoiceTranscription(int $encounterId, string $transcription): array
    {
        Log::info('EncounterProcessingService: Starting transcription processing', [
            'encounter_id' => $encounterId,
            'transcription_length' => strlen($transcription),
            'user_id' => Auth::id(),
        ]);

        try {
            // Verify encounter exists and user has access
            $encounter = Encounter::findOrFail($encounterId);

            // Verify user has access to this encounter via client scoping
            $userClient = Auth::user()?->getCurrentClient();
            if (! $userClient || $encounter->patient->client_id !== $userClient->id) {
                throw new AuthorizationException('User does not have access to this encounter');
            }

            // Create and run the agent
            $agent = new EncounterProcessingAgent($encounterId, $transcription);
            $result = $agent->process();

            Log::info('EncounterProcessingService: Processing completed successfully', [
                'encounter_id' => $encounterId,
                'status' => $result['processing_status'] ?? 'unknown',
                'sections_completed' => count($result['sections_completed'] ?? []),
            ]);

            return [
                'success' => true,
                'encounter_id' => $encounterId,
                'processing_result' => $result,
            ];
        } catch (AuthorizationException $e) {
            Log::warning('EncounterProcessingService: Authorization denied', [
                'encounter_id' => $encounterId,
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);

            return [
                'success' => false,
                'error' => 'Unauthorized',
                'message' => $e->getMessage(),
                'encounter_id' => $encounterId,
            ];
        } catch (\Exception $e) {
            Log::error('EncounterProcessingService: Processing failed', [
                'encounter_id' => $encounterId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
            ]);

            return [
                'success' => false,
                'error' => 'Processing failed',
                'message' => $e->getMessage(),
                'encounter_id' => $encounterId,
            ];
        }
    }

    /**
     * Get processing status and summary for an encounter.
     */
    public function getEncounterSummary(int $encounterId): array
    {
        try {
            $encounter = Encounter::with([
                'presentIllnesses',
                'vitalSigns',
                'physicalExams',
                'diagnoses',
                'medications',
                'serviceRequests',
            ])->findOrFail($encounterId);

            return [
                'success' => true,
                'encounter_id' => $encounterId,
                'summary' => [
                    'reason' => $encounter->reason,
                    'general_notes' => $encounter->general_note,
                    'subjective_items' => [
                        'present_illnesses' => $encounter->presentIllnesses->count(),
                    ],
                    'objective_items' => [
                        'vital_signs' => $encounter->vitalSigns->count(),
                        'physical_exams' => $encounter->physicalExams->count(),
                    ],
                    'assessment_items' => [
                        'diagnoses' => $encounter->diagnoses->count(),
                    ],
                    'plan_items' => [
                        'medications' => $encounter->medications->count(),
                        'service_requests' => $encounter->serviceRequests->count(),
                    ],
                    'total_soap_items' => $encounter->presentIllnesses->count()
                        + $encounter->vitalSigns->count()
                        + $encounter->physicalExams->count()
                        + $encounter->diagnoses->count()
                        + $encounter->medications->count()
                        + $encounter->serviceRequests->count(),
                ],
            ];
        } catch (\Exception $e) {
            Log::error('EncounterProcessingService: Failed to get summary', [
                'encounter_id' => $encounterId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'encounter_id' => $encounterId,
            ];
        }
    }
}
