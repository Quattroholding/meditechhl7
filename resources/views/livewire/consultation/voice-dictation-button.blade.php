<div @if(config('services.claude.voice_dictation_enabled', true)) x-data="voiceDictation(@this)" x-init="init()" @endif>
    @if(config('services.claude.voice_dictation_enabled', true))
    <!-- Botón Flotante de Dictado por Voz -->
    <button
        @click="toggleRecording()"
        type="button"
        :class="{
            'btn-primary': !isRecording && !isProcessing,
            'btn-danger': isRecording,
            'btn-warning': isProcessing
        }"
        class="btn"
        style="position: fixed; bottom: 110px; right: 30px; z-index: 9999; border-radius: 50%; width: 60px; height: 60px; box-shadow: 0 4px 12px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; font-size: 24px;"
        :title="buttonTitle"
        :disabled="isProcessing"
    >
        <!-- Idle State: Blue Microphone Icon -->
        <template x-if="!isRecording && !isProcessing">
            <i class="fa fa-microphone"></i>
        </template>

        <!-- Recording State: Red Pulsating Microphone -->
        <template x-if="isRecording && !isProcessing">
            <i class="fa fa-microphone" style="animation: pulse 1s infinite;"></i>
        </template>

        <!-- Processing State: Yellow Spinner -->
        <template x-if="isProcessing">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        </template>

        <!-- Recording Timer (shown when recording) -->
        <template x-if="isRecording">
            <span
                x-text="recordingTime"
                style="position: absolute; bottom: -25px; left: 50%; transform: translateX(-50%); font-size: 12px; white-space: nowrap; color: #dc3545; font-weight: bold;"
            ></span>
        </template>
    </button>

    <!-- Voice Command Indicator (shown when recording with voice commands enabled) -->
    <template x-if="isRecording && voiceCommandEnabled">
        <div
            style="position: fixed; bottom: 175px; right: 30px; z-index: 9998; background: rgba(0, 123, 255, 0.9); color: white; padding: 8px 12px; border-radius: 8px; font-size: 11px; box-shadow: 0 2px 8px rgba(0,0,0,0.2); max-width: 180px; text-align: center;"
        >
            <i class="fa fa-microphone-alt"></i>
            Diga <strong>"ENVIAR"</strong><br>para finalizar
        </div>
    </template>

    <!-- CSS for Pulsating Animation -->
    <style>
        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 1;
            }
            50% {
                transform: scale(1.2);
                opacity: 0.7;
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }
    </style>

    <!-- Hidden Audio Element (for potential playback) -->
    <audio x-ref="audioPlayer" style="display: none;"></audio>
    @endif
</div>

@push('scripts')
@if(config('services.claude.voice_dictation_enabled', true))
<script>
// Alpine.js component for voice dictation
function voiceDictation(livewireComponent) {
    return {
        // State
        isRecording: false,
        isProcessing: false,
        mediaRecorder: null,
        audioChunks: [],
        recordingStartTime: null,
        recordingTime: '0:00',
        timerInterval: null,
        autoStopTimeout: null,
        maxDuration: {{ config('services.claude.voice_dictation_max_duration', 300) }}, // seconds
        recognition: null,
        voiceCommandEnabled: true,

        // Computed properties
        get buttonTitle() {
            if (this.isProcessing) return 'Procesando dictado...';
            if (this.isRecording) return 'Detener grabación (o diga "ENVIAR")';
            return 'Iniciar dictado por voz';
        },

        // Initialize
        init() {
            // Check browser support
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                toastr.error('Su navegador no soporta grabación de audio. Por favor use Chrome, Firefox o Edge.');
                console.error('MediaDevices API not supported');
            }

            // Initialize speech recognition for voice commands
            this.initSpeechRecognition();

            // Listen for Livewire events
            Livewire.on('voice-dictation-processing', () => {
                this.isProcessing = true;
            });

            Livewire.on('voice-dictation-completed', () => {
                this.isProcessing = false;
            });

            Livewire.on('voice-dictation-failed', () => {
                this.isProcessing = false;
            });
        },

        // Initialize speech recognition for voice commands
        initSpeechRecognition() {
            // Check if Speech Recognition is supported
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

            if (!SpeechRecognition) {
                console.warn('Speech Recognition API not supported in this browser');
                this.voiceCommandEnabled = false;
                return;
            }

            this.recognition = new SpeechRecognition();
            this.recognition.lang = 'es-ES'; // Spanish language
            this.recognition.continuous = true; // Keep listening
            this.recognition.interimResults = false; // Only final results

            // Handle speech recognition results
            this.recognition.onresult = (event) => {
                const lastResultIndex = event.results.length - 1;
                const transcript = event.results[lastResultIndex][0].transcript.toLowerCase().trim();

                console.log('Voice command detected:', transcript);

                // Check for stop command: "enviar", "envía", "stop", "detener"
                if (transcript.includes('enviar') ||
                    transcript.includes('envía') ||
                    transcript.includes('stop') ||
                    transcript.includes('detener')) {

                    console.log('Stop command recognized:', transcript);
                    toastr.info('Comando de voz detectado: finalizando dictado...');

                    // Stop recording
                    if (this.isRecording) {
                        this.stopRecording();
                    }
                }
            };

            this.recognition.onerror = (event) => {
                console.error('Speech recognition error:', event.error);
            };

            this.recognition.onend = () => {
                // Restart if still recording
                if (this.isRecording && this.voiceCommandEnabled) {
                    try {
                        this.recognition.start();
                    } catch (e) {
                        console.warn('Could not restart speech recognition:', e);
                    }
                }
            };
        },

        // Toggle recording on/off
        async toggleRecording() {
            if (this.isRecording) {
                this.stopRecording();
            } else {
                await this.startRecording();
            }
        },

        // Start recording
        async startRecording() {
            try {
                // Check if already recording
                if (this.isRecording) {
                    console.warn('Already recording');
                    return;
                }

                // Request microphone permission
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });

                // Detect supported MIME type
                let mimeType = 'audio/webm';
                if (MediaRecorder.isTypeSupported('audio/webm')) {
                    mimeType = 'audio/webm';
                } else if (MediaRecorder.isTypeSupported('audio/mp4')) {
                    mimeType = 'audio/mp4';
                } else if (MediaRecorder.isTypeSupported('audio/ogg')) {
                    mimeType = 'audio/ogg';
                } else {
                    throw new Error('No soporta formatos de audio compatibles');
                }

                this.mimeType = mimeType;

                // Create MediaRecorder
                this.mediaRecorder = new MediaRecorder(stream, { mimeType });
                this.audioChunks = [];

                // Event: data available
                this.mediaRecorder.ondataavailable = (event) => {
                    if (event.data.size > 0) {
                        this.audioChunks.push(event.data);
                    }
                };

                // Event: recording stopped
                this.mediaRecorder.onstop = () => {
                    this.handleRecordingComplete();
                };

                // Start recording
                this.mediaRecorder.start();
                this.isRecording = true;
                this.recordingStartTime = Date.now();

                // Start timer
                this.startTimer();

                // Start voice command recognition
                if (this.voiceCommandEnabled && this.recognition) {
                    try {
                        this.recognition.start();
                        console.log('Voice command recognition started');
                    } catch (e) {
                        console.warn('Could not start voice command recognition:', e);
                    }
                }

                // Auto-stop after max duration
                this.autoStopTimeout = setTimeout(() => {
                    if (this.isRecording) {
                        toastr.warning('Grabación detenida automáticamente después de ' + (this.maxDuration / 60) + ' minutos');
                        this.stopRecording();
                    }
                }, this.maxDuration * 1000);

                const commandHint = this.voiceCommandEnabled ? ' Diga "ENVIAR" para finalizar.' : '';
                toastr.info('Grabación iniciada. Hable claramente cerca del micrófono.' + commandHint);

            } catch (error) {
                console.error('Error starting recording:', error);

                if (error.name === 'NotAllowedError' || error.name === 'PermissionDeniedError') {
                    toastr.error('Permiso de micrófono denegado. Por favor permita el acceso al micrófono.');
                } else {
                    toastr.error('Error al iniciar la grabación: ' + error.message);
                }

                this.isRecording = false;
            }
        },

        // Stop recording
        stopRecording() {
            if (!this.isRecording || !this.mediaRecorder) {
                console.warn('Not recording');
                return;
            }

            this.mediaRecorder.stop();
            this.isRecording = false;

            // Stop all tracks
            this.mediaRecorder.stream.getTracks().forEach(track => track.stop());

            // Stop voice command recognition
            if (this.recognition) {
                try {
                    this.recognition.stop();
                    console.log('Voice command recognition stopped');
                } catch (e) {
                    console.warn('Could not stop voice command recognition:', e);
                }
            }

            // Clear auto-stop timeout
            if (this.autoStopTimeout) {
                clearTimeout(this.autoStopTimeout);
                this.autoStopTimeout = null;
            }

            // Stop timer
            this.stopTimer();

            toastr.info('Procesando dictado...');
        },

        // Handle recording complete
        async handleRecordingComplete() {
            try {
                // Create audio blob
                const audioBlob = new Blob(this.audioChunks, { type: this.mimeType });

                // Validate size (max 10MB)
                const maxSize = {{ config('services.claude.voice_dictation_max_file_size', 10485760) }};
                if (audioBlob.size > maxSize) {
                    toastr.error('El archivo de audio es muy grande (máx ' + (maxSize / 1048576).toFixed(1) + ' MB)');
                    this.isProcessing = false;
                    return;
                }

                // Convert to base64
                const audioBase64 = await this.blobToBase64(audioBlob);

                // Remove data URI prefix (e.g., "data:audio/webm;base64,")
                const base64Data = audioBase64.split(',')[1];

                // Send to Livewire backend
                this.isProcessing = true;
                livewireComponent.call('processAudioDictation', base64Data, this.mimeType);

            } catch (error) {
                console.error('Error processing recording:', error);
                toastr.error('Error al procesar la grabación: ' + error.message);
                this.isProcessing = false;
            }
        },

        // Convert Blob to Base64
        blobToBase64(blob) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onloadend = () => resolve(reader.result);
                reader.onerror = reject;
                reader.readAsDataURL(blob);
            });
        },

        // Start timer
        startTimer() {
            this.timerInterval = setInterval(() => {
                const elapsed = Math.floor((Date.now() - this.recordingStartTime) / 1000);
                const minutes = Math.floor(elapsed / 60);
                const seconds = elapsed % 60;
                this.recordingTime = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            }, 1000);
        },

        // Stop timer
        stopTimer() {
            if (this.timerInterval) {
                clearInterval(this.timerInterval);
                this.timerInterval = null;
            }
            this.recordingTime = '0:00';
        }
    };
}
</script>
@endif
@endpush
