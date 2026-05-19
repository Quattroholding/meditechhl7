@extends('emails.layouts.base', [
    'title' => 'Cita Reprogramada',
    'headerIcon' => '🔄',
    'headerTitle' => 'Cita Reprogramada',
    'headerSubtitle' => 'Una de sus citas médicas ha sido reprogramada',
    'headerColor' => '#ff9800'
])

@section('content')
    {{-- Mensaje de saludo --}}
    <x-email.message-box title="Estimado {{ $practitionerName }}">
        <p style="font-size: 16px; margin: 0;">
            Le informamos que una de sus citas médicas ha sido <strong>reprogramada</strong>.
            A continuación encontrará los detalles del cambio.
        </p>
    </x-email.message-box>

    {{-- Información del paciente --}}
    <x-email.message-box type="info" title="👤 Información del Paciente">
        <x-email.info-grid :items="[
            'Paciente' => $patientName,
            'Tipo de servicio' => $serviceType,
            'Especialidad' => $specialty ?? 'Medicina General'
        ]" />
    </x-email.message-box>

    {{-- Horario anterior --}}
    <x-email.message-box type="danger" title="❌ Horario Anterior">
        <x-email.info-grid :items="[
            'Fecha anterior' => $originalDate,
            'Hora anterior' => $originalTime,
        ]" />
    </x-email.message-box>

    {{-- Nuevo horario --}}
    <x-email.message-box type="success" title="✅ Nuevo Horario">
        <x-email.info-grid :items="[
            'Nueva fecha' => $newDate,
            'Nueva hora' => $newTime,
            'Duración' => $durationMinutes . ' minutos',
        ]" />
    </x-email.message-box>

    {{-- Detalles de la ubicación --}}
    <x-email.message-box type="highlight" title="🏥 Detalles de la Ubicación">
        <x-email.info-grid :items="[
            'Sucursal' => $branchName,
            'Consultorio' => $consultingRoom,
            'Centro médico' => $clinicName
        ]" />
    </x-email.message-box>

    {{-- Información del cambio --}}
    @if($reason || $changedBy)
        <x-email.message-box type="warning" title="ℹ️ Información del Cambio">
            @if($changedBy)
                <div style="margin-bottom: {{ $reason ? '15px' : '0' }};">
                    <p style="margin: 0; color: #856404;">
                        <strong>👤 Modificado por:</strong> {{ $changedBy }}
                    </p>
                </div>
            @endif

            @if($reason)
                <div>
                    <p style="margin: 0; color: #856404;">
                        <strong>📝 Motivo del cambio:</strong> {{ $reason }}
                    </p>
                </div>
            @endif
        </x-email.message-box>
    @endif

    {{-- Botón de acción --}}
    @if($appointmentUrl ?? false)
        <x-email.button href="{{ $appointmentUrl }}" type="primary" icon="📅">
            Ver en Calendario
        </x-email.button>
    @endif

    {{-- Recordatorio importante --}}
    <x-email.message-box type="info" title="⏰ Recordatorio Importante">
        <ul style="color: #1565c0; line-height: 1.8; margin: 10px 0; padding-left: 20px;">
            <li style="margin-bottom: 8px;">El paciente ha sido notificado sobre el cambio de horario</li>
            <li style="margin-bottom: 8px;">Recibirá un recordatorio automático antes de la cita</li>
            <li style="margin-bottom: 8px;">Puede gestionar todas sus citas desde el calendario del sistema</li>
        </ul>
    </x-email.message-box>

    {{-- Mensaje de cierre --}}
    <div style="text-align: center; margin: 30px 0;">
        <p style="color: #6c757d; font-size: 16px; font-style: italic;">
            "Gracias por su dedicación y compromiso con nuestros pacientes"
        </p>
        <p style="color: #ff9800; font-weight: bold; font-size: 18px;">
            Sistema de Gestión Médica - {{ $clinicName }}
        </p>
    </div>
@endsection
