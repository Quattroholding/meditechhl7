<!-- Consultation Header New - SAMI Inspired -->
<div class="patient-banner-new">
    <!-- Detalles de Atención -->
    <div class="banner-block-new">
        <h3>
            <i class="fas fa-file-medical"></i>
            Detalles de Atención
        </h3>
        <div class="data-row-new">
            <span class="data-label-new">
                <i class="fas fa-hashtag"></i>
                Consulta:
            </span>
            <span class="data-value-new">#{{ $encounter->id }}</span>
        </div>
        <div class="data-row-new">
            <span class="data-label-new">
                <i class="fas fa-calendar-alt"></i>
                Fecha:
            </span>
            <span class="data-value-new">{{ $encounter->created_at }}</span>
        </div>
        <div class="data-row-new">
            <span class="data-label-new">
                <i class="fas fa-building"></i>
                Consultorio:
            </span>
            <span class="data-value-new">
                @if($encounter->appointment->consultation_type=='presencial')
                {{ $encounter->appointment->consultingRoom->name }}
                @else
                    <span style="display: inline-flex; align-items: center; gap: 6px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                        🎥 {{ __('Cita Virtual') }}
                    </span>
                @endif
            </span>
        </div>
        <div class="data-row-new">
            <span class="data-label-new">
                <i class="fas fa-concierge-bell"></i>
                Servicio:
            </span>
            <span class="data-value-new">{{ $encounter->appointment->service_type }}</span>
        </div>


    </div>


    <!-- Datos del Paciente -->
    <div class="banner-block-new">
        <h3>
            <i class="fas fa-user"></i>
            Datos del Paciente
        </h3>
        <div class="data-row-new">
            <span class="data-label-new">
                <i class="fas fa-user-circle"></i>
                Paciente:
            </span>
            <span class="data-value-new">{!! $patient->profile_name !!}</span>
        </div>
        <div class="data-row-new">
            <span class="data-label-new">
                <i class="fas fa-birthday-cake"></i>
                Edad:
            </span>
            <span class="data-value-new">{{ \Carbon\Carbon::parse($patient->birth_date)->age }} años</span>
        </div>
        <div class="data-row-new">
            <span class="data-label-new">
                <i class="fas fa-venus-mars"></i>
                Género:
            </span>
            <span class="data-value-new">
                @if($patient->gender === 'male')
                    Masculino
                @elseif($patient->gender === 'female')
                    Femenino
                @else
                    {{ ucfirst($patient->gender) }}
                @endif
            </span>
        </div>
        <div class="data-row-new">
            <span class="data-label-new">
                <i class="fas fa-id-card"></i>
                {{ $patient->identifier_type }}:
            </span>
            <span class="data-value-new">{{ $patient->identifier }}</span>
        </div>
    </div>


</div>

<!-- Zoom Meeting Details (if virtual) -->
@if($encounter->appointment->consultation_type === 'virtual' && $encounter->appointment->virtual_room_id)
@php
        $password = $encounter->appointment->virtual_session_metadata['meeting_password'] ?? null;
        $zoomUrl = 'https://zoom.us/j/' . $encounter->appointment->virtual_room_id;
        if ($password) {
            $encodedPassword = urlencode(base64_encode($password));
            $zoomUrl .= '?pwd=' . $encodedPassword;
        }
    @endphp
<div class="patient-banner-new">
    <!-- Equipo Médico -->
    <div class="banner-block-new">
        <h3>
            <i class="fas fa-video"></i>
            Detalles video consulta
        </h3>
        <div class="data-row-new">
            <span class="data-label-new">
                <i class="fas fa-list-numeric"></i>
                Meeting Id:
            </span>
            <span class="data-value-new">{{ $encounter->appointment->virtual_room_id }}</span>
        </div>
        <div class="data-row-new">
            <span class="data-label-new">
                <i class="fas fa-link"></i>
                Link zoom:
            </span>
                <span class="data-value-new">{{ $zoomUrl }}</span>
        </div>

        <div class="data-row-new">
            <span class="data-label-new">
                <i class="fas fa-graduation-cap"></i>
                Acción:
            </span>
            <span class="data-value-new">
                 <a href="{{ $zoomUrl }}" target="_blank" rel="noopener noreferrer"
                    style="display: inline-flex; align-items: center; gap: 4px; background: #0e5aa8; color: white; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; text-decoration: none; transition: all 0.3s ease; cursor: pointer; white-space: nowrap; flex-shrink: 0;"
                    onmouseover="this.style.background='#0d4a8a'; this.style.transform='scale(1.05)';"
                    onmouseout="this.style.background='#0e5aa8'; this.style.transform='scale(1)';">
                    <i class="fas fa-external-link-alt"></i> Abrir Zoom
                </a>
            </span>
        </div>
        @if($password)
        <div class="data-row-new">
            <span class="data-label-new">
                <i class="fas fa-graduation-cap"></i>
                Código Reunión:
            </span>
            <span class="data-value-new">{{ $password }}</span>
        </div>
        @endif
    </div>

</div>
@endif
<!-- Timer de consulta (si aplica) -->
@livewire('consultation.consultation-timer', ['encounter' => $encounter, 'appointment' => $appointment])
