@extends('emails.layouts.base', [
    'title' => 'Encuesta de Satisfacción - ' . $clinicName,
    'headerIcon' => '📋',
    'headerTitle' => $surveyTitle,
    'headerSubtitle' => 'Su opinión nos ayuda a mejorar'
])

@section('content')
    {{-- Mensaje de saludo --}}
    <x-email.message-box title="Estimado/a {{ $patientName }}">
        <p style="font-size: 16px; margin: 0;">
            Esperamos que se encuentre bien. Su opinión es muy importante para nosotros y nos ayuda a mejorar continuamente la calidad de nuestros servicios.
        </p>
    </x-email.message-box>

    {{-- Detalles de la consulta --}}
    <x-email.message-box type="info" title="📅 Detalles de su Consulta">
        <x-email.info-grid :items="[
            'Fecha' => $encounterDate,
            'Médico' => $practitionerName,
            'Centro médico' => $clinicName
        ]" />
    </x-email.message-box>

    {{-- Botón de acción principal --}}
    <x-email.button href="{{ $surveyUrl }}" icon="📝">
        Completar Encuesta
    </x-email.button>

    {{-- Beneficios de completar la encuesta --}}
    <x-email.message-box type="success" title="🎯 Su Respuesta nos Permitirá">
        <ul style="color: #155724; line-height: 1.8; margin: 10px 0; padding-left: 20px;">
            <li style="margin-bottom: 8px;">Mejorar la calidad de nuestros servicios</li>
            <li style="margin-bottom: 8px;">Optimizar los tiempos de atención</li>
            <li style="margin-bottom: 8px;">Brindar una mejor experiencia a nuestros pacientes</li>
            <li style="margin-bottom: 8px;">Fortalecer la relación médico-paciente</li>
        </ul>
    </x-email.message-box>

    {{-- Información importante --}}
    <x-email.message-box type="highlight" title="⏱️ Información Importante">
        <p style="margin: 0; color: #856404;">
            <strong>Tiempo estimado:</strong> Esta encuesta tomará aproximadamente <strong>2-3 minutos</strong> en completarse.
        </p>
        <p style="margin: 10px 0 0; color: #856404;">
            <strong>Confidencial:</strong> Sus respuestas son completamente confidenciales y nos ayudan a brindar un mejor servicio.
        </p>
    </x-email.message-box>

    {{-- Enlaces alternativos --}}
    <x-email.message-box type="warning" title="🔗 Enlace Alternativo">
        <p style="margin: 0; color: #c53030; font-size: 14px;">
            Si no puede hacer clic en el botón, puede copiar y pegar el siguiente enlace en su navegador:
        </p>
        <p style="word-break: break-all; color: #4299e1; margin: 10px 0 0; font-size: 14px;">
            {{ $surveyUrl }}
        </p>
    </x-email.message-box>

    {{-- Mensaje de agradecimiento --}}
    <div style="text-align: center; margin: 30px 0;">
        <p style="color: #6c757d; font-size: 16px; font-style: italic;">
            "Gracias por confiar en nosotros para su cuidado médico"
        </p>
        <p style="color: #2E37A4; font-weight: bold; font-size: 18px;">
            Equipo de {{ $clinicName }}
        </p>
    </div>
@endsection