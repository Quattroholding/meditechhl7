<div @if($type === 'laboratory' && $this->hasPendingLabResults()) wire:poll.10s="checkForNewResults" @endif>
    {{-- HemoScreen Reminder - Solo para laboratorio --}}
    @if($type === 'laboratory' && $encounter && $encounter->practitioner && $encounter->practitioner->hasActiveHemoScreen())
        <x-hemoscreen-reminder :practitioner="$encounter->practitioner" class="mb-3" />
    @endif

    @if(count($selectedLists)>0)
        <div class="mb-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="mb-0" style="color: var(--primary-color); font-weight: 600;">
                    <i class="fas fa-clipboard-list me-2"></i>
                    {{ __('consultation.service_request_section.selected_services') }}
                </h5>
                <span class="badge" style="background: var(--sami-green); font-size: 0.9rem; padding: 0.4rem 0.8rem;">
                    {{ count($selectedLists) }} {{ __('consultation.service_request_section.items') }}
                </span>
            </div>

            <div class="service-cards-container">
                @foreach($selectedLists as $s)
                    <div class="service-card" x-data="{ confirmDelete: false }">
                        <!-- Header de la tarjeta -->
                        <div class="service-card-header">
                            <div class="service-card-title">
                                <i class="fas fa-flask me-2" style="color: var(--sami-green);"></i>
                                {{$s->cpt->full_name}}
                            </div>
                            <button
                                type="button"
                                class="btn-delete-service"
                                @click="confirmDelete = !confirmDelete"
                                title="{{ __('consultation.service_request_section.delete') }}"
                            >
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>

                        <!-- Confirmación de borrado -->
                        <div x-show="confirmDelete"
                             x-transition
                             class="delete-confirmation"
                             style="display: none;">
                            <div class="d-flex align-items-center justify-content-between">
                                <span style="color: #721c24; font-weight: 500;">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    {{ __('consultation.service_request_section.confirm_delete') }}
                                </span>
                                <div class="d-flex gap-2">
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-danger"
                                        @click.prevent="$wire.delete({{$s->id}}); confirmDelete = false;"
                                    >
                                        <i class="fas fa-check me-1"></i>
                                        {{ __('generic.yes') }}
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-secondary"
                                        @click.prevent="confirmDelete = false"
                                    >
                                        <i class="fas fa-times me-1"></i>
                                        {{ __('generic.no') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Contenido de la tarjeta -->
                        <div class="service-card-body">
                            @if(in_array($s->code, ['85025', '85027']) && $encounter && $encounter->practitioner && $encounter->practitioner->hasActiveHemoScreen())
                                <div class="hemoscreen-badge">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="white" class="bi bi-fingerprint me-2" viewBox="0 0 16 16">
                                        <path d="M8.06 6.5a.5.5 0 0 1 .5.5v.776a11.5 11.5 0 0 1-.552 3.519l-1.331 4.14a.5.5 0 0 1-.952-.305l1.33-4.141a10.5 10.5 0 0 0 .504-3.213V7a.5.5 0 0 1 .5-.5Z"/>
                                        <path d="M6.06 7a2 2 0 1 1 4 0 .5.5 0 1 1-1 0 1 1 0 1 0-2 0v.332q0 .613-.066 1.221A.5.5 0 0 1 6 8.447q.06-.555.06-1.115zm3.509 1a.5.5 0 0 1 .5.5v.67q0 .613-.066 1.221a.5.5 0 1 1-.994-.112q.06-.555.06-1.109V8.5a.5.5 0 0 1 .5-.5"/>
                                        <path d="M7.507 1.17a.5.5 0 0 1 .986 0v.726a11.5 11.5 0 0 1-.552 3.519l-1.331 4.14a.5.5 0 1 1-.952-.305l1.33-4.141a10.5 10.5 0 0 0 .504-3.213V1.17ZM6 4.5a.5.5 0 0 1 .5.5v.776a11.5 11.5 0 0 1-.552 3.519l-1.331 4.14a.5.5 0 1 1-.952-.305l1.33-4.141a10.5 10.5 0 0 0 .504-3.213V5a.5.5 0 0 1 .5-.5"/>
                                    </svg>
                                    <div>
                                        <strong class="text-white">{{ __('consultation.service_request_section.hemoscreen_code') }}:</strong>
                                        <span class="text-white fs-5 fw-bold ms-2 font-monospace" style="letter-spacing: 3px;">{{ $s->hemo_identification }}</span>
                                    </div>
                                </div>
                            @endif

                            {{-- Resultados de Laboratorio --}}
                            @if($s->observations()->count() > 0)
                                <div class="lab-results-container">
                                    <div class="lab-results-header">
                                        <div class="lab-results-title">
                                            🧪 {{ __('consultation.service_request_section.lab_results') }}
                                        </div>
                                        <span class="lab-results-badge">
                                            {{ $s->observations()->count() }} {{ __('consultation.service_request_section.results') }}
                                        </span>
                                    </div>

                                    <div class="lab-results-grid">
                                        @foreach($s->observations as $observation)
                                            <div class="lab-result-item">
                                                <div class="lab-result-label">
                                                    {{ \App\Enums\LoincCode::getShortLabel($observation->code) }}
                                                </div>
                                                <div class="lab-result-value">
                                                    {{ $observation->value }}
                                                    <span class="lab-result-unit">{{ $observation->unit }}</span>
                                                </div>
                                                @if($observation->status === 'final')
                                                    <div class="lab-result-status">✓ {{ __('consultation.service_request_section.final') }}</div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>

                                    @if($s->observations->first() && $s->observations->first()->issued_date)
                                        <div class="lab-results-footer">
                                            <strong>{{ __('consultation.service_request_section.results_issued') }}:</strong>
                                            {{ \Carbon\Carbon::parse($s->observations->first()->issued_date)->format('d/m/Y H:i') }}
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <!-- Campo de instrucciones -->
                            <div class="service-instruction">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-clipboard-list me-2"></i>
                                    {{ __('consultation.service_request_section.instruction') }}
                                </label>
                                <x-autosave-input
                                    type="text"
                                    :value="$notes[$s->id]"
                                    wire:model.live.debounce.500ms="notes.{{$s->id}}"
                                    save-method="updateNote"
                                    save-key="{{ $s->id }}"
                                    class="form-control"
                                    placeholder="{{ __('consultation.service_request_section.instruction_placeholder') }}"
                                />
                            </div>

                            @if($type === 'procedure')
                                <div class="procedure-performed-section">
                                    <div class="form-check form-switch">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            role="switch"
                                            id="performed-{{ $s->id }}"
                                            @if($performedInConsultation[$s->id] ?? false) checked @endif
                                            wire:click.prevent="togglePerformedInConsultation({{ $s->id }})"
                                        >
                                        <label class="form-check-label fw-bold" for="performed-{{ $s->id }}">
                                            <i class="fas fa-check-circle me-2"></i>
                                            {{ __('consultation.service_request_section.procedure_performed') }}
                                        </label>
                                    </div>

                                    @if($performedInConsultation[$s->id] ?? false)
                                        <div class="procedure-notes-field" x-transition>
                                            <label class="form-label fw-semibold">
                                                <i class="fas fa-notes-medical me-2"></i>
                                                {{ __('consultation.service_request_section.procedure_notes') }}
                                            </label>
                                            <x-autosave-input
                                                type="textarea"
                                                :value="$procedureNotes[$s->id] ?? ''"
                                                wire:model.live.debounce.500ms="procedureNotes.{{ $s->id }}"
                                                save-method="updateProcedureNotes"
                                                save-key="procedure-notes-{{ $s->id }}"
                                                class="form-control"
                                                rows="4"
                                                placeholder="{{ __('consultation.service_request_section.procedure_notes_placeholder') }}"
                                            />
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
        <div class="selector-field selector-field-on">
        <x-autosave-action save-key="service-{{$type}}-search" />

        <!-- Área de búsqueda mejorada -->
        <div class="search-area">
            <div class="search-container">
                <button type="button" class="search-icon-btn">
                    <i class="fas fa-search"></i>
                </button>
                <input
                    type="text"
                    wire:model.live="query"
                    class="search-input"
                    placeholder="{{ __('consultation.service_request_section.search_placeholder') }}"
                >
            </div>

            <button
                type="button"
                class="btn-rapid-access"
                data-offcanvas-target="offcanvasRight-{{$encounter_id}}-{{$section_id}}"
                onclick="openRapidAccessOffcanvas(this.getAttribute('data-offcanvas-target'))"
                title="{{ __('consultation.service_request_section.rapid_access_list') }}"
            >
                <i class="fas fa-star me-2"></i>
                <span class="btn-text-desktop">{{ __('consultation.service_request_section.rapid_access_list') }}</span>
                <span class="btn-text-mobile">
                    <i class="fas fa-star"></i>
                </span>
            </button>
        </div>
        {{-- Componente independiente de accesos rápidos --}}
        @livewire('consultation.rapid-access-offcanvas', [
            'sectionId' => $section_id,
            'offcanvasId' => 'offcanvasRight-'.$encounter_id.'-'.$section_id,
            'encounterId' => $encounter_id
        ], key('rapid-access-'.$encounter_id.'-'.$section_id))

        {{-- RESULTADOS DE BÚSQUEDA --}}
        @if(!empty($results))
            <div style="position: absolute; z-index: 1000; width: 100%; background: white; border: 1px solid #ddd; border-radius: 4px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">

                {{-- Header FIJO - NO forma parte del scroll --}}
                <div style="background: #ffffff; padding: 8px 12px; border-bottom: 2px solid #0d6efd;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div style="font-size: 0.9rem;">
                            <i class="fas fa-search text-primary"></i>
                            <strong>
                                @if($isCodeSearch)
                                    Búsqueda por código
                                @else
                                    Búsqueda por descripción
                                @endif
                            </strong>
                        </div>
                        <div class="text-muted" style="font-size: 0.85rem;">
                            <i class="fas fa-list"></i>
                            {{ count($results) }} / {{ $totalResults }}
                            @if($hasMoreResults)
                                <span class="badge bg-primary ms-1">+{{ $totalResults - count($results) }}</span>
                            @endif
                        </div>
                    </div>


                </div>

                {{-- Contenedor con scroll - SOLO los resultados - MÁS ALTO para ver varios --}}
                <div style="max-height: 320px; min-height: 300px; overflow-y: auto;">
                    {{-- Resultados SIN agrupación - MÁS COMPACTOS --}}
                    @foreach($results as $result)
                        <div
                            class="sel-list-item"
                            wire:click.debounce.300ms="selectOption({{ json_encode($result) }})"
                            x-on:click="window.dispatchEvent(new CustomEvent('autosave-start', { detail: 'service-{{$type}}-search' }))"
                            style="padding: 6px 10px; cursor: pointer; border-bottom: 1px solid #e9ecef; transition: background 0.15s; display: flex; align-items: center; gap: 8px;"
                            onmouseover="this.style.background='#f0f7ff'"
                            onmouseout="this.style.background='white'"
                        >
                            <span class="badge bg-primary" style="font-size: 0.75rem; padding: 2px 6px; min-width: 60px; text-align: center;">{{ $result['code'] }}</span>
                            <span style="font-size: 0.85rem; color: #212529; flex: 1;">{{ app()->getLocale() === 'es' ? $result['description_es'] : $result['description']  }}</span>
                            <button
                                type="button"
                                class="btn btn-sm btn-outline-primary"
                                wire:click.stop="addToRapidAccess({{ $result['id'] }})"
                                title="Agregar a accesos rápidos"
                                style="padding: 2px 8px; font-size: 0.75rem;"
                            >
                                <i class="fas fa-star"></i>
                            </button>
                        </div>
                    @endforeach

                    {{-- Botón "Ver más" - MUY VISIBLE --}}
                    @if($hasMoreResults)
                        <div
                            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);width: 99%; padding: 10px; text-align: center; cursor: pointer; border: none; color: white; font-weight: bold; box-shadow: 0 4px 6px rgba(0,0,0,0.2);"
                            wire:click.prevent="loadMore"
                            onmouseover="this.style.transform='scale(1.02)'; this.style.boxShadow='0 6px 12px rgba(0,0,0,0.3)'"
                            onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 4px 6px rgba(0,0,0,0.2)'"
                        >
                            <div wire:loading.remove wire:target="loadMore">
                                <i class="fas fa-chevron-down" style="font-size: 1.2rem;"></i>
                                <div style="font-size: 1rem; margin-top: 4px;">
                                    <strong>CARGAR MÁS RESULTADOS</strong>
                                </div>
                                <div style="font-size: 0.9rem; margin-top: 4px; opacity: 0.9;">
                                    Hay {{ $totalResults - count($results) }} resultados más disponibles
                                </div>
                            </div>
                            <div wire:loading wire:target="loadMore">
                                <div class="spinner-border text-white" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                                <div style="margin-top: 8px;">Cargando resultados...</div>
                            </div>
                        </div>
                    @endif

                    {{-- Mensaje final --}}
                    @if(!$hasMoreResults && count($results) > 0)
                        <div style="padding: 10px 12px; text-align: center; color: #6c757d; font-size: 0.85rem; background: #f8f9fa;">
                            <i class="fas fa-check-circle text-success"></i>
                            Todos los resultados mostrados
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <div style="height:200px;">&nbsp;</div>

    <style>
        /* Search Area */
        .search-area {
            display: flex;
            gap: 1rem;
            align-items: center;
            padding: 1.25rem;
            background: white;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .search-container {
            flex: 1;
            display: flex;
            align-items: stretch;
            height: 48px;
        }

        .search-icon-btn {
            background: linear-gradient(135deg, var(--primary-color) 0%, #003366 100%);
            color: white;
            border: 2px solid var(--primary-color);
            border-right: none;
            border-radius: 8px 0 0 8px;
            padding: 0 1.25rem;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 56px;
            flex-shrink: 0;
        }

        .search-icon-btn:hover {
            background: linear-gradient(135deg, #003366 0%, #002244 100%);
            border-color: #003366;
        }

        .search-input {
            flex: 1;
            border: 2px solid #dee2e6;
            border-left: none;
            border-radius: 0 8px 8px 0;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            outline: none;
        }

        .search-input:focus {
            border-color: var(--sami-green);
            box-shadow: 0 0 0 3px rgba(122, 193, 66, 0.1);
        }

        .search-container:focus-within .search-icon-btn {
            background: linear-gradient(135deg, var(--sami-green) 0%, #63a733 100%);
            border-color: var(--sami-green);
        }

        .btn-rapid-access {
            background: linear-gradient(135deg, var(--sami-green) 0%, #63a733 100%);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            white-space: nowrap;
            box-shadow: 0 2px 4px rgba(122, 193, 66, 0.3);
        }

        .btn-rapid-access:hover {
            background: linear-gradient(135deg, #63a733 0%, #4d8a26 100%);
            box-shadow: 0 4px 8px rgba(122, 193, 66, 0.4);
            transform: translateY(-2px);
        }

        .btn-rapid-access:active {
            transform: translateY(0);
        }

        .btn-text-mobile {
            display: none;
        }

        /* Service Cards Container */
        .service-cards-container {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        /* Service Card */
        .service-card {
            background: white;
            border-radius: 12px;
            border: 2px solid #e9ecef;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .service-card:hover {
            border-color: var(--sami-green);
            box-shadow: 0 4px 12px rgba(122, 193, 66, 0.15);
            transform: translateY(-2px);
        }

        /* Service Card Header */
        .service-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1.25rem;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-bottom: 2px solid #dee2e6;
        }

        .service-card-title {
            font-weight: 600;
            color: var(--primary-color);
            font-size: 1.05rem;
            flex: 1;
        }

        .btn-delete-service {
            background: white;
            border: 2px solid #dc3545;
            color: #dc3545;
            padding: 0.4rem 0.8rem;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 0.9rem;
        }

        .btn-delete-service:hover {
            background: #dc3545;
            color: white;
            transform: scale(1.05);
        }

        /* Delete Confirmation */
        .delete-confirmation {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 0.75rem 1.25rem;
            margin: 0;
        }

        /* Service Card Body */
        .service-card-body {
            padding: 1.25rem;
        }

        /* Hemoscreen Badge */
        .hemoscreen-badge {
            display: inline-flex;
            align-items: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 0.75rem 1rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.2);
            margin-bottom: 1rem;
        }

        /* Lab Results Container */
        .lab-results-container {
            background: white;
            padding: 1rem;
            border-radius: 10px;
            border: 2px solid #059669;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
        }

        .lab-results-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .lab-results-title {
            font-size: 14px;
            font-weight: 700;
            color: #065f46;
        }

        .lab-results-badge {
            background: #d1fae5;
            color: #065f46;
            font-size: 11px;
            padding: 4px 12px;
            border-radius: 4px;
            font-weight: 600;
        }

        .lab-results-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 10px;
        }

        .lab-result-item {
            background: #f0fdf4;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #bbf7d0;
        }

        .lab-result-label {
            font-size: 10px;
            color: #059669;
            font-weight: 600;
            margin-bottom: 4px;
            text-transform: uppercase;
        }

        .lab-result-value {
            font-size: 22px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 2px;
        }

        .lab-result-unit {
            font-size: 11px;
            color: #64748b;
            font-weight: 500;
        }

        .lab-result-status {
            font-size: 9px;
            color: #059669;
            margin-top: 4px;
        }

        .lab-results-footer {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #bbf7d0;
            font-size: 10px;
            color: #64748b;
        }

        /* Service Instruction */
        .service-instruction {
            margin-bottom: 1rem;
        }

        .service-instruction .form-label {
            color: var(--text-main);
            margin-bottom: 0.5rem;
            display: block;
        }

        /* Procedure Performed Section */
        .procedure-performed-section {
            background-color: #f8f9fa;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1rem;
        }

        .procedure-performed-section .form-check {
            margin-bottom: 0;
        }

        .procedure-performed-section .form-check-input {
            cursor: pointer;
            width: 3rem;
            height: 1.5rem;
        }

        .procedure-performed-section .form-check-input:checked {
            background-color: var(--sami-green);
            border-color: var(--sami-green);
        }

        .procedure-performed-section .form-check-label {
            cursor: pointer;
            color: #212529;
            margin-left: 0.5rem;
        }

        .procedure-notes-field {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #dee2e6;
        }

        .procedure-notes-field .form-label {
            color: var(--text-main);
            margin-bottom: 0.5rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .search-area {
                flex-direction: column;
                gap: 0.75rem;
            }

            .btn-rapid-access {
                width: 100%;
                justify-content: center;
            }

            .btn-text-desktop {
                display: none;
            }

            .btn-text-mobile {
                display: inline;
            }

            .service-card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.75rem;
            }

            .btn-delete-service {
                width: 100%;
                justify-content: center;
            }

            .lab-results-grid {
                grid-template-columns: 1fr;
            }

            .service-card-body {
                padding: 1rem;
            }
        }

        @media (max-width: 480px) {
            .search-area {
                padding: 0.75rem;
            }

            .search-container {
                height: 42px;
            }

            .search-icon-btn {
                padding: 0 1rem;
                font-size: 1rem;
                min-width: 48px;
            }

            .search-input {
                padding: 0.6rem 0.75rem;
                font-size: 0.9rem;
            }

            .btn-rapid-access {
                padding: 0.6rem 1rem;
                font-size: 0.9rem;
            }
        }
    </style>

    <script>
        // Escuchar evento de resultados de laboratorio en tiempo real
        @if($type === 'laboratory' && $encounter_id)
        document.addEventListener('livewire:init', () => {
            if (typeof Echo !== 'undefined') {
                Echo.private('encounter.{{ $encounter_id }}')
                    .listen('.lab-results-received', (event) => {
                        console.log('Nuevos resultados de laboratorio recibidos:', event);

                        // Disparar evento de Livewire para refrescar
                        Livewire.dispatch('lab-results-received', event);

                        // Opcional: Mostrar notificación visual
                        if (window.showToast) {
                            window.showToast('Nuevos resultados de laboratorio disponibles', 'success');
                        }
                    });
            }
        });
        @endif

        // Función global para abrir offcanvas de acceso rápido
        window.openRapidAccessOffcanvas = function(offcanvasId) {
            // Intentar múltiples veces con retrasos incrementales
            let attempts = 0;
            const maxAttempts = 10;
            const baseDelay = 100;

            function tryOpen() {
                attempts++;
                const el = document.getElementById(offcanvasId);

                if (el) {
                    console.log('Elemento encontrado en intento', attempts);
                    let instance = bootstrap.Offcanvas.getInstance(el);

                    if (!instance) {
                        console.log('Creando instancia de offcanvas...');
                        try {
                            instance = bootstrap.Offcanvas.getOrCreateInstance(el, {
                                backdrop: true,
                                keyboard: true
                            });
                        } catch (error) {
                            console.error('Error al crear instancia:', error);
                            return;
                        }
                    }

                    if (instance) {
                        instance.show();
                        console.log('Offcanvas mostrado exitosamente');
                    }
                } else if (attempts < maxAttempts) {
                    console.log('Elemento no encontrado, reintentando... (intento', attempts, ')');
                    setTimeout(tryOpen, baseDelay * attempts);
                } else {
                    console.error('No se pudo encontrar el elemento después de', maxAttempts, 'intentos');
                }
            }

            tryOpen();
        };
    </script>
</div>
