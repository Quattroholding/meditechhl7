<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura</title>

    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
            color: #000;
        }

        .page-wrapper {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            background: white;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        /* HEADER GRADIENT SIMULADO */
        .header-top {
            background-color: #003b8f;
            color: #fff;
        }

        .header-top td {
            padding: 18px;
            font-size: 22px;
            font-weight: bold;
            background:  linear-gradient(to right,  #00aaa2,#003b8f);
        }

        .header-brand {
            background-color: #00aaa2;
            color: #ffffff;

        }

        .header-brand td {
            padding: 12px;
            font-size: 16px;
            font-weight: bold;
        }

        .info td {
            padding: 15px;
            vertical-align: top;
            font-size: 11px;
        }

        .info-title {
            color: #0070c0;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .services th {
            background-color: #d9d9d9;
            padding: 8px;
            font-size: 11px;
            border-bottom: 1px solid #aaa;
        }

        .services td {
            padding: 8px;
            font-size: 11px;
            border-bottom: 1px solid #ccc;
            vertical-align: top;
        }

        .code {
            color: #00aaa2;
            font-weight: bold;
        }

        .right {
            text-align: right;
            color: #00aaa2;
        }

        .center {
            text-align: center;
            background: #f4f6f4;
            color: #00a99d;
        }

        .section-title {
            color: #00a99d;
            font-weight: bold;
            margin-bottom: 6px;
        }

        .green {
            color: #008000;
            font-weight: bold;
        }

        .totals td {
            padding: 6px;
            font-size: 12px;
        }

        .total-final {
            font-size: 14px;
            font-weight: bold;
            border-top: 1px solid #000;
        }

        .terms {
            background-color: #00aaa2;
            color: #fff;
            padding: 4px 8px;
            font-size: 11px;
            display: inline-block;
            margin-bottom: 6px;
            font-weight: bold;
        }

        .top-footer{
            position: absolute;
            bottom: 50px;
            width: 100%;
            background-color: #edefec;
            padding:12px;
            width: 100%;
            max-width: 800px;
        }

        .footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            max-width: 800px;
            background-color: #00aaa2;
            color: #ffffff;
            text-align: left;
            font-size: 10px;
            padding: 12px;
            margin-top: 20px;
        }

        .text-black{
            color: #000000;
            font-weight: bold;
        }
    </style>
</head>

<body class="page-wrapper">

<!-- HEADER -->
<table class="header-top">
    <tr>
        <td align="right">FACTURA</td>
    </tr>
</table>

<table class="header-brand">
    <tr>
        <td>{{ strtoupper($organization->name) ?? 'CLÍNICA MÉDICA' }}</td>
    </tr>
</table>

<!-- INFO -->
<table class="info">
    <tr>
        <td width="65%">
            @if($invoice->encounter?->appointment?->consultingRoom?->branch?->address)
                {{ $invoice->encounter->appointment->consultingRoom->branch->address }}
            @endif
            @if($organization->whatsapp ?? false)
                <br>Tel: {{ $organization->whatsapp }}
            @endif
        </td>
        <td width="35%">
            <div class="info-title">INFORMACIÓN DE LA CONSULTA</div>
            <b>Consulta ID:</b> {{ $encounter->identifier }}<br>
            <b>Fecha Consulta:</b>{{ $encounter->start ? $encounter->start->format('d/m/Y H:i') : 'N/A' }}<br>
            <b>Médico:</b>{{ $practitioner->name ?? 'N/A' }}<br>
            <b>Licencia Médica:</b>  {{ $practitioner->registry }}
        </td>
    </tr>
</table>

<!-- SERVICIOS -->
<table class="services">
    <thead>
    <tr>
        <th width="5%">#</th>
        <th width="15%">Código</th>
        <th width="45%">Descripción del Servicio</th>
        <th width="10%">Precio Unit</th>
        <th width="10%">Cantidad</th>
        <th width="15%">Total</th>
    </tr>
    </thead>
    <tbody>
    @foreach($lineItems as $item)
    <tr>
        <td class="center">{{$item->sequence}}</td>
        <td class="code">{{ $item->service_code ?? 'N/A' }}</td>
        <td class="center">{{ $item->service_description }} </td>
        <td class="right">${{ number_format($item->line_total_gross, 2) }}</td>
        <td class="center">{{$item->quantity}}</td>
        <td class="right">${{ number_format($item->line_total_gross, 2) }}</td>
    </tr>
    @endforeach
    </tbody>
</table>

<br>

<!-- PACIENTE + TOTALES -->
<table>
    <tr>
        <td width="60%">
            <div class="section-title">INFORMACIÓN DEL PACIENTE</div>
            <span class="green">Nombre:</span>{{ $patient->name }}<br>
            <span class="green">Identificación:</span> {{ $patient->identifier_type }} {{ $patient->identifier }}<br>
            <span class="green">Fecha Nacimiento:</span>{{ $patient->birth_date ? \Carbon\Carbon::parse($patient->birth_date)->format('d/m/Y') : 'N/A' }}<br>
            <span class="green">Teléfono:</span>{{ $patient->phone }}<br>
            <span class="green">Email:</span> {{ $patient->email }}
        </td>
        <td width="40%">
            <table class="totals">
                <tr>
                    <td class="right text-black">Subtotal:</td>
                    <td class="right text-black">${{ number_format($subtotal, 2) }}</td>
                </tr>
                <tr>
                    <td class="right text-black">ITBMS (7%):</td>
                    <td class="right text-black">${{ number_format($tax, 2) }}</td>
                </tr>
                <tr>
                    <td class="right total-final text-black">TOTAL:</td>
                    <td class="right total-final text-black">${{ number_format($total, 2) }}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<br>

<!-- FACTURA + TERMINOS -->
<table>
    <tr>
        <td width="60%">
            <div class="section-title">INFORMACIÓN DE FACTURA</div>
            <span class="green">Número:</span>{{ $invoice->invoice_number }}<br>
            <span class="green">Fecha Emisión:</span>{{ $invoice->issue_date->format('d/m/Y') }}<br>
            <span class="green">Fecha Vencimiento:</span> {{ $invoice->due_date->format('d/m/Y') }}<br>
            <span class="green">Estado:</span> {{ $invoice->payment_status->label() }}<br>
            @php
                $paymentMethod = $invoice->payment_method;
                $paymentRef = $invoice->payment_reference;
                $transactionId = null;
                $paymentNotes = null;

                if ($invoice->payments && $invoice->payments->count() > 0) {
                    $lastPayment = $invoice->payments->first();
                    if (!$paymentMethod) $paymentMethod = $lastPayment->payment_method_label;
                    if (!$paymentRef) $paymentRef = $lastPayment->reference_number;
                    $transactionId = $lastPayment->transaction_id;
                    $paymentNotes = $lastPayment->notes;
                }
            @endphp
            @if($paymentMethod)
                <span class="green">Método de Pago:</span> {{ $paymentMethod }}<br>
            @endif
            @if($paymentRef)
                <span class="green">Referencia:</span> {{ $paymentRef }}<br>
            @endif
            @if($transactionId)
                <span class="green">Transacción ID:</span> {{ $transactionId }}<br>
            @endif
            @if($paymentNotes)
                <span class="green">Notas de Pago:</span> {{ $paymentNotes }}<br>
            @endif
            <span class="green">Moneda:</span> USD
        </td>
        <td width="40%">
            <div class="terms">TÉRMINOS DE PAGO</div><br>
            <span class="green">Condiciones:</span> {{ $invoice->payment_terms ?? '30 días' }}<br>
            <span class="green">Vencimiento:</span> {{ $invoice->due_date->format('d/m/Y') }}
        </td>
    </tr>
</table>

<!-- FOOTER -->
<div class="top-footer">
</div>
<div class="footer">
    Factura generada el {{ $generateDate }}<br>
    Este documento es válido como comprobante de servicios médicos prestados.
</div>

</body>
</html>

