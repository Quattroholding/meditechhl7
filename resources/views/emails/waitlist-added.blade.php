@extends('emails.layouts.base', [
    'title' => 'Agregado a Lista de Espera',
    'headerIcon' => '⏳',
    'headerTitle' => 'Lista de Espera',
    'headerSubtitle' => 'Se ha agregado a la lista de espera exitosamente',
    'headerColor' => '#ff9800'
])

@section('content')
    {{-- Mensaje de saludo --}}
    <x-email.message-box title="Estimado/a {{ $patientName }}">
        <p style="font-size: 16px; margin: 0;">
            Ha sido <strong>agregado a la lista de espera</strong> exitosamente. Te notificaremos tan pronto como se libere un espacio disponible.
        </p>
    </x-email.message-box>

    {{-- Detalles de la solicitud --}}
    <x-email.message-box type="warning" title="⏳ Detalles de su Solicitud">
        <x-email.info-grid :items="[
            'Médico' => 'Dr. ' . $practitionerName,
            'Especialidad' => $speciality ?? 'Medicina General',
            'Fecha Solicitada' => $requestedDate,
            'Hora Solicitada' => $requestedTime,
            'Nivel de Urgencia' => $urgencyLevel,
            'Máximo de Espera' => $maxWaitDays . ' días',
            'Centro Médico' => $clinicName
        ]" />
    </x-email.message-box>

    {{-- Posición en la lista --}}
    @if($position && $position !== 'N/A')
        <x-email.message-box type="info" title="📊 Posición en Lista">
            <p style="margin: 0; color: #1565c0; font-size: 18px;">
                <strong>Posición: #{{ $position }}</strong>
            </p>
            <p style="margin: 10px 0 0; color: #1565c0;">
                Eres el paciente número {{ $position }} en la lista de espera. Tan pronto como se libere un espacio, te contactaremos.
            </p>
        </x-email.message-box>
    @endif

    {{-- Instrucciones --}}
    <x-email.message-box type="info" title="📋 Qué Sucede Ahora">
        <ul style="color: #1565c0; line-height: 1.8; margin: 10px 0; padding-left: 20px;">
            <li style="margin-bottom: 8px;"><strong>Monitoreo:</strong> Monitoreamos constantemente nuevas disponibilidades</li>
            <li style="margin-bottom: 8px;"><strong>Notificación:</strong> Te notificaremos por email, SMS o WhatsApp cuando se libere un espacio</li>
            <li style="margin-bottom: 8px;"><strong>Confirmación:</strong> Tendrás que confirmar tu disponibilidad rápidamente</li>
            <li style="margin-bottom: 8px;"><strong>Cancelación:</strong> Puedes cancelar tu solicitud en cualquier momento si lo deseas</li>
        </ul>
    </x-email.message-box>

    {{-- Información sobre urgencia --}}
    @if($urgencyLevel && $urgencyLevel !== 'Routine')
        <x-email.message-box type="success" title="🔴 Prioridad Alta">
            <p style="margin: 0; color: #1a5e20;">
                Tu solicitud ha sido marcada como <strong>{{ $urgencyLevel }}</strong>, lo que significa que recibirá atención prioritaria en la lista de espera.
            </p>
        </x-email.message-box>
    @endif

    {{-- Botón de acción --}}
    <x-email.button href="{{ $clinicWebsite ?? '#' }}" type="warning" icon="📱">
        Ver tu Solicitud en el Portal
    </x-email.button>

    {{-- Información de contacto --}}
    <x-email.message-box type="info" title="📞 ¿Necesitas Ayuda?">
        <p style="margin: 0; color: #1565c0;">
            Si tienes preguntas sobre tu solicitud o necesitas cambiar tus preferencias, no dudes en contactarnos.
        </p>
        <p style="margin: 10px 0 0; color: #1565c0;">
            Estamos aquí para ayudarte a recibir la atención que necesitas.
        </p>
    </x-email.message-box>

    {{-- Mensaje de agradecimiento --}}
    <div style="text-align: center; margin: 30px 0;">
        <p style="color: #6c757d; font-size: 16px; font-style: italic;">
            "Gracias por elegirnos. Pronto tendremos disponibilidad para ti"
        </p>
        <p style="color: #ff9800; font-weight: bold; font-size: 18px;">
            Equipo de {{ $clinicName }}
        </p>
    </div>
@endsection
