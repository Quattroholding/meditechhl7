<div class="virtual-zoom-consultation-wrapper" x-data="zoomConsultation()" wire:ignore.self>
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
                <span class="button-text">Iniciar Video Consulta (Zoom)</span>
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

    {{-- Modal para Zoom --}}
    <div x-show="modalOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:leave="transition ease-in duration-200"
         class="zoom-modal-overlay"
         @click.self="closeModal()"
         style="display: none;">

        <div class="zoom-modal"
             :style="{ width: modalWidth + 'px', height: modalHeight + 'px', top: modalTop + 'px', left: modalLeft + 'px' }">

            {{-- Header del Modal --}}
            <div class="zoom-modal-header" @mousedown="startDrag">
                <div class="zoom-modal-title">
                    Video Consulta - Zoom
                </div>
                <div class="zoom-modal-actions">
                    <button @click="toggleDisplayMode()" class="zoom-modal-btn" title="Cambiar modo">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                        </svg>
                    </button>
                    <button @click="closeModal()" class="zoom-modal-btn" title="Cerrar">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Contenedor de Zoom Meeting --}}
            <div class="zoom-container" id="zoom-meeting-container">
                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; gap: 20px; padding: 20px;">
                    <div style="text-align: center; color: white;">
                        <h3>Sala de Zoom</h3>
                        <p>Meeting ID: {{ $appointment->virtual_room_id }}</p>
                        @if($isDoctor)
                            <p style="font-size: 12px; color: #ccc;">Iniciando...</p>
                        @else
                            <p style="font-size: 12px; color: #ccc;">Esperando que el doctor inicie...</p>
                        @endif
                    </div>
                    @php
                        $password = $appointment->virtual_session_metadata['meeting_password'] ?? null;
                        $zoomUrl = 'https://zoom.us/j/' . $appointment->virtual_room_id;
                        if ($password) {
                            $encodedPassword = base64_encode($password);
                            $zoomUrl .= '?pwd=' . $encodedPassword;
                        }
                    @endphp
                    <a href="{{ $zoomUrl }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       style="padding: 12px 24px; background: #0e5aa8; color: white; border-radius: 4px; text-decoration: none; font-weight: 600;">
                        Abrir Zoom en nueva ventana
                    </a>
                </div>
            </div>

            {{-- Resize Handles --}}
            <div class="resize-handle resize-handle-se" @mousedown="startResize('se')"></div>
            <div class="resize-handle resize-handle-sw" @mousedown="startResize('sw')"></div>
            <div class="resize-handle resize-handle-ne" @mousedown="startResize('ne')"></div>
            <div class="resize-handle resize-handle-nw" @mousedown="startResize('nw')"></div>
        </div>
    </div>

    {{-- Styles --}}
    <style>
        .zoom-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9998;
        }

        .zoom-modal {
            position: fixed;
            background: white;
            border-radius: 8px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            z-index: 9999;
            overflow: hidden;
        }

        .zoom-modal-header {
            background: linear-gradient(135deg, #0e5aa8 0%, #1a6db5 100%);
            color: white;
            padding: 12px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: move;
            user-select: none;
        }

        .zoom-modal-title {
            font-weight: 600;
            font-size: 14px;
        }

        .zoom-modal-actions {
            display: flex;
            gap: 8px;
        }

        .zoom-modal-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 28px;
            height: 28px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.2s;
        }

        .zoom-modal-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .zoom-modal-btn svg {
            width: 16px;
            height: 16px;
        }

        .zoom-container {
            flex: 1;
            background: #000;
            overflow: hidden;
        }

        .resize-handle {
            position: absolute;
            width: 10px;
            height: 10px;
            background: #0e5aa8;
            opacity: 0;
            transition: opacity 0.2s;
        }

        .zoom-modal:hover .resize-handle {
            opacity: 0.5;
        }

        .resize-handle-se {
            bottom: 0;
            right: 0;
            cursor: se-resize;
            border-radius: 0 0 8px 0;
        }

        .resize-handle-sw {
            bottom: 0;
            left: 0;
            cursor: sw-resize;
            border-radius: 0 0 0 8px;
        }

        .resize-handle-ne {
            top: 0;
            right: 0;
            cursor: ne-resize;
            border-radius: 8px 0 0 0;
        }

        .resize-handle-nw {
            top: 0;
            left: 0;
            cursor: nw-resize;
            border-radius: 0 0 0 8px;
        }

        .video-call-button-container {
            position: fixed;
            bottom: 180px;
            right: 20px;
            z-index: 9997;
        }

        .btn-start-video-call {
            background: linear-gradient(135deg, #0e5aa8 0%, #1a6db5 100%);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(14, 90, 168, 0.3);
            transition: all 0.3s;
        }

        .btn-start-video-call:hover {
            box-shadow: 0 6px 20px rgba(14, 90, 168, 0.4);
            transform: translateY(-2px);
        }

        .camera-icon {
            width: 18px;
            height: 18px;
        }

        .btn-copy-link {
            background: #10b981;
        }

        .btn-copy-link:hover {
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
        }

        .button-group {
            display: flex;
            gap: 10px;
        }

        .btn-active {
            box-shadow: 0 6px 20px rgba(14, 90, 168, 0.4);
        }

        .active-indicator {
            display: inline-block;
            width: 8px;
            height: 8px;
            background: #10b981;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    </style>
</div>


<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('zoomConsultation', () => ({
        modalOpen: false,
        sessionActive: false,
        initialized: false,

        // Modal position and size
        modalWidth: 640,
        modalHeight: 480,
        modalTop: 0,
        modalLeft: 0,
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
            console.log('Zoom consultation component initialized');
            this.modalOpen = false;
            this.sessionActive = this.$wire.sessionActive;

            // Center modal
            this.centerModal();

            // Load saved position and size
            const saved = localStorage.getItem('zoom_modal_state');
            if (saved) {
                try {
                    const state = JSON.parse(saved);
                    this.modalWidth = state.width || 640;
                    this.modalHeight = state.height || 480;
                    this.modalTop = state.top || this.getCenteredTop();
                    this.modalLeft = state.left || this.getCenteredLeft();
                } catch (e) {
                    console.error('Error loading modal state:', e);
                }
            }

            // Watch for session active changes
            this.$watch('$wire.sessionActive', (value) => {
                this.sessionActive = value;
                if (value && this.modalOpen) {
                    setTimeout(async () => {
                        await this.startZoomMeeting();
                    }, 100);
                }
            });

            // Auto-open if session is already active
            if (this.sessionActive) {
                this.modalOpen = true;
                setTimeout(async () => {
                    await this.startZoomMeeting();
                }, 100);
            }

            this.initialized = true;
        },

        getCenteredLeft() {
            return Math.max(0, (window.innerWidth - this.modalWidth) / 2);
        },

        getCenteredTop() {
            return Math.max(0, (window.innerHeight - this.modalHeight) / 2 - 60);
        },

        centerModal() {
            this.modalLeft = this.getCenteredLeft();
            this.modalTop = this.getCenteredTop();
        },

        openModal() {
            this.modalOpen = true;
        },

        closeModal() {
            this.modalOpen = false;
            this.saveModalState();
        },

        saveModalState() {
            localStorage.setItem('zoom_modal_state', JSON.stringify({
                width: this.modalWidth,
                height: this.modalHeight,
                top: this.modalTop,
                left: this.modalLeft,
            }));
        },

        async startZoomMeeting() {
            console.log('Zoom meeting ready. Users can click the link to open the meeting.');
        },

        toggleDisplayMode() {
            this.$wire.toggleDisplayMode();
        },

        copyPatientLink() {
            const link = this.$wire.patientJoinUrl;
            navigator.clipboard.writeText(link).then(() => {
                alert('Enlace copiado al portapapeles');
            });
        },

        // Drag functionality
        startDrag(e) {
            if (e.target.closest('.zoom-modal-btn')) return;
            this.isDragging = true;
            this.startX = e.clientX;
            this.startY = e.clientY;
            this.startTop = this.modalTop;
            this.startLeft = this.modalLeft;

            document.addEventListener('mousemove', this.doDrag.bind(this));
            document.addEventListener('mouseup', this.stopDrag.bind(this));
        },

        doDrag(e) {
            if (!this.isDragging) return;
            const dx = e.clientX - this.startX;
            const dy = e.clientY - this.startY;
            this.modalLeft = Math.max(0, this.startLeft + dx);
            this.modalTop = Math.max(0, this.startTop + dy);
        },

        stopDrag() {
            this.isDragging = false;
            this.saveModalState();
            document.removeEventListener('mousemove', this.doDrag);
            document.removeEventListener('mouseup', this.stopDrag);
        },

        // Resize functionality
        startResize(direction, e) {
            if (e && e.type === 'mousedown') {
                e.preventDefault();
            }
            this.isResizing = true;
            this.resizeDirection = direction;
            this.startX = e.clientX;
            this.startY = e.clientY;
            this.startWidth = this.modalWidth;
            this.startHeight = this.modalHeight;
            this.startTop = this.modalTop;
            this.startLeft = this.modalLeft;

            document.addEventListener('mousemove', this.doResize.bind(this));
            document.addEventListener('mouseup', this.stopResize.bind(this));
        },

        doResize(e) {
            if (!this.isResizing) return;
            const dx = e.clientX - this.startX;
            const dy = e.clientY - this.startY;

            switch (this.resizeDirection) {
                case 'se':
                    this.modalWidth = Math.max(this.minWidth, this.startWidth + dx);
                    this.modalHeight = Math.max(this.minHeight, this.startHeight + dy);
                    break;
                case 'sw':
                    this.modalWidth = Math.max(this.minWidth, this.startWidth - dx);
                    this.modalHeight = Math.max(this.minHeight, this.startHeight + dy);
                    this.modalLeft = this.startLeft + dx;
                    break;
                case 'ne':
                    this.modalWidth = Math.max(this.minWidth, this.startWidth + dx);
                    this.modalHeight = Math.max(this.minHeight, this.startHeight - dy);
                    this.modalTop = this.startTop + dy;
                    break;
                case 'nw':
                    this.modalWidth = Math.max(this.minWidth, this.startWidth - dx);
                    this.modalHeight = Math.max(this.minHeight, this.startHeight - dy);
                    this.modalTop = this.startTop + dy;
                    this.modalLeft = this.startLeft + dx;
                    break;
            }
        },

        stopResize() {
            this.isResizing = false;
            this.saveModalState();
            document.removeEventListener('mousemove', this.doResize);
            document.removeEventListener('mouseup', this.stopResize);
        },
    }));
});
</script>
