<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Licencia Médica</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Georgia', serif;
            padding: 50px;
            background: white;
            color: #2c3e50;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #3498db;
            padding: 40px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #3498db;
        }
        .header-left h1 {
            color: #3498db;
            font-size: 32px;
            margin-bottom: 5px;
        }
        .header-left p {
            color: #7f8c8d;
            font-size: 14px;
        }
        .header-right {
            text-align: right;
        }
        .doc-number {
            background: #3498db;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
        }
        .clinic-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
        }
        .clinic-section h2 {
            font-size: 22px;
            margin-bottom: 10px;
        }
        .clinic-section p {
            margin: 5px 0;
            font-size: 14px;
        }
        .section-title {
            background: #ecf0f1;
            padding: 10px 15px;
            margin: 25px 0 15px 0;
            border-left: 5px solid #3498db;
            font-weight: bold;
            font-size: 16px;
            color: #2c3e50;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 25px;
        }
        .info-item {
            padding: 12px;
            background: #f8f9fa;
            border-radius: 5px;
            border-left: 3px solid #3498db;
        }
        .info-item .label {
            font-weight: bold;
            color: #3498db;
            display: block;
            margin-bottom: 5px;
            font-size: 13px;
        }
        .info-item .value {
            color: #2c3e50;
            font-size: 15px;
        }
        .medical-details {
            background: #fff9e6;
            border: 2px solid #f39c12;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .medical-details h3 {
            color: #e67e22;
            margin-bottom: 15px;
            font-size: 18px;
        }
        .medical-details p {
            line-height: 1.8;
            margin-bottom: 10px;
        }
        .rest-period {
            background: #e8f8f5;
            border: 2px solid #1abc9c;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin: 25px 0;
        }
        .rest-period h3 {
            color: #16a085;
            margin-bottom: 15px;
            font-size: 20px;
        }
        .dates {
            display: flex;
            justify-content: space-around;
            margin-top: 15px;
        }
        .date-box {
            background: white;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .date-box .label {
            font-size: 12px;
            color: #7f8c8d;
            margin-bottom: 5px;
        }
        .date-box .date {
            font-size: 16px;
            font-weight: bold;
            color: #16a085;
        }
        .signature-area {
            margin-top: 60px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .doctor-info {
            text-align: center;
            flex: 1;
        }
        .signature-line {
            width: 250px;
            border-top: 2px solid #2c3e50;
            padding-top: 10px;
            margin: 0 auto;
        }
        .doctor-info p {
            margin: 5px 0;
        }
        .doctor-name {
            font-weight: bold;
            font-size: 16px;
            color: #2c3e50;
        }
        .stamp-area {
            width: 150px;
            height: 150px;
            border: 2px dashed #bdc3c7;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #bdc3c7;
            font-size: 12px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-left">
                <h1>LICENCIA MÉDICA</h1>
                <p>Certificado de Incapacidad Temporal</p>
            </div>
            <div class="header-right">
                <div class="doc-number">N° {{ $documentNumber ?? '0000' }}</div>
                <p style="margin-top: 10px; font-size: 13px;">Fecha: {{ $issueDate ?? now()->format('d/m/Y') }}</p>
            </div>
        </div>

        <div class="clinic-section">
            <h2>{{ $branch->name ?? 'Centro Médico' }}</h2>
            <p>{{ $branch->address ?? 'Dirección' }}</p>
            <p>☎ {{ $branch->phone ?? 'Teléfono' }} | ✉ {{ $branch->email ?? 'Email' }}</p>
        </div>

        <div class="section-title">INFORMACIÓN DEL PACIENTE</div>
        <div class="info-grid">
            <div class="info-item">
                <span class="label">NOMBRE COMPLETO</span>
                <span class="value">{{ $patient->full_name ?? 'Nombre del Paciente' }}</span>
            </div>
            <div class="info-item">
                <span class="label">IDENTIFICACIÓN</span>
                <span class="value">{{ $patient->identifier_value ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="label">FECHA DE NACIMIENTO</span>
                <span class="value">{{ $patient->birth_date ? $patient->birth_date: 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="label">EDAD</span>
                <span class="value">{{ $patient->age ?? 'N/A' }} años</span>
            </div>
        </div>

        <div class="medical-details">
            <h3>📋 DIAGNÓSTICO MÉDICO</h3>
            <p><strong>{{ $diagnosis ?? 'Diagnóstico médico detallado' }}</strong></p>
            <p style="margin-top: 15px;">
                El paciente ha sido evaluado y se determina que requiere reposo médico para su recuperación.
                Se recomienda abstención total de actividades laborales durante el período indicado.
            </p>
        </div>

        <div class="rest-period">
            <h3>⏰ PERÍODO DE REPOSO MÉDICO</h3>
            <div class="dates">
                <div class="date-box">
                    <div class="label">DESDE</div>
                    <div class="date">{{ $startDate ?? 'DD/MM/AAAA' }}</div>
                </div>
                <div style="display: flex; align-items: center; font-size: 24px; color: #16a085;">→</div>
                <div class="date-box">
                    <div class="label">HASTA</div>
                    <div class="date">{{ $endDate ?? 'DD/MM/AAAA' }}</div>
                </div>
            </div>
            <p style="margin-top: 15px; font-weight: bold; color: #16a085; font-size: 18px;">
                Total: {{ $days ?? 'X' }} días de reposo
            </p>
        </div>

        <div class="section-title">RECOMENDACIONES MÉDICAS</div>
        <div style="padding: 15px; line-height: 1.8;">
            <ul style="list-style: none; padding: 0;">
                <li style="margin: 10px 0;">✓ Guardar reposo absoluto durante el período indicado</li>
                <li style="margin: 10px 0;">✓ Seguir el tratamiento médico prescrito</li>
                <li style="margin: 10px 0;">✓ Asistir a controles médicos según indicación</li>
                <li style="margin: 10px 0;">✓ Evitar esfuerzos físicos y actividades laborales</li>
            </ul>
        </div>

        <div class="signature-area">
            <div class="doctor-info">
                <div class="signature-line">
                    <p class="doctor-name">{{ $doctor->full_name ?? 'Dr. Nombre del Médico' }}</p>
                    <p style="font-size: 14px; color: #7f8c8d;">{{ $doctor->specialty ?? 'Especialidad Médica' }}</p>
                    <p style="font-size: 13px; color: #7f8c8d;">Lic. Médica: {{ $doctor->license ?? 'XXXX' }}</p>
                </div>
            </div>
            <div class="stamp-area">
                @if($firma)
                    <img src="{{ $firma }}"
                         alt="Firma"
                         style="max-width: 150px; max-height: 150px;">
                @endif
            </div>
        </div>
    </div>
</body>
</html>
