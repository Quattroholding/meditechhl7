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
            font-family: 'Helvetica Neue', Arial, sans-serif;
            padding: 60px;
            background: white;
            color: #1a1a1a;
            line-height: 1.6;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #3498db;
            padding: 40px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 120px;
            color: rgba(52, 152, 219, 0.05);
            font-weight: bold;
            z-index: -1;
            user-select: none;
        }
        .header-bar {
            background: linear-gradient(90deg, #2c3e50 0%, #3498db 100%);
            height: 8px;
            margin-bottom: 40px;
        }
        .title-section {
            margin-bottom: 50px;
        }
        .main-title {
            font-size: 36px;
            font-weight: 300;
            color: #2c3e50;
            margin-bottom: 10px;
            letter-spacing: 3px;
        }
        .subtitle {
            font-size: 16px;
            color: #7f8c8d;
            font-weight: 300;
        }
        .clinic-details {
            text-align: right;
            margin-bottom: 50px;
            padding: 20px;
            background: #f8f9fa;
            border-left: 4px solid #3498db;
        }
        .clinic-details h3 {
            font-size: 18px;
            color: #2c3e50;
            margin-bottom: 8px;
            font-weight: 600;
        }
        .clinic-details p {
            font-size: 13px;
            color: #7f8c8d;
            margin: 3px 0;
        }
        .section {
            margin-bottom: 35px;
        }
        .section-heading {
            font-size: 14px;
            color: #3498db;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 15px;
            font-weight: 600;
            border-bottom: 1px solid #ecf0f1;
            padding-bottom: 8px;
        }
        .data-row {
            display: flex;
            margin-bottom: 12px;
            padding: 12px;
            background: #fafafa;
            border-radius: 4px;
        }
        .data-row:hover {
            background: #f0f0f0;
        }
        .data-label {
            width: 180px;
            font-weight: 600;
            color: #555;
            font-size: 14px;
        }
        .data-value {
            flex: 1;
            color: #2c3e50;
            font-size: 14px;
        }
        .certification-box {
            background: white;
            border: 2px solid #e74c3c;
            padding: 25px;
            margin: 30px 0;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        .certification-box p {
            text-align: justify;
            font-size: 15px;
            line-height: 1.9;
            color: #2c3e50;
        }
        .diagnosis-box {
            background: #fff8e1;
            border-left: 5px solid #ffc107;
            padding: 20px;
            margin: 25px 0;
        }
        .diagnosis-box .label {
            font-size: 12px;
            color: #f57c00;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            margin-bottom: 10px;
            display: block;
        }
        .diagnosis-box .content {
            font-size: 16px;
            color: #1a1a1a;
            font-weight: 500;
        }
        .period-box {
            background: #e3f2fd;
            border-left: 5px solid #2196f3;
            padding: 20px;
            margin: 25px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .period-item {
            text-align: center;
        }
        .period-item .label {
            font-size: 11px;
            color: #1976d2;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
            font-weight: 600;
        }
        .period-item .value {
            font-size: 20px;
            color: #0d47a1;
            font-weight: 600;
        }
        .separator {
            width: 40px;
            height: 2px;
            background: #2196f3;
        }
        .footer-info {
            margin-top: 60px;
            padding-top: 30px;
            border-top: 2px solid #ecf0f1;
        }
        .date-location {
            text-align: right;
            margin-bottom: 50px;
            font-size: 14px;
            color: #7f8c8d;
        }
        .signature-container {
            display: flex;
            justify-content: center;
            margin-top: 70px;
        }
        .signature-box {
            text-align: center;
            min-width: 300px;
        }
        .signature-line {
            border-top: 2px solid #2c3e50;
            padding-top: 12px;
            margin-bottom: 8px;
        }
        .doctor-name {
            font-size: 17px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        .doctor-details {
            font-size: 13px;
            color: #7f8c8d;
            margin: 3px 0;
        }
        .document-number {
            position: absolute;
            top: 60px;
            right: 60px;
            background: #2c3e50;
            color: white;
            padding: 8px 15px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }
    </style>
</head>
<body class="container">
    <div class="watermark">LICENCIA MÉDICA</div>
    <div class="document-number">DOC N° {{ $documentNumber ?? '0000' }}</div>

    <div class="header-bar"></div>

    <div class="title-section">
        <h1 class="main-title">LICENCIA MÉDICA</h1>
        <p class="subtitle">Certificado de Incapacidad Temporal para Actividades Laborales</p>
    </div>

    <div class="clinic-details">
        <h3>{{ $branch->name ?? 'Centro Médico' }}</h3>
        <p>{{ $branch->address ?? 'Dirección no disponible' }}</p>
        <p>Tel: {{ $branch->phone ?? 'N/A' }} | {{ $branch->email ?? 'N/A' }}</p>
    </div>

    <div class="section">
        <div class="section-heading">Información del Paciente</div>
        <div class="data-row">
            <span class="data-label">Nombre Completo:</span>
            <span class="data-value">{{ $patient->full_name ?? 'Nombre del Paciente' }}</span>
        </div>
        <div class="data-row">
            <span class="data-label">Documento de Identidad:</span>
            <span class="data-value">{{ $patient->identifier_value ?? 'N/A' }}</span>
        </div>
        <div class="data-row">
            <span class="data-label">Fecha de Nacimiento:</span>
            <span class="data-value">{{ $patient->birth_date ? $patient->birth_date : 'N/A' }}</span>
        </div>
        <div class="data-row">
            <span class="data-label">Edad:</span>
            <span class="data-value">{{ $patient->age ?? 'N/A' }} años</span>
        </div>
    </div>

    <div class="certification-box">
        <p>
            El suscrito médico tratante certifica que el paciente arriba identificado ha sido evaluado
            y examinado en esta fecha, determinándose que presenta un cuadro clínico que amerita reposo
            médico y abstención de actividades laborales según el período establecido en este documento.
        </p>
    </div>

    <div class="diagnosis-box">
        <span class="label">Diagnóstico Médico</span>
        <div class="content">{{ $diagnosis ?? 'Diagnóstico médico detallado del paciente' }}</div>
    </div>

    <div class="section">
        <div class="section-heading">Período de Incapacidad</div>
        <div class="period-box">
            <div class="period-item">
                <div class="label">Fecha Inicio</div>
                <div class="value">{{ $startDate ?? 'DD/MM/AAAA' }}</div>
            </div>
            <div class="separator"></div>
            <div class="period-item">
                <div class="label">Fecha Término</div>
                <div class="value">{{ $endDate ?? 'DD/MM/AAAA' }}</div>
            </div>
            <div class="separator"></div>
            <div class="period-item">
                <div class="label">Total Días</div>
                <div class="value">{{ $days ?? 'X' }}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-heading">Indicaciones Médicas</div>
        <div style="padding: 15px; background: #fafafa; border-radius: 4px;">
            <p style="margin-bottom: 12px;">Durante el período de reposo médico, el paciente deberá:</p>
            <ul style="list-style: none; padding-left: 0;">
                <li style="margin: 8px 0; padding-left: 25px; position: relative;">
                    <span style="position: absolute; left: 0; color: #3498db;">▸</span>
                    Mantener reposo absoluto y abstenerse de actividades laborales
                </li>
                <li style="margin: 8px 0; padding-left: 25px; position: relative;">
                    <span style="position: absolute; left: 0; color: #3498db;">▸</span>
                    Cumplir estrictamente con el tratamiento médico indicado
                </li>
                <li style="margin: 8px 0; padding-left: 25px; position: relative;">
                    <span style="position: absolute; left: 0; color: #3498db;">▸</span>
                    Asistir a controles médicos programados
                </li>
                <li style="margin: 8px 0; padding-left: 25px; position: relative;">
                    <span style="position: absolute; left: 0; color: #3498db;">▸</span>
                    Evitar esfuerzos físicos y situaciones de estrés
                </li>
            </ul>
        </div>
    </div>

    <div class="footer-info">
        <div class="date-location">
            {{ $city ?? 'Ciudad' }}, {{ $issueDate ?? now()->format('d \d\e F \d\e Y') }}
        </div>

        <div class="signature-container">
            <div class="signature-box">
                @if($firma)
                    <img src="{{ $firma }}"
                         alt="Firma"
                         style="max-width: 150px; max-height: 150px;">
                @endif
                <div class="signature-line">
                    <div class="doctor-name">{{ $doctor->full_name ?? 'Dr. Nombre del Médico' }}</div>
                    <div class="doctor-details">{{ $doctor->specialty ?? 'Especialidad Médica' }}</div>
                    <div class="doctor-details">Licencia Médica N° {{ $doctor->license ?? 'XXXX' }}</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
