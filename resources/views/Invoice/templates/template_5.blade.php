<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura</title>

    <style>
        body{
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size:12px;
            margin:0;
            padding:0;
            color:#222;
            background:#fff;
        }

        .page-wrapper {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            background: white;
        }

        table{ width:100%; border-collapse:collapse; }

        /* --- LAYOUT --- */
        .side{
            width:70px;
            background-color:#222;
            color:#fff;
            vertical-align:top;
            text-align:center;
            position:relative;
        }

        .side-pattern{
            /*height:100%;
            width:100%;*/
            background-color:#222;
        }

        .side-title{
            font-size:18px;
            font-weight:bold;
            letter-spacing:3px;
            padding-top:120px;
            line-height:18px;
        }

        /* --- HEADER TOP ICON BLOCKS --- */
        .topbar{
            padding:16px 20px 10px 20px;
        }

        .mini-box{
            background:#2b2b2b;
            color:#fff;
            padding:10px;
            font-size:10px;
            height:60px;
            vertical-align:top;
        }

        .logo{
            text-align:right;
            font-size:30px;
            font-weight:bold;
            color:#444;
            padding:10px 20px 0 20px;
        }

        .logo img{
            max-width: 120px;
            max-height: 80px;
            height: auto;
            width: auto;
        }

        .brandline{
            background:#ddd;
            height:10px;
        }

        /* --- CONSULTA INFO --- */
        .consulta{
           /* background:#d9d9d9;*/
            padding:18px 20px;
        }

        .consulta-title{
            font-size:16px;
            font-weight:bold;
            color:#444;
            margin-bottom:8px;
        }

        /* --- SERVICES --- */
        .services th{
            background:#333;
            color:#fff;
            padding:8px;
            font-size:11px;
            text-align:left;
        }

        .services td{
            padding:10px 8px;
            border-bottom:1px solid #aaa;
            font-size:11px;
            vertical-align:top;
        }

        .center{ text-align:center; }
        .right{ text-align:right; }

        .code{
            font-weight:bold;
            color:#333;
        }

        /* --- TOTALS BOXES --- */
        .box-total{
            background:#333;
            color:#fff;
            font-weight:bold;
            font-size:11px;
            padding:8px 10px;
        }

        .box-val{
            background:#555;
            color:#fff;
            font-weight:bold;
            font-size:11px;
            padding:8px 10px;
            text-align:right;
        }

        .total-row .box-total{
            background:#222;
            font-size:12px;
        }
        .total-row .box-val{
            background:#222;
            font-size:12px;
        }

        /* --- SECTIONS --- */
        .section-title{
            font-size:13px;
            font-weight:bold;
            color:#555;
            margin-bottom:6px;
        }

        .section{
            padding:10px 20px;
            font-size:11px;
        }

        .terms-title{
            background:#333;
            color:#fff;
            font-weight:bold;
            padding:6px 10px;
            display:inline-block;
            font-size:11px;
            margin-bottom:6px;
        }

        /* --- FOOTER --- */
        .footer{
            padding:12px 20px 20px 20px;
            font-size:10px;
            color:#777;
        }

    </style>
</head>

<body class="page-wrapper">

<table>
    <tr>

        <!-- BARRA IZQUIERDA -->
        <td class="side">
            <div class="side-pattern">
                <div class="side-title">
                    F<br>A<br>C<br>T<br>U<br>R<br>A
                </div>
            </div>
        </td>

        <!-- CONTENIDO -->
        <td>

            <!-- TOP HEADER ICON BLOCKS + LOGO -->
            <table>
                <tr>
                    <td class="topbar" width="65%">
                        <table>
                            <tr>
                                <td class="mini-box" width="50%">
                                   @if($invoice->encounter?->appointment?->consultingRoom?->branch?->address)
                                        {{ $invoice->encounter->appointment->consultingRoom->branch->address }}
                                    @endif
                                       @if($organization->whatsapp ?? false)
                                           <br>Tel: {{ $organization->whatsapp }}
                                       @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td class="logo" width="35%">  @if(is_file(public_path('storage/'.$organization->logo))) <img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('storage/'.$organization->logo)))}}"> @else LOGO @endif</td>
                </tr>
            </table>



            <!-- CONSULTA -->
            <div class="consulta">
                <div class="consulta-title">INFORMACIÓN DE LA CONSULTA</div>
                Consulta ID:{{ $encounter->identifier }}<br>
                Fecha Consulta:{{ $encounter->start ? $encounter->start->format('d/m/Y H:i') : 'N/A' }}<br>
                Médico: {{ $practitioner->name ?? 'N/A' }}<br>
                Licencia Médica: {{ $practitioner->registry }}
            </div>
            <div class="brandline"></div><br/>
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

            <br>

            <!-- PACIENTE + TOTALES -->
            <table>
                <tr>
                    <td width="60%" class="section">
                        <div class="section-title">INFORMACIÓN DEL PACIENTE</div>
                        Nombre: {{ $patient->name }}<br>
                        Identificación:{{ $patient->identifier_type }} {{ $patient->identifier }}<br>
                        Fecha Nacimiento:{{ $patient->birth_date ? \Carbon\Carbon::parse($patient->birth_date)->format('d/m/Y') : 'N/A' }}<br>
                        Teléfono:  {{ $patient->phone }}<br>
                        Email: {{ $patient->email }}
                        <br><br>

                        <div class="section-title">INFORMACIÓN DE FACTURA</div>
                        Número: {{ $invoice->invoice_number }}<br>
                        Fecha Emisión: {{ $invoice->issue_date->format('d/m/Y') }}<br>
                        Fecha Vencimiento: {{ $invoice->due_date->format('d/m/Y') }}<br>
                        Estado: {{ $invoice->payment_status->label() }}<br>
                        Moneda: USD
                    </td>

                    <td width="40%" class="section">
                        <table>
                            <tr>
                                <td class="box-total" width="65%">SUB TOTAL</td>
                                <td class="box-val" width="35%">${{ number_format($subtotal, 2) }}</td>
                            </tr>
                            <tr>
                                <td style="height:8px;"></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="box-total">ITBMS (7%)</td>
                                <td class="box-val">${{ number_format($tax, 2) }}</td>
                            </tr>
                            <tr>
                                <td style="height:8px;"></td>
                                <td></td>
                            </tr>
                            <tr class="total-row">
                                <td class="box-total">TOTAL</td>
                                <td class="box-val"> ${{ number_format($total, 2) }}</td>
                            </tr>
                        </table>

                        <br><br>

                        <div class="terms-title">TÉRMINOS DE PAGO</div><br>
                        Condiciones:{{ $invoice->payment_terms ?? '30 días' }}<br>
                        Vencimiento: {{ $invoice->due_date->format('d/m/Y') }}
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

