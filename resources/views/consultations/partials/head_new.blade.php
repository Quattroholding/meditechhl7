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
                    {{ __('Cita Virtual') }}
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

    <!-- Equipo Médico -->
    <div class="banner-block-new">
        <h3>
            <i class="fas fa-user-md"></i>
            Equipo Médico
        </h3>
        <div class="data-row-new">
            <span class="data-label-new">
                <i class="fas fa-stethoscope"></i>
                Doctor:
            </span>
            <span class="data-value-new">{!! $encounter->practitioner->profile_name !!}</span>
        </div>
        @if($appointment->practitioner->qualifications()->first())
        <div class="data-row-new">
            <span class="data-label-new">
                <i class="fas fa-graduation-cap"></i>
                Especialidad:
            </span>
            <span class="data-value-new">{{ $appointment->medicalSpeciality->name }}</span>
        </div>
        @endif
    </div>
</div>

<!-- Timer de consulta (si aplica) -->
@livewire('consultation.consultation-timer', ['encounter' => $encounter, 'appointment' => $appointment])
