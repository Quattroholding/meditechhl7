<div class="daily-view" style="display: grid; grid-template-columns: 100px 1fr; gap: 1px; background: #e9ecef; border-radius: 10px; overflow: hidden; max-height: 600px; overflow-y: auto;">
    @foreach($calendarData['timeSlots'] as $slot)
        @php
            $slotAppointments = collect($this->getAppointmentsByTimeSlot($calendarData['appointments'], $slot['hour'], $slot['minutes']));
            $blockHeight = $timeBlockMinutes === 15 ? 50 : ($timeBlockMinutes === 30 ? 80 : 100);
        @endphp

            <!-- Columna de tiempo -->
        <div class="day-header" style="font-size: {{ $slot['isHourStart'] ? '16px' : '14px' }}; opacity: {{ $slot['isHourStart'] ? '1' : '0.8' }}; background: {{ $slot['isHourStart'] ? 'rgb(46, 55, 164)' : '#8a94ea' }}; min-height: {{ $blockHeight }}px; display: flex; align-items: center; justify-content: center;">
            @if($slot['isHourStart'])
                {{ $slot['display'] }}
            @else
                <div style="font-size: 12px;">{{ $slot['display'] }}</div>
            @endif
        </div>

        <!-- Celda del día -->
        <div class="day-cell"
             style="min-height: {{ $blockHeight }}px; position: relative;"
             wire:click="openModal('{{ $calendarData['date']->format('Y-m-d') }}', '{{ $slot['time'] }}')">

            @foreach($slotAppointments as $appointment)
                @php
                    $position = $this->calculateAppointmentPosition($appointment, $blockHeight);
                @endphp

                <div class="appointment appointment-{{ $appointment['status'] }}"
                     style="top: {{ $position['top'] }}px; height: {{ $position['height'] }}px; left: 2px; right: 2px; position: absolute; font-size: 13px; padding: 8px;"
                     wire:click.stop="editAppointment({{ $appointment['id'] }})">

                    <div style="font-weight: bold; margin-bottom: 4px;">
                        {{ date('H:i', strtotime($appointment['start'])) }} -   {{ date('H:i', strtotime($appointment['end'])) }}
                    </div>
                    <div style="margin-bottom: 2px; font-size: 12px;">
                        {{ $appointment['patient']['name'] }}
                    </div>
                    <div style="margin-bottom: 2px; font-size: 12px;">
                        {{ $appointment['practitioner']['name'] }} - {{ $appointment['medical_speciality']['name'] }}
                    </div>
                    @if($position['height'] > 60)
                        <div style="font-size: 11px; opacity: 0.9; margin-bottom: 2px;">{{ $appointment['description'] }}</div>
                        {{--}}
                        <div style="font-size: 10px; opacity: 0.8;">Tel: {{ $appointment['patient']['phone'] }}</div>
                        {{--}}
                    @endif

                    <!-- Botones de cambio rápido de estado -->
                    @if($position['height'] > 80)
                        <div style="margin-top: 8px; display: flex; gap: 4px; flex-wrap: wrap;">
                            @if($appointment['status'] !== 'booked')
                                <button wire:click.stop="updateStatus({{ $appointment['id'] }}, 'confirmed')"
                                        style="padding: 2px 6px; font-size: 8px; background: #007bff; color: white; border: none; border-radius: 3px; cursor: pointer;">
                                    Confirmar
                                </button>
                            @endif
                            @if($appointment['status'] !== 'checked-in')
                                <button wire:click.stop="updateStatus({{ $appointment['id'] }}, 'in-progress')"
                                        style="padding: 2px 6px; font-size: 8px; background: #ffc107; color: #333; border: none; border-radius: 3px; cursor: pointer;">
                                    En Progreso
                                </button>
                            @endif
                            @if($appointment['status'] !== 'fulfilled')
                                <button wire:click.stop="updateStatus({{ $appointment['id'] }}, 'completed')"
                                        style="padding: 2px 6px; font-size: 8px; background: #28a745; color: white; border: none; border-radius: 3px; cursor: pointer;">
                                    Completar
                                </button>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endforeach
</div>

