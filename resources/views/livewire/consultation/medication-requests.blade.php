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
    <style>

        .producto-full-name {
            font-size: 16px; color: #333;padding: 10px;
        }

        .producto-form{
            font-size: 12px; color: #666;
        }

        .medication-records-group {
            margin-bottom: 15px;
            border-radius: 6px;
            border: 1px solid #ddd;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .medication-records-group:hover {
            background-color: #005dba;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .medication-records-group:hover table {
            background-color: #005dba;
        }

        .medication-records-group:hover table tr {
            background-color: #005dba !important;
        }

        .medication-records-group:hover .producto-full-name{
            color:#fff;
        }

        .medication-records-group:hover .producto-form{
            color:#fff;
        }

        .medication-records-group:hover .producto-full-name {
            background-color: #005dba;
        }

        .sprite-trash-container {
            transition: all 0.2s ease;
            padding: 8px 12px;
            border-radius: 4px;
            background-color: transparent;
            display: inline-block;
        }

        .sprite-trash-container:hover {
            background-color: #ba0900;
            transform: scale(1.05);
            box-shadow: 0 2px 8px rgba(255, 0, 0, 0.2);
            color:#fff;
        }

        .sprite-trash-container:hover .sprite-trash {
            filter: brightness(1.2);
        }

        .medication-records-group:hover .sprite-trash-container{
            color:#fff;
        }
    </style>
    @if(count($selectedLists)>0)
        <div style="display: grid; gap: 15px;">
            @foreach($selectedLists as $m)
                <div class="medication-records-group"
                     @mouseenter="hoveredMedication = {{ $m->id }}"
                     @mouseleave="hoveredMedication = null">
                    <table width="100%">
                        <tr>
                            <td colspan="5">
                                <b rel="producto-full-name" class="producto-full-name" >
                                    @if($m->medication2)
                                        {{ $m->medication2->display }}
                                        @if($m->medication2->ingredients->count() > 0)
                                            @php $ing = $m->medication2->ingredients->first(); @endphp
                                            <span class="producto-form">({{ $m->medication2->form }} {{ $ing->strength_value }} {{ $ing->strength_unit }})</span>
                                        @endif
                                    @elseif($m->medicine)
                                        {{ $m->medicine->full_name }}
                                    @else
                                        {{ $m->medication }}
                                    @endif
                                </b>
                            </td>
                            <td style="text-align: right; padding-right: 15px;">
                                <div class="sprite-trash-container"
                                     ani="1"
                                     style="cursor:pointer;"
                                     wire:click="delete({{ $m->id }})">
                                    <div class="sprite-trash"></div>
                                    <div style="font-weight: 500; font-size: 12px;">{{ __('consultation.medication_requests_section.delete') }}</div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6" style="padding: 5px;"></td>
                        </tr>
                        <tr>
                            <td style="width: 20%;padding:10px;">
                                <div class="input-block local-forms">
                                    <x-input-label for="quantity" :value="__('consultation.medication_requests_section.quantity')" />
                                    <x-autosave-input
                                        type="number"
                                        :value="$quantitys[$m->id]"
                                        wire:model.live="quantitys.{{ $m->id }}"
                                        save-method="saveValue"
                                        save-key="quantity-{{ $m->id }}"
                                        class="form-control block w-full"
                                        onkeydown="return (event.keyCode !== 69 && event.keyCode !== 189 && event.keyCode !== 187)"
                                        min="0" step="1"
                                        placeholder="Ejemplo : 2"
                                    />

                                </div>
                            </td>
                            <td style="width: 20%;padding:10px;">
                                <div class="input-block local-forms">
                                    <x-input-label for="frecuency" :value="__('consultation.medication_requests_section.frequency')" />
                                    <x-autosave-input
                                        type="number"
                                        :value="$frecuencies[$m->id]"
                                        onkeydown="return (event.keyCode !== 69 && event.keyCode !== 189 && event.keyCode !== 187)"
                                        min="0" step="1"
                                        placeholder="Ejemplo : 2"
                                        wire:model.live="frecuencies.{{ $m->id }}"
                                        save-method="updateField"
                                        save-key="frecuency-{{ $m->id }}"
                                        class="form-control block w-full"
                                    />
                                </div>
                            </td>
                            <td style="width: 20%;padding:10px;">
                                <div class="input-block local-forms">
                                    <x-input-label for="route" :value="__('consultation.medication_requests_section.route')" />
                                    <x-autosave-input
                                        type="select"
                                        :value="$routes[$m->id]"
                                        :options="\App\Models\Lista::medicationVias()"
                                        :selected="$routes[$m->id]"
                                        wire:model.live="routes.{{ $m->id }}"
                                        save-method="updateField"
                                        save-key="route-{{ $m->id }}"
                                        class="form-control block w-full"
                                    />
                                </div>
                            </td>
                            <td style="width: 20%;padding:10px;">
                                <div class="input-block local-forms">
                                    <x-input-label for="duration" :value="__('consultation.medication_requests_section.duration')" />
                                    <x-autosave-input
                                        type="number"
                                        :value="$durations[$m->id]"
                                        onkeydown="return (event.keyCode !== 69 && event.keyCode !== 189 && event.keyCode !== 187)"
                                        min="0" step="1"
                                        placeholder="Ejemplo : 5"
                                        wire:model.live="durations.{{ $m->id }}"
                                        save-method="updateField"
                                        save-key="duration-{{ $m->id }}"
                                        class="form-control block w-full"
                                    />
                                </div>
                            </td>
                            <td colspan="2" style="width: 20%;padding:10px;">
                                <div class="input-block local-forms">
                                    <x-input-label for="duration_type" :value="__('consultation.medication_requests_section.duration_type')" />
                                    <x-autosave-input
                                        type="select"
                                        :value="$duration_types[$m->id] ?? 'dias'"
                                        :options="[
                                                    'dias' => 'Días',
                                                    'semanas' => 'Semanas',
                                                    'meses' => 'Meses',
                                                    'años' => 'Años',
                                                    'indefinido' => 'Indefinido'
                                                ]"
                                        :selected="$duration_types[$m->id] ?? 'dias'"
                                        wire:model.live="duration_types.{{ $m->id }}"
                                        save-method="updateField"
                                        save-key="duration-type-{{ $m->id }}"
                                        class="form-control block w-full"
                                    />
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="padding: 10px">
                                <div class="input-block local-forms">
                                    <x-input-label for="dosage_text" :value="__('consultation.medication_requests_section.indications')"/>
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
                            </td>
                            <td colspan="3" style="padding: 10px">
                                <div class="input-block local-forms">
                                    <x-input-label for="additional_indications" :value="__('consultation.medication_requests_section.additional_indications')"/>
                                    <x-autosave-input
                                        type="textarea"
                                        :value="$additional_indications[$m->id]"
                                        class="form-control mt-1 block w-full"
                                        rows="2"
                                        wire:model.live="additional_indications.{{ $m->id }}"
                                        placeholder="Ejemplo: Tomar con alimentos, evitar alcohol"
                                        save-method="updateField"
                                        save-key="additional-indications-{{ $m->id }}"
                                    />
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            @endforeach
        </div>
    @endif
    <div class="my-3"></div>
    <div class="general-btn-small" wire:click="medical_request_history">
        <div class="general-btn-small-text general-btn-small-text-a">{{ __('consultation.medication_requests_section.medication_history') }}</div>
        <div class="general-btn-small-text general-btn-small-text-b">{{ __('consultation.medication_requests_section.view_list') }}</div>
    </div>
    <p>&nbsp;</p>
    @php $id =\Illuminate\Support\Str::uuid();@endphp
    <div class="selector-field selector-field-on">
        <x-autosave-action save-key="medication-search" />

        <table style="width:100%">
            <tbody>
            <tr>
                <td style="width:80%;padding:20px;">
                    <input type="text"  wire:model.live="query"   class="form-control" placeholder="{{ __('consultation.medication_requests_section.search_placeholder') }}" >
                </td>
                <td style="padding-top: 6px;padding-left: 6px;padding-right: 6px; width:10%">
                    <div class="general-btn-small"
                            type="button"
                            data-bs-toggle="offcanvas"
                            data-bs-target="#offcanvasRight{{$id}}"
                            aria-controls="offcanvasRight">
                        <div class="general-btn-small-text general-btn-small-text-a">{{ __('consultation.medication_requests_section.rapid_access_list') }}</div>
                        <div class="general-btn-small-text general-btn-small-text-b">{{ __('consultation.medication_requests_section.view_list') }}</div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
        <div class="offcanvas offcanvas-end quick-items quick-items-active" tabindex="-1" id="offcanvasRight{{$id}}" aria-labelledby="offcanvasRightLabel" >
            <div class="offcanvas-body  quick-items-content">
                <div  class="quick-items-close" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="{{ __('consultation.medication_requests_section.close_panel') }}">
                    <img src="/images/close-floating.png" alt="">
                </div>
                <div class="sel-item-list-category">{{ strtoupper(__('consultation.rapid_access.title')) }}</div>
                @if(count($rapidAccess) > 0)
                    @foreach($rapidAccess as $i)
                        <div class="sel-list-item mb-2"
                             style="cursor: pointer; padding: 10px; border-radius: 5px; border: 1px solid #dee2e6;">

                            {{-- Contenido principal clickeable --}}
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
                {{-- Botones de control del panel --}}
                <div class="mt-4 d-flex gap-2 border-top pt-3">
                    <button type="button"
                            class="btn btn-sm btn-outline-secondary"
                            wire:click="clearSearch">
                        <i class="fas fa-eraser"></i> {{ __('consultation.medication_requests_section.clear_search') }}
                    </button>

                    <button type="button"
                            class="btn btn-sm btn-secondary"
                            data-bs-dismiss="offcanvas">
                        <i class="fas fa-times"></i> {{ __('consultation.medication_requests_section.close_panel') }}
                    </button>
                </div>
            </div>
        </div> <!-- end offcanvas-body-->

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

                {{-- Contenedor con scroll - SOLO los resultados - MÁS ALTO para ver varios --}}
                <div style="max-height: 320px; min-height: 300px; overflow-y: auto;">
                    {{-- Resultados SIN agrupación - MÁS COMPACTOS --}}
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

                    {{-- Mensaje final --}}
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
