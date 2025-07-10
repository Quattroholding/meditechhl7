<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura {{ $invoice->identifier }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 15px;
        }

        .company-info {
            margin-bottom: 10px;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 5px;
        }

        .invoice-title {
            font-size: 18px;
            font-weight: bold;
            color: #dc3545;
            margin-top: 15px;
        }

        .invoice-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .patient-info, .invoice-info {
            width: 100%;
        }

        .section-title {
            font-weight: bold;
            font-size: 14px;
            color: #007bff;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        .info-row {
            margin-bottom: 5px;
        }

        .label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }

        .services-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .services-table th {
            background-color: #007bff;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }

        .services-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #dee2e6;
        }

        .services-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .totals-section {
            width: 300px;
            margin-left: auto;
            margin-top: 20px;
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 5px 10px;
            border-top: 1px solid #dee2e6;
        }

        .total-row {
            font-weight: bold;
            font-size: 14px;
            background-color: #f8f9fa;
        }

        .payment-info {
            margin-top: 30px;
            padding: 15px;
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #6c757d;
        }

        .encounter-info {
            background-color: #e3f2fd;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body style="max-width: 800px;margin: 0 auto;">
    <div class="header">
        <div class="company-info">
            <div class="company-name">{{ $organization->name ?? 'Centro Médico' }}</div>
            @if($invoice->encounter->appointment->consultingRoom->branch->address)
                <div>{{ $invoice->encounter->appointment->consultingRoom->branch->address }}</div>
            @endif
            @if($organization->whatsapp ?? false)
                <div>Tel: {{ $organization->whatsapp }}</div>
            @endif
            @if($organization->email ?? false)
                <div>Email: {{ $organization->email }}</div>
            @endif
        </div>
        <div class="invoice-title">FACTURA MÉDICA</div>
    </div>

    <div class="invoice-details">
        <table style="width: 100%">
            <tr>
                <td>
                    <div class="patient-info">
                        <div class="section-title">INFORMACIÓN DEL PACIENTE</div>
                        <div class="info-row">
                            <span class="label">Nombre:</span>
                            {{ $patient->name }}
                        </div>
                        <div class="info-row">
                            <span class="label">Identificación:</span>
                            {{ $patient->identifier_type }}    {{ $patient->identifier }}
                        </div>
                        <div class="info-row">
                            <span class="label">Fecha Nacimiento:</span>
                            {{ $patient->birth_date ? $patient->birth_date : 'N/A' }}
                        </div>
                        @if($patient->phone)
                            <div class="info-row">
                                <span class="label">Teléfono:</span>
                                {{ $patient->phone }}
                            </div>
                        @endif
                        @if($patient->email)
                            <div class="info-row">
                                <span class="label">Email:</span>
                                {{ $patient->email }}
                            </div>
                        @endif
                    </div>
                </td>
                <td>
                    <div class="invoice-info">
                        <div class="section-title">INFORMACIÓN DE FACTURA</div>
                        <div class="info-row">
                            <span class="label">Número:</span>
                            {{ $invoice->identifier }}
                        </div>
                        <div class="info-row">
                            <span class="label">Fecha Emisión:</span>
                            {{ $invoice->issue_date->format('d/m/Y') }}
                        </div>
                        <div class="info-row">
                            <span class="label">Fecha Vencimiento:</span>
                            {{ $invoice->due_date->format('d/m/Y') }}
                        </div>
                        <div class="info-row">
                            <span class="label">Estado:</span>
                            <span style="color: {{ $invoice->payment_status === 'paid' ? 'green' : ($invoice->payment_status === 'partial' ? 'orange' : 'red') }};">
                    {{ ucfirst($invoice->payment_status) }}
                </span>
                        </div>
                        <div class="info-row">
                            <span class="label">Moneda:</span>
                            {{ strtoupper($invoice->currency) }}
                        </div>
                    </div>
                </td>
            </tr>
        </table>


    </div>

    @if($encounter)
        <div class="encounter-info">
            <div class="section-title">INFORMACIÓN DE LA CONSULTA</div>
            <div class="info-row">
                <span class="label">Consulta ID:</span>
                {{ $encounter->identifier }}
            </div>
            <div class="info-row">
                <span class="label">Fecha Consulta:</span>
                {{ $encounter->start ? $encounter->start->format('d/m/Y H:i') : 'N/A' }}
            </div>
            <div class="info-row">
                <span class="label">Médico:</span>
                {{ $practitioner->name }}
            </div>
            @if($practitioner->registry ?? false)
                <div class="info-row">
                    <span class="label">Licencia Médica:</span>
                    {{ $practitioner->registry }}
                </div>
            @endif
        </div>
    @endif

    <table class="services-table" border="1">
        <thead>
            <tr>
                <th style="width: 10%;">#</th>
                <th style="width: 15%;text-align: center">Código</th>
                <th style="width: 40%;text-align: center">Descripción del Servicio</th>
                <th style="width: 10%;text-align: center">Cantidad</th>
                <th style="width: 12%;text-align: center">Precio Unit.</th>
                <th style="width: 13%;text-align: center">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lineItems as $index => $item)
                <tr>
                    <td >{{ $index + 1 }}</td>
                    <td class="text-center">{{ $item->service_code ?? 'N/A' }}</td>
                    <td>
                        {{ $item->service_description }}
                        @if($item->service_date && $item->service_date != $invoice->date)
                            <br><small style="color: #6c757d;">Fecha servicio: {{ $item->service_date->format('d/m/Y') }}</small>
                        @endif
                    </td>
                    <td class="text-center">{{ number_format($item->quantity, 2) }}</td>
                    <td class="text-right">${{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right">${{ number_format($item->line_total_gross, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals-section">
        <table class="totals-table">
            <tr>
                <td>Subtotal:</td>
                <td class="text-right">${{ number_format($subtotal, 2) }}</td>
            </tr>
            @if($tax > 0)
                <tr>
                    <td>ITBMS (7%):</td>
                    <td class="text-right">${{ number_format($tax, 2) }}</td>
                </tr>
            @endif
            <tr class="total-row">
                <td>TOTAL:</td>
                <td class="text-right">${{ number_format($total, 2) }}</td>
            </tr>
            @if($invoice->amount_paid > 0)
                <tr>
                    <td>Pagado:</td>
                    <td class="text-right">-${{ number_format($invoice->amount_paid, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td>Saldo Pendiente:</td>
                    <td class="text-right">${{ number_format($invoice->amount_due, 2) }}</td>
                </tr>
            @endif
        </table>
    </div>

    <div class="payment-info">
        <div class="section-title">TÉRMINOS DE PAGO</div>
        <p><strong>Condiciones:</strong> {{ $invoice->payment_terms ?? 'Pago a 30 días' }}</p>

        @if($invoice->payment_status !== 'paid')
            <p><strong>Vencimiento:</strong> {{ $invoice->due_date->format('d/m/Y') }}</p>

            @if($invoice->due_date->isPast())
                <p style="color: #dc3545;"><strong>⚠️ FACTURA VENCIDA</strong></p>
            @endif
        @endif

        @if($account && $account->notes)
            <p><strong>Notas de la cuenta:</strong> {{ $account->notes }}</p>
        @endif
    </div>

    <div class="footer">
        <p>Factura generada el {{ $generateDate }}</p>
        <p>Este documento es válido como comprobante de servicios médicos prestados.</p>
        @if($organization->tax_id ?? false)
            <p>RUC: {{ $organization->tax_id }}</p>
        @endif
    </div>
</body>
</html>
