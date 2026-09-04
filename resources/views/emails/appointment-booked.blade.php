@extends('emails.layouts.base', [
    'title' => 'Cita Médica Agendada',
    'headerIcon' => '✅',
    'headerTitle' => 'Cita Agendada',
    'headerSubtitle' => 'Su cita médica ha sido registrada exitosamente',
    'headerColor' => '#28a745'
])

@section('content')
    {{-- Mensaje de saludo --}}
    <x-email.message-box title="Estimado/a {{ $patientName }}">
        <p style="font-size: 16px; margin: 0;">
            Su cita médica ha sido <strong>agendada exitosamente</strong>. A continuación encontrará los detalles de su cita.
        </p>
    </x-email.message-box>

    {{-- Detalles de la cita --}}
    <x-email.message-box type="success" title="🏥 Detalles de su Cita">
        <x-email.info-grid :items="[
            'Médico' => 'Dr. ' . $practitionerName,
            'Especialidad' => $specialty ?? 'Medicina General',
            'Fecha' => $appointmentDate,
            'Hora' => $appointmentTime,
            'Duración' => $durationMinutes . ' minutos',
            'Tipo de consulta' => $serviceType ?? 'Consulta general',
            'Centro médico' => $clinicName
        ]" />

        @if($isVirtual ?? false)
            <div style="margin-top: 15px; padding: 15px; background-color: #e3f2fd; border-left: 4px solid #1976d2; border-radius: 4px;">
                <p style="margin: 0 0 10px 0; color: #1565c0;">
                    <strong>🎥 Cita Virtual</strong>
                </p>
                <p style="margin: 0 0 10px 0; color: #1565c0; font-size: 14px;">
                    Esta es una consulta virtual. Recibirá un enlace de acceso a la videollamada.
                </p>
                @if($virtualRoomUrl ?? false)
                    <p style="margin: 0 0 10px 0; color: #1565c0;">
                        <strong>Enlace de acceso:</strong><br>
                        <a href="{{ $zoomRoomUrl }}" style="color: #1976d2; text-decoration: none; word-break: break-all;">
                            {{ $zoomRoomUrl }}
                        </a>
                    </p>
                @endif
                @if($meetingPassword ?? false)
                    <p style="margin: 0; color: #1565c0;">
                        <strong>Código de acceso de Zoom:</strong> <code style="background: #fff3cd; padding: 2px 6px; border-radius: 3px; font-family: monospace; font-weight: bold;">{{ $meetingPassword }}</code>
                    </p>
                @endif
            </div>
        @else
            @if($branchName)
                <div style="margin-top: 10px;">
                    <p style="margin: 0; color: #155724;">
                        <strong>🏪 Sede:</strong> {{ $branchName }}
                    </p>
                </div>
            @endif

            @if($consultingRoom)
                <div style="margin-top: 5px;">
                    <p style="margin: 0; color: #155724;">
                        <strong>🚪 Consultorio:</strong> {{ $consultingRoom }}
                    </p>
                </div>
            @endif
        @endif

        @if($comment)
            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #b8dabc;">
                <p style="margin: 0; color: #155724;">
                    <strong>Nota:</strong> {{ $comment }}
                </p>
            </div>
        @endif
    </x-email.message-box>

    {{-- Recordatorio --}}
    <x-email.message-box type="info" title="⏰ Recordatorio Automático">
        <p style="margin: 0; color: #1565c0; font-size: 15px;">
            <strong>Recibirá una notificación recordatoria 2 horas antes de su cita.</strong>
        </p>
        <p style="margin: 10px 0 0; color: #1565c0;">
            Le enviaremos un mensaje con toda la información necesaria para que no olvide su cita médica.
        </p>
    </x-email.message-box>

    {{-- Instrucciones importantes --}}
    <x-email.message-box type="info" title="📋 Instrucciones Importantes">
        <ul style="color: #1565c0; line-height: 1.8; margin: 10px 0; padding-left: 20px;">
            @if($isVirtual ?? false)
                <li style="margin-bottom: 8px;"><strong>Conexión:</strong> Acceda 10 minutos antes de la hora de su cita</li>
                <li style="margin-bottom: 8px;"><strong>Requisitos técnicos:</strong> Asegúrese de tener micrófono y cámara funcionales</li>
                <li style="margin-bottom: 8px;"><strong>Conexión a internet:</strong> Utilice una conexión estable y rápida</li>
                <li style="margin-bottom: 8px;"><strong>Privacidad:</strong> Ubíquese en un lugar privado y tranquilo</li>
            @else
                <li style="margin-bottom: 8px;"><strong>Llegada:</strong> Llegue 15 minutos antes de su cita</li>
                <li style="margin-bottom: 8px;"><strong>Documentos:</strong> Traiga su documento de identidad</li>
                <li style="margin-bottom: 8px;"><strong>Exámenes:</strong> Traiga sus exámenes médicos previos si los tiene</li>
            @endif
            @if($patientInstruction)
                <li style="margin-bottom: 8px;"><strong>Instrucción especial:</strong> {{ $patientInstruction }}</li>
            @endif
        </ul>
    </x-email.message-box>

    {{-- Botón de acción --}}
    @if($isVirtual ?? false)
        @if($virtualRoomUrl ?? false)
            <x-email.button href="{{ $virtualRoomUrl }}" type="success" icon="🎥">
                Acceder a Videollamada
            </x-email.button>
        @endif
    @elseif($appointmentUrl ?? false)
        <x-email.button href="{{ $appointmentUrl }}" type="success" icon="👁️">
            Ver Calendario de Citas
        </x-email.button>
    @endif

    {{-- Información sobre cambios --}}
    <x-email.message-box type="warning" title="⚠️ Cambios y Cancelaciones">
        <p style="margin: 0; color: #856404;">
            Si necesita reprogramar o cancelar su cita, por favor contáctenos con <strong>al menos 24 horas de anticipación</strong>.
        </p>
        <p style="margin: 10px 0 0; color: #856404;">
            Esto nos permite ofrecer el horario a otros pacientes que puedan necesitarlo.
        </p>
    </x-email.message-box>

    {{-- Mensaje de agradecimiento --}}
    <div style="text-align: center; margin: 30px 0;">
        <p style="color: #6c757d; font-size: 16px; font-style: italic;">
            "Gracias por confiar en nosotros para su cuidado médico"
        </p>
        <p style="color: #28a745; font-weight: bold; font-size: 18px;">
            Equipo de {{ $clinicName }}
        </p>
    </div>
@endsection
