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
        <table style="width:100%;" border="1"
               class="medicine-table">
            <tbody>
            <tr>

            </tr>
            @foreach($selectedLists as $m)
            <tr class="consultation-tr-inputs" style="background: {{ $loop->iteration % 2 == 0 ? '#fff' : '#ededed' }}">
                <td>
                    <b rel="producto-full-name">
                        @if($m->medication2)
                            {{ $m->medication2->display }}
                            @if($m->medication2->ingredients->count() > 0)
                                @php $ing = $m->medication2->ingredients->first(); @endphp
                                ({{ $m->medication2->form }} {{ $ing->strength_value }} {{ $ing->strength_unit }})
                            @endif
                        @elseif($m->medicine)
                            {{ $m->medicine->full_name }}
                        @else
                            {{ $m->medication }}
                        @endif
                    </b>
                </td>
                <td>
                    <table style="width:100%;">
                        <tbody>
                        <tr>
                            <td >
                                <div class="input-block local-forms">
                                    <x-input-label for="quantity" :value="__('Cantidad')" />
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
                                <div class="input-block local-forms">
                                    <x-input-label for="frecuency" :value="__('Frecuencia (Horas)')" />
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
                            <td>
                                <div class="input-block local-forms">
                                    <x-input-label for="route" :value="__('Via')" />
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

                                <div class="input-block local-forms">
                                    <x-input-label for="duration" :value="__('Duración (Días)')" />
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
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div class="input-block local-forms">
                                    <x-input-label for="dosage_text" :value="__('Indicaciones')"/>
                                    <x-autosave-input
                                        type="textarea"
                                        :value="$dosage_texts[$m->id]"
                                        disabled="disabled"
                                        class="form-control mt-1 block w-full"
                                        rows="2"
                                        wire:model.live="dosage_texts.{{$m->id}}"
                                        laceholder="Ejemplo: Una tableta cada 8 horas vía oral por 5 días"
                                        save-method="updateField"
                                        save-key="dosage_text_{{ $m->id }}"
                                    />
                                </div>
                            </td>
                            {{--}}
                            <td>
                                <div class="input-block local-forms">
                                    <x-input-label for="refills" :value="__('Meses de Refill')" />
                                    <select class="form-control" wire:change="updateField({{$m->id}},$event.target.value,'refills')">
                                        <option value="0">Sin Refill</option>
                                        @for($i=2;$i<6;$i++)
                                            <option value="{{$i}}" @if($m->refills==$i) selected @endif>{{$i}} meses</option>
                                        @endfor
                                    </select>
                                </div>
                            </td>
                            {{--}}
                        </tr>
                        </tbody>
                    </table>
                </td>
                <td>
                    <div class="sprite-trash-container" ani="1" style="cursor:pointer" wire:click="delete({{$m->id}})">
                        <div class="sprite-trash"></div>
                        <div>Borrar</div>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
        </table>
    @endif
    <div class="my-3"></div>
    <div class="general-btn-small" wire:click="medical_request_history">
        <div class="general-btn-small-text general-btn-small-text-a">Historial de Medicamentos</div>
        <div class="general-btn-small-text general-btn-small-text-b">Ver listado</div>
    </div>
    <p>&nbsp;</p>
    @php $id =\Illuminate\Support\Str::uuid();@endphp
    <div class="selector-field selector-field-on">
        <x-autosave-action save-key="medication-search" />

        <table style="width:100%">
            <tbody>
            <tr>
                <td style="width:80%;padding:20px;">
                    <input type="text"  wire:model.live="query"   class="form-control" placeholder="Buscar medicamento por nombre, código NDC o nombre genérico" >
                </td>
                <td style="padding-top: 6px;padding-left: 6px;padding-right: 6px; width:10%">
                    <div class="general-btn-small"
                            type="button"
                            data-bs-toggle="offcanvas"
                            data-bs-target="#offcanvasRight{{$id}}"
                            aria-controls="offcanvasRight">
                        <div class="general-btn-small-text general-btn-small-text-a">Listado de Acceso Rápido</div>
                        <div class="general-btn-small-text general-btn-small-text-b">Ver listado</div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
        <div class="offcanvas offcanvas-end quick-items quick-items-active" tabindex="-1" id="offcanvasRight{{$id}}" aria-labelledby="offcanvasRightLabel" >
            <div class="offcanvas-body  quick-items-content">
                <div  class="quick-items-close" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Cerrar">
                    <img src="/images/close-floating.png" alt="">
                </div>
                <div class="sel-item-list-category">ACCESOS RAPIDOS</div>
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
                        <p>No hay accesos rápidos configurados</p>
                    </div>
                @endif
                {{-- Botones de control del panel --}}
                <div class="mt-4 d-flex gap-2 border-top pt-3">
                    <button type="button"
                            class="btn btn-sm btn-outline-secondary"
                            wire:click="clearSearch">
                        <i class="fas fa-eraser"></i> Limpiar búsqueda
                    </button>

                    <button type="button"
                            class="btn btn-sm btn-secondary"
                            data-bs-dismiss="offcanvas">
                        <i class="fas fa-times"></i> Cerrar Panel
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
                                    Búsqueda por código
                                @else
                                    Búsqueda por nombre
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

   <!-- Componente del historial de medicamentos -->
   <livewire:patient.medication-history />
</div>
