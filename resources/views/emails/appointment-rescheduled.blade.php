@extends('emails.layouts.base', [
    'title' => 'Cambio de Horario en su Cita',
    'headerIcon' => '🔄',
    'headerTitle' => 'Cita Reprogramada',
    'headerSubtitle' => 'Su cita médica ha sido reprogramada',
    'headerColor' => '#ff9800'
])

@section('content')
    {{-- Mensaje de saludo --}}
    <x-email.message-box title="Estimado/a {{ $patientName }}">
        <p style="font-size: 16px; margin: 0;">
            Le informamos que su cita médica ha sido <strong>reprogramada</strong>.
            A continuación encontrará los detalles del cambio.
        </p>
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

    {{-- Detalles de la cita --}}
    <x-email.message-box type="info" title="🏥 Detalles de su Cita">
        <x-email.info-grid :items="[
            'Médico' => 'Dr. ' . $practitionerName,
            'Especialidad' => $specialty ?? 'Medicina General',
            'Centro médico' => $clinicName
        ]" />

        @if($branchName)
            <div style="margin-top: 10px;">
                <p style="margin: 0; color: #1565c0;">
                    <strong>🏪 Sede:</strong> {{ $branchName }}
                </p>
            </div>
        @endif

        @if($consultingRoom)
            <div style="margin-top: 5px;">
                <p style="margin: 0; color: #1565c0;">
                    <strong>🚪 Consultorio:</strong> {{ $consultingRoom }}
                </p>
            </div>
        @endif

        @if($reason)
            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #90caf9;">
                <p style="margin: 0; color: #1565c0;">
                    <strong>📝 Motivo del cambio:</strong> {{ $reason }}
                </p>
            </div>
        @endif
    </x-email.message-box>

    {{-- Recordatorio --}}
    <x-email.message-box type="info" title="⏰ Recordatorio Automático">
        <p style="margin: 0; color: #1565c0; font-size: 15px;">
            <strong>Recibirá una notificación recordatoria 2 horas antes de su nueva cita.</strong>
        </p>
        <p style="margin: 10px 0 0; color: #1565c0;">
            Le enviaremos un mensaje con toda la información necesaria para que no olvide su cita médica.
        </p>
    </x-email.message-box>

    {{-- Instrucciones importantes --}}
    <x-email.message-box type="info" title="📋 Instrucciones Importantes">
        <ul style="color: #1565c0; line-height: 1.8; margin: 10px 0; padding-left: 20px;">
            <li style="margin-bottom: 8px;"><strong>Llegada:</strong> Llegue 15 minutos antes de su cita</li>
            <li style="margin-bottom: 8px;"><strong>Documentos:</strong> Traiga su documento de identidad</li>
            <li style="margin-bottom: 8px;"><strong>Exámenes:</strong> Traiga sus exámenes médicos previos si los tiene</li>
        </ul>
    </x-email.message-box>

    {{-- Botón de acción --}}
    @if($appointmentUrl ?? false)
        <x-email.button href="{{ $appointmentUrl }}" type="primary" icon="📅">
            Ver Calendario de Citas
        </x-email.button>
    @endif

    {{-- Disculpas --}}
    <x-email.message-box type="warning" title="💬 Disculpe las molestias">
        <p style="margin: 0; color: #856404;">
            Lamentamos cualquier inconveniente que este cambio pueda causarle. Si tiene alguna pregunta o necesita
            realizar algún cambio adicional, por favor contáctenos.
        </p>
    </x-email.message-box>

    {{-- Mensaje de agradecimiento --}}
    <div style="text-align: center; margin: 30px 0;">
        <p style="color: #6c757d; font-size: 16px; font-style: italic;">
            "Gracias por su comprensión y por confiar en nosotros"
        </p>
        <p style="color: #ff9800; font-weight: bold; font-size: 18px;">
            Equipo de {{ $clinicName }}
        </p>
    </div>
@endsection
