<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Incapacidad Médica</title>
    @vite('resources/css/app.css')
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .verification-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 100%;
            padding: 40px;
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .status-badge {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .status-badge.success {
            background-color: #d4edda;
            color: #155724;
        }

        .status-badge.danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status-badge.warning {
            background-color: #fff3cd;
            color: #856404;
        }

        .header h1 {
            color: #333;
            font-size: 28px;
            margin: 0;
            margin-top: 10px;
        }

        .status-icon {
            font-size: 48px;
            margin-bottom: 10px;
        }

        .info-section {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #555;
            font-size: 14px;
        }

        .info-value {
            color: #333;
            font-size: 14px;
            text-align: right;
            font-weight: 500;
        }

        .section-title {
            font-size: 16px;
            font-weight: 700;
            color: #333;
            margin-top: 25px;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .card-section {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .period-box {
            background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin: 20px 0;
        }

        .period-dates {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }

        .period-total {
            font-size: 24px;
            font-weight: 700;
            color: #667eea;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            font-size: 12px;
            color: #999;
        }

        .verified-icon {
            display: inline-block;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 32px;
            margin: 0 auto 15px;
        }

        .doctor-info {
            display: flex;
            gap: 15px;
            align-items: start;
        }

        .doctor-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #667eea;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 20px;
            flex-shrink: 0;
        }

        .doctor-details {
            flex: 1;
        }

        .doctor-name {
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }

        .doctor-specialty {
            font-size: 13px;
            color: #999;
            margin-bottom: 3px;
        }

        .doctor-license {
            font-size: 12px;
            color: #999;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="verification-container">
        <div class="header">
            <div class="status-icon">
                @if ($isValid)
                    ✓
                @else
                    ⚠
                @endif
            </div>
            <div class="status-badge {{ $statusColor }}">
                {{ $status }}
            </div>
            <h1>Verificación de Incapacidad Médica</h1>
        </div>

        <!-- Información del Paciente -->
        <div class="section-title">Información del Paciente</div>
        <div class="card-section">
            <div class="info-row">
                <span class="info-label">Nombre</span>
                <span class="info-value">{{ $patientName }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Cédula</span>
                <span class="info-value">{{ $medicalLeave->patient->identifier ?? 'N/A' }}</span>
            </div>
        </div>

        <!-- Período de Incapacidad -->
        <div class="section-title">Período de Incapacidad</div>
        <div class="period-box">
            <div class="period-dates">
                <strong>Desde:</strong> {{ $startDate }} <strong>Hasta:</strong> {{ $endDate }}
            </div>
            <div class="period-total">{{ $totalDays }} días</div>
        </div>

        <!-- Información Médica -->
        <div class="section-title">Información Médica</div>
        <div class="card-section">
            <div class="info-row">
                <span class="info-label">Diagnóstico</span>
                <span class="info-value">{{ $diagnosis }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Clínica/Centro</span>
                <span class="info-value">{{ $clinicName }}</span>
            </div>
        </div>

        <!-- Profesional Médico -->
        <div class="section-title">Profesional Médico</div>
        <div class="card-section">
            <div class="doctor-info">
                <div class="doctor-avatar">
                    {{ substr($doctorName, 0, 1) }}
                </div>
                <div class="doctor-details">
                    <div class="doctor-name">{{ $doctorName }}</div>
                    <div class="doctor-specialty">{{ $specialty }}</div>
                    <div class="doctor-license">Lic: {{ $doctorLicense }}</div>
                </div>
            </div>
        </div>

        <!-- Información de Validación -->
        <div class="section-title">Detalles de Validación</div>
        <div class="card-section">
            <div class="info-row">
                <span class="info-label">Código de Incapacidad</span>
                <span class="info-value" style="font-family: monospace;">{{ $medicalLeave->identifier }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Emitida</span>
                <span class="info-value">{{ $medicalLeave->issue_date->format('d/m/Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Código de Verificación</span>
                <span class="info-value" style="font-family: monospace; font-size: 11px;">
                    {{ substr($medicalLeave->verification_hash, 0, 12) }}...
                </span>
            </div>
        </div>

        <div class="footer">
            <p>Este documento ha sido verificado en el sistema de registros médicos.</p>
            <p>Fecha de consulta: {{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>
</body>
</html>
