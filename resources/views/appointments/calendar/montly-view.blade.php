<div class="monthly-grid">
    <!-- Encabezados de días -->
    <div class="day-header">Lun</div>
    <div class="day-header">Mar</div>
    <div class="day-header">Mié</div>
    <div class="day-header">Jue</div>
    <div class="day-header">Vie</div>
    <div class="day-header">Sáb</div>
    <div class="day-header">Dom</div>

    <!-- Días del mes -->
    @foreach($calendarData as $week)
        @foreach($week as $day)

            <div class="day-cell {{ !$day['isCurrentMonth'] ? 'other-month' : '' }} {{ $day['isToday'] ? 'today' : '' }}"
                 wire:click="openModal('{{ $day['date']->format('Y-m-d') }}')">
                <div class="day-number">{{ $day['date']->day }}</div>

                @foreach($day['appointments'] as $index => $appointment)
                    @if($index < 3)
                        <div class="appointment appointment-{{ $appointment['status'] }}" style="top: {{ 20 + ($index * 16) }}px;"
                             wire:click.stop="editAppointment({{ $appointment['id'] }})">
                            <div style="font-weight: 600; font-size: 10px;">{{ date('H:i', strtotime($appointment['start'])) }} - {{ date('H:i', strtotime($appointment['end'])) }}</div>
                            <div style="font-size: 9px;">{{ $appointment['patient']['name'] }}</div>
                            <div style="font-size: 8px; opacity: 0.8;">{{ $appointment['practitioner']['name'] }}</div>
                        </div>
                    @endif
                @endforeach

                @if(count($day['appointments']) > 3)
                    <div style="position: absolute; bottom: 2px; right: 4px; font-size: 10px; color: #666;">
                        +{{ count($day['appointments']) - 3 }} más
                    </div>
                @endif
            </div>
        @endforeach
    @endforeach
</div>
