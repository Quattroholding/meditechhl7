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

        html, body{
            margin:0;
            padding:0;
            font-family: Arial, Helvetica, sans-serif;
            color:#111;
        }

        .page{
            position: relative;
            width: 297mm;
            height: 210mm;
            overflow: hidden;
            background:#fff;
        }

        :root{
            --cyan: #00B9C6;
            --cyan-soft: #7ED9E6;
            --line: #243b63;
            --muted: #6b7280;
        }

        /* ===== Franja celeste derecha ===== */
        .right-band{
            position:absolute;
            top:0;
            right:0;
            width: 48mm;
            height: 210mm;
            background: rgba(0, 185, 198, 0.55);
        }

        /* ===== Pastillas (opcional) =====
           Si NO quieres imagen, comenta este bloque completo.
           Si tienes una imagen transparente solo de pastillas, ponla aquí.
        */
        .pills{
            position:absolute;
            right: 12mm;
            top: 95mm;
            width: 60mm;
            height: 60mm;
            z-index: 3;
            /* DESCOMENTA si tienes la imagen:
            background: url('{{ public_path("images/pills.png") }}') no-repeat center;
            background-size: contain;
            */
        }

        /* ===== Header ===== */
        .logo{
            position:absolute;
            top: 18mm;
            left: 20mm;
            font-size: 44pt;
            font-weight: 900;
            color: var(--cyan);
        }

        .clinic-info{
            position:absolute;
            top: 16mm;
            left: 105mm;
            font-size: 12pt;
            line-height: 1.35;
            color: var(--muted);
        }

        .top-fields{
            position:absolute;
            top: 52mm;
            left: 20mm;
            font-size: 14pt;
            line-height: 2;
            color:#111827;
        }

        .label{ font-weight: 900; }

        .inline-line{
            display:inline-block;
            border-bottom: 2px solid var(--line);
            min-width: 110mm;
            height: 10mm;
            vertical-align: baseline;
        }

        /* ===== Título ===== */
        .title{
            position:absolute;
            top: 80mm;
            left: 20mm;
            background: var(--cyan-soft);
            color:#fff;
            font-weight: 900;
            font-size: 32pt;
            padding: 1mm 10mm;
            width: 170mm;
        }

        /* ===== Contenido ===== */
        .content{
            position:absolute;
            top: 100mm;
            left: 20mm;
            right: 50mm; /* evita montarse con la banda derecha */
            font-size: 18pt;
            line-height: 1.9;
        }

        .fill-line{
            display:inline-block;
            border-bottom: 2px solid #000;
            height: 10mm;
            vertical-align: baseline;
        }

        .xl{ min-width: 135mm; }
        .lg{ min-width: 115mm; }
        .md{ min-width: 50mm; }
        .sm{ min-width: 28mm; }

        /* ===== Footer ===== */
        .doctor{
            position:absolute;
            bottom: 10mm;
            left: 20mm;
            font-size: 20pt;
            font-weight: 900;
            color: rgba(0,185,198,.65);
            white-space: nowrap;
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

    {{-- Banda derecha --}}
    <div class="right-band"></div>

    {{-- Pastillas (opcional) --}}
    <div class="pills"></div>

    {{-- Header --}}
    <div class="logo"> @if(is_file(storage_path('/app/public/'.$logo))) <img src="{{storage_path('/app/public/'.$logo)}}"> @else LOGO @endif</div>

    <div class="clinic-info">
        <h3>{{ $branch->name ?? 'Centro Médico' }}</h3>
        <p>{{ $branch->address ?? 'Dirección no disponible' }}</p>
        <p>Tel: {{ $branch->phone ?? 'N/A' }} | {{ $branch->email ?? 'N/A' }}</p>
    </div>

    <div class="top-fields">
        <div>
            <span class="label">Fecha:</span>
            <span class="inline-line">{{ \Carbon\Carbon::parse($medicalLeave->date ?? now())->translatedFormat('d \\d\\e F \\d\\e Y') }}</span>
        </div>

        <div>
            <span class="label">Paciente:</span>
            <span class="inline-line">&nbsp;{{ $medicalLeave->patient_name ?? '' }}</span>
        </div>
    </div>

    {{-- Título --}}
    <div class="title">Certificado de Incapacidad</div>

    {{-- Contenido --}}
    <div class="content">

        El suscrito médico certifica que
        <span class="fill-line xl">&nbsp;{{ $medicalLeave->patient_name ?? '' }}</span>
        <br>

        con cédula No.
        <span class="fill-line lg">&nbsp;{{ $patient->identifier_value ?? '' }}</span>,
        ha sido examinado (a)
        <br>

        y considera que está (ha estado) incapacitado (a) por
        <span class="fill-line sm">&nbsp;{{ $medicalLeave->total_days ?? '' }}</span>
        días /horas
        <br>

        para efectuar sus labores Hora:
        <span class="fill-line sm">&nbsp;{{ $start_time ?? '' }}</span>;
        Día:
        <span class="fill-line sm">&nbsp;{{ $start_day ?? '' }}</span>;
        Mes:
        <span class="fill-line sm">&nbsp;{{ $start_month ?? '' }}</span>;
        Año:
        <span class="fill-line sm">&nbsp;{{ $start_year ?? '' }}</span>
        hasta
        <br>

        Hora:
        <span class="fill-line sm">&nbsp;{{ $end_time ?? '' }}</span>;
        Día:
        <span class="fill-line sm">&nbsp;{{ $end_day ?? '' }}</span>;
        Mes:
        <span class="fill-line sm">&nbsp;{{ $end_month ?? '' }}</span>;
        Año:
        <span class="fill-line sm">&nbsp;{{ $end_year ?? '' }}</span>

    </div>

    {{-- Doctor --}}
    <div class="doctor">
        {{ $medicalLeave->practitioner_name ?? 'Nombre del Médico aquí' }} – Registro No.{{ $medicalLeave->doctor_license ?? '0000000' }}
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

