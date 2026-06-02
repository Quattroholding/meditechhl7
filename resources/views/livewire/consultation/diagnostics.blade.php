<div>
    @if(count($selectedLists)>0)
        <x-input-label  :value="__('consultation.diagnostics')" />
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
                                    <span>{{ __('general.delete') }}</span>
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

        {{-- Validation Message Alert --}}
        @if($showValidationMessage)
            <div style="margin: 20px 20px 0 20px;">
                <div class="alert alert-warning" style="display: flex; justify-content: space-between; align-items: center; margin: 0; animation: slideDown 0.3s ease-out;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                        </svg>
                        <div>
                            <strong>{{ __('consultation.diagnostics_ai.required_fields_missing') }}</strong>
                            <div style="font-size: 0.9rem;">{{ $validationMessage }}</div>
                        </div>
                    </div>
                    <button wire:click="closeValidationMessage" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #856404; line-height: 1;">&times;</button>
                </div>
            </div>
        @endif

        {{-- Search Input and AI Button --}}
        <div style="width:100%;padding:20px;">
            <div style="display: flex; gap: 10px; align-items: center;">
                <input type="text"  wire:model.live="query" class="form-control" placeholder="{{ __('consultation.select_diagnosis') }}" style="padding: 0 20px; flex: 1;">

                @if($this->aiSuggestionsEnabled)
                <button
                    wire:click="getAiSuggestions"
                    wire:loading.attr="disabled"
                    wire:target="getAiSuggestions"
                    class="btn btn-primary"
                    style="white-space: nowrap; padding: 10px 20px; display: flex; align-items: center; gap: 8px;"
                    @if(!$this->canRequestAiSuggestions)
                        title="{{ __('consultation.diagnostics_ai.complete_required_fields') }}"
                        disabled
                        style="opacity: 0.6; cursor: not-allowed; white-space: nowrap; padding: 10px 20px; display: flex; align-items: center; gap: 8px;"
                    @else
                        title="{{ __('consultation.diagnostics_ai.suggest_with_ai') }}"
                    @endif
                >
                    <svg wire:loading.remove wire:target="getAiSuggestions" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8 0a1 1 0 0 1 1 1v5.268l4.562-2.634a1 1 0 1 1 1 1.732L10 8l4.562 2.634a1 1 0 1 1-1 1.732L9 9.732V15a1 1 0 1 1-2 0V9.732l-4.562 2.634a1 1 0 1 1-1-1.732L6 8 1.438 5.366a1 1 0 0 1 1-1.732L7 6.268V1a1 1 0 0 1 1-1z"/>
                    </svg>
                    <span class="spinner-border spinner-border-sm" wire:loading wire:target="getAiSuggestions"></span>
                    <span wire:loading.remove wire:target="getAiSuggestions">{{ __('consultation.diagnostics_ai.suggest_with_ai') }}</span>
                    <span wire:loading wire:target="getAiSuggestions">{{ __('consultation.diagnostics_ai.analyzing') }}</span>
                </button>
                @endif
            </div>

            {{-- Helper text for button --}}
            @if($this->aiSuggestionsEnabled)
            @if(!$this->canRequestAiSuggestions)
                <div style="margin-top: 8px; font-size: 0.85rem; color: #6c757d; display: flex; align-items: center; gap: 5px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                        <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                    </svg>
                    <span>{{ __('consultation.diagnostics_ai.complete_required_fields') }}</span>
                </div>
            @endif
            @endif
        </div>

        <style>
            @keyframes slideDown {
                from {
                    opacity: 0;
                    transform: translateY(-10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        </style>

        {{-- AI Suggestions Section --}}
        @if($showAiSuggestions)
            <div style="margin: 0 20px 20px 20px; border: 2px solid #0d6efd; border-radius: 8px; background: #f8f9fa;">
                {{-- Header --}}
                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 15px; border-radius: 6px 6px 0 0; color: white;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M8 0a1 1 0 0 1 1 1v5.268l4.562-2.634a1 1 0 1 1 1 1.732L10 8l4.562 2.634a1 1 0 1 1-1 1.732L9 9.732V15a1 1 0 1 1-2 0V9.732l-4.562 2.634a1 1 0 1 1-1-1.732L6 8 1.438 5.366a1 1 0 0 1 1-1.732L7 6.268V1a1 1 0 0 1 1-1z"/>
                            </svg>
                            <strong style="font-size: 1.1rem;">{{ __('consultation.diagnostics_ai.ai_suggestions') }}</strong>
                        </div>
                        <button wire:click="closeAiSuggestions" style="background: none; border: none; color: white; cursor: pointer; font-size: 1.5rem; line-height: 1;">&times;</button>
                    </div>

                    {{-- Disclaimer --}}
                    <div style="margin-top: 10px; padding: 10px; background: rgba(255,255,255,0.2); border-radius: 4px; font-size: 0.85rem;">
                        <strong>⚠️ {{ __('general.important') }}:</strong> {{ __('consultation.diagnostics_ai.important_disclaimer') }}
                    </div>
                </div>

                {{-- Loading State --}}
                @if($loadingAiSuggestions)
                    <div style="padding: 40px; text-align: center;">
                        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">{{ __('general.loading') }}</span>
                        </div>
                        <p style="margin-top: 15px; color: #6c757d;">{{ __('consultation.diagnostics_ai.loading_suggestions') }}</p>
                    </div>
                @endif

                {{-- Error State --}}
                @if($aiSuggestionsError && !$loadingAiSuggestions)
                    <div style="padding: 20px;">
                        <div class="alert alert-warning" style="margin: 0;">
                            <strong>⚠️ Atención:</strong> {{ $aiSuggestionsError }}
                        </div>
                    </div>
                @endif

                {{-- Suggestions List --}}
                @if(!$loadingAiSuggestions && !empty($aiSuggestions))
                    <div style="padding: 15px;max-height: 250px;overflow: auto;">
                        <p style="color: #6c757d; font-size: 0.9rem; margin-bottom: 15px;">
                            <strong>{{ count($aiSuggestions) }}</strong> {{ __('consultation.diagnostics_ai.suggested_diagnoses') }}
                        </p>

                        @foreach($aiSuggestions as $suggestion)
                            <div
                                wire:click="selectOption({{ json_encode($suggestion) }})"
                                x-on:click="window.dispatchEvent(new CustomEvent('autosave-start', { detail: 'diagnostic-search' }))"
                                style="
                                    margin-bottom: 10px;
                                    padding: 15px;
                                    background: white;
                                    border: 2px solid #dee2e6;
                                    border-radius: 6px;
                                    cursor: pointer;
                                    transition: all 0.2s;
                                    border-left: 4px solid {{ $suggestion['confidence'] === 'high' ? '#28a745' : ($suggestion['confidence'] === 'medium' ? '#ffc107' : '#6c757d') }};
                                "
                                onmouseover="this.style.borderColor='#0d6efd'; this.style.boxShadow='0 4px 8px rgba(0,0,0,0.1)'; this.style.transform='translateY(-2px)';"
                                onmouseout="this.style.borderColor='#dee2e6'; this.style.boxShadow='none'; this.style.transform='translateY(0)';"
                            >
                                <div style="display: flex; justify-content: space-between; align-items: start; gap: 10px;">
                                    <div style="flex: 1;">
                                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                                            <span class="badge bg-primary" style="font-size: 0.85rem; padding: 4px 8px;">
                                                {{ $suggestion['code'] }}
                                            </span>
                                            <span class="badge" style="
                                                background: {{ $suggestion['confidence'] === 'high' ? '#28a745' : ($suggestion['confidence'] === 'medium' ? '#ffc107' : '#6c757d') }};
                                                font-size: 0.75rem;
                                                padding: 3px 6px;
                                            ">
                                                {{ $suggestion['confidence'] === 'high' ? __('consultation.diagnostics_ai.high_confidence') : ($suggestion['confidence'] === 'medium' ? __('consultation.diagnostics_ai.medium_confidence') : __('consultation.diagnostics_ai.low_confidence')) }}
                                            </span>
                                        </div>
                                        <div style="font-weight: 600; color: #212529; margin-bottom: 5px;">
                                            {{ $suggestion['description_es'] }}
                                        </div>
                                        @if(!empty($suggestion['reasoning']))
                                            <div style="font-size: 0.85rem; color: #6c757d; font-style: italic;">
                                                💡 {{ $suggestion['reasoning'] }}
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#6c757d" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        @if(!empty($results))
            <div style="position: absolute; z-index: 1000; width: 100%; background: white; border: 1px solid #ddd; border-radius: 4px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">

                {{-- Header FIJO - NO forma parte del scroll --}}
                <div style="background: #ffffff; padding: 8px 12px; border-bottom: 2px solid #0d6efd;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div style="font-size: 0.9rem;">
                            <i class="fas fa-search text-primary"></i>
                            <strong>
                                @if($isCodeSearch)
                                    {{ __('consultation.diagnostics_ai.search_by_code') }}
                                @else
                                    {{ __('consultation.diagnostics_ai.search_by_description') }}
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
                            <span style="font-size: 0.85rem; color: #212529; flex: 1;">{{  app()->getLocale() === 'es' ? $result['description_es'] : $result['description']  }}</span>
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
                                    <strong>{{ strtoupper(__('consultation.diagnostics_ai.load_more_results')) }}</strong>
                                </div>
                                <div style="font-size: 0.9rem; margin-top: 4px; opacity: 0.9;">
                                    {{ __('general.there_are') }} {{ $totalResults - count($results) }} {{ __('consultation.diagnostics_ai.results_available') }}
                                </div>
                            </div>
                            <div wire:loading wire:target="loadMore">
                                <div class="spinner-border text-white" role="status">
                                    <span class="visually-hidden">{{ __('consultation.diagnostics_ai.loading_results') }}</span>
                                </div>
                                <div style="margin-top: 8px;">{{ __('consultation.diagnostics_ai.loading_results') }}</div>
                            </div>
                        </div>
                    @endif

                    {{-- Mensaje final --}}
                    @if(!$hasMoreResults && count($results) > 0)
                        <div style="padding: 10px 12px; text-align: center; color: #6c757d; font-size: 0.85rem; background: #f8f9fa;">
                            <i class="fas fa-check-circle text-success"></i>
                            {{ __('consultation.diagnostics_ai.all_results_shown') }}
                        </div>
                    @endif
                </div>
            </div>
        @endif


        <div style="height:200px;">&nbsp;</div>
    </div>

</div>
