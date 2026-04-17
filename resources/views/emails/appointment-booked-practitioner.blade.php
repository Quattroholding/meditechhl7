@extends('emails.layouts.base', [
    'title' => 'Nueva Cita Médica Agendada',
    'headerIcon' => '✅',
    'headerTitle' => 'Nueva Cita Agendada',
    'headerSubtitle' => 'Un paciente ha agendado una cita médica con usted',
    'headerColor' => '#28a745'
])

@section('content')
    {{-- Mensaje de saludo --}}
    <x-email.message-box title="Estimado Dr. {{ $practitionerName }}">
        <p style="font-size: 16px; margin: 0;">
            Se ha <strong>agendado una nueva cita médica</strong> con usted.
            A continuación encontrará todos los detalles de la cita.
        </p>
    </x-email.message-box>

    {{-- Información del paciente y cita --}}
    <x-email.message-box type="info" title="👤 Información de la Cita">
        <x-email.info-grid :items="[
            'Paciente' => $patientName,
            'Fecha' => $appointmentDate,
            'Hora' => $appointmentTime,
            'Duración' => $durationMinutes . ' minutos',
            'Tipo de servicio' => $serviceType,
            'Especialidad' => $specialty ?? 'Medicina General'
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

    {{-- Botones de acción --}}
    <div style="margin: 30px 0;">
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
            <tr>
                <td style="padding-right: 10px;" width="50%">
                    {{--}}
                    <x-email.button href="{{ $confirmUrl }}" type="success" icon="✅">
                        Confirmar Cita
                    </x-email.button>
                    {{--}}
                    @if($calendarUrl ?? false)
                        <x-email.button href="{{ $calendarUrl }}" type="primary" icon="📅">
                            Ver en Calendario
                        </x-email.button>
                    @endif
                </td>
                <td style="padding-left: 10px;" width="50%">
                    <x-email.button href="{{ $cancelUrl }}" type="danger" icon="❌">
                        Cancelar Cita
                    </x-email.button>
                </td>
            </tr>
        </table>
    </div>

    {{-- Botón para ver en calendario
    @if($calendarUrl ?? false)
        <x-email.button href="{{ $calendarUrl }}" type="primary" icon="📅">
            Ver en Calendario
        </x-email.button>
    @endif
     --}}

    {{-- Recordatorio importante --}}
    <x-email.message-box type="warning" title="⏰ Recordatorio Importante">
        <ul style="color: #856404; line-height: 1.8; margin: 10px 0; padding-left: 20px;">
            <li style="margin-bottom: 8px;">Si no puede atender esta cita, por favor cancélela lo antes posible</li>
            <li style="margin-bottom: 8px;">El paciente recibirá notificaciones automáticas sobre el estado de su cita</li>
            <li style="margin-bottom: 8px;">Puede gestionar todas sus citas desde el calendario del sistema</li>
        </ul>
    </x-email.message-box>

    {{-- Mensaje de cierre --}}
    <div style="text-align: center; margin: 30px 0;">
        <p style="color: #6c757d; font-size: 16px; font-style: italic;">
            "Gracias por su dedicación y compromiso con nuestros pacientes"
        </p>
        <p style="color: #28a745; font-weight: bold; font-size: 18px;">
            Sistema de Gestión Médica - {{ $clinicName }}
        </p>
    </div>
@endsection
