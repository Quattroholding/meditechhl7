<div style="display: grid; grid-template-columns: 100px repeat(7, 1fr); gap: 1px; background: #e9ecef; border-radius: 10px; overflow: hidden; max-height: 600px; overflow-y: auto;">
    <!-- Encabezado vacío -->
    <div class="day-header"></div>

    <!-- Encabezados de días -->
    @foreach($calendarData as $day)
        <div class="day-header {{ $day['isToday'] ? 'today' : '' }}">
            <div>{{ ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'][$loop->index] }}</div>
            <div style="font-size: 14px; margin-top: 5px;">{{ $day['date']->day }}</div>
        </div>
    @endforeach

    <!-- Bloques de tiempo y citas -->
    @foreach($calendarData[0]['timeSlots'] as $slot)
        <!-- Columna de tiempo -->
        <div class="day-header" style="font-size: {{ $slot['isHourStart'] ? '14px' : '12px' }}; opacity: {{ $slot['isHourStart'] ? '1' : '0.7' }}; background: {{ $slot['isHourStart'] ? 'rgb(46, 55, 164)' : '#8a94ea' }};">
            @if($slot['isHourStart'])
                {{ $slot['display'] }}
            @else
                <div style="font-size: 10px;">{{ $slot['display'] }}</div>
            @endif
        </div>

        <!-- Celdas para cada día de la semana -->
        @foreach($calendarData as $day)
            @php
                $slotAppointments = collect($this->getAppointmentsByTimeSlot($day['appointments'], $slot['hour'], $slot['minutes']));
                $blockHeight = $timeBlockMinutes === 15 ? 40 : ($timeBlockMinutes === 30 ? 60 : 80);
            @endphp

            <div class="day-cell"
                 style="min-height: {{ $blockHeight }}px; position: relative; "
                 wire:click="openModal('{{ $day['date']->format('Y-m-d') }}', '{{ $slot['time'] }}')">

                @foreach($slotAppointments as $appointment)
                    @php
                        $position = $this->calculateAppointmentPosition($appointment, $blockHeight);
                    @endphp

                    <div class="appointment appointment-{{ $appointment['status'] }}"
                         style="top: {{ $position['top'] }}px; height: {{ $position['height'] }}px; left: 2px; right: 2px; position: absolute;"
                         wire:click.stop="editAppointment({{ $appointment['id'] }})">
                        <div style="font-weight: 600; font-size: 11px;">
                            {{ date('H:i', strtotime($appointment['start'])) }} - {{ date('H:i', strtotime($appointment['end'])) }}
                        </div>
                        <div style="font-size: 10px;">{{ $appointment['patient']['name'] }}</div>
                        <div style="font-size: 9px; opacity: 0.9;">{{ $appointment['practitioner']['name'] }}</div>
                        @if($position['height'] > 40)
                            <div style="font-size: 8px; opacity: 0.7; margin-top: 2px;">{{ Str::limit($appointment['description'], 20) }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endforeach
    @endforeach
</div>
