<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Factura</title>

<style>
body {
    font-family: DejaVu Sans, Arial, sans-serif;
        font-size: 12px;
        color: #000;
        margin: 0;
        padding: 0;
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

    .header {
    background-color: #1fb0c9;
        color: #ffffff;
    }

    .header td {
    padding: 15px;
        font-size: 18px;
        font-weight: bold;
    }

    .sub-header {
    background-color: #1fb0c9;
        color: #ffffff;
        font-size: 11px;
    }

    .sub-header td {
    padding: 10px;
        vertical-align: top;
    }

    .divider {
    height: 6px;
        background-color: #1c8f2e;
    }

    .section-title {
    color: #1c8f2e;
    font-weight: bold;
        margin-top: 10px;
        margin-bottom: 5px;
    }

    .services th {
    background-color: #e9f6f9;
        padding: 8px;
        border-bottom: 1px solid #ccc;
        text-align: left;
        font-size: 11px;
    }

    .services td {
    padding: 8px;
        border-bottom: 1px solid #ddd;
        font-size: 11px;
        vertical-align: top;
    }

    .right {
    text-align: right;
    }

    .totals {
    margin-top: 10px;
    }

    .totals td {
    padding: 5px;
        font-size: 12px;
    }

    .total-final {
    font-weight: bold;
        font-size: 14px;
    }

    .footer {
    font-size: 10px;
        text-align: center;
        margin-top: 20px;
    }

    .green {
    color: #1c8f2e;
    font-weight: bold;
    }
</style>
</head>

<body class="page-wrapper">

<!-- HEADER -->
<table class="header">
    <tr>
        <td align="left">{{ $organization->name ?? 'CLÍNICA MÉDICA' }}</td>
        <td align="right">FACTURA</td>
    </tr>
</table>

<div class="divider"></div>

<!-- INFO CONSULTA -->
<table class="sub-header">
    <tr>
        <td width="50%">
            @if($invoice->encounter?->appointment?->consultingRoom?->branch?->address)
                {{ $invoice->encounter->appointment->consultingRoom->branch->address }}
            @endif
            @if($organization->whatsapp ?? false)
                <br>Tel: {{ $organization->whatsapp }}
            @endif
</td>
        <td width="50%">
            <strong>INFORMACIÓN DE LA CONSULTA</strong><br><br>
            Consulta ID: {{ $encounter->identifier }}<br>
            Fecha Consulta:{{ $encounter->start ? $encounter->start->format('d/m/Y H:i') : 'N/A' }}<br>
            Médico:{{ $practitioner->name ?? 'N/A' }}<br>
            Licencia Médica: {{ $practitioner->registry }}
</td>
    </tr>
</table>

<br>

<!-- SERVICIOS -->
<table class="services">
    <thead>
        <tr>
            <th width="5%">#</th>
            <th width="15%">Código</th>
            <th width="45%">Descripción del Servicio</th>
            <th width="10%" class="right">Precio Unit</th>
            <th width="10%" class="right">Cantidad</th>
            <th width="15%" class="right">Total</th>
        </tr>
    </thead>
    <tbody>
    @foreach($lineItems as $item)
        <tr>
            <td>{{$item->sequence}}</td>
            <td class="green">{{ $item->service_code ?? 'N/A' }}</td>
            <td>{{ $item->service_description }}</td>
            <td class="right">${{ number_format($item->line_total_gross, 2) }}</td>
            <td class="right">{{$item->quantity}}</td>
            <td class="right">${{ number_format($item->line_total_gross, 2) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<!-- TOTALES -->
<table class="totals">
    <tr>
        <td width="70%"></td>
        <td width="15%" class="right">Subtotal:</td>
        <td width="15%" class="right">${{ number_format($subtotal, 2) }}</td>
    </tr>
    <tr>
        <td></td>
        <td class="right">ITBMS (7%):</td>
        <td class="right">${{ number_format($tax, 2) }}</td>
    </tr>
    <tr>
        <td></td>
        <td class="right total-final">TOTAL:</td>
        <td class="right total-final">${{ number_format($total, 2) }}</td>
    </tr>
</table>

<br>

<!-- PACIENTE & FACTURA -->
<table>
    <tr>
        <td width="50%">
            <div class="section-title">INFORMACIÓN DEL PACIENTE</div>
            <span class="green">Nombre:</span>  {{ $patient->name }}<br>
            <span class="green">Identificación:</span> {{ $patient->identifier_type }} {{ $patient->identifier }}<br>
            <span class="green">Fecha Nacimiento:</span> {{ $patient->birth_date ? \Carbon\Carbon::parse($patient->birth_date)->format('d/m/Y') : 'N/A' }}<br>
            <span class="green">Teléfono:</span>{{ $patient->phone }}<br>
            <span class="green">Email:</span> {{ $patient->email }}
</td>
        <td width="50%">
            <div class="section-title">INFORMACIÓN DE FACTURA</div>
            <span class="green">Número:</span>{{ $invoice->invoice_number }}<br>
            <span class="green">Fecha Emisión:</span>{{ $invoice->issue_date->format('d/m/Y') }}<br>
            <span class="green">Fecha Vencimiento:</span> {{ $invoice->due_date->format('d/m/Y') }}<br>
            <span class="green">Estado:</span> {{ $invoice->payment_status->label() }}<br>
            <span class="green">Moneda:</span> USD
        </td>
    </tr>
</table>

<br>

<!-- TERMINOS -->
<table>
    <tr>
        <td width="60%"></td>
        <td width="40%">
            <div class="section-title">TÉRMINOS DE PAGO</div>
            <span class="green">Condiciones:</span> {{ $invoice->payment_terms ?? '30 días' }}<br>
            <span class="green">Vencimiento:</span> {{ $invoice->due_date->format('d/m/Y') }}
        </td>
    </tr>
</table>

<div class="footer">
Factura generada el {{ $generateDate }}<br>
    Este documento es válido como comprobante de servicios médicos prestados.
</div>

</body>
</html>
