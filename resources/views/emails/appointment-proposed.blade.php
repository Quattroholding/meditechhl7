@extends('emails.layouts.base', [
    'title' => 'Nueva Solicitud de Cita Médica',
    'headerIcon' => '📋',
    'headerTitle' => 'Nueva Solicitud de Cita',
    'headerSubtitle' => 'Un paciente ha solicitado una cita médica',
    'headerColor' => '#2E37A4'
])

@section('content')
    {{-- Mensaje de saludo --}}
    <x-email.message-box title="Estimado Dr. {{ $practitionerName }}">
        <p style="font-size: 16px; margin: 0;">
            Ha recibido una nueva <strong>solicitud de cita médica</strong> que requiere su revisión y confirmación. 
            A continuación encontrará todos los detalles de la solicitud.
        </p>
    </x-email.message-box>

    {{-- Información del paciente --}}
    <x-email.message-box type="info" title="👤 Información del Paciente">
        <x-email.info-grid :items="[
            'Paciente' => $patientName,
            'Fecha solicitada' => $requestedDate,
            'Hora solicitada' => $requestedTime,
            'Duración' => $durationMinutes . ' minutos',
            'Tipo de servicio' => $serviceType ?? 'Consulta'
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

    {{-- Comentarios y descripción --}}
    @if($description || $comment)
        <x-email.message-box type="success" title="💬 Comentarios Adicionales">
            @if($description)
                <div style="margin-bottom: 15px;">
                    <p style="margin: 0; color: #155724;">
                        <strong>Descripción:</strong> {{ $description }}
                    </p>
                </div>
            @endif
            
            @if($comment)
                <div>
                    <p style="margin: 0; color: #155724;">
                        <strong>Comentario del paciente:</strong> {{ $comment }}
                    </p>
                </div>
            @endif
        </x-email.message-box>
    @endif

    {{-- Botón de acción principal --}}
    @if($reviewUrl ?? false)
        <x-email.button href="{{ $reviewUrl }}" type="primary" icon="👁️">
            Revisar Solicitud de Cita
        </x-email.button>
    @endif

    {{-- Opciones disponibles --}}
    <x-email.message-box type="info" title="⚡ Acciones Disponibles">
        <ul style="color: #1565c0; line-height: 1.8; margin: 10px 0; padding-left: 20px;">
            <li style="margin-bottom: 8px;"><strong>Confirmar:</strong> Aceptar la cita en la fecha y hora solicitada</li>
            <li style="margin-bottom: 8px;"><strong>Reprogramar:</strong> Proponer una fecha y hora alternativa</li>
            <li style="margin-bottom: 8px;"><strong>Rechazar:</strong> Declinar la solicitud con motivo</li>
            <li style="margin-bottom: 8px;"><strong>Contactar:</strong> Comunicarse directamente con el paciente</li>
        </ul>
    </x-email.message-box>

    {{-- Recordatorio importante --}}
    <x-email.message-box type="warning" title="⏰ Recordatorio Importante">
        <p style="margin: 0; color: #856404;">
            <strong>Respuesta oportuna:</strong> Por favor revise y responda a esta solicitud lo antes posible para brindar una mejor experiencia al paciente.
        </p>
        <p style="margin: 10px 0 0; color: #856404;">
            <strong>Tiempo de respuesta recomendado:</strong> 24 horas o menos
        </p>
    </x-email.message-box>

    {{-- Mensaje de cierre --}}
    <div style="text-align: center; margin: 30px 0;">
        <p style="color: #6c757d; font-size: 16px; font-style: italic;">
            "Su pronta respuesta mejora la experiencia de nuestros pacientes"
        </p>
        <p style="color: #2E37A4; font-weight: bold; font-size: 18px;">
            Sistema de Gestión Médica - {{ $clinicName }}
        </p>
    </div>
@endsection