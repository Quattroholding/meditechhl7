@extends('emails.layouts.base', [
    'title' => 'Pago Rechazado - ' . $invoice->invoice_number,
    'headerIcon' => '❌',
    'headerTitle' => 'Pago Rechazado',
    'headerSubtitle' => 'Factura ' . $invoice->invoice_number,
    'headerColor' => '#dc3545'
])

@section('content')
    {{-- Mensaje de rechazo --}}
    <x-email.message-box title="Su pago ha sido rechazado">
        <p style="font-size: 16px; margin: 0; color: #721c24;">
            Lamentamos informarle que su pago de <strong>${{ number_format($payment->amount, 2) }}</strong> para la factura <strong>{{ $invoice->invoice_number }}</strong> ha sido <strong>rechazado</strong>.
        </p>
    </x-email.message-box>

    {{-- Motivo del rechazo --}}
    <x-email.message-box type="highlight" title="📋 Motivo del Rechazo">
        <div style="background: #fff3cd; padding: 20px; border-radius: 8px; border-left: 4px solid #ffc107; margin-top: 15px;">
            <p style="margin: 0; color: #856404; font-size: 15px; line-height: 1.6;">
                <strong>{{ $rejectionReason }}</strong>
            </p>
        </div>
    </x-email.message-box>

    {{-- Detalles del pago rechazado --}}
    <x-email.message-box type="info" title="💳 Detalles del Pago Rechazado">
        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-top: 15px;">
            <x-email.info-grid :items="[
                'Monto' => '$' . number_format($payment->amount, 2),
                'Método de Pago' => $payment->payment_method->label(),
                'Referencia' => $payment->payment_reference,
                'Fecha de Pago' => $payment->payment_date->format('d/m/Y')
            ]" />
        </div>
    </x-email.message-box>

    {{-- Detalles de la factura --}}
    <x-email.message-box type="info" title="📄 Detalles de la Factura">
        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-top: 15px;">
            <x-email.info-grid :items="[
                'Número de Factura' => $invoice->invoice_number,
                'Plan' => $invoice->subscription->package->name ?? 'N/A',
                'Total Factura' => '$' . number_format($invoice->total, 2),
                'Saldo Pendiente' => '$' . number_format($invoice->amount_due, 2),
                'Fecha de Vencimiento' => $invoice->due_date->format('d/m/Y')
            ]" />
        </div>
    </x-email.message-box>

    {{-- Botón para ver factura --}}
    <x-email.button href="{{ route('suscriptions.invoices.show', $invoice->id) }}" icon="👁️">
        Ver Factura Completa
    </x-email.button>

    {{-- Instrucciones para volver a pagar --}}
    <x-email.message-box type="success" title="💡 ¿Qué hacer ahora?">
        <div style="margin-top: 15px;">
            <ol style="margin: 10px 0; padding-left: 20px; color: #155724; line-height: 1.8;">
                <li style="margin-bottom: 10px;">
                    <strong>Revise el motivo del rechazo</strong> indicado arriba.
                </li>
                <li style="margin-bottom: 10px;">
                    <strong>Corrija el problema</strong> identificado (por ejemplo, verificar el monto, usar el método de pago correcto, etc.).
                </li>
                <li style="margin-bottom: 10px;">
                    <strong>Ingrese a su cuenta</strong> en la plataforma.
                </li>
                <li style="margin-bottom: 10px;">
                    Vaya a <strong>Suscripciones > Facturas</strong>.
                </li>
                <li style="margin-bottom: 10px;">
                    Busque la factura <strong>{{ $invoice->invoice_number }}</strong>.
                </li>
                <li style="margin-bottom: 10px;">
                    Haga clic en el ícono azul con el símbolo de tarjeta de crédito <strong>💳</strong> para registrar un nuevo pago.
                </li>
                <li style="margin-bottom: 10px;">
                    <strong>Adjunte el comprobante de pago</strong> para una aprobación más rápida.
                </li>
            </ol>
        </div>
    </x-email.message-box>

    {{-- Métodos de pago disponibles --}}
    <x-email.message-box type="success" title="💳 Métodos de Pago Disponibles">
        <div style="margin-top: 15px;">
            <h4 style="color: #155724; margin: 15px 0 10px; font-size: 16px;">
                <strong>1. ACH - Transferencia Bancaria</strong>
            </h4>
            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                <x-email.info-grid :items="[
                    'Banco' => 'Banco General',
                    'Cuenta' => '04-99-99-999999-9',
                    'Tipo' => 'Cuenta Corriente',
                    'Beneficiario' => 'Meditech S.A.'
                ]" />
            </div>

            <h4 style="color: #155724; margin: 25px 0 10px; font-size: 16px;">
                <strong>2. YAPPY</strong>
            </h4>
            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                <x-email.info-grid :items="[
                    'Número' => '6XXX-XXXX',
                    'Nombre' => 'Meditech',
                    'Código YAPPY' => $client->yappy_code
                ]" />
            </div>

            <div style="background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px; padding: 15px; margin-top: 15px;">
                <p style="margin: 0; color: #856404; font-weight: 600;">
                    ⚠️ <strong>IMPORTANTE:</strong> En el mensaje de la transferencia YAPPY incluya su código (<strong>{{ $client->yappy_code }}</strong>) para identificar el pago.
                </p>
            </div>
        </div>
    </x-email.message-box>

    {{-- Mensaje de apoyo --}}
    <div style="background: #e7f3ff; border: 1px solid #b3d9ff; border-radius: 8px; padding: 20px; margin: 25px 0;">
        <p style="margin: 0; color: #004085; font-size: 15px; line-height: 1.6;">
            <strong>💬 ¿Necesita ayuda?</strong><br>
            Si tiene alguna pregunta sobre el motivo del rechazo o necesita asistencia para realizar el pago, no dude en contactarnos. Estamos aquí para ayudarle.
        </p>
    </div>

    {{-- Mensaje de cierre --}}
    <div style="text-align: center; margin: 30px 0;">
        <p style="color: #2D1B69; font-size: 18px; font-weight: 600;">
            Gracias por su comprensión 🙏
        </p>
        <p style="color: #6c757d; font-size: 14px; margin-top: 10px;">
            Esperamos recibir su pago pronto.
        </p>
    </div>
@endsection
