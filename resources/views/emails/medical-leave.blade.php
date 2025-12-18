<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Licencia Médica</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 30px;
            border: 1px solid #ddd;
            border-top: none;
        }
        .info-box {
            background-color: white;
            padding: 15px;
            margin: 15px 0;
            border-left: 4px solid #4CAF50;
            border-radius: 3px;
        }
        .info-label {
            font-weight: bold;
            color: #555;
        }
        .footer {
            background-color: #333;
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 12px;
            border-radius: 0 0 5px 5px;
        }
        .button {
            display: inline-block;
            padding: 12px 25px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0;">Licencia Médica</h1>
        <p style="margin: 5px 0 0 0;">{{ $medicalLeave->clinic_name }}</p>
    </div>

    <div class="content">
        <p>Estimado/a <strong>{{ $patientName }}</strong>,</p>

        <p>Se ha generado su licencia médica por incapacidad. Adjunto a este correo encontrará el documento oficial en formato PDF.</p>

        <div class="info-box">
            <p class="info-label">Número de Registro:</p>
            <p style="font-size: 18px; margin: 5px 0;"><strong>{{ $identifier }}</strong></p>
        </div>

        <div class="info-box">
            <p class="info-label">Período de Incapacidad:</p>
            <p style="margin: 5px 0;">
                <strong>Desde:</strong> {{ $medicalLeave->start_datetime->format('d/m/Y H:i') }}<br>
                <strong>Hasta:</strong> {{ $medicalLeave->end_datetime->format('d/m/Y H:i') }}<br>
                <strong>Total:</strong> {{ $totalDays }} día(s)
            </p>
        </div>

        <div class="info-box">
            <p class="info-label">Médico Tratante:</p>
            <p style="margin: 5px 0;">{{ $practitionerName }}</p>
        </div>

        @if($medicalLeave->notes)
        <div class="info-box">
            <p class="info-label">Observaciones:</p>
            <p style="margin: 5px 0;">{{ $medicalLeave->notes }}</p>
        </div>
        @endif

        <hr style="border: none; border-top: 1px solid #ddd; margin: 20px 0;">

        <p><strong>Instrucciones importantes:</strong></p>
        <ul>
            <li>Conserve este documento como parte de su expediente médico personal</li>
            <li>Presente esta licencia a su empleador según corresponda</li>
            <li>El documento PDF adjunto tiene validez oficial</li>
            <li>Si tiene dudas, comuníquese con nuestra clínica</li>
        </ul>

        <p style="margin-top: 30px; font-size: 12px; color: #666;">
            <em>Este es un correo automático, por favor no responda a este mensaje. Para cualquier consulta, comuníquese directamente con la clínica.</em>
        </p>
    </div>

    <div class="footer">
        <p style="margin: 0;">{{ $medicalLeave->clinic_name }}</p>
        @if($medicalLeave->clinic_phone)
            <p style="margin: 5px 0 0 0;">Tel: {{ $medicalLeave->clinic_phone }}</p>
        @endif
        @if($medicalLeave->clinic_address)
            <p style="margin: 5px 0 0 0;">{{ $medicalLeave->clinic_address }}</p>
        @endif
    </div>
</body>
</html>
