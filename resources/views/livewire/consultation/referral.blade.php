<div>
    @if(count($selectedLists)>0)
        <table style="width:100%" class="medicine-table">
            <tbody>
            <tr>
                <td>Especialista</td>
                <td></td>
            </tr>
            @foreach($selectedLists as $s)
            <tr class="consultation-tr-inputs" style="background: {{ $loop->iteration % 2 == 0 ? '#fff' : '#ededed' }}">
                <td>{{$s->speciality->name}}</td>
                <td>
                    <div class="input-block local-forms">
                        <x-input-label for="physical_address" :value="__('Nota de Referencia')" />
                        <x-autosave-input
                            type="textarea"
                            :value="$selectedReason[$s->id]"
                            class="form-control mt-1 block w-full"
                            rows="2"
                            wire:model.live.debounce.500ms="selectedReason.{{$s->id}}"
                            save-method="setReason"
                            save-key="referral_{{ $s->id }}"
                        />
                    </div>
                </td>
                <td></td>
                <td>
                    <div class="multivalue-item-sustento-container">
                        <div class="input-block local-forms">
                            <x-input-label for="especialist" :value="__('Especialista')" />

                            @if($s->referred_to_id)
                                @php
                                    $selectedPractitioner = \App\Models\Practitioner::find($s->referred_to_id);
                                @endphp

                                @if($selectedPractitioner)
                                    <!-- Médico seleccionado -->
                                    <div style="display: flex; gap: 10px; align-items: center; padding: 12px; background: #f8f9fa; border: 2px solid #28a745; border-radius: 8px; margin-bottom: 10px;">
                                        <div style="flex: 1;">
                                            <div style="font-weight: 600; color: #333; margin-bottom: 2px;">
                                                <i class="fa fa-user-md" style="color: #28a745; margin-right: 5px;"></i>
                                                {{ $selectedPractitioner->name }}
                                            </div>
                                            <div style="font-size: 12px; color: #666;">
                                                ID: {{ $selectedPractitioner->identifier }}
                                            </div>
                                        </div>
                                        <button type="button" wire:click="openMedicalDirectory({{$s->code}}, {{$s->id}})" class="btn btn-sm btn-outline-primary" style="white-space: nowrap;">
                                            <i class="fa fa-exchange-alt"></i> Cambiar
                                        </button>
                                    </div>
                                @endif
                            @else
                                <!-- No hay médico seleccionado -->
                                <div style="text-align: center; padding: 20px; background: #f8f9fa; border: 2px dashed #dee2e6; border-radius: 8px;">
                                    <i class="fa fa-user-md" style="font-size: 32px; color: #ccc; margin-bottom: 10px;"></i>
                                    <div style="color: #666; margin-bottom: 15px; font-size: 14px;">No hay médico seleccionado</div>
                                    <button type="button" wire:click="openMedicalDirectory({{$s->code}}, {{$s->id}})" class="btn btn-primary">
                                        <i class="fa fa-users"></i> Ver Directorio Médico
                                    </button>
                                </div>
                            @endif

                            @include('partials.input_saving',['saving'=>$savingEspecialist[$s->id] ?? false, 'saved'=>$savedEspecialist[$s->id] ?? false])
                            @if(isset($savedEspecialist[$s->id]) && $savedEspecialist[$s->id] && isset($savedEspecialistTimer[$s->id]) && $savedEspecialistTimer[$s->id])
                                <div wire:poll.3s="resetSavedEspecialist({{$s->id}})" style="display:none;"></div>
                            @endif
                        </div>
                    </div>
                </td>
                <td>
                    <div class="sprite-trash-container" ani="1" style="cursor:pointer" wire:click="delete({{$s->id}})">
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
    <div class="selector-field selector-field-on">
    <x-autosave-action save-key="speciality-search" />
    <div style="width:100%;padding:20px;">
        <input type="text"  wire:model.live="query"   class="form-control" placeholder="Buscar especialidad." >
    </div>
    @if(!empty($results))
        <div style="position: absolute; z-index: 1000; width: 100%; background: white; border: 1px solid #ddd; border-radius: 4px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">

            {{-- Header FIJO - NO forma parte del scroll --}}
            <div style="background: #ffffff; padding: 8px 12px; border-bottom: 2px solid #0d6efd;">
                <div class="d-flex justify-content-between align-items-center">
                    <div style="font-size: 0.9rem;">
                        <i class="fas fa-search text-primary"></i>
                        <strong>Especialidades Médicas</strong>
                    </div>
                    <div class="text-muted" style="font-size: 0.85rem;">
                        <i class="fas fa-list"></i>
                        {{ count($results) }} / {{ $totalResults }}
                        @if($hasMoreResults)
                            <span class="badge bg-primary ms-1">+{{ $totalResults - count($results) }}</span>
                        @endif
                    </div>
                </div>

                {{-- BOTÓN CARGAR MÁS EN EL HEADER SI HAY MÁS RESULTADOS --}}
                @if($hasMoreResults)
                    <div class="mt-2">
                        <button
                            type="button"
                            class="btn btn-primary w-100"
                            wire:click="loadMore"
                            wire:loading.attr="disabled"
                        >
                            <span wire:loading.remove wire:target="loadMore">
                                <i class="fas fa-plus-circle"></i>
                                Cargar {{ $totalResults - count($results) }} resultados más
                            </span>
                            <span wire:loading wire:target="loadMore">
                                <span class="spinner-border spinner-border-sm" role="status"></span>
                                Cargando...
                            </span>
                        </button>
                    </div>
                @endif
            </div>

            {{-- Contenedor con scroll - SOLO los resultados - MÁS ALTO para ver varios --}}
            <div style="max-height: 320px; min-height: 300px; overflow-y: auto;">
                {{-- Resultados SIN agrupación - MÁS COMPACTOS --}}
                @foreach($results as $result)
                    <div
                        class="sel-list-item"
                        wire:click.debounce.300ms="selectOption({{ json_encode($result) }})"
                        x-on:click="window.dispatchEvent(new CustomEvent('autosave-start', { detail: 'speciality-search' }))"
                        style="padding: 6px 10px; cursor: pointer; border-bottom: 1px solid #e9ecef; transition: background 0.15s; display: flex; align-items: center; gap: 8px;"
                        onmouseover="this.style.background='#f0f7ff'"
                        onmouseout="this.style.background='white'"
                    >
                        <i class="fas fa-user-md text-primary" style="font-size: 0.9rem;"></i>
                        <span style="font-size: 0.85rem; color: #212529; flex: 1; font-weight: 500;">{{ $result['name'] }}</span>
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

    <!-- Modal de Directorio Médico -->
    @if($showMedicalDirectory)
        <div class="modal-overlay" wire:click="closeMedicalDirectory" style="z-index: 10000;">
            <div class="modal-content" wire:click.stop style="max-width: 1200px; max-height: 90vh; overflow-y: auto;">
                <div class="modal-header">
                    <h2 class="modal-title">Directorio Médico - {{ $currentSpecialityName }}</h2>
                    <button wire:click="closeMedicalDirectory" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
                </div>

                <div style="padding: 20px;">
                    <!-- Buscador -->
                    <div style="margin-bottom: 20px;">
                        <input type="text" wire:model.live.debounce.300ms="searchPractitioner" class="form-control" placeholder="Buscar por nombre o cédula...">
                    </div>

                    <!-- Loading state -->
                    <div wire:loading.delay wire:target="searchPractitioner,loadPractitioners" style="text-align: center; padding: 20px;">
                        <div style="display: inline-flex; align-items: center; gap: 10px;">
                            <div class="spinner-border spinner-border-sm" role="status"></div>
                            <span>Cargando...</span>
                        </div>
                    </div>

                    <!-- Grid de médicos -->
                    @if(count($practitioners) > 0)
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px;">
                            @foreach($practitioners as $practitioner)
                                <div style="background: white; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1); transition: box-shadow 0.2s;">
                                    <!-- Header de la tarjeta -->
                                    <div style="padding: 20px;">
                                        <div style="display: flex; align-items: start; gap: 15px;">
                                            <!-- Avatar -->
                                            <div>
                                                @if($practitioner->avatar())
                                                    <img width="60" height="60" src="{{url('storage/'.$practitioner->avatar()->path)}}" style="border-radius: 50%; object-fit: cover;" alt="">
                                                @else
                                                    <img width="60" height="60" src="{{url('assets/img/profiles/avatar-02.jpg')}}" style="border-radius: 50%; object-fit: cover;" alt="">
                                                @endif
                                            </div>

                                            <!-- Información -->
                                            <div style="flex: 1; min-width: 0;">
                                                <h3 style="margin: 0 0 5px 0; font-size: 16px; font-weight: 600; color: #333;">
                                                    {{ $practitioner->name }}
                                                </h3>
                                                <p style="margin: 0 0 8px 0; font-size: 13px; color: #666;">
                                                    ID: {{ $practitioner->identifier }}
                                                </p>
                                                <span style="display: inline-flex; align-items: center; padding: 4px 10px; background: #d4edda; color: #155724; border-radius: 12px; font-size: 11px; font-weight: 500;">
                                                    <span style="width: 6px; height: 6px; background: #28a745; border-radius: 50%; margin-right: 5px;"></span>
                                                    Activo
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Información de contacto -->
                                    @if($practitioner->phone || $practitioner->email)
                                        <div style="padding: 0 20px 15px 20px; border-top: 1px solid #f0f0f0;">
                                            <div style="margin-top: 15px;">
                                                @if($practitioner->phone)
                                                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px; font-size: 13px; color: #666;">
                                                        <i class="fa fa-phone" style="width: 16px;"></i>
                                                        <span>{{ $practitioner->phone }}</span>
                                                    </div>
                                                @endif
                                                @if($practitioner->email)
                                                    <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #666;">
                                                        <i class="fa fa-envelope" style="width: 16px;"></i>
                                                        <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $practitioner->email }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Horario Laboral -->
                                    @if($practitioner->user && $practitioner->user->workingHours->count() > 0)
                                        <div style="padding: 15px 20px; border-top: 1px solid #f0f0f0;">
                                            <h4 style="margin: 0 0 10px 0; font-size: 13px; font-weight: 600; color: #333;">Horario Laboral</h4>
                                            <div style="display: flex; flex-direction: column; gap: 6px;">
                                                @foreach($practitioner->user->workingHours()->limit(3)->get() as $wh)
                                                    <div style="font-size: 12px; color: #666;">
                                                        <strong>{{ $wh->day_of_week }}:</strong> {{ substr($wh->start_time,0,5) }} - {{ substr($wh->end_time,0,5) }}
                                                    </div>
                                                @endforeach
                                                @if($practitioner->user->workingHours->count() > 3)
                                                    <span style="font-size: 11px; color: #999;">+{{ $practitioner->user->workingHours->count() - 3 }} días más</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Especialidades -->
                                    @if($practitioner->qualifications->count() > 0)
                                        <div style="padding: 15px 20px; border-top: 1px solid #f0f0f0;">
                                            <h4 style="margin: 0 0 10px 0; font-size: 13px; font-weight: 600; color: #333;">Especialidades</h4>
                                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                                @foreach($practitioner->qualifications->take(2) as $qualification)
                                                    <div>
                                                        <p style="margin: 0; font-size: 12px; font-weight: 500; color: #333;">
                                                            {{ $qualification->medicalSpeciality->name }}
                                                        </p>
                                                        @if($qualification->issuer_name)
                                                            <p style="margin: 2px 0 0 0; font-size: 11px; color: #999;">
                                                                {{ $qualification->issuer_name }}
                                                            </p>
                                                        @endif
                                                    </div>
                                                @endforeach
                                                @if($practitioner->qualifications->count() > 2)
                                                    <span style="font-size: 11px; color: #999;">+{{ $practitioner->qualifications->count() - 2 }} especialidades más</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Botón de selección -->
                                    <div style="padding: 15px 20px; background: #f8f9fa; border-top: 1px solid #e0e0e0;">
                                        <button wire:click="selectPractitioner({{ $practitioner->id }})" class="btn btn-primary" style="width: 100%;">
                                            <i class="fa fa-check"></i> Seleccionar Médico
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div style="text-align: center; padding: 40px 20px;">
                            <i class="fa fa-user-md" style="font-size: 48px; color: #ccc; margin-bottom: 15px;"></i>
                            <h3 style="margin: 0 0 10px 0; font-size: 16px; color: #666;">No se encontraron profesionales</h3>
                            <p style="margin: 0; font-size: 14px; color: #999;">
                                @if($searchPractitioner)
                                    Intenta ajustar los filtros de búsqueda.
                                @else
                                    No hay profesionales con esta especialidad.
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
