<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Reporte de Facturas y Pagos</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9px;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #4472C4;
        }
        .header h1 {
            margin: 0 0 10px 0;
            font-size: 20px;
            color: #4472C4;
        }
        .header p {
            margin: 3px 0;
            font-size: 10px;
            color: #666;
        }
        .filters {
            background: #f5f5f5;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 5px;
            border-left: 4px solid #4472C4;
        }
        .filters strong {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-size: 10px;
        }
        .filters div {
            margin: 4px 0;
            padding-left: 10px;
            font-size: 9px;
            color: #555;
        }
        .summary {
            display: table;
            width: 100%;
            margin-bottom: 15px;
            background: #fff;
        }
        .summary-item {
            display: table-cell;
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
            background: #f9f9f9;
        }
        .summary-item .label {
            font-size: 8px;
            color: #666;
            margin-bottom: 5px;
        }
        .summary-item .value {
            font-size: 14px;
            font-weight: bold;
            color: #4472C4;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background: #4472C4;
            color: white;
            padding: 8px 5px;
            text-align: left;
            font-size: 8px;
            font-weight: 600;
        }
        td {
            padding: 6px 5px;
            border-bottom: 1px solid #e0e0e0;
            font-size: 8px;
            vertical-align: top;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        tr:hover {
            background: #f0f4ff;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7px;
            font-weight: 600;
        }
        .badge-success {
            background: #d4edda;
            color: #155724;
        }
        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }
        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }
        .badge-info {
            background: #d1ecf1;
            color: #0c5460;
        }
        .badge-secondary {
            background: #e2e3e5;
            color: #383d41;
        }
        .footer {
            margin-top: 25px;
            padding-top: 15px;
            text-align: center;
            font-size: 8px;
            color: #666;
            border-top: 2px solid #e0e0e0;
        }
        .footer p {
            margin: 5px 0;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p><strong>Fecha de Generación:</strong> {{ now()->format('d/m/Y H:i:s') }}</p>
        <p><strong>Generado por:</strong> {{ auth()->user()->full_name ?? auth()->user()->name }}</p>
    </div>

    @if(count($appliedFilters) > 0)
        <div class="filters">
            <strong>Filtros Aplicados:</strong>
            @foreach($appliedFilters as $filter)
                <div>• {{ $filter }}</div>
            @endforeach
        </div>
    @endif

    @php
        $totalFacturado = collect($data)->sum(function($row) { return (float)str_replace(',', '', $row[5]); });
        $totalPagado = collect($data)->sum(function($row) { return (float)str_replace(',', '', $row[6]); });
        $totalPendiente = collect($data)->sum(function($row) { return (float)str_replace(',', '', $row[7]); });
    @endphp

    <div class="summary">
        <div class="summary-item">
            <div class="label">Total Facturado</div>
            <div class="value">${{ number_format($totalFacturado, 2) }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Total Pagado</div>
            <div class="value" style="color: #28a745;">${{ number_format($totalPagado, 2) }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Saldo Pendiente</div>
            <div class="value" style="color: #dc3545;">${{ number_format($totalPendiente, 2) }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Total de Facturas</div>
            <div class="value">{{ count($data) }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                @foreach($columns as $column)
                    <th>{{ $column }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
                <tr>
                    <td>{{ $row[0] }}</td>
                    <td>{{ $row[1] }}</td>
                    <td>{{ $row[2] }}</td>
                    <td>{{ $row[3] }}</td>
                    <td>{{ $row[4] }}</td>
                    <td class="text-right">${{ $row[5] }}</td>
                    <td class="text-right">${{ $row[6] }}</td>
                    <td class="text-right">${{ $row[7] }}</td>
                    <td class="text-center">
                        @php
                            $badgeClass = match($row[8]) {
                                'Emitida' => 'badge-info',
                                'Balanceada' => 'badge-success',
                                'Cancelada' => 'badge-danger',
                                default => 'badge-secondary'
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $row[8] }}</span>
                    </td>
                    <td class="text-center">
                        @php
                            $paymentBadgeClass = match($row[9]) {
                                'Pagado' => 'badge-success',
                                'Pago Parcial' => 'badge-warning',
                                'Sin Pagar' => 'badge-danger',
                                default => 'badge-secondary'
                            };
                        @endphp
                        <span class="badge {{ $paymentBadgeClass }}">{{ $row[9] }}</span>
                    </td>
                    <td class="text-center">{{ $row[10] }}</td>
                    <td>{{ $row[11] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p><strong>Total de Registros:</strong> {{ count($data) }} factura(s)</p>
        <p><strong>Resumen Financiero:</strong>
            Facturado: ${{ number_format($totalFacturado, 2) }} |
            Pagado: ${{ number_format($totalPagado, 2) }} |
            Pendiente: ${{ number_format($totalPendiente, 2) }}
        </p>
        <p style="margin-top: 10px;">Este documento fue generado automáticamente por el Sistema de Gestión Médica Meditech</p>
    </div>
</body>
</html>
