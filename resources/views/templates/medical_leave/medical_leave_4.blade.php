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
            color: #111;
        }

        .page {
            position: relative;
            width: 297mm;
            height: 210mm;
            overflow: hidden;
            background: #fff;
        }

        .top-left-stripes{
            position:absolute;
            left:0;
            top:0;
            width:85mm;
            height:55mm;
            overflow:hidden;
        }

        .top-left-stripes .stripe{
            position:absolute;
            width:125mm;
            height:1mm;
            background:#000;
            transform:rotate(135deg);
            left:-50mm;
        }


        /* ===== Bloque negro superior derecho ===== */
        .top-right-block{
            position:absolute;
            right:0;
            top:0;
            width: 20mm;
            height: 60mm;
            background:#000;
        }

        /* ===== Bloque negro inferior derecho ===== */
        .bottom-right-block{
            position:absolute;
            right:0;
            bottom:0;
            width: 10mm;
            height: 300mm;
            background:#000;
        }

        /* ===== Header ===== */
        .logo{
            position:absolute;
            top: 18mm;
            left: 70mm;
            font-size: 42pt;
            font-weight: 800;
            color:#000;
            letter-spacing: 1px;
        }

        .clinic-info{
            position:absolute;
            top: 12mm;
            left: 150mm;
            font-size: 12pt;
            color:#6b7280;
            line-height: 1.35;
        }

        /* ===== Title bar ===== */
        .title{
            position:absolute;
            top: 60mm;
            left: 20mm;
            background:#000;
            color:#fff;
            font-weight: 900;
            font-size: 32pt;
            padding: 1mm 12mm;
            width: 190mm;
        }

        /* ===== Texto principal ===== */
        .content{
            position:absolute;
            top: 80mm;
            left: 20mm;
            right: 20mm;
            font-size: 18pt;
            line-height: 1.9;
        }

        /* Líneas para rellenar */
        .fill-line{
            display:inline-block;
            border-bottom: 2px solid #000;
            height: 10mm;
            vertical-align: baseline;
        }

        .xl{ min-width: 135mm; }
        .lg{ min-width: 115mm; }
        .md{ min-width: 55mm; }
        .sm{ min-width: 30mm; }

        /* ===== Footer doctor ===== */
        .doctor{
            position:absolute;
            bottom: 15mm;
            left: 10mm;
            right: 0;
            text-align: center;
            font-size: 20pt;
            font-weight: 900;
            color:#000;
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

    {{-- Decoración --}}
    <div class="top-left-stripes">
        @for($i=0;$i<7;$i++)
            <div class="stripe" style="top: {{ $i*6 }}mm;"></div>
        @endfor
    </div>
    <div class="top-right-block"></div>
    <div class="bottom-right-block"></div>

    {{-- Header --}}
    <div class="logo"> @if(is_file(storage_path('/app/public/'.$logo))) <img src="{{storage_path('/app/public/'.$logo)}}"> @else LOGO @endif</div>

    <div class="clinic-info">
        <h3>{{ $branch->name ?? 'Centro Médico' }}</h3>
        <p>{{ $branch->address ?? 'Dirección no disponible' }}</p>
        <p>Tel: {{ $branch->phone ?? 'N/A' }} | {{ $branch->email ?? 'N/A' }}</p>
    </div>

    {{-- Title --}}
    <div class="title">Certificado de Incapacidad</div>

    {{-- Content --}}
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
        días /horas para efectuar sus labores
        <br>

        Hora:
        <span class="fill-line sm">&nbsp;{{ $start_time ?? '' }}</span>;
        Día:
        <span class="fill-line sm">&nbsp;{{ $start_day ?? '' }}</span>;
        Mes:
        <span class="fill-line sm">&nbsp;{{ $start_month ?? '' }}</span>;
        Año:
        <span class="fill-line sm">&nbsp;{{ $start_year ?? '' }}</span>
        hasta Hora:
        <span class="fill-line sm">&nbsp;{{ $end_time ?? '' }}</span>;
        Día:
        <span class="fill-line sm">&nbsp;{{ $end_day ?? '' }}</span>;
        Mes:
        <span class="fill-line sm">&nbsp;{{ $end_month ?? '' }}</span>;
        Año:
        <span class="fill-line sm">&nbsp;{{ $end_year ?? '' }}</span>
    </div>

    {{-- Footer --}}
    <div class="doctor">
        {{ $medicalLeave->practitioner_name ?? 'Nombre del Médico aquí' }} – Registro No.{{ $medicalLeave->practitioner_license_number ?? '0000000' }}
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
