<div>
    <!-- Dragula JS -->
    <script src="{{ url('assets/plugins/dragula/js/dragula.min.js') }}"></script>
    <script src="{{ url('assets/plugins/dragula/js/drag-drop.min.js') }}"></script>
    {{-- Nothing in the world is as soft and yielding as water. --}}
    <div class="row" x-data>
        <!-- Tabla de disponibles -->
        <div class="col-xl-5" style="border: 3px solid #2E37A4;padding: 20px;">
            <h2 class="text-center">{{__('Secciones Disponibles')}}</h2>
            <ul class="list-group" id="basic-list-group">
                @foreach($availableItems as $item)
                    <li class="list-group-item">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <strong>{{ $item['name_esp'] }}</strong>
                                @if($item['category'])
                                    <span class="badge bg-info ms-2">{{ $item['category'] }}</span>
                                @endif
                                @if($item['medical_speciality_id'])
                                    <span class="badge bg-warning ms-2">
                                        <i class="fas fa-user-md"></i> Especialidad
                                    </span>
                                @endif
                            </div>
                            <button wire:click="moveToSelected({{ $item['id'] }})"
                                    type="button"
                                    class="btn btn-sm btn-primary"
                                    title="Agregar a seleccionados">
                                <i class="fa fa-arrow-right"></i>
                            </button>
                        </div>
                        @if(isset($item['livewire_component_fields']) && count($item['livewire_component_fields']) > 0)
                            <small class="text-muted d-block mt-2">
                                <i class="fas fa-info-circle"></i>
                                Campos:
                                @foreach($item['livewire_component_fields'] as $index => $field)
                                    {{ $field['name'] }}@if(!$loop->last), @endif
                                @endforeach
                            </small>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="col-xl-2  text-center d-none d-lg-block"><i class="fa fa-arrow-right fa-4x" ></i><br/><i class="fa fa-arrow-left fa-4x" ></i> </div>
        <div class="col-xl-2  text-center d-block d-md-none"><i class="fa fa-arrow-down fa-4x" style="margin:0.1em;"></i><i class="fa fa-arrow-up fa-4x" style="margin:0.1em;"></i> </div>
        <!-- Tabla de seleccionados -->
        <div class="col-xl-5" style="border: 3px solid #2E37A4;padding: 20px;">
            <h2 class="text-center">{{__('Secciones Seleccionados')}}</h2>
            <ul  class="list-group" id="basic-list-group">
                @foreach($selectedItems as $item)
                    <li class="list-group-item draggable ">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <strong>{{ $item['name_esp'] }}</strong>
                                @if($item['category'])
                                    <span class="badge bg-info ms-2">{{ $item['category'] }}</span>
                                @endif
                                @if($item['medical_speciality_id'])
                                    <span class="badge bg-warning ms-2">
                                        <i class="fas fa-user-md"></i> Especialidad
                                    </span>
                                @endif
                            </div>
                            <button wire:click="moveToAvailable({{ $item['id'] }})"
                                    type="button"
                                    class="btn btn-sm btn-danger"
                                    title="Remover de seleccionados">
                                <i class="fa fa-arrow-left"></i>
                            </button>
                        </div>
                        @if(isset($item['livewire_component_fields']) && count($item['livewire_component_fields']) > 0)
                            <small class="text-muted d-block mt-2">
                                <i class="fas fa-info-circle"></i>
                                Campos:
                                @foreach($item['livewire_component_fields'] as $index => $field)
                                    {{ $field['name'] }}@if(!$loop->last), @endif
                                @endforeach
                            </small>
                        @endif

                        {{-- Configuración específica para Present Illness --}}
                        @if($item['name'] === 'Present Illness')
                            <div class="mt-3 p-2" style="background-color: #f8f9fa; border-radius: 4px;">
                                <label class="form-label mb-2">
                                    <i class="fas fa-cog"></i> <strong>Modo de visualización:</strong>
                                </label>
                                <select
                                    wire:change="updatePresentIllnessMode({{ $item['id'] }}, $event.target.value)"
                                    class="form-select form-select-sm">
                                    <option value="full"
                                        @if(isset($sectionConfigs[$item['id']]['present_illness_mode']) && $sectionConfigs[$item['id']]['present_illness_mode'] === 'full') selected
                                        @elseif(!isset($sectionConfigs[$item['id']]['present_illness_mode'])) selected
                                        @endif>
                                        Completo (Ubicación, Gravedad, Duración, etc.)
                                    </option>
                                    <option value="simplified"
                                        @if(isset($sectionConfigs[$item['id']]['present_illness_mode']) && $sectionConfigs[$item['id']]['present_illness_mode'] === 'simplified') selected @endif>
                                        Simplificado (Solo descripción)
                                    </option>
                                </select>
                                <small class="text-muted d-block mt-1">
                                    <i class="fas fa-info-circle"></i>
                                    Elige cómo deseas ver la sección de Enfermedad Actual
                                </small>
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>


    </div>
</div>
