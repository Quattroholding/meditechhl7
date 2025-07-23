{{-- Ejemplo de uso de la plantilla base de emails --}}
@extends('emails.layouts.base', [
    'title' => 'Título del Email - ' . config('app.name'),
    'headerIcon' => '📧',                    {{-- Emoji opcional para el header --}}
    'headerTitle' => 'Título del Email',     {{-- Título principal --}}
    'headerSubtitle' => 'Subtítulo opcional', {{-- Subtítulo opcional --}}
    'headerColor' => '#2E37A4',             {{-- Color del header (opcional, por defecto #2E37A4) --}}
    'buttonColor' => '#2E37A4',             {{-- Color de botones (opcional, por defecto #2E37A4) --}}
    'contactEmail' => 'contacto@ejemplo.com', {{-- Email de contacto (opcional) --}}
    'footerMessage' => 'Mensaje personalizado del footer' {{-- Mensaje del footer (opcional) --}}
])

@section('content')
    {{-- Mensaje de bienvenida o principal --}}
    <x-email.message-box title="¡Hola {{ $user->first_name ?? 'Usuario' }}!">
        <p style="font-size: 16px; margin: 0;">
            Este es un ejemplo de cómo usar la plantilla base para crear emails consistentes.
        </p>
    </x-email.message-box>

    {{-- Grid de información --}}
    <x-email.info-grid :items="[
        'Nombre' => $user->full_name ?? 'Usuario Ejemplo',
        'Email' => $user->email ?? 'usuario@ejemplo.com',
        'Fecha' => now()->format('d/m/Y'),
        'Estado' => 'Activo'
    ]" />

    {{-- Botón de acción principal --}}
    <x-email.button href="https://ejemplo.com/accion" icon="🚀">
        Realizar Acción
    </x-email.button>

    {{-- Caja de éxito --}}
    <x-email.message-box type="success" title="✅ Operación Exitosa">
        <p style="margin: 0; color: #155724;">
            La operación se completó correctamente.
        </p>
    </x-email.message-box>

    {{-- Caja de información --}}
    <x-email.message-box type="info" title="📋 Información Adicional">
        <p style="margin: 0; color: #1565c0;">
            Aquí puedes agregar información relevante para el usuario.
        </p>
    </x-email.message-box>

    {{-- Caja de advertencia --}}
    <x-email.message-box type="warning" title="⚠️ Advertencia">
        <p style="margin: 0; color: #c53030;">
            Este es un mensaje de advertencia importante.
        </p>
    </x-email.message-box>

    {{-- Caja de highlight/importante --}}
    <x-email.message-box type="highlight" title="⏰ Recordatorio">
        <p style="margin: 0; color: #856404;">
            No olvides completar tu perfil antes del vencimiento.
        </p>
    </x-email.message-box>

    {{-- Botones adicionales con diferentes estilos --}}
    <x-email.button href="https://ejemplo.com/secundaria" type="secondary" icon="📄">
        Acción Secundaria
    </x-email.button>

    <x-email.button href="https://ejemplo.com/peligro" type="danger" icon="⚠️">
        Acción de Peligro
    </x-email.button>
@endsection