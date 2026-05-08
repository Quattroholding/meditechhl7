<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo de Pago - {{ $payment->payment_reference ?? $payment->id }}</title>
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
            border-bottom: 2px solid #28a745;
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
            color: #28a745;
            font-size: 18pt;
            margin-bottom: 5px;
        }

        .company-info p {
            margin: 2px 0;
            font-size: 9pt;
            color: #666;
        }

        .receipt-title {
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            font-size: 16pt;
            margin-bottom: 20px;
            text-align: center;
        }

        .receipt-details {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }

        .client-info, .payment-info {
            display: table-cell;
            width: 50%;
            padding: 15px;
            vertical-align: top;
        }

        .client-info {
            background-color: #f8f9fa;
            border-left: 4px solid #28a745;
        }

        .payment-info {
            padding-left: 20px;
        }

        .info-label {
            font-weight: bold;
            color: #28a745;
            font-size: 10pt;
            margin-bottom: 5px;
        }

        .info-value {
            margin-bottom: 8px;
            font-size: 10pt;
        }

        .amount-box {
            background-color: #d4edda;
            border: 2px solid #28a745;
            padding: 20px;
            text-align: center;
            margin: 30px 0;
        }

        .amount-label {
            font-size: 12pt;
            color: #28a745;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .amount-value {
            font-size: 24pt;
            color: #28a745;
            font-weight: bold;
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
        .status-completed { background-color: #28a745; }
        .status-failed { background-color: #dc3545; }
        .status-refunded { background-color: #17a2b8; }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .details-table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .details-table td:first-child {
            font-weight: bold;
            color: #666;
            width: 35%;
        }

        .invoice-box {
            margin-top: 30px;
            padding: 15px;
            background-color: #e7f3ff;
            border-left: 4px solid #0066cc;
        }

        .invoice-box h4 {
            color: #0066cc;
            margin-bottom: 10px;
            font-size: 11pt;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #28a745;
            text-align: center;
            font-size: 9pt;
            color: #666;
        }

        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80pt;
            color: rgba(40, 167, 69, 0.05);
            font-weight: bold;
            z-index: -1;
        }
    </style>
</head>
<body>
    <div class="watermark">PAGADO</div>

    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <div class="logo">
                    <img src="{{ public_path('images/logo1.png') }}" alt="Logo">
                </div>
                <div class="company-info">
                    <h2>{{ $company->name }}</h2>
                    <p><strong>RUC:</strong> {{ $company->ruc }}-{{ $company->dv }}</p>
                    <p><strong>Email:</strong> {{ $company->email }}</p>
                    <p><strong>Teléfono:</strong> {{ $company->whatsapp }}</p>
                </div>
            </div>
        </div>

        <!-- Receipt Title -->
        <div class="receipt-title">
            RECIBO DE PAGO
        </div>

        <!-- Receipt Details -->
        <div class="receipt-details">
            <div class="client-info">
                <div class="info-label">PAGADO POR:</div>
                <div class="info-value"><strong>{{ $payment->invoice->client->name }}</strong></div>
                @if($payment->invoice->client->ruc)
                    <div class="info-value">RUC: {{ $payment->invoice->client->ruc }}-{{ $payment->invoice->client->dv }}</div>
                @endif
                <div class="info-value">{{ $payment->invoice->client->email }}</div>
                @if($payment->invoice->client->whatsapp)
                    <div class="info-value">Tel: {{ $payment->invoice->client->whatsapp }}</div>
                @endif
            </div>

            <div class="payment-info">
                <div class="info-label">INFORMACIÓN DEL PAGO</div>
                <div class="info-value"><strong>Referencia:</strong> {{ $payment->payment_reference ?? 'N/A' }}</div>
                <div class="info-value"><strong>Fecha:</strong> {{ $payment->payment_date->format('d/m/Y') }}</div>
                <div class="info-value"><strong>Estado:</strong> <span class="status-badge status-{{ $payment->status->value }}">{{ $payment->status->label() }}</span></div>
                @if($payment->processedBy)
                    <div class="info-value"><strong>Procesado por:</strong> {{ $payment->processedBy->name }}</div>
                @endif
            </div>
        </div>

        <!-- Amount Box -->
        <div class="amount-box">
            <div class="amount-label">MONTO PAGADO</div>
            <div class="amount-value">${{ number_format($payment->amount, 2) }}</div>
        </div>

        <!-- Payment Details -->
        <table class="details-table">
            <tr>
                <td>Método de Pago:</td>
                <td>
                    @php
                        $methodLabels = [
                            'cash' => 'Efectivo',
                            'bank_transfer' => 'Transferencia Bancaria',
                            'credit_card' => 'Tarjeta de Crédito',
                            'yappy' => 'Yappy',
                            'ach' => 'ACH',
                        ];
                    @endphp
                    <strong>{{ $methodLabels[$payment->payment_method->value] ?? $payment->payment_method->value }}</strong>
                </td>
            </tr>
            @if($payment->payment_gateway)
            <tr>
                <td>Plataforma de Pago:</td>
                <td>{{ ucfirst($payment->payment_gateway) }}</td>
            </tr>
            @endif
            @if($payment->gateway_transaction_id)
            <tr>
                <td>ID de Transacción:</td>
                <td><code style="background-color: #f0f0f0; padding: 3px 8px; border-radius: 3px;">{{ $payment->gateway_transaction_id }}</code></td>
            </tr>
            @endif
            <tr>
                <td>Fecha de Registro:</td>
                <td>{{ $payment->created_at }}</td>
            </tr>
        </table>

        @if($payment->notes)
        <div style="margin-top: 20px; padding: 10px; background-color: #fff3cd; border-left: 4px solid #ffc107;">
            <strong>Notas:</strong><br>
            {{ $payment->notes }}
        </div>
        @endif

        <!-- Invoice Information -->
        <div class="invoice-box">
            <h4>Factura Relacionada</h4>
            <table style="width: 100%; font-size: 10pt;">
                <tr>
                    <td style="padding: 5px;"><strong>Número de Factura:</strong></td>
                    <td style="padding: 5px;">{{ $payment->invoice->invoice_number }}</td>
                </tr>
                <tr>
                    <td style="padding: 5px;"><strong>Plan:</strong></td>
                    <td style="padding: 5px;">{{ $payment->invoice->subscription->package->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="padding: 5px;"><strong>Total de Factura:</strong></td>
                    <td style="padding: 5px;">${{ number_format($payment->invoice->total, 2) }}</td>
                </tr>
                <tr>
                    <td style="padding: 5px;"><strong>Total Pagado:</strong></td>
                    <td style="padding: 5px; color: #28a745; font-weight: bold;">${{ number_format($payment->invoice->getTotalPaid(), 2) }}</td>
                </tr>
                @if($payment->invoice->amount_due > 0)
                <tr>
                    <td style="padding: 5px;"><strong>Saldo Pendiente:</strong></td>
                    <td style="padding: 5px; color: #dc3545; font-weight: bold;">${{ number_format($payment->invoice->amount_due, 2) }}</td>
                </tr>
                @else
                <tr>
                    <td colspan="2" style="padding: 10px; background-color: #d4edda; color: #155724; text-align: center; font-weight: bold;">
                        ✓ FACTURA PAGADA COMPLETAMENTE
                    </td>
                </tr>
                @endif
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>{{ $company->name }}</strong></p>
            <p>{{ $company->email }} | {{ $company->whatsapp }}</p>
            <p style="margin-top: 10px;">Este es un comprobante de pago válido</p>
            <p>Documento generado electrónicamente el {{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>
</body>
</html>
