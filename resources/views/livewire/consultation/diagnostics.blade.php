<div>
    @if(count($selectedLists)>0)
        <x-input-label  :value="__('Diagnosticos')" />
        <div id="" class="multiple-field-values mb-3">
            <div class="multivalue-item-container">
                @foreach($selectedLists as $s)
                    <div class="multivalue-item" code="{{$s->id}}">
                        <table wire:click="delete({{$s->id}})">
                            <tbody>
                            <tr>
                                <td>
                                <span>
                                <div class="delete-multivalue">
                                    <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
                                        <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5Zm-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5ZM4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06Zm6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528ZM8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5Z"></path></svg>
                                    </span>
                                    <span>Borrar</span>
                                </div>
                                </span>
                                </td>
                                <td>
                                    {{$s->condition->icd10Code ? $s->condition->icd10Code->full_name : $s->condition->onset_info}}
                                </td>
                            </tr>
                            </tbody>
                        </table>

                        <div style="width:100%" class="">{{__('condition.severity')}}
                            <x-autosave-input
                                type="select"
                                name="severity"
                                class="form-control block mt-1 w-full"
                                :options="\App\Models\Lista::conditionSeverity()"
                                :selected="$s->condition->severity"
                                wire:model.live="severity.{{ $s->id }}"
                                save-method="updateSeverity"
                                save-key="{{ $s->id }}"
                            />
                        </div>
                        <div class="my-3"></div>
                        <div style="width:100%" class="">{{__('consultation.diagnostic_note')}}
                            <x-autosave-input
                                type="textarea"
                                :value="$s->condition->note"
                                class="form-control mt-1 block w-full"
                                rows="2"
                                wire:model.live.debounce.500ms="notes.{{$s->id}}"
                                save-method="updateNote"
                                save-key="note_{{$s->id}}"
                            />
                        </div>
                    </div>

                @endforeach
            </div>
        </div>
    @endif

    <div class="selector-field selector-field-on">
    <x-autosave-action save-key="diagnostic-search" />
    <div style="width:100%;padding:20px;">
        <input type="text"  wire:model.live="query"   class="form-control" placeholder="Escribir el diagnostico" style="padding: 0 20px;">
    </div>

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

            {{-- Contenedor con scroll - SOLO los resultados - MÁS ALTO para ver varios --}}
            <div style="max-height: 320px; min-height: 300px; overflow-y: auto;">
                {{-- Resultados SIN agrupación - MÁS COMPACTOS --}}
                @foreach($results as $result)
                    <div
                        class="sel-list-item"
                        wire:click.debounce.300ms="selectOption({{ json_encode($result) }})"
                        x-on:click="window.dispatchEvent(new CustomEvent('autosave-start', { detail: 'diagnostic-search' }))"
                        style="padding: 6px 10px; cursor: pointer; border-bottom: 1px solid #e9ecef; transition: background 0.15s; display: flex; align-items: center; gap: 8px;"
                        onmouseover="this.style.background='#f0f7ff'"
                        onmouseout="this.style.background='white'"
                    >
                        <span class="badge bg-primary" style="font-size: 0.75rem; padding: 2px 6px; min-width: 60px; text-align: center;">{{ $result['code'] }}</span>
                        <span style="font-size: 0.85rem; color: #212529; flex: 1;">{{ $result['description_es'] }}</span>
                    </div>
                @endforeach

                {{-- Botón "Ver más" - MUY VISIBLE --}}
                @if($hasMoreResults)
                    <div
                        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); width:99%;padding: 10px; text-align: center; cursor: pointer; border: none; color: white; font-weight: bold; box-shadow: 0 4px 6px rgba(0,0,0,0.2);"
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
</div>
