@extends('emails.layouts.base', [
    'title' => 'Cita Médica No Disponible',
    'headerIcon' => '⚠️',
    'headerTitle' => 'Cita No Disponible',
    'headerSubtitle' => 'Su solicitud de cita no ha podido ser confirmada',
    'headerColor' => '#dc3545'
])

@section('content')
    {{-- Mensaje de saludo --}}
    <x-email.message-box title="Estimado/a {{ $patientName }}">
        <p style="font-size: 16px; margin: 0;">
            Lamentamos informarle que su <strong>solicitud de cita médica</strong> no ha podido ser confirmada para la fecha y hora solicitada. 
            Entendemos que esto puede ser inconveniente y queremos ayudarle a encontrar una alternativa.
        </p>
    </x-email.message-box>

    {{-- Detalles de la solicitud --}}
    <x-email.message-box type="warning" title="📋 Detalles de la Solicitud">
        <x-email.info-grid :items="[
            'Médico solicitado' => 'Dr. ' . $practitionerName,
            'Fecha solicitada' => $requestedDate,
            'Hora solicitada' => $requestedTime,
            'Especialidad' => $specialty ?? 'Medicina General',
            'Centro médico' => $clinicName
        ]" />
        
        @if($rejectionReason)
            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #ffeaa7;">
                <p style="margin: 0; color: #856404;">
                    <strong>Motivo:</strong> {{ $rejectionReason }}
                </p>
            </div>
        @endif
    </x-email.message-box>

    {{-- Opciones alternativas --}}
    <x-email.message-box type="info" title="🔄 ¿Qué Puede Hacer Ahora?">
        <ul style="color: #1565c0; line-height: 1.8; margin: 10px 0; padding-left: 20px;">
            <li style="margin-bottom: 8px;"><strong>Nuevas fechas:</strong> Puede solicitar una nueva cita en fechas y horarios diferentes</li>
            <li style="margin-bottom: 8px;"><strong>Disponibilidad:</strong> Consulte la disponibilidad del médico en nuestro sistema</li>
            <li style="margin-bottom: 8px;"><strong>Otros especialistas:</strong> Considere solicitar cita con otro especialista disponible</li>
            <li style="margin-bottom: 8px;"><strong>Lista de espera:</strong> Puede agregarse a la lista de espera para cancelaciones</li>
        </ul>
    </x-email.message-box>

    {{-- Botones de acción --}}
    <div style="text-align: center; margin: 25px 0;">
        @if($availableDoctorsUrl ?? false)
            <x-email.button href="{{ $availableDoctorsUrl }}" type="primary" icon="🩺">
                Ver Médicos Disponibles
            </x-email.button>
        @endif
        
        @if($rescheduleUrl ?? false)
            <x-email.button href="{{ $rescheduleUrl }}" type="secondary" icon="📅">
                Solicitar Nueva Fecha
            </x-email.button>
        @endif
    </div>

    {{-- Asistencia personalizada --}}
    <x-email.message-box type="success" title="🤝 Asistencia Personalizada">
        <p style="margin: 0; color: #155724;">
            <strong>Apoyo especializado:</strong> Nuestro equipo está disponible para ayudarle a encontrar una nueva fecha conveniente.
        </p>
        <p style="margin: 10px 0 0; color: #155724;">
            <strong>Contacto directo:</strong> Puede contactarnos directamente para recibir asistencia personalizada en la reprogramación de su cita.
        </p>
    </x-email.message-box>

    {{-- Información de contacto --}}
    <x-email.message-box type="highlight" title="📞 Información de Contacto">
        <x-email.info-grid :items="[
            'Línea de atención' => $contactPhone ?? 'Ver información de contacto',
            'Email de soporte' => $contactEmail ?? config('mail.from.address'),
            'Horario de atención' => 'Lunes a viernes de 8:00 AM a 6:00 PM'
        ]" />
    </x-email.message-box>

    {{-- Mensaje de comprensión --}}
    <div style="text-align: center; margin: 30px 0;">
        <p style="color: #6c757d; font-size: 16px; font-style: italic;">
            "Gracias por su comprensión. Estamos comprometidos en brindarle la mejor atención"
        </p>
        <p style="color: #dc3545; font-weight: bold; font-size: 18px;">
            Equipo de {{ $clinicName }}
        </p>
    </div>
@endsection