<div>
    @include('partials.message')
    <form wire:submit.prevent="save" class="space-y-4">
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Configure el horario laboral para cada día de la semana. Puede agregar múltiples horarios por día para trabajar en diferentes sucursales.
        </div>

        {{-- Resumen visual de horarios configurados --}}
        @php
            $hasConfiguredSchedules = collect($workingHours)->flatten(1)->filter(fn($config) => $config['enabled'])->isNotEmpty();
        @endphp

        @if($hasConfiguredSchedules)
        <div class="card bg-light mb-4">
            <div class="card-header">
                <h5 class="mb-0" style="color:#fff;"><i class="fas fa-calendar-check"></i> Resumen de Horarios</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="table-primary">
                            <tr>
                                <th>Día</th>
                                <th>Sucursal</th>
                                <th>Consultorio</th>
                                <th>Horario</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($workingHours as $day => $schedules)
                                @foreach($schedules as $index => $config)
                                    @if($config['enabled'])
                                        @php
                                            $branchName = $config['branch_id'] ? ($branches[$config['branch_id']] ?? 'No especificada') : 'No especificada';
                                            $roomOptions = $this->getRoomsForSchedule($day, $index);
                                            $roomName = $config['consulting_room_id'] && isset($roomOptions[$config['consulting_room_id']])
                                                ? $roomOptions[$config['consulting_room_id']]
                                                : 'No especificado';
                                        @endphp
                                        <tr>
                                            <td class="fw-bold">{{ ucfirst($day) }}</td>
                                            <td>
                                                @if($config['branch_id'])
                                                    <span class="badge bg-primary">{{ $branchName }}</span>
                                                @else
                                                    <span class="text-warning"><i class="fas fa-exclamation-triangle"></i> Sin sucursal</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($config['consulting_room_id'])
                                                    <span class="badge bg-info">{{ $roomName }}</span>
                                                @else
                                                    <span class="text-warning"><i class="fas fa-exclamation-triangle"></i> Sin consultorio</span>
                                                @endif
                                            </td>
                                            <td>
                                                <i class="fas fa-clock"></i>
                                                {{ substr($config['start'] ?? '00:00', 0, 5) }} - {{ substr($config['end'] ?? '00:00', 0, 5) }}
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        @foreach($workingHours as $day => $schedules)
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center bg-light">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-calendar-day me-2"></i>{{ ucfirst($day) }}
                        @if($this->hasEnabledSchedule($day))
                            <span class="badge bg-success ms-2">Configurado</span>
                        @endif
                    </h6>
                    <button type="button" wire:click="addSchedule('{{ $day }}')" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-plus"></i> Agregar Horario
                    </button>
                </div>
                <div class="card-body">
                    @foreach($schedules as $index => $config)
                        <div class="@if(!$loop->first) border-top pt-3 mt-3 @endif">
                            <div class="row align-items-center">
                                <!-- Checkbox del horario -->
                                <div class="col-12 col-md-2 mb-2">
                                    <div class="form-check">
                                        <input type="checkbox"
                                               id="enabled-{{ $day }}-{{ $index }}"
                                               wire:model.live="workingHours.{{ $day }}.{{ $index }}.enabled"
                                               class="form-check-input">
                                        <label class="form-check-label" for="enabled-{{ $day }}-{{ $index }}">
                                            @if(count($schedules) > 1)
                                                Turno {{ $index + 1 }}
                                            @else
                                                Habilitar
                                            @endif
                                        </label>
                                    </div>
                                </div>

                                @if($config['enabled'])
                                    <!-- Sucursal -->
                                    <div class="col-12 col-md-2 mb-2">
                                        <div class="input-block local-forms">
                                            <x-input-label for="branch_{{ $day }}_{{ $index }}" :value="__('Sucursal')" />
                                            <x-select-input
                                                name="branch_{{ $day }}_{{ $index }}"
                                                :options="$branches"
                                                class="block w-full"
                                                wire:model.live="workingHours.{{ $day }}.{{ $index }}.branch_id"/>
                                            <x-input-error :messages="$errors->get('workingHours.'.$day.'.'.$index.'.branch_id')" class="mt-2" />
                                        </div>
                                    </div>

                                    <!-- Consultorio -->
                                    <div class="col-12 col-md-2 mb-2">
                                        <div class="input-block local-forms">
                                            <x-input-label for="consulting_room_{{ $day }}_{{ $index }}" :value="__('Consultorio')" />
                                            <x-select-input
                                                name="consulting_room_{{ $day }}_{{ $index }}"
                                                :options="$this->getRoomsForSchedule($day, $index)"
                                                class="block w-full"
                                                wire:model.live="workingHours.{{ $day }}.{{ $index }}.consulting_room_id"/>
                                            <x-input-error :messages="$errors->get('workingHours.'.$day.'.'.$index.'.consulting_room_id')" class="mt-2" />
                                        </div>
                                    </div>

                                    <!-- Hora Entrada -->
                                    <div class="col-12 col-md-2 mb-2">
                                        <div class="input-block local-forms">
                                            <x-input-label for="start_{{ $day }}_{{ $index }}" :value="__('Hora Entrada')" />
                                            <input id="start-{{ $day }}-{{ $index }}"
                                                   type="time"
                                                   wire:model.live="workingHours.{{ $day }}.{{ $index }}.start"
                                                   class="form-control p-2">
                                            <x-input-error :messages="$errors->get('workingHours.'.$day.'.'.$index.'.start')" class="mt-2" />
                                        </div>
                                    </div>

                                    <!-- Hora Salida -->
                                    <div class="col-12 col-md-2 mb-2">
                                        <div class="input-block local-forms">
                                            <x-input-label for="end_{{ $day }}_{{ $index }}" :value="__('Hora Salida')" />
                                            <input type="time"
                                                   wire:model.live="workingHours.{{ $day }}.{{ $index }}.end"
                                                   class="form-control p-2">
                                            <x-input-error :messages="$errors->get('workingHours.'.$day.'.'.$index.'.end')" class="mt-2" />
                                        </div>
                                    </div>

                                    <!-- Botón eliminar -->
                                    <div class="col-12 col-md-2 mb-2">
                                        @if(count($schedules) > 1)
                                            <button type="button"
                                                    wire:click="removeSchedule('{{ $day }}', {{ $index }})"
                                                    class="btn btn-sm btn-outline-danger mt-4"
                                                    title="Eliminar este horario">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                @else
                                    <div class="col-12 col-md-8 mb-2">
                                        <span class="text-muted"><i class="fas fa-times-circle"></i> Horario no habilitado</span>
                                    </div>
                                    <div class="col-12 col-md-2 mb-2">
                                        @if(count($schedules) > 1)
                                            <button type="button"
                                                    wire:click="removeSchedule('{{ $day }}', {{ $index }})"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Eliminar este horario">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <div class="flex items-center justify-end mt-4">
            <div class="doctor-submit text-end">
                <button type="submit" class="btn btn-primary submit-form me-2">
                    <i class="fas fa-save"></i> {{ __('button.register') }}
                </button>
            </div>
        </div>
    </form>
</div>
