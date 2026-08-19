<div>
    <!-- Estilos para el diseño de grid -->
    @section('css')
        {{--}}
    <link rel="stylesheet" href="{{ asset('styles/consultation.css?time='.time()) }}">
    {{--}}
    <link rel="stylesheet" href="{{ asset('assets/css/consultation-grid.css?time='.time()) }}">
    <link rel="stylesheet" href="{{ asset('assets/css/consultation-header-new.css?time='.time()) }}">
    @stop
    <!-- Mensajes del sistema -->
    <div class="col-md-12 col-sm-12">
        @include('partials.message')
    </div>

    <!-- Header del paciente -->
    <div class="col-md-12 col-sm-12" id="paciente">
        @include('consultations.partials.head_new', array('patient' => $patient, 'appointment' => $appointment, 'encounter' => $encounter))
    </div>

    <!-- Contenedor principal con grid de tarjetas -->
    <div class="consultation-grid-container">
        <div class="consultation-section-title">
            <h2>{{ __('consultation.medical_consultation') }}</h2>
            <p>{{ __('consultation.select_section_to_record') }}</p>
        </div>

        <div class="grid-container" id="module-grid">
            @foreach($encounter_sections as $section)
            <div class="card {{ $section->obligatory ? 'card-required' : '' }}"  data-section-id="{{ $section->id }}"
                 data-required="{{ $section->obligatory ? 'true' : 'false' }}"
                 onclick="openSectionModal('{{ $section->id }}', '{{ app()->getLocale() === 'es' ? $section->name_esp : $section->name }}', '{{ $section->icon ?? '📄' }}')"
                 role="button"
                 tabindex="0"
                 aria-label="{{ app()->getLocale() === 'es' ? $section->name_esp : $section->name }}">
                <div class="card-icon">{{ $section->icon ?? '📄' }}</div>
                <div class="card-title">   {{ app()->getLocale() === 'es' ? $section->name_esp : $section->name }}</div>
                @if($section->obligatory)
                    <div class="card-required-badge">
                        <i class="fas fa-exclamation-circle"></i> Requerido
                    </div>
                @endif
            </div>
            @endforeach
       </div>
    </div>

    <!-- Espaciado para el footer fijo -->


    <!-- Modales con componentes Livewire (uno por sección) -->
    @foreach($encounter_sections as $section)
        <div class="consultation-modal-overlay" id="section-modal-{{ $section->id }}" data-section-id="{{ $section->id }}">
            <div class="consultation-modal-content">
                <div class="consultation-modal-header">
                    <div class="consultation-modal-title-wrapper">
                        <span class="consultation-modal-icon">{{ $section->icon ?? '📄' }}</span>
                        <h2 class="consultation-modal-title">{{ app()->getLocale() === 'es' ? $section->name_esp : $section->name }}</h2>
                    </div>
                    <button class="consultation-modal-close" onclick="closeSectionModal('{{ $section->id }}')" aria-label="Cerrar">
                        &times;
                    </button>
                </div>

                <div class="consultation-modal-body">
                    @livewire($section->livewire_component_name, [
                        'encounter_id' => $encounter->id,
                        'section_id' => $section->id,
                        'section_name' => app()->getLocale() === 'es' ? $section->name_esp : $section->name,
                        'medical_specialty_id' => $section->medical_speciality_id
                    ], key('section-' . $section->id))
                </div>
            </div>
        </div>
    @endforeach
    <p class="mb-5">&nbsp;</p>
    <p class="mb-5">&nbsp;</p>
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <!-- Footer fijo con estado y botones -->
            <footer class="consultation-footer">

                <div class="footer-left">
                    <button class="btn-consultation btn-consultation-outline"
                            type="button"
                            data-bs-toggle="offcanvas"
                            data-bs-target="#offcanvasRight{{ $patient->id }}"
                            aria-controls="offcanvasRight">
                        <i class="fas fa-user"></i>
                        {{ __('consultation.footer.view_patient_info') }}
                    </button>

                    {{--}}

                    <button class="btn-consultation btn-consultation-outline"
                            onclick="window.location.href='{{ route('appointments.index') }}'">
                        <i class="fas fa-calendar-alt"></i>
                        {{ __('consultation.footer.back_to_calendar') }}
                    </button>
                    {{--}}
                </div>

                <div class="footer-center" id="pending-status">
                    <i class="fas fa-spinner fa-spin"></i>
                    <strong>Validando formulario...</strong>
                </div>

                <div class="footer-right">
                    @livewire('consultation.finished-button-new', ['encounter_id' => $encounter_id])
                </div>

            </footer>
        </div>
    </div>

    <!-- Botón Flotante de Licencia Médica -->
    @livewire('consultation.medical-leave-button', ['encounter_id' => $encounter_id])

    <!-- Botón Flotante de Dictado por Voz -->
    @livewire('consultation.voice-dictation-button', ['encounter_id' => $encounter_id])

    <!-- Menú lateral de información del paciente -->
    @include('consultations.partials.patient_info', array('id' => $patient->id))

    <!-- Sala virtual si aplica -->
    @if($appointment->isVirtual())
        <div class="my-3"></div>
        @livewire('consultation.virtual-consultation-room', [
            'appointment' => $appointment,
            'displayMode' => 'sidebar'
        ])
    @endif

    <!-- Scripts -->
    <script>
        // Mapeo de componentes Livewire por sección
        const sectionComponents = {};

        @foreach($encounter_sections as $section)
            sectionComponents['{{ $section->id }}'] = {
                component: '{{ $section->livewire_component_name }}',
                icon: '{{ $section->icon ?? '📄' }}'
            };
        @endforeach

        // Estado de secciones completadas (inicializar con las ya completadas del backend)
        let completedSections = new Set(@json($completedSections));

        // Secciones obligatorias
        const requiredSections = @json($encounter_sections->where('obligatory', true)->pluck('id')->toArray());

        /**
         * Abre el modal de una sección
         */
        function openSectionModal(sectionId, sectionName, sectionIcon = '📄') {
            // Cerrar todos los modales primero
            closeAllModals();

            // Abrir el modal de la sección específica
            const modal = document.getElementById('section-modal-' + sectionId);

            if (modal) {
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';

                // Disparar evento de Livewire para que los componentes se refresquen
                setTimeout(() => {
                    if (typeof Livewire !== 'undefined') {
                        Livewire.dispatch('sectionOpened', { sectionId: sectionId });
                    }
                }, 100);
            } else {
                console.error('Modal no encontrado para la sección:', sectionId);
            }
        }

        /**
         * Cierra el modal de sección específica
         */
        function closeSectionModal(sectionId) {
            const modal = document.getElementById('section-modal-' + sectionId);

            if (modal) {
                modal.classList.remove('active');
            }

            document.body.style.overflow = '';

            // Actualizar el estado de completitud desde el backend
            if (typeof Livewire !== 'undefined') {
                @this.call('getCompletedSectionsStatus');
                Livewire.dispatch('sectionClosed');

                // IMPORTANTE: Disparar validación del botón de finalizar para actualizar el footer
                Livewire.dispatch('findFinishedButtonStatus');
            }
        }

        /**
         * Cierra todos los modales
         */
        function closeAllModals() {
            const modals = document.querySelectorAll('.consultation-modal-overlay');
            modals.forEach(modal => {
                modal.classList.remove('active');
            });
        }

        /**
         * Marca una sección como completada
         */
        function markSectionComplete(sectionId) {
            completedSections.add(sectionId);

            // Actualizar tarjeta visual
            const card = document.querySelector(`[data-section-id="${sectionId}"]`);
            if (card) {
                card.classList.add('completed');
                // Remover la clase de requerido cuando se completa
                card.classList.remove('card-required');
            }

            // No actualizar el footer aquí - el FinishedButton lo hará con mensajes detallados
        }

        /**
         * Actualiza el estado de secciones pendientes en el footer
         * NOTA: Esta función ya no se usa - el FinishedButtonNew maneja el footer con mensajes detallados
         */
        function updatePendingStatus() {
            // No hacer nada - dejar que FinishedButtonNew maneje el footer
            // El evento 'updateFooterMessages' tiene los mensajes detallados de campos específicos
        }

        /**
         * Cerrar modal al hacer clic fuera
         */
        window.onclick = function(event) {
            if (event.target.classList.contains('consultation-modal-overlay')) {
                const sectionId = event.target.getAttribute('data-section-id');
                if (sectionId) {
                    closeSectionModal(sectionId);
                }
            }
        }

        /**
         * Cerrar modal con tecla ESC
         */
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                // Cerrar el modal activo
                const activeModal = document.querySelector('.consultation-modal-overlay.active');
                if (activeModal) {
                    const sectionId = activeModal.getAttribute('data-section-id');
                    if (sectionId) {
                        closeSectionModal(sectionId);
                    }
                }
            }
        });

        /**
         * Eventos de Livewire
         */
        document.addEventListener('livewire:initialized', () => {
            // Mostrar notificaciones
            Livewire.on('showToastrConsultation', (event) => {
                const data = Array.isArray(event) ? event[0] : event;
                const type = data.type || 'info';
                const message = data.message || '{{ __('consultation.operation_completed') }}';

                if (typeof toastr !== 'undefined' && typeof toastr[type] === 'function') {
                    toastr[type](message, '', {
                        closeButton: true,
                        progressBar: true,
                        positionClass: 'toast-top-right',
                        timeOut: 5000,
                    });
                }
            });

            // Evento cuando se guarda una sección
            Livewire.on('sectionSaved', (event) => {
                const data = Array.isArray(event) ? event[0] : event;
                // No marcar automáticamente como completado
                // La validación se hará al cerrar el modal
                if (data.sectionId) {
                    // closeSectionModal(data.sectionId);
                }
            });

            // Evento para actualizar mensajes del footer
            Livewire.on('updateFooterMessages', (event) => {
                console.log('updateFooterMessages event received:', event);
                const data = Array.isArray(event) ? event[0] : event;
                console.log('Extracted data:', data);
                updateFooterWithMessages(data.messages, data.enabled);
            });

            // Escuchar actualizaciones del estado de secciones desde el backend
            Livewire.on('sectionsStatusUpdated', (event) => {
                const data = Array.isArray(event) ? event[0] : event;
                const updatedCompletedSections = data.completedSections || [];

                // Actualizar el Set de completedSections
                completedSections = new Set(updatedCompletedSections);

                // Actualizar visualmente todas las tarjetas
                document.querySelectorAll('.card[data-section-id]').forEach(card => {
                    const sectionId = parseInt(card.getAttribute('data-section-id'));
                    if (completedSections.has(sectionId)) {
                        card.classList.add('completed');
                        card.classList.remove('card-required');
                    } else if (card.getAttribute('data-required') === 'true') {
                        card.classList.remove('completed');
                        card.classList.add('card-required');
                    }
                });

                // No actualizar el footer aquí - dejar que el FinishedButton lo maneje con mensajes detallados
            });
        });

        /**
         * Actualiza el footer con los mensajes de validación
         */
        function updateFooterWithMessages(messages, enabled) {
            const pendingStatus = document.getElementById('pending-status');

            if (!pendingStatus) {
                console.error('Footer element #pending-status not found');
                return;
            }

            // Debug: mostrar los mensajes recibidos
            console.log('updateFooterWithMessages called with:', {messages, enabled, messageCount: messages ? Object.keys(messages).length : 0});

            if (!messages || Object.keys(messages).length === 0) {
                // No hay mensajes de error - todo está completo
                pendingStatus.innerHTML = '<i class="fas fa-check-circle"></i> <strong>{{ __("consultation.footer.ready_to_finish") }}</strong>';
                pendingStatus.classList.add('ready');
            } else {
                // Hay mensajes de error - mostrarlos
                pendingStatus.classList.remove('ready');

                // Convertir objeto de mensajes a array
                const messageArray = Object.values(messages);

                pendingStatus.innerHTML = `
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>{{ __('consultation.finished_button.add_required_info') }}</strong>
                    <div style="margin-top: 8px; font-size: 0.85rem; line-height: 1.6;">
                        ${messageArray.join('<br>')}
                    </div>
                `;
            }
        }

        /**
         * Inicializar el estado visual de las tarjetas completadas
         */
        function initializeCompletedCards() {
            completedSections.forEach(sectionId => {
                const card = document.querySelector(`[data-section-id="${sectionId}"]`);
                if (card) {
                    card.classList.add('completed');
                    card.classList.remove('card-required');
                }
            });
        }

        // Inicializar estado
        initializeCompletedCards();
        // No llamar updatePendingStatus() aquí - el FinishedButtonNew lo manejará con mensajes detallados

        // Disparar evento para obtener el estado inicial del botón de finalizar
        document.addEventListener('livewire:initialized', () => {
            setTimeout(() => {
                Livewire.dispatch('findFinishedButtonStatus');
            }, 500);
        });
    </script>
</div>
