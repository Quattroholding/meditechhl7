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
<!-- Jitsi Meeting Details (if virtual) -->
@if($encounter->appointment->consultation_type === 'virtual' && $encounter->getRawOriginal('status') <> 'finished')
@php
    $token = hash_hmac('sha256', $encounter->appointment->id . $encounter->appointment->patient_id, config('app.key'));
    $patientJoinUrl = route('virtual-consultation.join', [
        'appointment' => $encounter->appointment->id,
        'token' => $token,
    ]);
@endphp
<div class="patient-banner-new">
    <div class="banner-block-new">
        <h3>
            <i class="fas fa-video"></i>
            Detalles Cita Virtual
        </h3>
        <div class="data-row-new">
            <span class="data-label-new">
                <i class="fas fa-list-numeric"></i>
                Sala:
            </span>
            <span class="data-value-new">{{ $encounter->appointment->virtual_room_id }}</span>
        </div>
        <div class="data-row-new">
            <span class="data-label-new">
                <i class="fas fa-link"></i>
                Enlace Paciente:
            </span>
            <span class="data-value-new"><a href="{{ $patientJoinUrl }}" target="_blank" rel="noopener noreferrer" style="color: #667eea; text-decoration: none;">{{ $patientJoinUrl }}</a></span>
        </div>
        {{--}}
        <div class="data-row-new">
            <span class="data-label-new">
                <i class="fas fa-graduation-cap"></i>
                Acción:
            </span>
            <span class="data-value-new">
                <a href="{{ $patientJoinUrl }}" target="_blank" rel="noopener noreferrer"
                   style="display: inline-flex; align-items: center; gap: 4px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; text-decoration: none; transition: all 0.3s ease; cursor: pointer; white-space: nowrap; flex-shrink: 0;"
                   onmouseover="this.style.transform='scale(1.05)';"
                   onmouseout="this.style.transform='scale(1)';">
                    <i class="fas fa-external-link-alt"></i> Abrir Jitsi
                </a>
            </span>
        </div>
        {{--}}
    </div>
</div>
@endif
<!-- Timer de consulta (si aplica) -->
{{-- @livewire('consultation.consultation-timer', ['encounter' => $encounter, 'appointment' => $appointment]) --}}
