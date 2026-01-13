@extends('emails.layouts.base', [
    'title' => '¡Suscripción Activada! - ' . $client->name,
    'headerIcon' => '✅',
    'headerTitle' => '¡Suscripción Activada!',
    'headerSubtitle' => 'Su pago ha sido verificado exitosamente',
    'headerColor' => '#10b981'
])

@section('content')
    {{-- Mensaje de agradecimiento principal --}}
    <x-email.message-box title="¡Gracias por su pago!">
        <p style="font-size: 18px; margin: 0; color: #10b981; font-weight: 600;">
            🎉 ¡Excelentes noticias!
        </p>
        <p style="font-size: 16px; margin: 15px 0 0 0;">
            Hemos verificado su pago y su suscripción al plan <strong>{{ $package->name }}</strong>
            está ahora <strong style="color: #10b981;">activa</strong> y lista para usar.
        </p>
    </x-email.message-box>

    {{-- Estado de la suscripción --}}
    <x-email.message-box type="success" title="📊 Estado de su Suscripción">
        <div style="background: #f0fdf4; padding: 20px; border-radius: 8px; margin-top: 15px; border-left: 4px solid #10b981;">
            <x-email.info-grid :items="[
                'Plan Contratado' => $package->name,
                'Estado' => '✅ Activa',
                'Período Actual' => $subscription->current_period_starts_at->format('d/m/Y') . ' - ' . $subscription->current_period_ends_at->format('d/m/Y'),
                'Próxima Facturación' => $subscription->next_billing_date->format('d/m/Y')
            ]" />
        </div>
    </x-email.message-box>

    {{-- Funcionalidades disponibles --}}
    <x-email.message-box type="info" title="🚀 Funcionalidades Disponibles">
        <p style="margin: 10px 0 15px 0; color: #1e40af; font-weight: 600;">
            Ahora tiene acceso completo a todas las funcionalidades de su plan:
        </p>
        <ul style="margin: 0; padding-left: 20px; color: #374151;">
            <li style="margin-bottom: 10px; line-height: 1.6;">
                📅 <strong>Gestión de Citas:</strong> Programe, confirme y gestione citas médicas
            </li>
            <li style="margin-bottom: 10px; line-height: 1.6;">
                👥 <strong>Gestión de Pacientes:</strong> Administre expedientes e historiales médicos completos
            </li>
            <li style="margin-bottom: 10px; line-height: 1.6;">
                📊 <strong>Reportes y Estadísticas:</strong> Acceda a reportes detallados y análisis de su práctica
            </li>
            <li style="margin-bottom: 10px; line-height: 1.6;">
                💊 <strong>Prescripciones:</strong> Genere recetas médicas digitales
            </li>
            <li style="margin-bottom: 10px; line-height: 1.6;">
                🔔 <strong>Notificaciones:</strong> Recordatorios automáticos para pacientes
            </li>
            <li style="margin-bottom: 10px; line-height: 1.6;">
                📱 <strong>Acceso Multi-dispositivo:</strong> Trabaje desde cualquier lugar
            </li>
        </ul>
    </x-email.message-box>

    {{-- Pasos siguientes de configuración (si hay pendientes) --}}
    @if(!empty($pendingSteps))
    <x-email.message-box type="highlight" title="📝 Siguientes Pasos de Configuración">
        <p style="margin: 10px 0 15px 0; color: #856404; font-weight: 600;">
            Para aprovechar al máximo la plataforma, complete estos pasos:
        </p>

        @php
            $requiredSteps = array_filter($pendingSteps, fn($step) => $step['required']);
            $optionalSteps = array_filter($pendingSteps, fn($step) => !$step['required']);
        @endphp

        @if(!empty($requiredSteps))
        <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
            <p style="margin: 0 0 10px 0; color: #856404; font-weight: 700; font-size: 15px;">
                ⚠️ Pasos Obligatorios (requeridos para agendar citas):
            </p>
            <ol style="margin: 0; padding-left: 25px; color: #856404;">
                @foreach($requiredSteps as $step)
                <li style="margin-bottom: 12px; line-height: 1.6;">
                    <strong>{{ $step['icon'] }} {{ $step['title'] }}</strong>
                    <br>
                    <span style="font-size: 14px;">{{ $step['description'] }}</span>
                </li>
                @endforeach
            </ol>
        </div>
        @endif

        @if(!empty($optionalSteps))
        <div style="background: #e3f2fd; border-left: 4px solid #2196f3; padding: 15px; border-radius: 8px;">
            <p style="margin: 0 0 10px 0; color: #0d47a1; font-weight: 700; font-size: 15px;">
                💡 Pasos Recomendados (opcionales):
            </p>
            <ul style="margin: 0; padding-left: 25px; color: #0d47a1;">
                @foreach($optionalSteps as $step)
                <li style="margin-bottom: 12px; line-height: 1.6;">
                    <strong>{{ $step['icon'] }} {{ $step['title'] }}</strong>
                    <br>
                    <span style="font-size: 14px;">{{ $step['description'] }}</span>
                </li>
                @endforeach
            </ul>
        </div>
        @endif

        <p style="margin: 15px 0 0 0; color: #856404; font-size: 14px; font-style: italic;">
            💡 Esta lista se actualizará automáticamente a medida que complete cada paso.
        </p>
    </x-email.message-box>
    @endif

    {{-- Botón de acceso --}}
    <x-email.button href="{{ route('login') }}" icon="🚀">
        Acceder a la Plataforma
    </x-email.button>

    {{-- Información de soporte --}}
    <x-email.message-box type="highlight" title="💬 ¿Necesita Ayuda?">
        <p style="margin: 10px 0; color: #856404;">
            Nuestro equipo de soporte está disponible para ayudarle a aprovechar al máximo todas las funcionalidades de la plataforma.
        </p>
        <div style="background: #fff; padding: 15px; border-radius: 8px; margin-top: 15px;">
            <x-email.info-grid :items="[
                '📧 Email de Soporte' => 'support@meditecpty.com',
                '📱 WhatsApp' => '+507 XXXX-XXXX',
                '⏰ Horario' => 'Lunes a Viernes, 8:00 AM - 6:00 PM'
            ]" />
        </div>
    </x-email.message-box>

    {{-- Recordatorio de próxima facturación --}}
    <div style="background: #e0f2fe; border: 2px solid #0284c7; border-radius: 8px; padding: 20px; margin: 25px 0;">
        <p style="margin: 0; color: #0c4a6e; font-size: 15px;">
            ℹ️ <strong>Recordatorio:</strong> Su próxima facturación será generada el
            <strong>{{ $subscription->next_billing_date->format('d/m/Y') }}</strong>.
            Le notificaremos con anticipación para que pueda realizar el pago sin interrupciones en su servicio.
        </p>
    </div>

    {{-- Mensaje de agradecimiento final --}}
    <div style="text-align: center; margin: 30px 0;">
        <p style="color: #10b981; font-size: 20px; font-weight: 700; margin: 0;">
            ¡Gracias por confiar en Meditech! 💚
        </p>
        <p style="color: #6b7280; font-size: 15px; margin-top: 10px; line-height: 1.6;">
            Estamos comprometidos en brindarle las mejores herramientas para la gestión de su práctica médica.
        </p>
    </div>
@endsection
