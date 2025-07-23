@extends('emails.layouts.base', [
    'title' => 'Recordatorio de Cita Médica',
    'headerIcon' => '⏰',
    'headerTitle' => 'Recordatorio de Cita',
    'headerSubtitle' => 'Su cita médica es muy pronto',
    'headerColor' => '#2E37A4'
])

@section('content')
    {{-- Mensaje de saludo --}}
    <x-email.message-box title="Estimado/a {{ $patientName }}">
        <p style="font-size: 16px; margin: 0;">
            Este es un recordatorio amigable de que tiene una <strong>cita médica programada</strong>. Esperamos verle pronto.
        </p>
    </x-email.message-box>

    {{-- Detalles de la cita --}}
    <x-email.message-box type="info" title="🏥 Detalles de su Cita">
        <x-email.info-grid :items="[
            'Médico' => $practitionerName,
            'Especialidad' => $specialty,
            'Fecha' => $appointmentDate,
            'Hora' => $appointmentTime,
            'Duración' => $durationMinutes . ' minutos',
            'Tipo de consulta' => $serviceType
        ]" />
    </x-email.message-box>

    {{-- Ubicación --}}
    <x-email.message-box type="success" title="📍 Ubicación">
        <x-email.info-grid :items="[
            'Centro médico' => $clinicName,
            'Sucursal' => $branchName,
            'Consultorio' => $consultingRoom
        ]" />
    </x-email.message-box>

    {{-- Comentarios especiales --}}
    @if($comment || $patientInstruction)
        <x-email.message-box type="highlight" title="📝 Información Importante">
            @if($comment)
                <div style="margin-bottom: 15px;">
                    <p style="margin: 0; color: #856404;">
                        <strong>Nota del médico:</strong> {{ $comment }}
                    </p>
                </div>
            @endif

            @if($patientInstruction)
                <div>
                    <p style="margin: 0; color: #856404;">
                        <strong>Instrucciones especiales:</strong> {{ $patientInstruction }}
                    </p>
                </div>
            @endif
        </x-email.message-box>
    @endif

    {{-- Preparación para la cita --}}
    <x-email.message-box type="info" title="✅ Preparación para su Cita">
        <ul style="color: #1565c0; line-height: 1.8; margin: 10px 0; padding-left: 20px;">
            <li style="margin-bottom: 8px;"><strong>Llegada:</strong> Llegue 15 minutos antes de su cita</li>
            <li style="margin-bottom: 8px;"><strong>Documentos:</strong> Traiga su documento de identidad</li>
            <li style="margin-bottom: 8px;"><strong>Historia clínica:</strong> Traiga sus exámenes médicos previos</li>
            <li style="margin-bottom: 8px;"><strong>Medicamentos:</strong> Lista de medicamentos actuales si los tiene</li>
            <li style="margin-bottom: 8px;"><strong>Síntomas:</strong> Prepare una lista de síntomas o preguntas</li>
        </ul>
    </x-email.message-box>

    {{-- Botón de acción --}}
    <x-email.button href="{{ $appointmentUrl }}" type="primary" icon="📅">
        Ver Detalles de la Cita
    </x-email.button>

    {{-- Información de contacto --}}
    <x-email.message-box type="warning" title="📞 ¿Necesita Hacer Cambios?">
        <p style="margin: 0; color: #856404;">
            Si necesita <strong>reprogramar o cancelar</strong> su cita, por favor contáctenos lo antes posible.
        </p>
        <p style="margin: 10px 0 0; color: #856404;">
            <strong>Importante:</strong> Las cancelaciones deben hacerse con al menos 2 horas de anticipación.
        </p>
    </x-email.message-box>

    {{-- Tiempo hasta la cita --}}
    <div style="text-align: center; margin: 30px 0; padding: 20px; background: #f8f9ff; border-radius: 12px; border-left: 5px solid #2E37A4;">
        <p style="color: #2E37A4; font-size: 24px; font-weight: bold; margin: 0;">
            ⏰ {{ floor($hoursUntilAppointment) }} horas restantes
        </p>
        <p style="color: #6c757d; font-size: 16px; margin: 10px 0 0;">
            {{ $appointmentDate }} a las {{ $appointmentTime }}
        </p>
    </div>

    {{-- Mensaje de cierre --}}
    <div style="text-align: center; margin: 30px 0;">
        <p style="color: #6c757d; font-size: 16px; font-style: italic;">
            "Nos vemos pronto. Gracias por confiar en nosotros para su cuidado médico"
        </p>
        <p style="color: #2E37A4; font-weight: bold; font-size: 18px;">
            Equipo de {{ $clinicName }}
        </p>
    </div>
@endsection
