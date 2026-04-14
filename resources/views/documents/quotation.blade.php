<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cotización {{ $quotation->quotation_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 8pt;
            line-height: 1.3;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 750px;
            margin: 0 auto;
            padding: 10px;
        }

        /* Header */
        .header {
            border-bottom: 2px solid #0066cc;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .header-content {
            display: table;
            width: 100%;
        }

        .logo-section {
            display: table-cell;
            width: 25%;
            vertical-align: middle;
        }

        .logo {
            max-width: 100px;
            height: auto;
        }

        .company-info {
            display: table-cell;
            width: 75%;
            text-align: right;
            vertical-align: middle;
        }

        .company-name {
            font-size: 14pt;
            font-weight: bold;
            color: #0066cc;
            margin-bottom: 4px;
        }

        .company-details {
            font-size: 7pt;
            color: #666;
            line-height: 1.4;
        }

        /* Title */
        .quotation-title {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            color: #0066cc;
            margin: 10px 0;
            text-transform: uppercase;
        }

        /* Info Sections */
        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .info-column {
            display: table-cell;
            width: 50%;
            padding: 3px;
        }

        .info-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 3px;
            padding: 8px;
        }

        .info-title {
            font-weight: bold;
            color: #0066cc;
            margin-bottom: 6px;
            font-size: 9pt;
        }

        .info-row {
            margin-bottom: 3px;
            font-size: 8pt;
        }

        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 100px;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            table-layout: fixed;
        }

        th {
            background-color: #0066cc;
            color: white;
            padding: 5px;
            text-align: left;
            font-weight: bold;
            font-size: 8pt;
        }

        td {
            padding: 4px 5px;
            border-bottom: 1px solid #dee2e6;
            word-wrap: break-word;
            overflow: hidden;
        }

        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        /* Totals */
        .totals-section {
            margin-top: 15px;
            float: right;
            width: 220px;
            max-width: 35%;
        }

        .totals-table {
            width: 100%;
            margin-bottom: 0;
        }

        .totals-table td {
            border: none;
            padding: 3px 5px;
            font-size: 8pt;
        }

        .totals-label {
            text-align: right;
            font-weight: bold;
        }

        .totals-value {
            text-align: right;
            width: 90px;
        }

        .total-final {
            background-color: #0066cc;
            color: white;
            font-weight: bold;
            font-size: 10pt;
        }

        /* Footer */
        .footer {
            clear: both;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #dee2e6;
            font-size: 7pt;
            color: #666;
        }

        .notes {
            margin-top: 10px;
            padding: 8px;
            background-color: #fff3cd;
            border-left: 3px solid #ffc107;
            font-size: 8pt;
        }

        .notes-title {
            font-weight: bold;
            margin-bottom: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <div class="logo-section">
                    @php
                        $logoPath = public_path('images/logoFull.png');
                    @endphp
                    @if(file_exists($logoPath))
                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents($logoPath)) }}"
                             alt="Meditec Soluciones" class="logo">
                    @endif
                </div>
                <div class="company-info">
                    <div class="company-name">MEDITEC SOLUCIONES</div>
                    <div class="company-details">
                        Teléfono: +507 8316174<br>
                        Dirección: Ciudad de Panamá, Panamá<br>
                        Email: business@meditecpty.com<br>
                        Web: www.meditecpty.com
                    </div>
                </div>
            </div>
        </div>

        <!-- Title -->
        <div class="quotation-title">
            Cotización {{ $quotation->quotation_number }}
        </div>

        <!-- Info Section -->
        <div class="info-section">
            <div class="info-column">
                <div class="info-box">
                    <div class="info-title">Datos del Cliente</div>
                    <div class="info-row">
                        <span class="info-label">Razón Social:</span>
                        {{ $quotation->client_company_name }}
                    </div>
                    <div class="info-row">
                        <span class="info-label">RUC:</span>
                        {{ $quotation->client_ruc }}
                    </div>
                    @if($quotation->client_phone)
                    <div class="info-row">
                        <span class="info-label">Teléfono:</span>
                        {{ $quotation->client_phone }}
                    </div>
                    @endif
                    @if($quotation->client_email)
                    <div class="info-row">
                        <span class="info-label">Email:</span>
                        {{ $quotation->client_email }}
                    </div>
                    @endif
                    @if($quotation->client_address)
                    <div class="info-row">
                        <span class="info-label">Dirección:</span>
                        {{ $quotation->client_address }}
                    </div>
                    @endif
                </div>
            </div>
            <div class="info-column">
                <div class="info-box">
                    <div class="info-title">Información de la Cotización</div>
                    <div class="info-row">
                        <span class="info-label">Fecha de Emisión:</span>
                        {{ $pdfService->formatDate($quotation->issue_date) }}
                    </div>
                    <div class="info-row">
                        <span class="info-label">Fecha de Vencimiento:</span>
                        {{ $pdfService->formatDate($quotation->expiration_date) }}
                    </div>
                    <div class="info-row">
                        <span class="info-label">Válida por:</span>
                        {{ $quotation->issue_date->diffInDays($quotation->expiration_date) }} días
                    </div>
                </div>
            </div>
        </div>

        <!-- Services Table -->
        <table>
            <thead>
                <tr>
                    <th style="width: 6%;">#</th>
                    <th style="width: 38%;">Tipo de Servicio</th>
                    <th style="width: 19%; text-align: right;">Precio Unit.</th>
                    <th style="width: 19%; text-align: right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quotation->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $item->service_name }}</strong>
                        @if($item->service_description)
                        <br><small style="color: #666;">{{ $item->service_description }}</small>
                        @endif
                    </td>
                    <td class="text-right">${{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right">${{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td class="totals-label">Subtotal:</td>
                    <td class="totals-value">${{ number_format($quotation->subtotal, 2) }}</td>
                </tr>
                <tr>
                    <td class="totals-label">ITBMS ({{ $quotation->itbms_rate }}%):</td>
                    <td class="totals-value">${{ number_format($quotation->itbms, 2) }}</td>
                </tr>
                <tr class="total-final">
                    <td class="totals-label">TOTAL:</td>
                    <td class="totals-value">${{ number_format($quotation->total, 2) }}</td>
                </tr>
            </table>
        </div>

        <!-- Notes -->
        @if($quotation->notes)
        <div class="notes" style="clear: both; margin-top: 20px;">
            <div class="notes-title">Notas:</div>
            <div>{{ $quotation->notes }}</div>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p style="text-align: center; margin-bottom: 10px;">
                <strong>Términos y Condiciones</strong>
            </p>
            <p style="text-align: justify; font-size: 8pt;">
                Esta cotización es válida por {{ $quotation->issue_date->diffInDays($quotation->expiration_date) }} días
                a partir de la fecha de emisión. Los precios están sujetos a cambios sin previo aviso después de la
                fecha de vencimiento. El ITBMS ({{ $quotation->itbms_rate }}%) está incluido en el total.
                Para proceder con la adquisición de estos servicios, por favor contactar a nuestro departamento
                de ventas. Nos reservamos el derecho de modificar los términos en caso de cambios en las
                regulaciones fiscales o en las condiciones del mercado.
            </p>
            <p style="text-align: center; margin-top: 20px; font-size: 8pt; color: #999;">
                Documento generado el {{ now()->format('d/m/Y H:i:s') }}
            </p>
        </div>
    </div>
</body>
</html>
