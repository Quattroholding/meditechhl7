<?php

namespace App\Livewire\Consultation;

use App\Models\Encounter;
use App\Services\ClaudeService;
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

    public function mount()
    {
        $this->encounter = Encounter::with(['patient', 'practitioner'])
            ->findOrFail($this->encounter_id);
    }

    /**
     * Process audio dictation from base64 encoded audio
     */
    public function processAudioDictation(string $audioBase64, string $mimeType)
    {
        // Check if voice dictation is enabled
        if (! config('services.claude.voice_dictation_enabled', true)) {
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

            // Call ClaudeService to process the audio
            $claudeService = app(ClaudeService::class);
            $extractedData = $claudeService->processMedicalDictation(
                $audioBase64,
                $mimeType,
                $this->encounter_id
            );

            // Store transcription for display
            $this->transcription = $extractedData['transcription'] ?? null;

            // Save transcription to encounter general_note
            if ($this->transcription) {
                $this->encounter->general_note = $this->transcription;
                $this->encounter->save();

                Log::info('VoiceDictationButton: Transcription saved to general_note', [
                    'encounter_id' => $this->encounter_id,
                    'transcription_length' => strlen($this->transcription),
                ]);
            }

            // Dispatch events to update each component with extracted data
            $this->dispatchFieldUpdates($extractedData);

            // Show success message
            $this->dispatch('showToastrConsultation', [
                'type' => 'success',
                'message' => 'Dictado procesado correctamente. Los campos se han actualizado automáticamente.',
            ]);

            // Notify frontend that processing completed successfully
            $this->dispatch('voice-dictation-completed');

            Log::info('VoiceDictationButton: Audio dictation processed successfully', [
                'encounter_id' => $this->encounter_id,
                'has_transcription' => ! empty($this->transcription),
            ]);
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
            $this->dispatch('voice-dictation-diagnostics', [
                'diagnostics' => $extractedData['diagnostics'],
            ]);
            Log::debug('Dispatched voice-dictation-diagnostics event', ['count' => count($extractedData['diagnostics'])]);
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
