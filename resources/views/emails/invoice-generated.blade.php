@extends('emails.layouts.base', [
    'title' => 'Nueva Factura Generada - ' . $invoice->invoice_number,
    'headerIcon' => '🧾',
    'headerTitle' => 'Nueva Factura Generada',
    'headerSubtitle' => 'Factura de Suscripción - ' . $invoice->invoice_number,
    'headerColor' => '#1E3A8A'
])

@section('content')
    {{-- Mensaje de bienvenida --}}
    <x-email.message-box title="¡Hola!">
        <p style="font-size: 16px; margin: 0;">
            Se ha generado una nueva factura para su suscripción de <strong>{{ $subscription->package->name }}</strong>.
        </p>
    </x-email.message-box>

    {{-- Detalles de la factura --}}
    <x-email.message-box type="info" title="📋 Detalles de la Factura">
        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-top: 15px;">
            <x-email.info-grid :items="[
                'Número de Factura' => $invoice->invoice_number,
                'Período' => $invoice->period_start->format('d/m/Y') . ' - ' . $invoice->period_end->format('d/m/Y'),
                'Fecha de vencimiento' => $invoice->due_date->format('d/m/Y')
            ]" />
        </div>

        {{-- Desglose de Costos --}}
        <div style="background: #ffffff; border: 2px solid #e3f2fd; padding: 20px; border-radius: 8px; margin-top: 20px;">
            <h4 style="color: #1976d2; margin: 0 0 15px; font-size: 16px; border-bottom: 2px solid #e3f2fd; padding-bottom: 10px;">
                💰 Desglose de Costos
            </h4>

            <table style="width: 100%; border-collapse: collapse;">
                <tr style="border-bottom: 1px solid #e0e0e0;">
                    <td style="padding: 10px 0; color: #666;">Subtotal:</td>
                    <td style="padding: 10px 0; text-align: right; font-weight: 600;">${{ number_format($invoice->subtotal, 2) }}</td>
                </tr>
                @if($invoice->discount_amount > 0)
                <tr style="border-bottom: 1px solid #e0e0e0;">
                    <td style="padding: 10px 0; color: #28a745;">Descuento:</td>
                    <td style="padding: 10px 0; text-align: right; font-weight: 600; color: #28a745;">-${{ number_format($invoice->discount_amount, 2) }}</td>
                </tr>
                <tr style="border-bottom: 1px solid #e0e0e0;">
                    <td style="padding: 10px 0; color: #666;">Subtotal después de descuento:</td>
                    <td style="padding: 10px 0; text-align: right; font-weight: 600;">${{ number_format($invoice->subtotal - $invoice->discount_amount, 2) }}</td>
                </tr>
                @endif
                <tr style="border-bottom: 2px solid #1976d2;">
                    <td style="padding: 10px 0; color: #666;">
                        <strong>ITBMS ({{ number_format(($invoice->tax_rate ?? 0.07) * 100, 0) }}%):</strong>
                    </td>
                    <td style="padding: 10px 0; text-align: right; font-weight: 600; color: #1976d2;">
                        <strong>${{ number_format($invoice->tax_amount ?? 0, 2) }}</strong>
                    </td>
                </tr>
                <tr style="background-color: #e3f2fd;">
                    <td style="padding: 15px 10px;">
                        <strong style="font-size: 18px; color: #1976d2;">TOTAL A PAGAR:</strong>
                    </td>
                    <td style="padding: 15px 10px; text-align: right;">
                        <strong style="font-size: 22px; color: #1976d2;">${{ number_format($invoice->total, 2) }}</strong>
                    </td>
                </tr>
            </table>
        </div>
    </x-email.message-box>

    {{-- Botón para ver factura --}}
    <x-email.button href="{{ url('/suscriptions/invoices/' . $invoice->id) }}" icon="👁️">
        Ver Factura Completa
    </x-email.button>

    {{-- Métodos de pago --}}
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
                    'Beneficiario' => 'Soluciones Meditec S.A.'
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

    {{-- Registro de pago manual --}}
    <x-email.message-box type="highlight" title="📸 Registro de Pago Manual">
        <p style="margin: 10px 0; color: #856404;">
            Puede registrar su pago manualmente en la plataforma adjuntando la captura de pantalla de su transacción para una aprobación más rápida.
        </p>
        <ol style="margin: 10px 0; padding-left: 20px; color: #856404;">
            <li style="margin-bottom: 8px;">Acceda a <strong>Suscripciones > Facturas</strong></li>
            <li style="margin-bottom: 8px;">Busque la factura <strong>{{ $invoice->invoice_number }}</strong></li>
            <li style="margin-bottom: 8px;">Haga clic en el ícono azul con el símbolo de tarjeta de crédito 💳</li>
            <li style="margin-bottom: 8px;">Adjunte el comprobante de pago</li>
        </ol>
    </x-email.message-box>

    {{-- Mensaje de agradecimiento --}}
    <div style="text-align: center; margin: 30px 0;">
        <p style="color: #2D1B69; font-size: 18px; font-weight: 600;">
            ¡Gracias por confiar en nosotros! 💚
        </p>
        <p style="color: #6c757d; font-size: 14px; margin-top: 10px;">
            Si tiene alguna pregunta sobre esta factura, no dude en contactarnos.
        </p>
    </div>
@endsection
