<div x-data="{}"
     x-init="
        $wire.on('option-selected', (data) => {
           document.body.removeAttribute('style');
           document.body.classList.remove('modal-open');
           const backdrop = document.querySelector('.offcanvas-backdrop');
           if (backdrop) {
               backdrop.remove();
           }
        });

        $wire.on('showSaving', (event) => {
            const medicationId = event.medicationId;
            const savingEl = document.getElementById('saving-' + medicationId);
            const savedEl = document.getElementById('saved-' + medicationId);

            if (savingEl) savingEl.style.display = 'flex';
            if (savedEl) savedEl.style.display = 'none';
        });

        $wire.on('showSaved', (event) => {
            const medicationId = event.medicationId;
            const savingEl = document.getElementById('saving-' + medicationId);
            const savedEl = document.getElementById('saved-' + medicationId);

            if (savingEl) savingEl.style.display = 'none';
            if (savedEl) {
                savedEl.style.display = 'flex';

                // Ocultar después de 3 segundos
                setTimeout(() => {
                    savedEl.style.display = 'none';
                }, 3000);
            }
        });

        $wire.on('hideSaving', (event) => {
            const medicationId = event.medicationId;
            const savingEl = document.getElementById('saving-' + medicationId);
            if (savingEl) savingEl.style.display = 'none';
        });
     ">

    @if(count($selectedLists)>0)
        <div class="mb-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="mb-0" style="color: var(--primary-color); font-weight: 600;">
                    <i class="fas fa-prescription-bottle-alt me-2"></i>
                    {{ __('consultation.medication_requests_section.selected_medications') }}
                </h5>
                <span class="badge" style="background: var(--sami-green); font-size: 0.9rem; padding: 0.4rem 0.8rem;">
                    {{ count($selectedLists) }} {{ __('consultation.medication_requests_section.items') }}
                </span>
            </div>

            <div class="service-cards-container">
                @foreach($selectedLists as $m)
                    <div class="service-card" x-data="{ confirmDelete: false }">
                        <!-- Header de la tarjeta -->
                        <div class="service-card-header">
                            <div class="service-card-title">
                                <i class="fas fa-pills me-2" style="color: var(--sami-green);"></i>
                                @if($m->medication2)
                                    {{ $m->medication2->display }}
                                    @if($m->medication2->ingredients->count() > 0)
                                        @php $ing = $m->medication2->ingredients->first(); @endphp
                                        <span style="font-size: 0.85rem; color: #666;">({{ $m->medication2->form }} {{ $ing->strength_value }} {{ $ing->strength_unit }})</span>
                                    @endif
                                @elseif($m->medicine)
                                    {{ $m->medicine->full_name }}
                                @else
                                    {{ $m->medication }}
                                @endif
                            </div>
                            <button
                                type="button"
                                class="btn-delete-service"
                                @click="confirmDelete = !confirmDelete"
                                title="{{ __('consultation.medication_requests_section.delete') }}"
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
                                    {{ __('consultation.medication_requests_section.confirm_delete') }}
                                </span>
                                <div class="d-flex gap-2">
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-danger"
                                        @click.prevent="$wire.delete({{$m->id}}); confirmDelete = false;"
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
                            <!-- Campos del medicamento -->
                            <div class="medication-fields-grid">
                                <!-- Cantidad -->
                                <div class="input-block local-forms">
                                    <x-input-label for="quantity_{{$m->id}}" :value="__('consultation.medication_requests_section.quantity')" />
                                    <x-autosave-input
                                        type="number"
                                        :value="$quantitys[$m->id]"
                                        wire:model.live="quantitys.{{ $m->id }}"
                                        save-method="saveValue"
                                        save-key="quantity-{{ $m->id }}"
                                        class="form-control block w-full"
                                        onkeydown="return (event.keyCode !== 69 && event.keyCode !== 189 && event.keyCode !== 187)"
                                        min="0" step="1"
                                        placeholder="Ejemplo: 2"
                                    />
                                </div>

                                <!-- Indicaciones -->
                                <div class="input-block local-forms">
                                    <x-input-label for="dosage_text_{{$m->id}}" :value="__('consultation.medication_requests_section.indications')"/>
                                    <x-autosave-input
                                        type="textarea"
                                        :value="$dosage_texts[$m->id]"
                                        class="form-control mt-1 block w-full"
                                        rows="2"
                                        wire:model.live="dosage_texts.{{$m->id}}"
                                        placeholder="Ejemplo: 1 tableta cada 8 horas vía oral por 5 días"
                                        save-method="updateField"
                                        save-key="dosage_text_{{ $m->id }}"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="my-3"></div>

    <!-- Botón de historial de medicamentos -->
    <div class="general-btn-small" wire:click="medical_request_history" style="margin-bottom: 15px;">
        <div class="general-btn-small-text general-btn-small-text-a">{{ __('consultation.medication_requests_section.medication_history') }}</div>
        <div class="general-btn-small-text general-btn-small-text-b">{{ __('consultation.medication_requests_section.view_list') }}</div>
    </div>

    <p>&nbsp;</p>

    @php $id = \Illuminate\Support\Str::uuid(); @endphp

    <div class="selector-field selector-field-on">
        <x-autosave-action save-key="medication-search" />

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
                    placeholder="{{ __('consultation.medication_requests_section.search_placeholder') }}"
                >
            </div>

            <button
                type="button"
                class="btn-rapid-access"
                data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasRight{{$id}}"
                aria-controls="offcanvasRight"
                title="{{ __('consultation.medication_requests_section.rapid_access_list') }}"
            >
                <i class="fas fa-star me-2"></i>
                <span class="btn-text-desktop">{{ __('consultation.medication_requests_section.rapid_access_list') }}</span>
                <span class="btn-text-mobile">
                    <i class="fas fa-star"></i>
                </span>
            </button>
        </div>

        <!-- Offcanvas para acceso rápido -->
        <div class="offcanvas offcanvas-end quick-items quick-items-active" tabindex="-1" id="offcanvasRight{{$id}}" aria-labelledby="offcanvasRightLabel">
            <div class="offcanvas-body quick-items-content">
                <div class="quick-items-close" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="{{ __('consultation.medication_requests_section.close_panel') }}">
                    <img src="/images/close-floating.png" alt="">
                </div>
                <div class="sel-item-list-category">{{ strtoupper(__('consultation.rapid_access.title')) }}</div>
                @if(count($rapidAccess) > 0)
                    @foreach($rapidAccess as $i)
                        <div class="sel-list-item mb-2" style="cursor: pointer; padding: 10px; border-radius: 5px; border: 1px solid #dee2e6;">
                            @if($i->medication)
                                <div wire:click="selectOption({{ json_encode(['id'=>$i->medication_id,'name'=>$i->medication->display]) }})">
                                    <div class="sel-list-item-code fw-bold">{{ $i->medication->home_name ?? $i->medication->display }}</div>
                                    <div class="sel-list-item-content">
                                        {{ $i->medication->display }}
                                        @if($i->medication->ingredients->count() > 0)
                                            @php $ing = $i->medication->ingredients->first(); @endphp
                                            ({{ $i->medication->form }} {{ $ing->strength_value }} {{ $ing->strength_unit }})
                                        @endif
                                    </div>
                                </div>
                            @elseif($i->medicine)
                                <div wire:click="selectOption({{ json_encode(['id'=>$i->medicine_id,'name'=>$i->medicine->full_name]) }})">
                                    <div class="sel-list-item-code fw-bold">{{ $i->medicine->home_name }}</div>
                                    <div class="sel-list-item-content">{{ $i->medicine->full_name }}</div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="text-center text-muted py-4">
                        <p>{{ __('consultation.medication_requests_section.no_rapid_access') }}</p>
                    </div>
                @endif

                <div class="mt-4 d-flex gap-2 border-top pt-3">
                    <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="clearSearch">
                        <i class="fas fa-eraser"></i> {{ __('consultation.medication_requests_section.clear_search') }}
                    </button>
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="offcanvas">
                        <i class="fas fa-times"></i> {{ __('consultation.medication_requests_section.close_panel') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Resultados de búsqueda -->
        @if(!empty($results))
            <div style="position: absolute; z-index: 1000; width: 100%; background: white; border: 1px solid #ddd; border-radius: 4px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <!-- Header FIJO -->
                <div style="background: #ffffff; padding: 8px 12px; border-bottom: 2px solid #0d6efd;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div style="font-size: 0.9rem;">
                            <i class="fas fa-search text-primary"></i>
                            <strong>
                                @if($isCodeSearch)
                                    {{ __('consultation.medication_requests_section.search_by_code') }}
                                @else
                                    {{ __('consultation.medication_requests_section.search_by_name') }}
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

                <!-- Contenedor con scroll -->
                <div style="max-height: 320px; min-height: 300px; overflow-y: auto;">
                    @foreach($results as $result)
                        <div
                            class="sel-list-item"
                            wire:click.debounce.300ms="selectOption({{ json_encode($result) }})"
                            x-on:click="window.dispatchEvent(new CustomEvent('autosave-start', { detail: 'medication-search' }))"
                            style="padding: 6px 10px; cursor: pointer; border-bottom: 1px solid #e9ecef; transition: background 0.15s; display: flex; align-items: center; gap: 8px;"
                            onmouseover="this.style.background='#f0f7ff'"
                            onmouseout="this.style.background='white'"
                        >
                            @if(!empty($result['code']))
                                <span class="badge bg-success" style="font-size: 0.75rem; padding: 2px 6px; min-width: 80px; text-align: center;">{{ $result['code'] }}</span>
                            @endif
                            <div style="flex: 1;">
                                <div style="font-size: 0.85rem; color: #212529; font-weight: 500;">{{ $result['name'] }}</div>
                                @if(!empty($result['generic_name']) && $result['generic_name'] !== $result['name'])
                                    <div style="font-size: 0.75rem; color: #6c757d;">{{ $result['generic_name'] }}</div>
                                @endif
                            </div>
                            <button
                                type="button"
                                class="btn btn-sm btn-outline-primary"
                                wire:click.stop="addToRapidAccess({{ $result['id'] }})"
                                title="{{ __('consultation.medication_requests_section.add_to_rapid_access') }}"
                                style="padding: 2px 8px; font-size: 0.75rem;"
                            >
                                <i class="fas fa-star"></i>
                            </button>
                        </div>
                    @endforeach

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
                                    <strong>{{ strtoupper(__('consultation.medication_requests_section.load_more_results')) }}</strong>
                                </div>
                                <div style="font-size: 0.9rem; margin-top: 4px; opacity: 0.9;">
                                    {{ __('generic.there_are') }} {{ $totalResults - count($results) }} {{ __('consultation.medication_requests_section.results_available') }}
                                </div>
                            </div>
                            <div wire:loading wire:target="loadMore">
                                <div class="spinner-border text-white" role="status">
                                    <span class="visually-hidden">{{ __('consultation.medication_requests_section.loading') }}</span>
                                </div>
                                <div style="margin-top: 8px;">{{ __('consultation.medication_requests_section.loading') }}</div>
                            </div>
                        </div>
                    @endif

                    @if(!$hasMoreResults && count($results) > 0)
                        <div style="padding: 10px 12px; text-align: center; color: #6c757d; font-size: 0.85rem; background: #f8f9fa;">
                            <i class="fas fa-check-circle text-success"></i>
                            {{ __('consultation.medication_requests_section.all_results_shown') }}
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <div style="height:200px;">&nbsp;</div>

    <!-- Componente del historial de medicamentos -->
    <livewire:patient.medication-history />

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

        /* Medication Fields Grid */
        .medication-fields-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
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

            .medication-fields-grid {
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
</div>

@script
<script>
    // Listen for voice dictation events
    console.log('MedicationRequests: Listener initialized');

    $wire.on('voice-dictation-medications', (event) => {
        console.log('MedicationRequests: Event received', event);

        // Call the updateFromVoice method
        $wire.updateFromVoice(event);
    });
</script>
@endscript
