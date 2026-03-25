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
            font-family: 'Arial', sans-serif;
            padding: 40px;
            background: white;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #3498db;
            padding: 40px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #2c3e50;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #2c3e50;
            font-size: 28px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .clinic-info {
            text-align: center;
            margin-bottom: 30px;
            color: #555;
        }
        .clinic-info h2 {
            font-size: 20px;
            margin-bottom: 5px;
            color: #2c3e50;
        }
        .clinic-info p {
            margin: 3px 0;
            font-size: 14px;
        }
        .content {
            margin: 30px 0;
            line-height: 1.8;
        }
        .patient-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 4px solid #2c3e50;
        }
        .patient-info .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .patient-info .label {
            font-weight: bold;
            color: #2c3e50;
        }
        .text-block {
            margin: 25px 0;
            text-align: justify;
            font-size: 16px;
            line-height: 2;
        }
        .diagnosis {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .diagnosis strong {
            color: #856404;
        }
        .leave-period {
            background: #d1ecf1;
            border-left: 4px solid #17a2b8;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            font-size: 16px;
        }
        .leave-period strong {
            color: #0c5460;
        }
        .footer {
            margin-top: 80px;
            text-align: center;
        }
        .signature-section {
            margin-top: 10px;
            text-align: center;
        }
        .signature-line {
            width: 300px;
            margin: 0 auto;
            border-top: 2px solid #333;
            padding-top: 10px;
        }
        .date-section {
            margin-top: 40px;
            text-align: right;
            font-size: 14px;
        }
    </style>
</head>
<body class="container">
    <div class="header">
        <h1>Licencia Médica</h1>
    </div>

    <div class="clinic-info">
        <h2>{{ $branch->name ?? 'Clínica Médica' }}</h2>
        <p>{{ $branch->address ?? 'Dirección no disponible' }}</p>
        <p>Teléfono: {{ $branch->phone ?? 'N/A' }} | Email: {{ $branch->email ?? 'N/A' }}</p>
    </div>

    <div class="content">
        <div class="patient-info">
            <div class="row">
                <span><span class="label">Paciente:</span> {{ $patient->full_name ?? 'Nombre del Paciente' }}</span>
                <span><span class="label">Identificación:</span> {{ $patient->identifier_value ?? 'N/A' }}</span>
            </div>
            <div class="row">
                <span><span class="label">Fecha de Nacimiento:</span> {{ $patient->birth_date ? $patient->birth_date : 'N/A' }}</span>
                <span><span class="label">Edad:</span> {{ $patient->age ?? 'N/A' }} años</span>
            </div>
        </div>

        <div class="text-block">
            Por medio de la presente, certifico que el(la) paciente mencionado(a) anteriormente ha sido examinado(a) en
            esta fecha y requiere reposo médico por las razones que se detallan a continuación:
        </div>

        <div class="diagnosis">
            <strong>Diagnóstico:</strong> {{ $diagnosis ?? 'Diagnóstico médico' }}
        </div>

        <div class="leave-period">
            <strong>Período de Reposo:</strong> Desde el {{ $startDate ?? 'DD/MM/AAAA' }} hasta el {{ $endDate ?? 'DD/MM/AAAA' }}
            <br>
            <strong>Total de Días:</strong> {{ $days ?? 'X' }} días
        </div>

        <div class="text-block">
            Se recomienda que el paciente guarde reposo absoluto durante el período indicado y se abstenga de realizar
            actividades laborales. Deberá continuar con el tratamiento médico prescrito y asistir a controles médicos
            según sea necesario.
        </div>

        <div class="date-section">
            <p>{{ $city ?? 'Ciudad' }}, {{ $issueDate ?? now()->format('d/m/Y') }}</p>
        </div>

        <div class="signature-section">
            @if($firma)
                <img src="{{ $firma }}"
                     alt="Firma"
                     style="max-width: 150px; max-height: 150px;">
            @endif
            <div class="signature-line">
                <p><strong>{{ $doctor->full_name ?? 'Dr. Nombre del Médico' }}</strong></p>
                <p>{{ $doctor->specialty ?? 'Especialidad' }}</p>
                <p>Licencia Médica: {{ $doctor->license ?? 'XXXX' }}</p>
            </div>
        </div>
    </div>
</body>
</html>
