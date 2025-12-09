<div>
    {{-- Knowing others is intelligence; knowing yourself is true wisdom. --}}
    @include('partials.message')
    <form wire:submit.prevent="save" class="space-y-4">
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Configure el horario laboral para cada día de la semana. Puede seleccionar diferentes sucursales y consultorios para cada día.
        </div>

        {{-- Resumen visual de horarios configurados --}}
        @php
            $hasConfiguredDays = collect($workingHours)->filter(fn($config) => $config['enabled'])->isNotEmpty();
        @endphp

        @if($hasConfiguredDays)
        <div class="card bg-light mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-calendar-check"></i> Resumen de Horarios</h5>
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
                            @foreach($workingHours as $day => $config)
                                @if($config['enabled'])
                                    @php
                                        $branchName = $config['branch_id'] ? ($branches[$config['branch_id']] ?? 'No especificada') : 'No especificada';
                                        $roomOptions = $this->getRoomsForDay($day);
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
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        @foreach($workingHours as $day => $config)
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row align-items-center">
                        <!-- Checkbox del día -->
                        <div class="col-12 col-md-2 mb-2">
                            <div class="form-check">
                                <input type="checkbox"
                                       id="enabled-{{ $day }}"
                                       wire:model.live="workingHours.{{$day}}.enabled"
                                       wire:click="changeEnabled('{{$day}}')"
                                       class="form-check-input">
                                <label class="form-check-label fw-bold" for="enabled-{{ $day }}">
                                    {{ucfirst($day)}}
                                </label>
                            </div>
                        </div>

                        @if($config['enabled'])
                            <!-- Sucursal -->
                            <div class="col-12 col-md-3 mb-2">
                                <div class="input-block local-forms">
                                    <x-input-label for="branch_{{$day}}" :value="__('Sucursal')" />
                                    <x-select-input
                                        name="branch_{{$day}}"
                                        :options="$branches"
                                        class="block w-full"
                                        wire:model.live="workingHours.{{$day}}.branch_id"/>
                                    <x-input-error :messages="$errors->get('workingHours.'.$day.'.branch_id')" class="mt-2" />
                                </div>
                            </div>

                            <!-- Consultorio -->
                            <div class="col-12 col-md-3 mb-2">
                                <div class="input-block local-forms">
                                    <x-input-label for="consulting_room_{{$day}}" :value="__('Consultorio')" />
                                    <x-select-input
                                        name="consulting_room_{{$day}}"
                                        :options="$this->getRoomsForDay($day)"
                                        class="block w-full"
                                        wire:model.live="workingHours.{{$day}}.consulting_room_id"/>
                                    <x-input-error :messages="$errors->get('workingHours.'.$day.'.consulting_room_id')" class="mt-2" />
                                </div>
                            </div>

                            <!-- Hora Entrada -->
                            <div class="col-12 col-md-2 mb-2">
                                <div class="input-block local-forms">
                                    <x-input-label for="start_{{$day}}" :value="__('Hora Entrada')" />
                                    <input id="start-{{ $day }}"
                                           type="time"
                                           wire:model.live="workingHours.{{$day}}.start"
                                           class="form-control p-2">
                                    <x-input-error :messages="$errors->get('workingHours.'.$day.'.start')" class="mt-2" />
                                </div>
                            </div>

                            <!-- Hora Salida -->
                            <div class="col-12 col-md-2 mb-2">
                                <div class="input-block local-forms">
                                    <x-input-label for="end_{{$day}}" :value="__('Hora Salida')" />
                                    <input type="time"
                                           wire:model.live="workingHours.{{$day}}.end"
                                           class="form-control p-2">
                                    <x-input-error :messages="$errors->get('workingHours.'.$day.'.end')" class="mt-2" />
                                </div>
                            </div>
                        @else
                            <div class="col-12 col-md-10 mb-2">
                                <span class="text-muted"><i class="fas fa-times-circle"></i> Día no habilitado</span>
                            </div>
                        @endif
                    </div>
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
