<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura {{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 11pt;
            color: #333;
            line-height: 1.4;
        }

        .container {
            padding: 20px;
        }

        .header {
            margin-bottom: 30px;
            border-bottom: 2px solid #0066cc;
            padding-bottom: 20px;
        }

        .header-content {
            display: table;
            width: 100%;
        }

        .logo {
            display: table-cell;
            width: 40%;
            vertical-align: middle;
        }

        .logo img {
            max-width: 180px;
            height: auto;
        }

        .company-info {
            display: table-cell;
            width: 60%;
            text-align: right;
            vertical-align: middle;
        }

        .company-info h2 {
            color: #0066cc;
            font-size: 18pt;
            margin-bottom: 5px;
        }

        .company-info p {
            margin: 2px 0;
            font-size: 9pt;
            color: #666;
        }

        .invoice-header {
            margin-bottom: 30px;
        }

        .invoice-title {
            background-color: #0066cc;
            color: white;
            padding: 10px 15px;
            font-size: 16pt;
            margin-bottom: 20px;
        }

        .invoice-details {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }

        .client-info, .invoice-info {
            display: table-cell;
            width: 50%;
            padding: 10px;
            vertical-align: top;
        }

        .client-info {
            background-color: #f8f9fa;
            border-left: 4px solid #0066cc;
        }

        .info-label {
            font-weight: bold;
            color: #0066cc;
            font-size: 10pt;
            margin-bottom: 5px;
        }

        .info-value {
            margin-bottom: 8px;
            font-size: 10pt;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .items-table thead {
            background-color: #0066cc;
            color: white;
        }

        .items-table th {
            padding: 10px;
            text-align: left;
            font-size: 10pt;
        }

        .items-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #ddd;
            font-size: 10pt;
        }

        .items-table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .totals {
            width: 40%;
            margin-left: auto;
            margin-top: 20px;
        }

        .totals table {
            width: 100%;
            border-collapse: collapse;
        }

        .totals td {
            padding: 8px;
            border-top: 1px solid #ddd;
        }

        .totals .total-row {
            background-color: #0066cc;
            color: white;
            font-weight: bold;
            font-size: 12pt;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 3px;
            font-size: 9pt;
            font-weight: bold;
            color: white;
        }

        .status-pending { background-color: #ffc107; }
        .status-paid { background-color: #28a745; }
        .status-overdue { background-color: #dc3545; }
        .status-partially_paid { background-color: #17a2b8; }
        .status-cancelled { background-color: #6c757d; }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #0066cc;
            text-align: center;
            font-size: 9pt;
            color: #666;
        }

        .payment-info {
            margin-top: 30px;
            padding: 15px;
            background-color: #f8f9fa;
            border-left: 4px solid #28a745;
        }

        .payment-info h4 {
            color: #28a745;
            margin-bottom: 10px;
            font-size: 11pt;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <div class="logo">
                    <img src="{{ public_path('images/logo1.png') }}" alt="Logo">
                </div>
                <div class="company-info">
                    <h2>{{ $company->name }}</h2>
                    <p><strong>RUC:</strong> {{ $company->ruc }} DV {{ $company->dv }}</p>
                    <p><strong>Email:</strong> {{ $company->email }}</p>
                    <p><strong>Teléfono:</strong> {{ $company->whatsapp }}</p>
                </div>
            </div>
        </div>

        <!-- Invoice Title -->
        <div class="invoice-title">
            FACTURA DE SUSCRIPCIÓN
        </div>

        <!-- Invoice Details -->
        <div class="invoice-details">
            <div class="client-info">
                <div class="info-label">FACTURAR A:</div>
                <div class="info-value"><strong>{{ $invoice->client->name }}</strong></div>
                @if($invoice->client->ruc)
                    <div class="info-value">RUC: {{ $invoice->client->ruc }}-{{ $invoice->client->dv }}</div>
                @endif
                <div class="info-value">{{ $invoice->client->email }}</div>
                @if($invoice->client->whatsapp)
                    <div class="info-value">Tel: {{ $invoice->client->whatsapp }}</div>
                @endif
            </div>

            <div class="invoice-info">
                <div class="info-label">INFORMACIÓN DE FACTURA</div>
                <div class="info-value"><strong>Número:</strong> {{ $invoice->invoice_number }}</div>
                <div class="info-value"><strong>Fecha:</strong> {{ $invoice->created_at }}</div>
                <div class="info-value"><strong>Vencimiento:</strong> {{ $invoice->due_date ? $invoice->due_date->format('d/m/Y') : 'N/A' }}</div>
                <div class="info-value"><strong>Estado:</strong> <span class="status-badge status-{{ $invoice->status->value }}">{{ $invoice->status->label() }}</span></div>
                @if($invoice->period_start && $invoice->period_end)
                    <div class="info-value"><strong>Período:</strong> {{ $invoice->period_start->format('d/m/Y') }} - {{ $invoice->period_end->format('d/m/Y') }}</div>
                @endif
            </div>
        </div>

        <!-- Plan Information -->
        @if($invoice->subscription && $invoice->subscription->package)
        <div style="margin-bottom: 20px; padding: 10px; background-color: #e3f2fd; border-left: 4px solid #0066cc;">
            <strong>Plan de Suscripción:</strong> {{ $invoice->subscription->package->name }}
        </div>
        @endif

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th class="text-center" style="width: 10%;">Cant.</th>
                    <th class="text-right" style="width: 15%;">Precio Unit.</th>
                    <th class="text-right" style="width: 15%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoice->items as $item)
                    <tr>
                        <td>{{ $item->description }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">${{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-right">${{ number_format($item->total, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center" style="padding: 20px; color: #999;">
                            No hay items en esta factura
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals">
            <table>
                <tr>
                    <td><strong>Subtotal:</strong></td>
                    <td class="text-right">${{ number_format($invoice->subtotal, 2) }}</td>
                </tr>
                @if($invoice->discount_amount > 0)
                    <tr>
                        <td>
                            <strong>Descuento:</strong>
                            @if($invoice->discount_reason)
                                <br><small style="color: #666;">{{ $invoice->discount_reason }}</small>
                            @endif
                        </td>
                        <td class="text-right" style="color: #28a745;">-${{ number_format($invoice->discount_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td><strong>Subtotal después de descuento:</strong></td>
                        <td class="text-right">${{ number_format($invoice->subtotal - $invoice->discount_amount, 2) }}</td>
                    </tr>
                @endif
                <tr>
                    <td>
                        <strong>ITBMS ({{ number_format(($invoice->tax_rate ?? 0.07) * 100, 0) }}%):</strong>
                    </td>
                    <td class="text-right">${{ number_format($invoice->tax_amount ?? 0, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td><strong>TOTAL A PAGAR:</strong></td>
                    <td class="text-right"><strong>${{ number_format($invoice->total, 2) }}</strong></td>
                </tr>
            </table>
        </div>

        <!-- Payment Information -->
        @if($invoice->payments->count() > 0)
        <div class="payment-info">
            <h4>Historial de Pagos</h4>
            <table style="width: 100%; font-size: 9pt;">
                <tr style="background-color: #e0e0e0;">
                    <th style="padding: 5px; text-align: left;">Fecha</th>
                    <th style="padding: 5px; text-align: left;">Método</th>
                    <th style="padding: 5px; text-align: left;">Referencia</th>
                    <th style="padding: 5px; text-align: right;">Monto</th>
                </tr>
                @foreach($invoice->payments as $payment)
                    <tr>
                        <td style="padding: 5px;">{{ $payment->payment_date->format('d/m/Y') }}</td>
                        <td style="padding: 5px;">{{ ucfirst(str_replace('_', ' ', $payment->payment_method->value)) }}</td>
                        <td style="padding: 5px;">{{ $payment->payment_reference ?? 'N/A' }}</td>
                        <td style="padding: 5px; text-align: right;">${{ number_format($payment->amount, 2) }}</td>
                    </tr>
                @endforeach
                <tr style="font-weight: bold; background-color: #e0e0e0;">
                    <td colspan="3" style="padding: 5px; text-align: right;">Total Pagado:</td>
                    <td style="padding: 5px; text-align: right;">${{ number_format($invoice->getTotalPaid(), 2) }}</td>
                </tr>
                @if($invoice->amount_due > 0)
                    <tr style="font-weight: bold; color: #dc3545;">
                        <td colspan="3" style="padding: 5px; text-align: right;">Saldo Pendiente:</td>
                        <td style="padding: 5px; text-align: right;">${{ number_format($invoice->amount_due, 2) }}</td>
                    </tr>
                @endif
            </table>
        </div>
        @endif

        @if($invoice->notes)
        <div style="margin-top: 20px; padding: 10px; background-color: #fff3cd; border-left: 4px solid #ffc107;">
            <strong>Notas:</strong><br>
            {{ $invoice->notes }}
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p><strong>{{ $company->name }}</strong></p>
            <p>{{ $company->email }} | {{ $company->whatsapp }}</p>
            <p style="margin-top: 10px;">Documento generado electrónicamente el {{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>
</body>
</html>
