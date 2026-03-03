<div @if($type === 'laboratory' && $this->hasPendingLabResults()) wire:poll.10s="checkForNewResults" @endif>
    {{-- HemoScreen Reminder - Solo para laboratorio --}}
    @if($type === 'laboratory' && $encounter && $encounter->practitioner && $encounter->practitioner->hasActiveHemoScreen())
        <x-hemoscreen-reminder :practitioner="$encounter->practitioner" class="mb-3" />
    @endif

    @if(count($selectedLists)>0)
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
                                    <div>{{$s->cpt->full_name}}</div>
                                    @if(in_array($s->code, ['85025', '85027']) && $encounter && $encounter->practitioner && $encounter->practitioner->hasActiveHemoScreen())
                                        <div class="alert alert-info d-inline-flex align-items-center mt-2 py-2 px-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.2);">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="white" class="bi bi-fingerprint me-2" viewBox="0 0 16 16">
                                                <path d="M8.06 6.5a.5.5 0 0 1 .5.5v.776a11.5 11.5 0 0 1-.552 3.519l-1.331 4.14a.5.5 0 0 1-.952-.305l1.33-4.141a10.5 10.5 0 0 0 .504-3.213V7a.5.5 0 0 1 .5-.5Z"/>
                                                <path d="M6.06 7a2 2 0 1 1 4 0 .5.5 0 1 1-1 0 1 1 0 1 0-2 0v.332q0 .613-.066 1.221A.5.5 0 0 1 6 8.447q.06-.555.06-1.115zm3.509 1a.5.5 0 0 1 .5.5v.67q0 .613-.066 1.221a.5.5 0 1 1-.994-.112q.06-.555.06-1.109V8.5a.5.5 0 0 1 .5-.5"/>
                                                <path d="M7.507 1.17a.5.5 0 0 1 .986 0v.726a11.5 11.5 0 0 1-.552 3.519l-1.331 4.14a.5.5 0 1 1-.952-.305l1.33-4.141a10.5 10.5 0 0 0 .504-3.213V1.17ZM6 4.5a.5.5 0 0 1 .5.5v.776a11.5 11.5 0 0 1-.552 3.519l-1.331 4.14a.5.5 0 1 1-.952-.305l1.33-4.141a10.5 10.5 0 0 0 .504-3.213V5a.5.5 0 0 1 .5-.5"/>
                                            </svg>
                                            <div>
                                                <strong class="text-white">Código HemoScreen:</strong>
                                                <span class="text-white fs-5 fw-bold ms-2 font-monospace" style="letter-spacing: 3px;">{{ $s->hemo_identification }}</span>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Resultados de Laboratorio --}}
                                    @if($s->observations()->count() > 0)
                                        <div class="mt-3" style="background: white; padding: 15px; border-radius: 10px; border: 2px solid #059669; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                                                <div style="font-size: 14px; font-weight: 700; color: #065f46;">
                                                    🧪 Resultados de Laboratorio
                                                </div>
                                                <span style="background: #d1fae5; color: #065f46; font-size: 11px; padding: 2px 8px; border-radius: 4px; font-weight: 600;">
                                                    {{ $s->observations()->count() }} resultados
                                                </span>
                                            </div>

                                            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 10px;">
                                                @foreach($s->observations as $observation)
                                                    <div style="background: #f0fdf4; padding: 12px; border-radius: 8px; border: 1px solid #bbf7d0;">
                                                        <div style="font-size: 10px; color: #059669; font-weight: 600; margin-bottom: 4px;">
                                                            {{ \App\Enums\LoincCode::getShortLabel($observation->code) }}
                                                        </div>
                                                        <div style="font-size: 22px; font-weight: 700; color: #1e293b; margin-bottom: 2px;">
                                                            {{ $observation->value }}
                                                            <span style="font-size: 11px; color: #64748b; font-weight: 500;">{{ $observation->unit }}</span>
                                                        </div>
                                                        @if($observation->status === 'final')
                                                            <div style="font-size: 9px; color: #059669;">✓ Final</div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>

                                            @if($s->observations->first() && $s->observations->first()->issued_date)
                                                <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #bbf7d0; font-size: 10px; color: #64748b;">
                                                    <strong>Resultados emitidos:</strong> {{ \Carbon\Carbon::parse($s->observations->first()->issued_date)->format('d/m/Y H:i') }}
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <div class="my-3">
                            {{__('consultation.instruction')}}
                            <x-autosave-input
                                type="text"
                                :value="$notes[$s->id]"
                                wire:model.live.debounce.500ms="notes.{{$s->id}}"
                                save-method="updateNote"
                                save-key="{{ $s->id }}"
                                class="form-control block w-full"
                            />
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
        <div class="selector-field selector-field-on">
        <x-autosave-action save-key="service-{{$type}}-search" />

        <table style="width:100%">
            <tbody>
            <tr>
                <td style="width:80%;padding:20px;">
                    <input type="text"  wire:model.live="query"   class="form-control" placeholder="Buscar por descripcion o codigo cpt" >
                </td>
                <td style="padding-top: 6px;padding-left: 6px;padding-right: 6px; width:10%">
                    <div class="general-btn-small"
                            type="button"
                            style="cursor: pointer;"
                            data-offcanvas-target="offcanvasRight-{{$encounter_id}}-{{$section_id}}"
                            onclick="openRapidAccessOffcanvas(this.getAttribute('data-offcanvas-target'))">
                        <div class="general-btn-small-text general-btn-small-text-a">Listado de Acceso Rápido</div>
                        <div class="general-btn-small-text general-btn-small-text-b">Ver listado</div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
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
                            <span style="font-size: 0.85rem; color: #212529; flex: 1;">{{ $result['description_es'] }}</span>
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
