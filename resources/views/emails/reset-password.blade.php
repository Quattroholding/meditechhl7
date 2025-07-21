@extends('emails.layouts.base', [
    'title' => 'Restablecer Contraseña - ' . config('app.name'),
    'headerIcon' => '🔐',
    'headerTitle' => 'Restablecer Contraseña'
])

@section('content')
    <x-email.message-box title="¡Hola!">
        <p style="font-size: 16px; margin: 0;">
            Has recibido este correo porque se ha solicitado restablecer la contraseña de tu cuenta.
        </p>
    </x-email.message-box>

    <x-email.button href="{{ $actionUrl }}" icon="🔑">
        Restablecer Contraseña
    </x-email.button>

    <x-email.message-box type="highlight" title="⏰ Importante">
        <p style="margin: 0; color: #856404;">
            Este enlace expirará en {{ $expireTime ?? '60 minutos' }}. Si no has solicitado restablecer tu contraseña, puedes ignorar este correo.
        </p>
    </x-email.message-box>

    <x-email.message-box type="warning" title="🛡️ Seguridad">
        <p style="margin: 0; color: #c53030;">
            Si no puedes hacer clic en el botón, copia y pega el siguiente enlace en tu navegador:
        </p>
        <p style="word-break: break-all; color: #4299e1; margin: 10px 0 0;">
            {{ $actionUrl }}
        </p>
    </x-email.message-box>
@endsection