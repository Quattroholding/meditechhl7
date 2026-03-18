<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado HemoScreen - {{ $result->test_performed_at->format('d/m/Y') }}</title>

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

        .header {
            text-align: center;
            border-bottom: 3px solid #007bff;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 24px;
            color: #007bff;
            margin-bottom: 5px;
        }

        .header h2 {
            font-size: 16px;
            color: #666;
            font-weight: normal;
        }

        .info-section {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 12px;
            margin-bottom: 20px;
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
            padding: 5px 10px;
            width: 50%;
        }

        .info-label {
            font-weight: bold;
            color: #666;
            font-size: 10px;
            text-transform: uppercase;
        }

        .info-value {
            font-size: 12px;
            color: #333;
            margin-top: 2px;
        }

        .alert-box {
            padding: 10px 15px;
            margin: 15px 0;
            border-radius: 5px;
            border-left: 4px solid;
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
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .category-title {
            background: #007bff;
            color: white;
            padding: 8px 12px;
            font-size: 13px;
            font-weight: bold;
            border-radius: 3px 3px 0 0;
        }

        .results-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #dee2e6;
        }

        .results-table th {
            background: #e9ecef;
            padding: 8px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
        }

        .results-table td {
            padding: 8px;
            border-bottom: 1px solid #dee2e6;
            font-size: 11px;
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
        }

        .param-code {
            font-size: 9px;
            color: #888;
        }

        .value-normal {
            color: #28a745;
            font-weight: bold;
            font-size: 13px;
        }

        .value-low {
            color: #007bff;
            font-weight: bold;
            font-size: 13px;
        }

        .value-high {
            color: #dc3545;
            font-weight: bold;
            font-size: 13px;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9px;
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
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #dee2e6;
            font-size: 9px;
            color: #666;
            text-align: center;
        }

        .legend {
            margin-top: 20px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
        }

        .legend-title {
            font-weight: bold;
            margin-bottom: 8px;
            font-size: 11px;
        }

        .legend-item {
            display: inline-block;
            margin-right: 15px;
            margin-bottom: 5px;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Resultado de Hemograma Completo (CBC)</h1>
        <h2>HemoScreen - Complete Blood Count</h2>
    </div>

    <!-- Test Information -->
    <div class="info-section">
        <div class="info-grid">
            <div class="info-row">
                <div class="info-cell">
                    <div class="info-label">Fecha y Hora del Test</div>
                    <div class="info-value">{{ $result->test_performed_at->format('d/m/Y H:i') }}</div>
                </div>
                <div class="info-cell">
                    <div class="info-label">Dispositivo</div>
                    <div class="info-value">{{ $result->device_serial }}</div>
                </div>
            </div>
            <div class="info-row">
                <div class="info-cell">
                    <div class="info-label">Panel</div>
                    <div class="info-value">{{ $result->panel_name }}</div>
                </div>
                <div class="info-cell">
                    <div class="info-label">Código CPT</div>
                    <div class="info-value">{{ $result->panel_code }}</div>
                </div>
            </div>
            <div class="info-row">
                <div class="info-cell">
                    <div class="info-label">ID de Control</div>
                    <div class="info-value">{{ $result->message_control_id }}</div>
                </div>
                <div class="info-cell">
                    <div class="info-label">Médico</div>
                    <div class="info-value">{{ $practitioner->name ?? 'N/A' }}</div>
                </div>
            </div>
            @if($result->patient_identifier)
            <div class="info-row">
                <div class="info-cell">
                    <div class="info-label">Identificador de Paciente</div>
                    <div class="info-value">{{ $result->patient_identifier }}</div>
                </div>
                <div class="info-cell"></div>
            </div>
            @endif
        </div>
    </div>

    <!-- Status Alert -->
    @if($hasAbnormal)
        <div class="alert-box alert-warning">
            <strong>⚠ Atención:</strong> Este resultado contiene {{ $abnormalCount }} valor(es) fuera del rango de referencia normal.
            Se recomienda consultar con un profesional de la salud para interpretación clínica.
        </div>
    @else
        <div class="alert-box alert-success">
            <strong>✓ Resultado Normal:</strong> Todos los valores están dentro de los rangos de referencia establecidos.
        </div>
    @endif

    <!-- Results by Category -->
    @foreach($groupedObservations as $categoryName => $observations)
        <div class="category-section">
            <div class="category-title">{{ $categoryName }}</div>
            <table class="results-table">
                <thead>
                    <tr>
                        <th style="width: 35%;">Parámetro</th>
                        <th style="width: 15%; text-align: center;">Valor</th>
                        <th style="width: 12%; text-align: center;">Unidad</th>
                        <th style="width: 23%; text-align: center;">Rango de Referencia</th>
                        <th style="width: 15%; text-align: center;">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($observations as $obs)
                        <tr class="{{ $obs['isAbnormal'] ? 'abnormal' : '' }}">
                            <td>
                                <div class="param-name">{{ $obs['name'] }}</div>
                                <div class="param-code">LOINC: {{ $obs['code'] }}</div>
                            </td>
                            <td style="text-align: center;">
                                <span class="value-{{ $obs['status'] }}">
                                    {{ number_format($obs['value'], 2) }}
                                </span>
                            </td>
                            <td style="text-align: center;">{{ $obs['unit'] }}</td>
                            <td style="text-align: center;">
                                @if($obs['reference'])
                                    {{ number_format($obs['reference']['min'], 2) }} -
                                    {{ number_format($obs['reference']['max'], 2) }}
                                    {{ $obs['reference']['unit'] }}
                                @else
                                    -
                                @endif
                            </td>
                            <td style="text-align: center;">
                                @if($obs['status'] === 'normal')
                                    <span class="badge badge-success">NORMAL</span>
                                @elseif($obs['status'] === 'low')
                                    <span class="badge badge-primary">↓ BAJO</span>
                                @elseif($obs['status'] === 'high')
                                    <span class="badge badge-danger">↑ ALTO</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach

    <!-- Legend -->
    <div class="legend">
        <div class="legend-title">Leyenda de Estados:</div>
        <div>
            <span class="legend-item"><span class="badge badge-success">NORMAL</span> Dentro del rango de referencia</span>
            <span class="legend-item"><span class="badge badge-primary">↓ BAJO</span> Por debajo del rango mínimo</span>
            <span class="legend-item"><span class="badge badge-danger">↑ ALTO</span> Por encima del rango máximo</span>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p><strong>Nota Importante:</strong> Los rangos de referencia pueden variar según el laboratorio, método de análisis y características del paciente (edad, sexo, etc.).
        Este reporte debe ser interpretado por un profesional de la salud calificado.</p>
        <p style="margin-top: 10px;">Generado por SAMI HemoScreen el {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
