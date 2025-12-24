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
                'Total a pagar' => '$' . number_format($invoice->total, 2),
                'Fecha de vencimiento' => $invoice->due_date->format('d/m/Y')
            ]" />
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
