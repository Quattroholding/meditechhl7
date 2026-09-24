<?php

namespace App\Livewire\Consultation;

use App\Models\Encounter;
use App\Services\ClaudeService;
use App\Services\EncounterProcessingService;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class VoiceDictationButton extends Component
{
    public $encounter_id;

    public $encounter;

    public $isRecording = false;

    public $isProcessing = false;

    public $errorMessage = null;

    public $transcription = null;

    public $voiceDictationEnabled = false;

    public function mount()
    {
        $this->encounter = Encounter::with(['patient', 'practitioner'])
            ->findOrFail($this->encounter_id);

        // Check if voice dictation is enabled for the current client
        $client = auth()->user()?->getCurrentClient();
        $this->voiceDictationEnabled = $client && $client->voice_dictation_enabled;
    }

    /**
     * Process audio dictation from base64 encoded audio
     */
    public function processAudioDictation(string $audioBase64, string $mimeType)
    {
        // Check if voice dictation is enabled for the current client
        $client = auth()->user()->getCurrentClient();
        if (! $client || ! $client->voice_dictation_enabled) {
            $this->errorMessage = 'El dictado por voz no está habilitado';
            $this->dispatch('showToastrConsultation', [
                'type' => 'error',
                'message' => 'El dictado por voz no está habilitado en el sistema',
            ]);

            return;
        }

        try {
            $this->isProcessing = true;
            $this->errorMessage = null;
            $this->transcription = null;

            Log::info('VoiceDictationButton: Processing audio dictation', [
                'encounter_id' => $this->encounter_id,
                'mime_type' => $mimeType,
                'audio_size' => strlen($audioBase64),
            ]);

            // Step 1: Call ClaudeService to transcribe audio (OpenAI Whisper)
            $claudeService = app(ClaudeService::class);
            $extractedData = $claudeService->processMedicalDictation(
                $audioBase64,
                $mimeType,
                $this->encounter_id
            );

            // Store transcription for display
            $this->transcription = $extractedData['transcription'] ?? null;

            // Save transcription and general notes to encounter
            $notes = [];
            if ($this->transcription) {
                $notes[] = "**Transcripción del Dictado:**\n{$this->transcription}";
            }
            if (isset($extractedData['general_notes']) && ! empty($extractedData['general_notes'])) {
                $notes[] = "**Notas Generales:**\n{$extractedData['general_notes']}";
            }

            if (! empty($notes)) {
                $this->encounter->general_note = implode("\n\n---\n\n", $notes);
                $this->encounter->save();

                Log::info('VoiceDictationButton: Notes saved to general_note', [
                    'encounter_id' => $this->encounter_id,
                    'transcription_length' => strlen($this->transcription ?? ''),
                    'general_notes_length' => strlen($extractedData['general_notes'] ?? ''),
                ]);
            }

            // Step 2: Process transcription with EncounterProcessingAgent
            // The agent autonomously uses tools to search for codes and populate SOAP sections
            $processingService = app(EncounterProcessingService::class);
            $processingResult = $processingService->processVoiceTranscription(
                $this->encounter_id,
                $this->transcription ?? ''
            );

            if ($processingResult['success']) {
                Log::info('VoiceDictationButton: EncounterProcessingAgent completed successfully', [
                    'encounter_id' => $this->encounter_id,
                    'processing_status' => $processingResult['processing_result']['processing_status'] ?? 'unknown',
                ]);

                // Dispatch events to update each component with extracted data
                // This maintains backward compatibility with existing Livewire components
                $this->dispatchFieldUpdates($extractedData);

                // Show success message
                $this->dispatch('showToastrConsultation', [
                    'type' => 'success',
                    'message' => 'Dictado procesado correctamente. Los campos se han actualizado automáticamente.',
                ]);

                // Notify frontend that processing completed successfully
                $this->dispatch('voice-dictation-completed', [
                    'processing_result' => $processingResult['processing_result'],
                ]);

                Log::info('VoiceDictationButton: Audio dictation processed successfully', [
                    'encounter_id' => $this->encounter_id,
                    'has_transcription' => ! empty($this->transcription),
                ]);
            } else {
                // Processing failed but transcription was successful
                Log::warning('VoiceDictationButton: EncounterProcessingAgent failed', [
                    'encounter_id' => $this->encounter_id,
                    'error' => $processingResult['message'] ?? 'Unknown error',
                ]);

                $this->dispatch('showToastrConsultation', [
                    'type' => 'warning',
                    'message' => 'Transcripción completada pero el procesamiento automático falló. Revise y edite manualmente.',
                ]);

                // Still dispatch field updates from basic extraction
                $this->dispatchFieldUpdates($extractedData);
                $this->dispatch('voice-dictation-completed');
            }
        } catch (\InvalidArgumentException $e) {
            $this->errorMessage = $e->getMessage();
            $this->dispatch('showToastrConsultation', [
                'type' => 'error',
                'message' => $e->getMessage(),
            ]);

            // Notify frontend that processing failed
            $this->dispatch('voice-dictation-failed');

            Log::warning('VoiceDictationButton: Invalid argument', [
                'error' => $e->getMessage(),
                'encounter_id' => $this->encounter_id,
            ]);
        } catch (\Exception $e) {
            $this->errorMessage = 'Error al procesar el dictado. Por favor intente nuevamente.';
            $this->dispatch('showToastrConsultation', [
                'type' => 'error',
                'message' => 'Error al procesar el dictado: '.$e->getMessage(),
            ]);

            // Notify frontend that processing failed
            $this->dispatch('voice-dictation-failed');

            Log::error('VoiceDictationButton: Error processing audio dictation', [
                'error' => $e->getMessage(),
                'encounter_id' => $this->encounter_id,
                'trace' => $e->getTraceAsString(),
            ]);
        } finally {
            $this->isProcessing = false;
        }
    }

    /**
     * Dispatch events to update individual components with extracted data
     */
    private function dispatchFieldUpdates(array $extractedData)
    {
        // Dispatch reason for encounter
        if (isset($extractedData['reason'])) {
            $this->dispatch('voice-dictation-reason', [
                'reason' => $extractedData['reason'],
            ]);

            Log::debug('Dispatched voice-dictation-reason event', ['reason' => $extractedData['reason']]);
        }

        // Dispatch present illness
        if (isset($extractedData['present_illness']) && ! empty($extractedData['present_illness'])) {
            Log::info('VoiceDictationButton: About to dispatch present illness', [
                'present_illness' => $extractedData['present_illness'],
            ]);

            $this->dispatch('voice-dictation-present-illness', $extractedData['present_illness']);
            Log::debug('Dispatched voice-dictation-present-illness event');
        } else {
            Log::warning('VoiceDictationButton: No present illness data to dispatch', [
                'has_key' => isset($extractedData['present_illness']),
                'is_empty' => isset($extractedData['present_illness']) ? empty($extractedData['present_illness']) : 'N/A',
            ]);
        }

        // Dispatch vital signs
        if (isset($extractedData['vital_signs']) && ! empty($extractedData['vital_signs'])) {
            $this->dispatch('voice-dictation-vital-signs', $extractedData['vital_signs']);
            Log::debug('Dispatched voice-dictation-vital-signs event', ['count' => count($extractedData['vital_signs'])]);
        }

        // Dispatch physical exam findings
        if (isset($extractedData['physical_exam']) && ! empty($extractedData['physical_exam'])) {
            $this->dispatch('voice-dictation-physical-exam', $extractedData['physical_exam']);
            Log::debug('Dispatched voice-dictation-physical-exam event', ['count' => count($extractedData['physical_exam'])]);
        }

        // Dispatch diagnostics (if any)
        if (isset($extractedData['diagnostics']) && ! empty($extractedData['diagnostics'])) {
            Log::info('VoiceDictationButton: About to dispatch diagnostics', [
                'count' => count($extractedData['diagnostics']),
                'diagnostics' => $extractedData['diagnostics'],
            ]);

            $this->dispatch('voice-dictation-diagnostics',
                diagnostics: $extractedData['diagnostics']
            );

            Log::info('VoiceDictationButton: Diagnostics event dispatched');
        }

        // Dispatch medications (if any)
        if (isset($extractedData['medications']) && ! empty($extractedData['medications'])) {
            Log::info('VoiceDictationButton: About to dispatch medications', [
                'count' => count($extractedData['medications']),
                'medications' => $extractedData['medications'],
            ]);

            // Dispatch to all components on the page
            $this->dispatch('voice-dictation-medications',
                medications: $extractedData['medications']
            );

            Log::info('VoiceDictationButton: Medications event dispatched');
        }

        // Dispatch service requests (laboratory, images, procedures)
        if (isset($extractedData['service_requests']) && ! empty($extractedData['service_requests'])) {
            Log::info('VoiceDictationButton: About to dispatch service requests', [
                'count' => count($extractedData['service_requests']),
                'service_requests' => $extractedData['service_requests'],
            ]);

            // Dispatch to service request components
            $this->dispatch('voice-dictation-service-requests',
                serviceRequests: $extractedData['service_requests']
            );

            Log::info('VoiceDictationButton: Service requests event dispatched');
        }
    }

    public function render()
    {
        return view('livewire.consultation.voice-dictation-button');
    }
}
