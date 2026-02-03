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

        .texto-vertical {
            transform: rotate(-180deg);
            transform-origin: left bottom; /* Ajusta el punto de giro si es necesario */
            white-space: nowrap; /* Evita que el texto se rompa en varias líneas */
        }

        .rotated {
            writing-mode: tb-rl;
            transform: rotate(-90deg);
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

        /* BARRA IZQUIERDA */
        .side {
            background-color: #1f2328;
            color: #ffffff;
            width: 60px;
            text-align: center;
            vertical-align: top;
        }

        .side-title {
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 3px;
            padding-top: 140px;
        }

        .side-date {
            background-color: #2f78ff;
            font-size: 10px;
            color: #ffffff;
        }

        /* HEADER */
        .brand {
            padding: 20px;
            font-size: 22px;
            font-weight: bold;
            color: #2f78ff;
        }

        .info td {
            padding: 10px 20px;
            vertical-align: top;
            font-size: 11px;
        }

        .green {
            color: #008000;
            font-weight: bold;
        }

        .blue {
            color: #2f78ff;
            font-weight: bold;
        }

        .section-title {
            color: #008000;
            font-weight: bold;
            margin-bottom: 6px;
        }

        /* TABLA SERVICIOS */
        .services th {
            padding: 8px;
            border-bottom: 1px solid #999;
            font-size: 11px;
            text-align: left;
        }

        .services td {
            padding: 8px;
            border-bottom: 1px solid #aaa;
            font-size: 11px;
            vertical-align: top;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        /* TOTALES */
        .totals td {
            padding: 6px;
            font-size: 12px;
        }

        .total-final {
            color: #008000;
            font-size: 14px;
            font-weight: bold;
        }

        .payment-title {
            background-color: #2f78ff;
            color: #fff;
            font-size: 11px;
            padding: 6px;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 6px;
        }

        .divider {
            border-left: 2px solid #2f78ff;
            padding-left: 20px;
        }

        .footer {
            font-size: 10px;
            text-align: center;
            padding: 10px;
            margin-top: 10px;
            max-width: 800px;
        }
    </style>
</head>

<body class="page-wrapper">

<table>
    <tr>

        <!-- BARRA IZQUIERDA -->
        <td class="side">
            <div class="side-title">
                F<br>A<br>C<br>T<br>U<br>R<br>A
            </div>


        </td>

        <!-- CONTENIDO -->
        <td>

            <!-- HEADER -->
            <div class="brand">{{ $organization->name ?? 'CLÍNICA MÉDICA' }}</div>

            <!-- INFO -->
            <table class="info">
                <tr>
                    <td width="50%"  class="green">
                        @if($invoice->encounter?->appointment?->consultingRoom?->branch?->address)
                            {{ $invoice->encounter->appointment->consultingRoom->branch->address }}
                        @endif
                        @if($organization->whatsapp ?? false)
                            <br>Tel: {{ $organization->whatsapp }}
                        @endif
                    </td>
                    <td width="50%" class="green">
                        <div class="section-title">INFORMACIÓN DE LA CONSULTA</div>
                        Consulta ID: :{{ $encounter->identifier }}<br>
                        Fecha Consulta: {{ $encounter->start ? $encounter->start->format('d/m/Y H:i') : 'N/A' }}<br>
                        Médico:{{ $practitioner->name ?? 'N/A' }}<br>
                        Licencia Médica: {{ $practitioner->registry }}
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
                    <td class="center blue">{{$item->sequence}}</td>
                    <td class="blue">{{ $item->service_code ?? 'N/A' }}</td>
                    <td class="blue">{{ $item->service_description }} </td>
                    <td class="right blue">${{ number_format($item->line_total_gross, 2) }}</td>
                    <td class="center blue">{{$item->quantity}}</td>
                    <td class="right blue">${{ number_format($item->line_total_gross, 2) }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>

            <br>
            <!-- TERMINOS + TOTALES -->
            <table>
                <tr>
                    <td width="50%">
                        <div class="payment-title">TÉRMINOS DE PAGO</div><br>
                        <span class="green">Condiciones:</span> {{ $invoice->payment_terms ?? '30 días' }}<br>
                        <span class="green">Vencimiento:</span>{{ $invoice->due_date->format('d/m/Y') }}
                    </td>
                    <td width="50%">
                        <table class="totals">
                            <tr>
                                <td class="right green">Subtotal:</td>
                                <td class="right green">${{ number_format($subtotal, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="right green">ITBMS (7%):</td>
                                <td class="right green">${{ number_format($tax, 2) }}</td>
                            </tr>
                            <tr style="border-top:1px solid #008000;">
                                <td class="right total-final green">TOTAL:</td>
                                <td class="right total-final">${{ number_format($total, 2) }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>


        </td>
    </tr>
    <tr>

        <td class="side-date">

                Factura generada  el  {{ $generateDate }}

        </td>

        <td>


            <br>

            <!-- PACIENTE + FACTURA -->
            <table>
                <tr>
                    <td width="50%">
                        <div class="section-title">INFORMACIÓN DEL PACIENTE</div>
                        <span class="green">Nombre:</span>{{ $patient->name }}<br>
                        <span class="green">Identificación:</span>{{ $patient->identifier_type }} {{ $patient->identifier }}<br>
                        <span class="green">Fecha Nacimiento:</span>{{ $patient->birth_date ? \Carbon\Carbon::parse($patient->birth_date)->format('d/m/Y') : 'N/A' }}<br>
                        <span class="green">Teléfono:</span> {{ $patient->phone }}<br>
                        <span class="green">Email:</span> {{ $patient->email }}
                    </td>
                    <td width="50%" class="divider">
                        <div class="section-title">INFORMACIÓN DE FACTURA</div>
                        <span class="green">Número:</span> {{ $invoice->invoice_number }}<br>
                        <span class="green">Fecha Emisión:</span> {{ $invoice->issue_date->format('d/m/Y') }}<br>
                        <span class="green">Fecha Vencimiento:</span> {{ $invoice->due_date->format('d/m/Y') }}<br>
                        <span class="green">Estado:</span> {{ $invoice->payment_status->label() }}<br>
                        <span class="green">Moneda:</span> USD
                    </td>
                </tr>
            </table>
            <br/>
            <div class="footer">
                Este documento es válido como comprobante de servicios médicos prestados.
            </div>
        </td>
    </tr>
</table>

</body>
</html>

