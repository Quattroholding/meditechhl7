<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Certificado de Incapacidad</title>

    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }

        html, body {
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
            color: #2a2a2a;
        }

        /* A4 horizontal */
        .page {
            position: relative;
            width: 297mm;
            height: 210mm;
            overflow: hidden;
            background: #ffffff;
        }

        /* ====== Colores del diseño ====== */
        :root{
            --turq: #00B9C6;      /* turquesa fuerte */
            --turq2: #4CA6A0;     /* verde agua */
            --title: #84D9E6;     /* celeste del título */
            --text: #1f2937;
            --line: #1f3c64;      /* azul oscuro de líneas */
        }

        /* ====== Decoraciones laterales ====== */
        .block-left {
            position: absolute;
            left: 0;
            top: 128mm;
            width: 35mm;
            height: 85mm;
            background: var(--turq);
        }

        .block-left-soft {
            position: absolute;
            left: 0;
            top: 100mm;
            width: 26mm;
            height: 98mm;
            background: var(--turq2);
        }

        .block-right {
            position: absolute;
            right: 0;
            top: 46mm;
            width: 30mm;
            height: 145mm;
            background: var(--turq2);
        }

        .block-left-2 {
            position: absolute;
            right: 0;
            top: 65mm;
            width: 20mm;
            height: 155mm;
            background: var(--turq);
        }

        /* ====== Header ====== */
        .header {
            position: absolute;
            top: 10mm;
            left: 18mm;
            right: 18mm;
            height: 35mm;
        }

        .logo {
            position: absolute;
            left: 0;
            top: 0;
            font-size: 28pt;
            color: var(--turq);
            font-weight: 800;
            letter-spacing: 1px;
        }

        .clinic-info {
            position: absolute;
            left: 60mm;
            top: 0mm;
            width: 95mm;
            font-size: 11pt;
            color: #4b5563;
            line-height: 1.4;
        }

        .top-right {
            position: absolute;
            right: 0;
            top: 0;
            width: 100mm;
            font-size: 12pt;
            text-align: left;
        }

        .top-right .row {
            margin-bottom: 6mm;
        }

        /* ====== Líneas para llenar ====== */
        .line {
            display: inline-block;
            border-bottom: 2px solid var(--line);
            min-width: 80mm;
            height: 6mm;
            vertical-align: baseline;
        }

        .line.sm { min-width: 60mm; }
        .line.xs { min-width: 30mm; }

        .label {
            font-weight: 700;
            color: #111827;
        }

        /* ====== Título ====== */
        .title-bar {
            position: absolute;
            top: 60mm;
            left: 35mm;
            width: 150mm;
            background: var(--title);
            padding: 1mm 10mm;
            border-radius: 2mm;
        }

        .title-bar h1 {
            margin: 0;
            font-size: 28pt;
            font-weight: 800;
            color: white;
        }

        /* ====== Body ====== */
        .content {
            position: absolute;
            top: 85mm;
            left: 45mm;
            right: 35mm;
            font-size: 16pt;
            line-height: 1.7;
        }

        .content .fill-line {
            display: inline-block;
            border-bottom: 2px solid #111827;
            min-width: 120mm;
            height: 8mm;
            vertical-align: baseline;
        }

        .content .fill-line.sm {
            min-width: 95mm;
        }

        .content .fill-line.xs {
            min-width: 28mm;
        }

        /* ====== Footer doctor ====== */
        .doctor {
            position: absolute;
            bottom: 18mm;
            left: 45mm;
            font-size: 18pt;
            font-weight: 800;
            color: #0b5b93;
        }

        /* ====== QR Code ====== */
        .qr-container {
            position: absolute;
            bottom: 10mm;
            right: 18mm;
            width: 40mm;
            height: 40mm;
            border: 2px solid #ddd;
            padding: 2mm;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 2mm;
        }

        .qr-container img {
            width: 35mm;
            height: 35mm;
            object-fit: contain;
        }

        .qr-label {
            position: absolute;
            bottom: 5mm;
            right: 18mm;
            font-size: 8pt;
            color: #666;
            font-weight: 600;
            text-align: center;
            width: 40mm;
        }
    </style>
</head>

<body>
<div class="page">

    {{-- bloques decorativos --}}
    <div class="block-left-soft"></div>
    <div class="block-left"></div>
    <div class="block-right"></div>
    <div class="block-left-2"></div>

    {{-- HEADER --}}
    <div class="header">
        <div class="logo">
            @if(is_file(storage_path('/app/public/'.$logo))) <img src="{{storage_path('/app/public/'.$logo)}}"> @else LOGO @endif
        </div>

        <div class="clinic-info">
            <h3>{{ $branch->name ?? 'Centro Médico' }}</h3>
            <p>{{ $branch->address ?? 'Dirección no disponible' }}</p>
            <p>Tel: {{ $branch->phone ?? 'N/A' }} | {{ $branch->email ?? 'N/A' }}</p>
        </div>

        <div class="top-right">
            <div class="row">
                <span class="label">Fecha:</span>
                <span class="line sm">
                    {{ \Carbon\Carbon::parse($medicalLeave->date ?? now())->translatedFormat('d \\d\\e F \\d\\e Y') }}
                </span>
            </div>

            <div class="row">
                <span class="label">Paciente:</span>
                <span class="line sm">
                    &nbsp;{{ $medicalLeave->patient_name ?? '' }}
                </span>
            </div>
        </div>
    </div>

    {{-- TÍTULO --}}
    <div class="title-bar">
        <h1>Certificado de Incapacidad</h1>
    </div>

    {{-- CONTENIDO --}}
    <div class="content">

        El suscrito médico certifica que
        <span class="fill-line">
            &nbsp;{{ $medicalLeave->patient_name ?? '' }}
        </span>
        <br>

        con cédula No.
        <span class="fill-line sm">
            &nbsp;{{ $patient->identifier_value ?? '' }}
        </span>,
        ha sido examinado (a)
        <br>

        y considera que está (ha estado) incapacitado (a) por
        <span class="fill-line xs">
            &nbsp;{{ $medicalLeave->total_days ?? '' }}
        </span>
        días /horas
        <br>

        para efectuar sus labores Hora:
        <span class="fill-line xs">&nbsp;{{ $start_time ?? '' }}</span>;
        Día:
        <span class="fill-line xs">&nbsp;{{ $start_day ?? '' }}</span>;
        Mes:
        <span class="fill-line xs">&nbsp;{{ $start_month ?? '' }}</span>;
        Año:
        <span class="fill-line xs">&nbsp;{{ $start_year ?? '' }}</span>
        <br>

        hasta Hora:
        <span class="fill-line xs">&nbsp;{{ $end_time ?? '' }}</span>;
        Día:
        <span class="fill-line xs">&nbsp;{{ $end_day ?? '' }}</span>;
        Mes:
        <span class="fill-line xs">&nbsp;{{ $end_month ?? '' }}</span>;
        Año:
        <span class="fill-line xs">&nbsp;{{ $end_year ?? '' }}</span>

    </div>

    {{-- FOOTER --}}
    <div class="doctor">
        {{ $medicalLeave->practitioner_name ?? 'Nombre del Médico aquí' }} -
        Registro No.{{ $medicalLeave->practitioner_license_number ?? '0000000' }}
    </div>

    {{-- QR CODE --}}
    @if($qrCode ?? false)
        <div class="qr-label">Escaneá para verificar</div>
        <div class="qr-container">
            <img src="{{ $qrCode }}" alt="Código QR de verificación">
        </div>
    @endif

</div>
</body>
</html>
