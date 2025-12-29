@extends('emails.layouts.base', [
    'title' => 'Nuevo Pago Registrado - ' . $invoice->invoice_number,
    'headerIcon' => '💳',
    'headerTitle' => 'Nuevo Pago Registrado',
    'headerSubtitle' => 'Factura ' . $invoice->invoice_number . ' - ' . $client->name,
    'headerColor' => '#059669'
])

@section('content')
    {{-- Mensaje de bienvenida --}}
    <x-email.message-box title="¡Hola Departamento de Contabilidad!">
        <p style="font-size: 16px; margin: 0;">
            Se ha registrado un nuevo pago para la factura <strong>{{ $invoice->invoice_number }}</strong> del cliente <strong>{{ $client->name }}</strong>.
        </p>
    </x-email.message-box>

    {{-- Detalles del pago --}}
    <x-email.message-box type="success" title="💰 Detalles del Pago">
        <div style="background: #f0fdf4; padding: 15px; border-radius: 8px; margin-top: 15px; border: 2px solid #10b981;">
            <x-email.info-grid :items="[
                'Monto' => '$' . number_format($payment->amount, 2),
                'Método de Pago' => $payment->payment_method->label(),
                'Referencia' => $payment->payment_reference,
                'Fecha de Pago' => $payment->payment_date->format('d/m/Y'),
                'Estado' => $payment->status->label()
            ]" />
        </div>
    </x-email.message-box>

    {{-- Detalles de la factura --}}
    <x-email.message-box type="info" title="📋 Detalles de la Factura">
        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-top: 15px;">
            <x-email.info-grid :items="[
                'Número de Factura' => $invoice->invoice_number,
                'Cliente' => $client->name,
                'Plan' => $invoice->subscription->package->name ?? 'N/A',
                'Total Factura' => '$' . number_format($invoice->total, 2),
                'Saldo Pendiente' => '$' . number_format($invoice->amount_due, 2)
            ]" />
        </div>
    </x-email.message-box>

    @if($payment->receipt_file_path)
        {{-- Comprobante adjunto --}}
        <x-email.message-box type="highlight" title="📎 Comprobante de Pago">
            <p style="margin: 10px 0; color: #856404;">
                ✅ El cliente adjuntó un comprobante de pago. Puede revisarlo en el sistema.
            </p>
        </x-email.message-box>
    @else
        {{-- Sin comprobante --}}
        <div style="background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px; padding: 15px; margin: 20px 0;">
            <p style="margin: 0; color: #856404; font-weight: 600;">
                ⚠️ <strong>NOTA:</strong> Este pago no incluye comprobante adjunto.
            </p>
        </div>
    @endif

    {{-- Información del registro --}}
    <x-email.message-box title="👤 Información del Registro">
        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-top: 15px;">
            <x-email.info-grid :items="[
                'Registrado por' => $registeredBy->full_name,
                'Correo' => $registeredBy->email,
                'Fecha de registro' => now()->format('d/m/Y H:i')
            ]" />
        </div>
    </x-email.message-box>

    @if($payment->status->value === 'pending')
        {{-- Acción requerida --}}
        <x-email.message-box type="highlight" title="⚡ Acción Requerida">
            <p style="margin: 10px 0; color: #856404; font-size: 16px; font-weight: 600;">
                Este pago está en estado <strong>PENDIENTE</strong> y requiere aprobación del departamento de contabilidad.
            </p>
        </x-email.message-box>
    @endif

    {{-- Botón para ver pago --}}
    <x-email.button href="{{ route('suscriptions.payments.show', $payment->id) }}" icon="👁️">
        Ver Detalles del Pago
    </x-email.button>

    {{-- Acciones disponibles --}}
    @if($payment->status->value === 'pending')
        <div style="text-align: center; margin: 30px 0;">
            <p style="color: #6c757d; font-size: 14px;">
                Desde el sistema puede aprobar o rechazar este pago según la verificación realizada.
            </p>
        </div>
    @endif

    {{-- Mensaje de cierre --}}
    <div style="text-align: center; margin: 30px 0;">
        <p style="color: #2D1B69; font-size: 16px; font-weight: 600;">
            Notificación automática del sistema 🤖
        </p>
    </div>
@endsection