@extends('emails.layouts.base', [
    'title' => 'Bienvenido al Sistema Meditech - Credenciales de Acceso',
    'headerIcon' => '👨‍⚕️',
    'headerTitle' => 'Bienvenido a Meditech',
    'headerSubtitle' => 'Sistema de Gestión Médica'
])

@section('content')
    {{-- Mensaje de bienvenida --}}
    <x-email.message-box title="Estimado/a {{ $userPrefix }}{{ $user->first_name }} {{ $user->last_name }}">
        <p style="font-size: 16px; margin: 0;">
            Le damos la bienvenida al sistema de gestión médica <strong>Meditech</strong>. Sus credenciales de acceso han sido creadas exitosamente.
        </p>
    </x-email.message-box>

    {{-- Credenciales de acceso --}}
    <x-email.message-box type="success" title="🔑 Datos de Acceso">
        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-top: 15px;">
            <x-email.info-grid :items="[
                'Email' => $user->email,
                'Contraseña temporal' => $temporaryPassword
            ]" />
        </div>
    </x-email.message-box>

    {{-- Botón de acceso --}}
    <x-email.button href="{{ url('/login') }}" icon="🚀">
        Acceder al Sistema
    </x-email.button>

    {{-- Información importante --}}
    <x-email.message-box type="warning" title="⚠️ IMPORTANTE">
        <p style="margin: 0; color: #c53030;">
            Esta es una contraseña temporal que <strong>debe cambiar</strong> en su primer acceso al sistema por motivos de seguridad.
        </p>
    </x-email.message-box>

    {{-- Instrucciones de primer acceso --}}
    <x-email.message-box type="info" title="📋 Instrucciones de Primer Acceso">
        <ol style="margin: 10px 0; padding-left: 20px; color: #1565c0;">
            <li style="margin-bottom: 8px;">Ingrese al sistema con las credenciales proporcionadas</li>
            <li style="margin-bottom: 8px;">Será dirigido automáticamente a cambiar su contraseña</li>
            <li style="margin-bottom: 8px;">Cree una contraseña segura de su elección</li>
            <li style="margin-bottom: 8px;">Complete su perfil profesional</li>
        </ol>
    </x-email.message-box>

    {{-- Recomendaciones de seguridad --}}
    <x-email.message-box type="highlight" title="🛡️ Recomendaciones de Seguridad">
        <ul style="margin: 10px 0; padding-left: 20px; color: #856404;">
            <li style="margin-bottom: 6px;">Use una contraseña con al menos 8 caracteres</li>
            <li style="margin-bottom: 6px;">Incluya letras mayúsculas, minúsculas y números</li>
            <li style="margin-bottom: 6px;">No comparta sus credenciales con terceros</li>
            <li style="margin-bottom: 6px;">Cierre sesión al terminar de usar el sistema</li>
        </ul>
    </x-email.message-box>

    {{-- Mensaje de soporte --}}
    <div style="text-align: center; margin: 30px 0;">
        <p style="color: #6c757d; font-size: 14px;">
            Si tiene alguna duda o problema para acceder, contacte al administrador del sistema.
        </p>
    </div>
@endsection