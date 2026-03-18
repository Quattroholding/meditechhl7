<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados HemoScreen - Batch Export</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            padding: 15mm;
        }

        .cover-page {
            text-align: center;
            padding-top: 80mm;
            page-break-after: always;
        }

        .cover-title {
            font-size: 32px;
            color: #007bff;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .cover-subtitle {
            font-size: 18px;
            color: #666;
            margin-bottom: 40px;
        }

        .cover-info {
            font-size: 14px;
            color: #333;
            line-height: 2;
        }

        .result-page {
            page-break-after: always;
        }

        .result-page:last-child {
            page-break-after: avoid;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #007bff;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 20px;
            color: #007bff;
            margin-bottom: 5px;
        }

        .header h2 {
            font-size: 14px;
            color: #666;
            font-weight: normal;
        }

        .info-section {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 15px;
        }

        .info-grid {
            display: table;
            width: 100%;
        }

        .info-row {
            display: table-row;
        }

        .info-cell {
            display: table-cell;
            padding: 4px 10px;
            width: 50%;
        }

        .info-label {
            font-weight: bold;
            color: #666;
            font-size: 9px;
            text-transform: uppercase;
        }

        .info-value {
            font-size: 11px;
            color: #333;
            margin-top: 2px;
        }

        .alert-box {
            padding: 8px 12px;
            margin: 12px 0;
            border-radius: 5px;
            border-left: 4px solid;
            font-size: 10px;
        }

        .alert-warning {
            background: #fff3cd;
            border-left-color: #ffc107;
            color: #856404;
        }

        .alert-success {
            background: #d4edda;
            border-left-color: #28a745;
            color: #155724;
        }

        .category-section {
            margin-bottom: 15px;
            page-break-inside: avoid;
        }

        .category-title {
            background: #007bff;
            color: white;
            padding: 6px 10px;
            font-size: 11px;
            font-weight: bold;
            border-radius: 3px 3px 0 0;
        }

        .results-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #dee2e6;
            font-size: 9px;
        }

        .results-table th {
            background: #e9ecef;
            padding: 6px;
            text-align: left;
            font-size: 9px;
            font-weight: bold;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
        }

        .results-table td {
            padding: 6px;
            border-bottom: 1px solid #dee2e6;
        }

        .results-table tr:last-child td {
            border-bottom: none;
        }

        .results-table tr.abnormal {
            background: #fff3cd;
        }

        .param-name {
            font-weight: bold;
            color: #333;
            font-size: 9px;
        }

        .param-code {
            font-size: 8px;
            color: #888;
        }

        .value-normal {
            color: #28a745;
            font-weight: bold;
            font-size: 10px;
        }

        .value-low {
            color: #007bff;
            font-weight: bold;
            font-size: 10px;
        }

        .value-high {
            color: #dc3545;
            font-weight: bold;
            font-size: 10px;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }

        .badge-success {
            background: #28a745;
            color: white;
        }

        .badge-primary {
            background: #007bff;
            color: white;
        }

        .badge-danger {
            background: #dc3545;
            color: white;
        }

        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #dee2e6;
            font-size: 8px;
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Cover Page -->
    <div class="cover-page">
        <div class="cover-title">Exportación de Resultados HemoScreen</div>
        <div class="cover-subtitle">Complete Blood Count (CBC) - Reporte Batch</div>
        <div class="cover-info">
            <p><strong>Total de Resultados:</strong> {{ $totalResults }}</p>
            <p><strong>Fecha de Exportación:</strong> {{ $exportDate->format('d/m/Y H:i') }}</p>
            <p style="margin-top: 30px;">Generado por SAMI HemoScreen</p>
        </div>
    </div>

    <!-- Individual Results -->
    @foreach($results as $data)
        <div class="result-page">
            <!-- Header -->
            <div class="header">
                <h1>Resultado {{ $loop->iteration }} de {{ $totalResults }}</h1>
                <h2>Hemograma Completo (CBC)</h2>
            </div>

            <!-- Test Information -->
            <div class="info-section">
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-cell">
                            <div class="info-label">Fecha y Hora</div>
                            <div class="info-value">{{ $data['result']->test_performed_at->format('d/m/Y H:i') }}</div>
                        </div>
                        <div class="info-cell">
                            <div class="info-label">Dispositivo</div>
                            <div class="info-value">{{ $data['result']->device_serial }}</div>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-cell">
                            <div class="info-label">Panel</div>
                            <div class="info-value">{{ $data['result']->panel_name }}</div>
                        </div>
                        <div class="info-cell">
                            <div class="info-label">ID de Control</div>
                            <div class="info-value">{{ $data['result']->message_control_id }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Alert -->
            @if($data['hasAbnormal'])
                <div class="alert-box alert-warning">
                    <strong>⚠ Atención:</strong> Contiene {{ $data['abnormalCount'] }} valor(es) anormal(es).
                </div>
            @else
                <div class="alert-box alert-success">
                    <strong>✓ Normal:</strong> Todos los valores dentro del rango de referencia.
                </div>
            @endif

            <!-- Results by Category -->
            @foreach($data['groupedObservations'] as $categoryName => $observations)
                <div class="category-section">
                    <div class="category-title">{{ $categoryName }}</div>
                    <table class="results-table">
                        <thead>
                            <tr>
                                <th style="width: 35%;">Parámetro</th>
                                <th style="width: 15%; text-align: center;">Valor</th>
                                <th style="width: 12%; text-align: center;">Unidad</th>
                                <th style="width: 23%; text-align: center;">Rango Ref.</th>
                                <th style="width: 15%; text-align: center;">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($observations as $obs)
                                <tr class="{{ $obs['isAbnormal'] ? 'abnormal' : '' }}">
                                    <td>
                                        <div class="param-name">{{ $obs['name'] }}</div>
                                        <div class="param-code">{{ $obs['code'] }}</div>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="value-{{ $obs['status'] }}">
                                            {{ number_format($obs['value'], 2) }}
                                        </span>
                                    </td>
                                    <td style="text-align: center;">{{ $obs['unit'] }}</td>
                                    <td style="text-align: center; font-size: 8px;">
                                        @if($obs['reference'])
                                            {{ number_format($obs['reference']['min'], 1) }}-{{ number_format($obs['reference']['max'], 1) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td style="text-align: center;">
                                        @if($obs['status'] === 'normal')
                                            <span class="badge badge-success">OK</span>
                                        @elseif($obs['status'] === 'low')
                                            <span class="badge badge-primary">↓</span>
                                        @elseif($obs['status'] === 'high')
                                            <span class="badge badge-danger">↑</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach

            <!-- Footer -->
            <div class="footer">
                <p>Los rangos de referencia pueden variar. Interpretación por profesional de salud requerida.</p>
            </div>
        </div>
    @endforeach
</body>
</html>
