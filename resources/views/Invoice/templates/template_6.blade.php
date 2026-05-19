<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura</title>

    <style>
        body{
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
            color: #222;
            background: #fff;
        }

        .page-wrapper {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            background: white;
        }

        table{ width: 100%; border-collapse: collapse; }

        /* Layout */
        .side{
            width: 55px;
            background-color: #3e3e3e;
            vertical-align: top;
        }

        .content{
            vertical-align: top;
        }

        /* Header */
        .header{
            padding: 20px 22px;
        }

        .logo{
            font-size: 32px;
            font-weight: bold;
            color: #444;
            margin-bottom: 12px;
        }

        .block-title{
            font-size: 16px;
            font-weight: bold;
            color: #444;
            margin-bottom: 6px;
        }

        .small{
            font-size: 11px;
            color: #333;
        }

        .invoice-title{
            font-size: 44px;
            font-weight: bold;
            color: #444;
            text-align: right;
            padding-top: 45px;
        }

        .invoice-line{
            border-bottom: 3px solid #444;
            width: 100%;
            margin-top: 5px;
        }

        .contact{
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .contact td{
            font-size: 10px;
            color: #666;
            padding: 6px;
            vertical-align: top;
        }

        .icon{
            width: 18px;
            text-align: center;
            background: #444;
            color: #fff;
            font-weight: bold;
        }

        /* Services table */
        .services th{
            background: #3e3e3e;
            color: #fff;
            padding: 10px 8px;
            font-size: 11px;
            text-align: left;
        }

        .services td{
            padding: 12px 8px;
            font-size: 11px;
            border-bottom: 1px solid #999;
            vertical-align: top;
        }

        .center{ text-align: center; }
        .right{ text-align: right; }

        .desc{
            font-weight: bold;
            color: #333;
            font-size: 10px;
        }

        .code{
            font-weight: bold;
            color: #444;
        }

        /* Section titles */
        .section{
            padding: 15px 22px;
        }

        .section-title{
            font-size: 14px;
            font-weight: bold;
            color: #666;
            margin-bottom: 8px;
        }

        /* Totals boxes */
        .totals{
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .totals td{
            padding: 7px 10px;
            font-weight: bold;
            font-size: 11px;
            color: #fff;
        }

        .totals .label{
            background: #444;
            text-align: right;
            width: 65%;
        }

        .totals .value{
            background: #6a6a6a;
            text-align: right;
            width: 35%;
        }

        .totals .total-label,
        .totals .total-value{
            background: #444;
            font-size: 12px;
        }

        /* Payment terms */
        .terms-title{
            background: #444;
            color: #fff;
            padding: 6px 10px;
            display: inline-block;
            font-size: 11px;
            font-weight: bold;
            margin-top: 18px;
            margin-bottom: 6px;
        }

        /* Footer */
        .footer{
            background: #3e3e3e;
            color: #fff;
            font-size: 10px;
            padding: 14px 22px;
            margin-top: 15px;
        }
    </style>
</head>

<body class="page-wrapper">

<table>
    <tr>

        <!-- Side bar -->
        <td class="side">&nbsp;</td>

        <!-- Main content -->
        <td class="content">

            <!-- HEADER -->
            <table>
                <tr>
                    <td width="60%" class="header">
                        <div class="logo">@if(is_file(storage_path('/app/public/'.$organization->logo))) <img src="{{url('/storage/'.$organization->logo)}}"> @else LOGO @endif</div>

                        <div class="block-title">INFORMACIÓN DE LA CONSULTA</div>
                        <div class="small">
                            Consulta ID:{{ $encounter->identifier }}<br>
                            Fecha Consulta:{{ $encounter->start ? $encounter->start->format('d/m/Y H:i') : 'N/A' }}<br>
                            Médico: {{ $practitioner->name ?? 'N/A' }}<br>
                            Licencia Médica: {{ $practitioner->registry }}
                        </div>
                    </td>

                    <td width="40%" class="header" align="right">
                        <table class="contact">
                            <tr>
                                <td class="icon">✉</td>
                                <td>  @if($organization->whatsapp ?? false)
                                        <br>Tel: {{ $organization->whatsapp }}
                                    @endif</td>
                            </tr>
                            <tr>
                                <td class="icon">📍</td>
                                <td> @if($invoice->encounter?->appointment?->consultingRoom?->branch?->address)
                                        {{ $invoice->encounter->appointment->consultingRoom->branch->address }}
                                    @endif
                                </td>
                            </tr>
                        </table>

                        <div class="invoice-title">
                            FACTURA
                            <div class="invoice-line"></div>
                        </div>
                    </td>
                </tr>
            </table>

            <!-- SERVICES -->
            <table class="services">
                <thead>
                <tr>
                    <th width="5%">&nbsp;</th>
                    <th width="15%">Código</th>
                    <th width="40%">Descripción del Servicio</th>
                    <th width="10%">Precio Unit</th>
                    <th width="10%">Cantidad</th>
                    <th width="20%">Total</th>
                </tr>
                </thead>

                <tbody>
                @foreach($lineItems as $item)
                @php
                    $code = $item->service_code ?? 'N/A';
                    $description = $item->service_description ?? 'N/A';

                    // If description is generic and chargeItem has product_reference, get inventory item
                    if (in_array($description, ['Servicio médico', 'N/A']) &&
                        $item->chargeItem &&
                        is_array($item->chargeItem->product_reference) &&
                        isset($item->chargeItem->product_reference['reference'])) {

                        $reference = $item->chargeItem->product_reference['reference'];
                        if (str_contains($reference, 'InventoryItem/')) {
                            $fhirId = str_replace('InventoryItem/', '', $reference);
                            $inventoryItem = \App\Models\InventoryItem::where('fhir_id', $fhirId)->first();
                            if ($inventoryItem) {
                                $description = $inventoryItem->name;
                                $code = $inventoryItem->sku;
                            }
                        }
                    }
                @endphp
                    <tr>
                        <td class="center">{{$item->sequence}}</td>
                        <td class="code">{{ $code }}</td>
                        <td>{{ $description }}</td>
                        <td class="right">${{ number_format($item->line_total_gross, 2) }}</td>
                        <td class="center">{{$item->quantity}}</td>
                        <td class="right">${{ number_format($item->line_total_gross, 2) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <!-- SECTIONS -->
            <table>
                <tr>
                    <td width="60%" class="section">
                        <div class="section-title">INFORMACIÓN DEL PACIENTE</div>
                        Nombre: {{ $patient->name }}<br>
                        Identificación: {{ $patient->identifier_type }} {{ $patient->identifier }}<br>
                        Fecha Nacimiento:{{ $patient->birth_date ? \Carbon\Carbon::parse($patient->birth_date)->format('d/m/Y') : 'N/A' }}<br>
                        Teléfono: {{ $patient->phone }}<br>
                        Email:  {{ $patient->email }}

                        <br><br>

                        <div class="section-title">INFORMACIÓN DE FACTURA</div>
                        Número:{{ $invoice->invoice_number }}<br>
                        Fecha Emisión: {{ $invoice->issue_date->format('d/m/Y') }}<br>
                        Fecha Vencimiento: {{ $invoice->due_date->format('d/m/Y') }}<br>
                        Estado: {{ $invoice->payment_status->label() }}<br>
                        Moneda: USD
                    </td>

                    <td width="40%" class="section">
                        <table class="totals">
                            <tr>
                                <td class="label">SUB TOTAL</td>
                                <td class="value">${{ number_format($subtotal, 2) }}</td>
                            </tr>
                            <tr><td colspan="2" style="height:8px;"></td></tr>
                            <tr>
                                <td class="label">ITBMS (7%)</td>
                                <td class="value">${{ number_format($tax, 2) }}</td>
                            </tr>
                            <tr><td colspan="2" style="height:8px;"></td></tr>
                            <tr>
                                <td class="total-label label">TOTAL</td>
                                <td class="total-value value">${{ number_format($total, 2) }}</td>
                            </tr>
                        </table>

                        <div class="terms-title">TÉRMINOS DE PAGO</div><br>
                        <span class="small">
                        Condiciones:{{ $invoice->payment_terms ?? '30 días' }}<br>
                        Vencimiento: {{ $invoice->due_date->format('d/m/Y') }}
                    </span>
                    </td>
                </tr>
            </table>

            <!-- FOOTER -->
            <div class="footer">
                Factura generada el  {{ $generateDate }}<br>
                Este documento es válido como comprobante de servicios médicos prestados.
            </div>

        </td>
    </tr>
</table>

</body>
</html>

