@extends('emails.layouts.base', [
    'title' => 'Sus Recetas Médicas - ' . ($practitioner->name ?? 'Medico'),
    'headerIcon' => '💊',
    'headerTitle' => 'Sus Recetas Médicas',
    'headerSubtitle' => 'Documentos adjuntos de su consulta',
    'headerColor' => '#2c5282'
])

@section('content')
    {{-- Mensaje principal --}}
    <x-email.message-box title="Estimado(a) {{ $patient->name ?? 'Paciente' }}">
        <p style="font-size: 16px; margin: 0;">
            El <strong>{{ $practitioner->name ?? 'Medico' }}</strong> le ha enviado
            @if($hasMedications && $hasServiceRequests)
                su receta de medicamentos y ordenes médicas
            @elseif($hasMedications)
                su receta de medicamentos
            @else
                sus ordenes medicas
            @endif
            correspondientes a su consulta del día <strong>{{ now()->format('d/m/Y') }}</strong>.
        </p>
    </x-email.message-box>

    {{-- Documentos adjuntos --}}
    <x-email.message-box type="success" title="📎 Documentos Adjuntos">
        <p style="margin: 10px 0 15px 0; color: #155724;">
            Encontrara los siguientes documentos adjuntos en este correo:
        </p>
        <ul style="margin: 0; padding-left: 20px; color: #374151;">
            @if($hasMedications)
                <li style="margin-bottom: 10px; line-height: 1.6;">
                    <strong>💊 Receta de Medicamentos (PDF)</strong>
                    <br>
                    <span style="font-size: 14px; color: #666;">Contiene los medicamentos recetados con sus indicaciones de uso.</span>
                </li>
            @endif
            @if($hasServiceRequests)
                <li style="margin-bottom: 10px; line-height: 1.6;">
                    <strong>🔬 Ordenes Médicas (PDF)</strong>
                    <br>
                    <span style="font-size: 14px; color: #666;">Contiene los examenes y procedimientos solicitados por su medico.</span>
                </li>
            @endif
        </ul>
    </x-email.message-box>

    {{-- Informacion del medico --}}
    <x-email.message-box type="info" title="👨‍⚕️ Información del Médico">
        <div style="background: #f7fafc; padding: 15px; border-radius: 8px; margin-top: 10px;">
            <p style="margin: 0 0 5px 0; font-weight: bold; color: #2c5282; font-size: 16px;">
                {{ $practitioner->name ?? 'Médico' }}
            </p>
            @if($practitioner->registry)
                <p style="margin: 3px 0; color: #4a5568; font-size: 14px;">
                    <strong>Registro Médico:</strong> {{ $practitioner->registry }}
                </p>
            @endif
            @if($practitioner->licence_code)
                <p style="margin: 3px 0; color: #4a5568; font-size: 14px;">
                    <strong>Idoneidad:</strong> {{ $practitioner->licence_code }}
                </p>
            @endif
            @if($practitioner->phone)
                <p style="margin: 3px 0; color: #4a5568; font-size: 14px;">
                    <strong>Teléfono:</strong> {{ $practitioner->phone }}
                </p>
            @endif
        </div>
    </x-email.message-box>

    {{-- Instrucciones importantes --}}
    <x-email.message-box type="highlight" title="⚠️ Instrucciones Importantes">
        <ul style="margin: 10px 0; padding-left: 20px; color: #856404;">
            <li style="margin-bottom: 8px; line-height: 1.6;">
                Guarde estos documentos en un lugar seguro para futuras referencias.
            </li>
            <li style="margin-bottom: 8px; line-height: 1.6;">
                Presente la receta en cualquier farmacia autorizada para adquirir sus medicamentos.
            </li>
            @if($hasServiceRequests)
                <li style="margin-bottom: 8px; line-height: 1.6;">
                    Lleve las ordenes medicas al laboratorio o centro de diagnostico de su preferencia.
                </li>
            @endif
            <li style="margin-bottom: 8px; line-height: 1.6;">
                Si tiene dudas sobre las indicaciones, consulte con su medico tratante.
            </li>
        </ul>
    </x-email.message-box>

    {{-- Referencia de consulta --}}
    <div style="background: #f8f9fa; border-radius: 8px; padding: 15px; margin: 20px 0; text-align: center;">
        <p style="margin: 0; color: #6c757d; font-size: 13px;">
            <strong>Referencia de Consulta:</strong> {{ $encounter->identifier ?? $encounter->id }}
            <br>
            <span style="font-size: 12px;">Fecha: {{ $encounter->start?->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i') }}</span>
        </p>
    </div>

    {{-- Mensaje de agradecimiento --}}
    <div style="text-align: center; margin: 30px 0;">
        <p style="color: #2c5282; font-size: 18px; font-weight: 600; margin: 0;">
            ¡Gracias por su confianza!
        </p>
        <p style="color: #6b7280; font-size: 14px; margin-top: 10px; line-height: 1.6;">
            Le deseamos una pronta recuperación.
        </p>
    </div>
@endsection
