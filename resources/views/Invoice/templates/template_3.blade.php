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

        /* HEADER */
        .header {
            background-color: #e6e6e6;
        }

        .header td {
            padding: 20px;
            font-size: 22px;
            font-weight: bold;
            color: #2f78ff;
        }

        .title {
            background-color: #2f78ff;
            color: #ffffff;
            font-size: 13px;
            font-weight: bold;
            padding: 8px;
        }

        .info td {
            padding: 12px;
            vertical-align: top;
            font-size: 11px;
        }

        .blue {
            color: #2f78ff;
            font-weight: bold;
        }

        .services th {
            background-color: #2f78ff;
            color: #ffffff;
            padding: 8px;
            font-size: 11px;
        }

        .services td {
            padding: 8px;
            font-size: 11px;
            border-bottom: 1px solid #999;
            vertical-align: top;
        }

        .light {
            background-color: #f0f2f2;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        .section-title {
            color: #2f78ff;
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

        .total-box {
            background-color: #2f78ff;
            color: #ffffff;
            font-weight: bold;
            padding: 10px;
            font-size: 14px;
        }

        .payment-title {
            background-color: #2f78ff;
            color: #ffffff;
            padding: 6px;
            font-size: 11px;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 6px;
        }

        .footer {
            background-color: #2f78ff;
            color: #ffffff;
            font-size: 10px;
            padding: 10px;
            margin-top: 20px;
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

<!-- INFO -->
<table class="info">
    <tr>
        <td width="50%">
            <div class="title">INFORMACIÓN DE LA CONSULTA</div><br>
            Consulta ID:{{ $encounter->identifier }}<br>
            Fecha Consulta:{{ $encounter->start ? $encounter->start->format('d/m/Y H:i') : 'N/A' }}<br>
            Médico:{{ $practitioner->name ?? 'N/A' }}<br>
            Licencia Médica:  {{ $practitioner->registry }}
        </td>
        <td width="50%">
            @if($invoice->encounter?->appointment?->consultingRoom?->branch?->address)
                {{ $invoice->encounter->appointment->consultingRoom->branch->address }}
            @endif
            @if($organization->whatsapp ?? false)
                <br>Tel: {{ $organization->whatsapp }}
            @endif
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
        <td class="center light">{{$item->sequence}}</td>
        <td class="blue">{{ $item->service_code ?? 'N/A' }}</td>
        <td class="light">{{ $item->service_description }}</td>
        <td class="right blue">${{ number_format($item->line_total_gross, 2) }}</td>
        <td class="center light">{{$item->quantity}}</td>
        <td class="right blue">${{ number_format($item->line_total_gross, 2) }}</td>
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
            <span class="green">Nombre:</span{{ $patient->name }}<br>
            <span class="green">Identificación:</span>{{ $patient->identifier_type }} {{ $patient->identifier }}<br>
            <span class="green">Fecha Nacimiento:</span>{{ $patient->birth_date ? \Carbon\Carbon::parse($patient->birth_date)->format('d/m/Y') : 'N/A' }}<br>
            <span class="green">Teléfono:</span> {{ $patient->phone }}<br>
            <span class="green">Email:</span> {{ $patient->email }}
        </td>
        <td width="40%">
            <table class="totals">
                <tr>
                    <td class="right">Subtotal:</td>
                    <td class="right">${{ number_format($subtotal, 2) }}</td>
                </tr>
                <tr>
                    <td class="right">ITBMS (7%):</td>
                    <td class="right">${{ number_format($tax, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="2" class="right">
                        <div class="total-box">TOTAL: ${{ number_format($total, 2) }}</div>
                    </td>
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
            <span class="green">Fecha Vencimiento:</span>  {{ $invoice->due_date->format('d/m/Y') }}<br>
            <span class="green">Estado:</span> {{ $invoice->payment_status->label() }}<br>
            <span class="green">Moneda:</span> USD
        </td>
        <td width="40%">
            <div class="payment-title">TÉRMINOS DE PAGO</div><br>
            <span class="green">Condiciones:</span> {{ $invoice->payment_terms ?? '30 días' }}<br>
            <span class="green">Vencimiento:</span> {{ $invoice->due_date->format('d/m/Y') }}
        </td>
    </tr>
</table>

<!-- FOOTER -->
<table class="footer">
    <tr>
        <td width="50%">Factura generada el {{ $generateDate }}</td>
        <td width="50%" align="right">
            Este documento es válido como comprobante de servicios médicos prestados.
        </td>
    </tr>
</table>

</body>
</html>

