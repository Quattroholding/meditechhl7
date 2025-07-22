@extends('emails.layouts.base', [
    'title' => 'Cita Médica Cancelada',
    'headerIcon' => '❌',
    'headerTitle' => 'Cita Cancelada',
    'headerSubtitle' => 'Información sobre la cancelación de su cita médica',
    'headerColor' => '#dc3545'
])

@section('content')
    {{-- Mensaje de saludo --}}
    <x-email.message-box title="Estimado/a {{ $patientName }}">
        <p style="font-size: 16px; margin: 0;">
            Lamentamos informarle que su cita médica confirmada ha sido cancelada. 
            Entendemos que esto puede causar inconvenientes y nos disculpamos por las molestias.
        </p>
    </x-email.message-box>

    {{-- Detalles de la cita cancelada --}}
    <x-email.message-box type="warning" title="📅 Detalles de la Cita Cancelada">
        <x-email.info-grid :items="[
            'Médico' => 'Dr. ' . $practitionerName,
            'Fecha' => $appointmentDate,
            'Hora' => $appointmentTime,
            'Especialidad' => $specialty ?? 'Medicina General',
            'Centro médico' => $clinicName
        ]" />
        
        @if($cancellationReason)
            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #ffeaa7;">
                <p style="margin: 0; color: #856404;">
                    <strong>Motivo de cancelación:</strong> {{ $cancellationReason }}
                </p>
            </div>
        @endif
    </x-email.message-box>

    {{-- Próximos pasos --}}
    <x-email.message-box type="info" title="🔄 Próximos Pasos">
        <ul style="color: #1565c0; line-height: 1.8; margin: 10px 0; padding-left: 20px;">
            <li style="margin-bottom: 8px;"><strong>Reagendar:</strong> Puede solicitar una nueva cita contactándose con nuestro equipo</li>
            <li style="margin-bottom: 8px;"><strong>Disponibilidad:</strong> Consulte nuevas fechas disponibles en el sistema</li>
            <li style="margin-bottom: 8px;"><strong>Urgencias:</strong> Si es urgente, puede solicitar cita con otro especialista</li>
            <li style="margin-bottom: 8px;"><strong>Asistencia:</strong> Nuestro equipo está disponible para ayudarle</li>
        </ul>
    </x-email.message-box>

    {{-- Botón de acción principal --}}
    @if($rescheduleUrl ?? false)
        <x-email.button href="{{ $rescheduleUrl }}" type="primary" icon="📅">
            Reagendar Cita
        </x-email.button>
    @endif

    {{-- Información de contacto --}}
    <x-email.message-box type="highlight" title="📞 Contacto y Asistencia">
        <p style="margin: 0; color: #856404;">
            <strong>Línea de atención:</strong> Puede contactarnos para recibir asistencia personalizada en la reprogramación de su cita.
        </p>
        <p style="margin: 10px 0 0; color: #856404;">
            <strong>Horario de atención:</strong> Lunes a viernes de 8:00 AM a 6:00 PM
        </p>
    </x-email.message-box>

    {{-- Mensaje de disculpa y apoyo --}}
    <div style="text-align: center; margin: 30px 0;">
        <p style="color: #6c757d; font-size: 16px; font-style: italic;">
            "Disculpe las molestias ocasionadas. Estamos aquí para ayudarle."
        </p>
        <p style="color: #dc3545; font-weight: bold; font-size: 18px;">
            Equipo de {{ $clinicName }}
        </p>
    </div>
@endsection