<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificado de Incapacidad - {{ $medicalLeave->identifier }}</title>
    <style>
        @page {
            margin: 1.5cm 2cm;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #000;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo {
            max-width: 100px;
            margin-bottom: 10px;
        }
        .header h1 {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 5px 0;
            color: #1a4d7a;
        }
        .header h2 {
            font-size: 12pt;
            margin: 3px 0;
            font-weight: normal;
        }
        .header .contact-info {
            font-size: 9pt;
            margin-top: 5px;
            color: #333;
        }
        .certificate-number {
            text-align: left;
            font-size: 14pt;
            font-weight: bold;
            margin: 20px 0 15px 0;
            color: #d32f2f;
        }
        .section-line {
            border-bottom: 1px solid #000;
            padding-bottom: 2px;
            margin-bottom: 20px;
            font-size: 10pt;
        }
        .content-block {
            margin: 25px 0;
            font-size: 11pt;
            line-height: 2;
        }
        .underline-field {
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 200px;
            padding: 0 10px;
            text-align: center;
        }
        .date-fields {
            margin: 30px 0;
            font-size: 11pt;
        }
        .date-row {
            margin: 15px 0;
            display: flex;
            align-items: center;
        }
        .date-label {
            font-weight: bold;
            width: 80px;
        }
        .date-field {
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 60px;
            text-align: center;
            padding: 0 10px;
            margin: 0 5px;
        }
        .signature-table {
            width: 100%;
            margin-top: 60px;
            border-collapse: collapse;
        }
        .signature-table td {
            vertical-align: top;
            text-align: center;
            padding: 10px;
        }
        .signature-image-container {
            min-height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 10px;
            padding-top: 5px;
            font-size: 9pt;
        }
        .doctor-info {
            margin-top: 10px;
            font-size: 9pt;
        }
        .footer-note {
            margin-top: 40px;
            font-size: 8pt;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    <!-- Encabezado -->
    <div class="header">
        @if($medicalLeave->clinic_logo_url)
            <img src="{{ public_path($medicalLeave->clinic_logo_url) }}" alt="Logo" class="logo">
        @endif
        <h1>CERTIFICADO DE INCAPACIDAD</h1>
        <h2>{{ strtoupper($medicalLeave->clinic_name) }}</h2>
        <div class="contact-info">
            @if($medicalLeave->clinic_phone)
                Tels.: {{ $medicalLeave->clinic_phone }}
            @endif
            @if($medicalLeave->clinic_address)
                 - {{ $medicalLeave->clinic_address }}
            @endif
        </div>
    </div>

    <!-- Número de Certificado -->
    <div class="certificate-number">
        N° {{ $medicalLeave->identifier }}
    </div>

    <!-- Servicio o Sección -->

        Servicio o Sección: <span class="underline-field">Consulta Médica</span>


    <!-- Texto principal del certificado -->
    <div class="content-block">
        El suscrito Médico, Certifica que el Sr. (a)
        <span class="underline-field"><strong>{{ $medicalLeave->patient_name }}</strong></span>
        cédula de identidad personal N°
        <span class="underline-field">{{ $medicalLeave->patient->identifier ?? '______________' }}</span>
        ha sido examinado (a) y extiende una incapacidad
        <span class="underline-field"></span>
        horas;
        <span class="underline-field"><strong>{{ $medicalLeave->total_days }}</strong></span>
        días para efectuar sus labores habituales.
    </div>

    <!-- Período de fechas -->
    <div class="date-fields">
        <div class="date-row">
            <span class="date-label">Del día</span>
            <span class="date-field">{{ $medicalLeave->start_datetime->format('d') }}</span>
            hora
            <span class="date-field">{{ $medicalLeave->start_datetime->format('H:i') }}</span>
            mes
            <span class="date-field">{{ $medicalLeave->start_datetime->locale('es')->monthName }}</span>
            año
            <span class="date-field">{{ $medicalLeave->start_datetime->format('y') }}</span>
        </div>

        <div class="date-row">
            <span class="date-label">Al día</span>
            <span class="date-field">{{ $medicalLeave->end_datetime->format('d') }}</span>
            hora
            <span class="date-field">{{ $medicalLeave->end_datetime->format('H:i') }}</span>
            mes
            <span class="date-field">{{ $medicalLeave->end_datetime->locale('es')->monthName }}</span>
            año
            <span class="date-field">{{ $medicalLeave->end_datetime->format('y') }}</span>
        </div>
    </div>

    @if($medicalLeave->diagnosis || $medicalLeave->diagnosis_code)
    <div style="margin: 25px 0; font-size: 10pt; line-height: 1.8;">
        <strong>Diagnóstico:</strong>
        @if($medicalLeave->diagnosis_code)
            (CIE-10: {{ $medicalLeave->diagnosis_code }})
        @endif
        {{ $medicalLeave->diagnosis }}
    </div>
    @endif

    @if($medicalLeave->notes)
    <div style="margin: 25px 0; font-size: 10pt; line-height: 1.8;">
        <strong>Observaciones:</strong> {{ $medicalLeave->notes }}
    </div>
    @endif

    <!-- Bloque de firmas -->
    <table class="signature-table">
        <tr>
            <!-- Columna de Firma -->
            <td style="width: 40%;">
                <div class="signature-image-container">
                    @if($medicalLeave->getPractitionerFilePathAttribute('signature'))
                        <img src="{{ $medicalLeave->getPractitionerFilePathAttribute('signature') }}"
                             alt="Firma"
                             style="max-width: 180px; max-height: 70px;">
                    @endif
                </div>
                <div class="signature-line">
                    Firma
                </div>
            </td>

            <!-- Columna de Sello y Nombre -->
            <td style="width: 60%;">
                <div class="signature-image-container">
                    @if($medicalLeave->getPractitionerFilePathAttribute('seal'))
                        <img src="{{ $medicalLeave->getPractitionerFilePathAttribute('seal') }}"
                             alt="Sello"
                             style="max-width: 150px; max-height: 150px;">
                    @endif
                </div>
                <div class="signature-line">
                    Nombre del Médico<br>
                    (en letra imprenta)
                </div>
                <div class="doctor-info">
                    <strong>{{ strtoupper($medicalLeave->practitioner_name) }}</strong>
                    @if($medicalLeave->practitioner_license_number)
                        <br>N° de Registro del Médico: {{ $medicalLeave->practitioner_license_number }}
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <!-- Nota al pie -->
    <div class="footer-note">
        Este documento debe quedar archivado en el expediente médico del paciente conforme a las regulaciones vigentes en la República de Panamá.
        <br>Generado electrónicamente el {{ now()->format('d/m/Y') }} a las {{ now()->format('H:i') }}
    </div>
</body>
</html>
