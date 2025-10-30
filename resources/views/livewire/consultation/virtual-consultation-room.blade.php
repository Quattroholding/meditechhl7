<div class="virtual-consultation-wrapper" x-data="jitsiConsultation()" wire:ignore.self>
    {{-- Botón flotante para iniciar videoconsulta --}}
    <div x-show="initialized && !modalOpen" x-transition class="video-call-button-container">
        @if($isDoctor && !$sessionActive)
            <button @click="openModal(); $wire.startSession()"
                    class="btn-start-video-call"
                    title="Iniciar Video Consulta">
                <svg class="camera-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
                <span class="button-text">Iniciar Video Consulta</span>
            </button>
        @elseif(!$isDoctor && $appointment->virtual_session_started_at && !$sessionActive)
            <button @click="openModal(); $wire.joinSession()"
                    class="btn-start-video-call btn-join"
                    title="Unirse a Video Consulta">
                <svg class="camera-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
                <span class="button-text">Unirse a Video Consulta</span>
            </button>
        @elseif($sessionActive)
            <div class="button-group">
                @if($isDoctor)
                    <button @click="copyPatientLink()"
                            class="btn-copy-link btn-active"
                            title="Copiar enlace para el paciente">
                        <svg class="camera-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        <span class="button-text">Copiar enlace</span>
                    </button>
                @endif
                <button @click="openModal()"
                        class="btn-start-video-call btn-active"
                        title="Abrir Video Consulta">
                    <svg class="camera-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    <span class="button-text">Abrir Video Consulta</span>
                    <span class="active-indicator"></span>
                </button>
            </div>
        @endif
    </div>

    {{-- Modal redimensionable --}}
    <div x-show="modalOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="video-modal-container"
         :style="`width: ${modalWidth}px; height: ${modalHeight}px; top: ${modalTop}px; left: ${modalLeft}px;`"
         @mousedown.self="startDrag($event)"
         style="display: none;">

        {{-- Header con controles --}}
        <div class="modal-header"
             @mousedown="startDrag($event)">
            <div class="modal-title">
                <svg class="title-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
                <div>
                    <span class="title-text">
                        @if($isDoctor)
                            <strong>Paciente:</strong> {{ $appointment->patient->name ?? 'Paciente' }}
                        @else
                            <strong>Doctor:</strong> {{ $appointment->practitioner->name ?? 'Médico' }}
                        @endif
                    </span>
                    <span class="connection-status" x-show="sessionActive">
                        <span class="status-dot" :class="{ 'connected': connectionStatus === 'Conectado' }"></span>
                        <span x-text="connectionStatus"></span>
                    </span>
                </div>
            </div>

            <div class="modal-controls">
                {{-- Copy Link Button (only for doctor) --}}
                @if($isDoctor)
                    <button @click="copyPatientLink()"
                            class="control-btn control-btn-copy"
                            title="Copiar enlace para paciente">
                        <svg class="control-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                    </button>
                @endif

                {{-- Minimizar --}}
                <button @click="minimizeModal()"
                        class="control-btn"
                        title="Minimizar">
                    <svg class="control-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                    </svg>
                </button>

                {{-- Cerrar (finalizar consulta) --}}
                @if($isDoctor && $sessionActive)
                    <button wire:click="endSession"
                            @click="closeModal()"
                            class="control-btn control-btn-danger"
                            title="Finalizar Consulta">
                        <svg class="control-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                @else
                    <button @click="closeModal()"
                            class="control-btn control-btn-danger"
                            title="Cerrar">
                        <svg class="control-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                @endif
            </div>
        </div>

        {{-- Área de video de Jitsi --}}
        <div id="jitsi-meet-container" class="jitsi-container">
            @if(!$sessionActive)
                <div class="flex flex-col items-center justify-center h-full text-white">
                    <svg class="w-16 h-16 mb-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    <p class="text-sm">Conectando...</p>
                </div>
            @endif
        </div>

        {{-- Controles de resize en las esquinas y bordes --}}
        <div class="resize-handle resize-handle-ne" @mousedown="startResize($event, 'ne')"></div>
        <div class="resize-handle resize-handle-nw" @mousedown="startResize($event, 'nw')"></div>
        <div class="resize-handle resize-handle-se" @mousedown="startResize($event, 'se')"></div>
        <div class="resize-handle resize-handle-sw" @mousedown="startResize($event, 'sw')"></div>
        <div class="resize-handle resize-handle-n" @mousedown="startResize($event, 'n')"></div>
        <div class="resize-handle resize-handle-s" @mousedown="startResize($event, 's')"></div>
        <div class="resize-handle resize-handle-e" @mousedown="startResize($event, 'e')"></div>
        <div class="resize-handle resize-handle-w" @mousedown="startResize($event, 'w')"></div>

        {{-- Footer con info y controles --}}
        @if($sessionActive)
            <div class="modal-footer">
                <div class="participants-info">
                    <span x-show="participantCount > 0">
                        <svg class="inline w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <span x-text="participantCount"></span> en línea
                    </span>
                </div>
            </div>
        @endif
    </div>

    {{-- Indicador de carga de Livewire --}}
    <div wire:loading class="loading-overlay">
        <div class="loading-spinner">
            <svg class="animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
    </div>

@assets
<script src="https://{{ config('services.jitsi.domain') }}/external_api.js" async></script>
@endassets

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('jitsiConsultation', () => ({
        api: null,
        modalOpen: false,
        sessionActive: false,
        connectionStatus: 'Listo',
        participantCount: 0,
        initialized: false,

        // Modal position and size
        modalWidth: 640,
        modalHeight: 480,
        modalTop: 20,
        modalLeft: 20,
        minWidth: 400,
        minHeight: 300,

        // Drag and resize state
        isDragging: false,
        isResizing: false,
        resizeDirection: null,
        startX: 0,
        startY: 0,
        startWidth: 0,
        startHeight: 0,
        startTop: 0,
        startLeft: 0,

        init() {
            console.log('Jitsi component initialized');
            this.modalOpen = false;

            // Get session active from Livewire component
            this.sessionActive = this.$wire.sessionActive;

            // Load saved position and size from localStorage
            const saved = localStorage.getItem('jitsi_modal_state');
            if (saved) {
                try {
                    const state = JSON.parse(saved);
                    this.modalWidth = state.width || 640;
                    this.modalHeight = state.height || 480;
                    this.modalTop = state.top || 20;
                    this.modalLeft = state.left || 20;
                } catch (e) {
                    console.error('Error loading modal state:', e);
                }
            }

            // Watch for session active changes from Livewire
            this.$watch('$wire.sessionActive', (value) => {
                this.sessionActive = value;
                if (value && !this.api) {
                    this.modalOpen = true;
                    setTimeout(() => {
                        this.startJitsiMeet(this.$wire.jitsiConfig);
                    }, 100);
                } else if (!value && this.api) {
                    this.hangUp();
                }
            });

            // Auto-open if session is already active on mount
            if (this.sessionActive) {
                this.modalOpen = true;
                setTimeout(() => {
                    this.startJitsiMeet(this.$wire.jitsiConfig);
                }, 100);
            }

            this.initialized = true;
        },

    openModal() {
        this.modalOpen = true;
    },

    minimizeModal() {
        this.modalOpen = false;
    },

    closeModal() {
        this.modalOpen = false;
    },

    copyPatientLink() {
        const url = '{{ $patientJoinUrl }}';

        // Try modern clipboard API first
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(url).then(() => {
                this.showCopyNotification('Enlace copiado al portapapeles');
            }).catch(() => {
                this.fallbackCopyToClipboard(url);
            });
        } else {
            this.fallbackCopyToClipboard(url);
        }
    },

    fallbackCopyToClipboard(text) {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.left = '-999999px';
        document.body.appendChild(textArea);
        textArea.select();

        try {
            document.execCommand('copy');
            this.showCopyNotification('Enlace copiado al portapapeles');
        } catch (err) {
            this.showCopyNotification('Error al copiar enlace', 'error');
        }

        document.body.removeChild(textArea);
    },

    showCopyNotification(message, type = 'success') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `copy-notification ${type === 'error' ? 'error' : 'success'}`;
        notification.textContent = message;
        document.body.appendChild(notification);

        // Show notification with animation
        setTimeout(() => notification.classList.add('show'), 10);

        // Remove after 3 seconds
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => document.body.removeChild(notification), 300);
        }, 3000);
    },

    saveModalState() {
        localStorage.setItem('jitsi_modal_state', JSON.stringify({
            width: this.modalWidth,
            height: this.modalHeight,
            top: this.modalTop,
            left: this.modalLeft,
        }));
    },

    startDrag(e) {
        if (e.target.closest('.modal-controls')) return;
        if (e.target.closest('.resize-handle')) return;

        this.isDragging = true;
        this.startX = e.clientX - this.modalLeft;
        this.startY = e.clientY - this.modalTop;

        document.addEventListener('mousemove', this.onDrag.bind(this));
        document.addEventListener('mouseup', this.stopDrag.bind(this));
        e.preventDefault();
    },

    onDrag(e) {
        if (!this.isDragging) return;

        let newLeft = e.clientX - this.startX;
        let newTop = e.clientY - this.startY;

        newLeft = Math.max(0, Math.min(newLeft, window.innerWidth - this.modalWidth));
        newTop = Math.max(0, Math.min(newTop, window.innerHeight - 100));

        this.modalLeft = newLeft;
        this.modalTop = newTop;
    },

    stopDrag() {
        this.isDragging = false;
        document.removeEventListener('mousemove', this.onDrag);
        document.removeEventListener('mouseup', this.stopDrag);
        this.saveModalState();
    },

    startResize(e, direction) {
        this.isResizing = true;
        this.resizeDirection = direction;
        this.startX = e.clientX;
        this.startY = e.clientY;
        this.startWidth = this.modalWidth;
        this.startHeight = this.modalHeight;
        this.startTop = this.modalTop;
        this.startLeft = this.modalLeft;

        document.addEventListener('mousemove', this.onResize.bind(this));
        document.addEventListener('mouseup', this.stopResize.bind(this));
        e.preventDefault();
        e.stopPropagation();
    },

    onResize(e) {
        if (!this.isResizing) return;

        const deltaX = e.clientX - this.startX;
        const deltaY = e.clientY - this.startY;

        switch (this.resizeDirection) {
            case 'se':
                this.modalWidth = Math.max(this.minWidth, this.startWidth + deltaX);
                this.modalHeight = Math.max(this.minHeight, this.startHeight + deltaY);
                break;
            case 'sw':
                this.modalWidth = Math.max(this.minWidth, this.startWidth - deltaX);
                this.modalHeight = Math.max(this.minHeight, this.startHeight + deltaY);
                this.modalLeft = this.startLeft + (this.startWidth - this.modalWidth);
                break;
            case 'ne':
                this.modalWidth = Math.max(this.minWidth, this.startWidth + deltaX);
                this.modalHeight = Math.max(this.minHeight, this.startHeight - deltaY);
                this.modalTop = this.startTop + (this.startHeight - this.modalHeight);
                break;
            case 'nw':
                this.modalWidth = Math.max(this.minWidth, this.startWidth - deltaX);
                this.modalHeight = Math.max(this.minHeight, this.startHeight - deltaY);
                this.modalLeft = this.startLeft + (this.startWidth - this.modalWidth);
                this.modalTop = this.startTop + (this.startHeight - this.modalHeight);
                break;
            case 'n':
                this.modalHeight = Math.max(this.minHeight, this.startHeight - deltaY);
                this.modalTop = this.startTop + (this.startHeight - this.modalHeight);
                break;
            case 's':
                this.modalHeight = Math.max(this.minHeight, this.startHeight + deltaY);
                break;
            case 'e':
                this.modalWidth = Math.max(this.minWidth, this.startWidth + deltaX);
                break;
            case 'w':
                this.modalWidth = Math.max(this.minWidth, this.startWidth - deltaX);
                this.modalLeft = this.startLeft + (this.startWidth - this.modalWidth);
                break;
        }
    },

    stopResize() {
        this.isResizing = false;
        this.resizeDirection = null;
        document.removeEventListener('mousemove', this.onResize);
        document.removeEventListener('mouseup', this.stopResize);
        this.saveModalState();
    },

    startJitsiMeet(config) {
        const domain = config.domain || 'meet.jit.si';

        // Preparar userInfo con el displayName
        const userInfo = config.userInfo || {};
        const displayName = userInfo.displayName || 'Usuario';

        const options = {
            roomName: config.roomName,
            width: '100%',
            height: '100%',
            parentNode: document.querySelector('#jitsi-meet-container'),
            configOverwrite: {
                ...config.configOverwrite,
                // Opciones adicionales para compatibilidad con Chrome
                disableRtx: false,
                enableLayerSuspension: true,
                testing: {
                    disableE2EE: false,
                },
            },
            interfaceConfigOverwrite: {
                ...config.interfaceConfigOverwrite,
                // Forzar compatibilidad y deshabilitar input de nombre
                ENFORCE_NOTIFICATION_AUTO_DISMISS_TIMEOUT: 15000,
                DISABLE_JOIN_LEAVE_NOTIFICATIONS: false,
                // Ocultar botones de autenticación para salas públicas
                TOOLBAR_BUTTONS: config.interfaceConfigOverwrite?.TOOLBAR_BUTTONS || [
                    'microphone', 'camera', 'closedcaptions', 'desktop',
                    'fullscreen', 'fodeviceselection', 'hangup', 'chat',
                    'settings', 'videoquality', 'filmstrip', 'stats', 'tileview'
                ],
            },
            userInfo: {
                displayName: displayName,
                email: userInfo.email || ''
            },
        };

        // Solo agregar JWT si está configurado (para servidores privados)
        // Para meet.jit.si público, NO usar JWT
        if (config.jwt) {
            options.jwt = config.jwt;
        }

        try {
            // Verificar que JitsiMeetExternalAPI esté disponible
            if (typeof JitsiMeetExternalAPI === 'undefined') {
                throw new Error('Jitsi Meet External API no está cargada. Por favor recarga la página.');
            }

            this.api = new JitsiMeetExternalAPI(domain, options);

            // Establecer el display name explícitamente INMEDIATAMENTE después de crear la API
            this.api.executeCommand('displayName', displayName);

            // Escuchar cuando se une al evento para volver a establecer el nombre
            this.api.addEventListener('videoConferenceJoined', () => {
                // Asegurar que el nombre se mantiene después de unirse
                setTimeout(() => {
                    this.api.executeCommand('displayName', displayName);
                }, 100);
            });

            this.setupEventListeners();
            this.connectionStatus = 'Conectando...';
            this.sessionActive = true;

            console.log('Jitsi Meet initialized successfully with user:', displayName);
        } catch (error) {
            console.error('Error starting Jitsi Meet:', error);
            this.connectionStatus = 'Error al conectar';
            alert('Error al iniciar la videollamada: ' + error.message + '\n\nAsegúrate de permitir el acceso a la cámara y micrófono.');
        }
    },

    setupEventListeners() {
        if (!this.api) return;

        this.api.addEventListener('videoConferenceJoined', (event) => {
            this.connectionStatus = 'Conectado';
            console.log('Joined conference:', event);
        });

        this.api.addEventListener('videoConferenceLeft', () => {
            this.connectionStatus = 'Desconectado';
            console.log('Left conference');
        });

        this.api.addEventListener('participantJoined', (participant) => {
            this.participantCount++;
            console.log('Participant joined:', participant);
        });

        this.api.addEventListener('participantLeft', (participant) => {
            this.participantCount--;
            console.log('Participant left:', participant);
        });

        this.api.addEventListener('readyToClose', () => {
            this.hangUp();
        });

        this.api.addEventListener('errorOccurred', (error) => {
            console.error('Jitsi error:', error);
            this.connectionStatus = 'Error: ' + error.error;
        });
    },

    hangUp() {
        if (this.api) {
            this.api.dispose();
            this.api = null;
        }
        this.sessionActive = false;
        this.connectionStatus = 'Desconectado';
        this.participantCount = 0;
        this.modalOpen = false;
    }
}));
});
</script>

<style>
        /* Alpine.js cloak - prevenir flash de contenido */
        [x-cloak] {
            display: none !important;
        }

        /* Botón flotante */
        .video-call-button-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 999;
        }

        /* Grupo de botones */
        .button-group {
            display: flex;
            gap: 10px;
            flex-direction: column;
        }

        .btn-start-video-call, .btn-copy-link {
            display: flex;
            align-items: center;
            gap: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 12px 24px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            transition: all 0.3s ease;
            position: relative;
        }

        .btn-start-video-call:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }

        .btn-start-video-call.btn-join {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }

        .btn-start-video-call.btn-active {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { box-shadow: 0 4px 15px rgba(245, 87, 108, 0.4); }
            50% { box-shadow: 0 4px 25px rgba(245, 87, 108, 0.8); }
        }

        .camera-icon {
            width: 24px;
            height: 24px;
        }

        .button-text {
            display: inline-block;
        }

        .active-indicator {
            width: 8px;
            height: 8px;
            background: #fff;
            border-radius: 50%;
            animation: blink 1.5s infinite;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }

        /* Copy Link Button */
        .btn-copy-link {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .btn-copy-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }

        .btn-copy-link.btn-active {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        /* Copy Notification */
        .copy-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #10b981;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 10000;
            opacity: 0;
            transform: translateY(-20px);
            transition: all 0.3s ease;
            font-size: 14px;
            font-weight: 500;
        }

        .copy-notification.show {
            opacity: 1;
            transform: translateY(0);
        }

        .copy-notification.error {
            background: #ef4444;
        }

        /* Modal redimensionable */
        .video-modal-container {
            position: fixed;
            background: #1f2937;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            z-index: 1000;
            resize: both;
        }

        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: move;
            user-select: none;
        }

        .modal-title {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
        }

        .title-icon {
            width: 20px;
            height: 20px;
        }

        .title-text {
            font-size: 14px;
            font-weight: 600;
        }

        .connection-status {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            margin-left: 12px;
            opacity: 0.9;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            background: #fbbf24;
            border-radius: 50%;
            animation: blink 1.5s infinite;
        }

        .status-dot.connected {
            background: #10b981;
            animation: none;
        }

        .modal-controls {
            display: flex;
            gap: 8px;
        }

        .control-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .control-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .control-btn-danger:hover {
            background: rgba(239, 68, 68, 0.8);
        }

        .control-btn-copy {
            background: rgba(79, 172, 254, 0.3);
        }

        .control-btn-copy:hover {
            background: rgba(79, 172, 254, 0.5);
            transform: scale(1.05);
        }

        .control-icon {
            width: 18px;
            height: 18px;
        }

        .jitsi-container {
            flex: 1;
            position: relative;
            background: #111827;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-footer {
            background: #111827;
            padding: 8px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .participants-info {
            font-size: 12px;
            color: #9ca3af;
        }

        /* Resize handles */
        .resize-handle {
            position: absolute;
            background: transparent;
            z-index: 10;
        }

        .resize-handle-n, .resize-handle-s {
            height: 8px;
            left: 0;
            right: 0;
            cursor: ns-resize;
        }

        .resize-handle-n { top: 0; }
        .resize-handle-s { bottom: 0; }

        .resize-handle-e, .resize-handle-w {
            width: 8px;
            top: 0;
            bottom: 0;
            cursor: ew-resize;
        }

        .resize-handle-e { right: 0; }
        .resize-handle-w { left: 0; }

        .resize-handle-ne, .resize-handle-nw,
        .resize-handle-se, .resize-handle-sw {
            width: 16px;
            height: 16px;
        }

        .resize-handle-ne {
            top: 0;
            right: 0;
            cursor: nesw-resize;
        }

        .resize-handle-nw {
            top: 0;
            left: 0;
            cursor: nwse-resize;
        }

        .resize-handle-se {
            bottom: 0;
            right: 0;
            cursor: nwse-resize;
        }

        .resize-handle-sw {
            bottom: 0;
            left: 0;
            cursor: nesw-resize;
        }

        /* Loading overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .loading-spinner {
            background: white;
            border-radius: 12px;
            padding: 20px;
        }

        .loading-spinner svg {
            width: 40px;
            height: 40px;
            color: #667eea;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .video-call-button-container {
                bottom: 10px;
                right: 10px;
            }

            .btn-start-video-call {
                padding: 10px 16px;
                font-size: 14px;
            }

            .button-text {
                display: none;
            }

            .btn-start-video-call {
                border-radius: 50%;
                width: 56px;
                height: 56px;
                padding: 0;
                justify-content: center;
            }
        }
    </style>
</div>  {{-- End of single root element for Livewire component --}}
