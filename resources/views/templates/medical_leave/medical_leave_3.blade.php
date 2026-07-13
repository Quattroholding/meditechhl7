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

            /* Fondo con diagonales suaves (similar al diseño) */
            background: linear-gradient(135deg,
            rgba(0,0,0,0.03) 0%,
            rgba(0,0,0,0.03) 10%,
            transparent 10%,
            transparent 30%);
        }

        :root{
            --blue-dark: #005B87;
            --blue: #006FA4;
            --cyan: #00B9C6;
            --green: #00B86B;
            --text-muted:#6b7280;
            --line:#263b5f;
        }

        /* ====== Header ====== */
        .clinic-info{
            position:absolute;
            top: 14mm;
            left: 35mm;
            font-size: 12pt;
            line-height: 1.35;
            color: var(--text-muted);
        }

        .logo{
            position:absolute;
            top: 18mm;
            left: 130mm;
            font-size: 40pt;
            font-weight: 900;
            color: var(--cyan);
            letter-spacing: 1px;
        }

        .top-right{
            position:absolute;
            top: 14mm;
            right: 5mm;
            font-size: 13pt;
            line-height: 1.7;
            text-align:left;
        }

        .label{
            font-weight: 800;
            color: #111827;
        }

        /* Línea paciente */
        .inline-line{
            display:inline-block;
            border-bottom: 2px solid var(--line);
            min-width: 100mm;
            height: 6mm;
            vertical-align: baseline;
        }

        /* ====== Barra título ====== */
        .title-wrap{
            position:absolute;
            top: 65mm;
            left: 0;
            right: 0;
            text-align:center;
        }
        .title{
            display:inline-block;
            background: var(--blue);
            color:#fff;
            font-weight: 900;
            font-size: 30pt;
            padding: 1mm 18mm;
            border-radius: 1mm;
        }

        /* ====== Texto principal ====== */
        .content{
            position:absolute;
            top: 88mm;
            left: 30mm;
            right: 35mm;
            font-size: 18pt;
            line-height: 1.9;
        }

        .fill-line{
            display:inline-block;
            border-bottom: 2px solid #111;
            height: 10mm;
            vertical-align: baseline;
        }

        .fill-xl{ min-width: 125mm; }
        .fill-lg{ min-width: 110mm; }
        .fill-md{ min-width: 35mm; }
        .fill-sm{ min-width: 25mm; }

        /* ====== Footer doctor ====== */
        .doctor{
            position:absolute;
            bottom: 22mm;
            left: 0;
            right: 0;
            text-align:center;
            font-size: 20pt;
            font-weight: 900;
            color: var(--cyan);
        }

        /* ====== Decoración Superior Izquierda ====== */
        .decor-top-left{
            position:absolute;
            left:0;
            top:0;
            width: 25mm;
            height: 70mm;
        }
        .decor-top-left .b1{
            position:absolute; left:0; top:0;
            width: 10mm; height: 50mm;
            background: var(--blue-dark);
        }
        .decor-top-left .b2{
            position:absolute; left:10mm; top:0;
            width: 10mm; height: 70mm;
            background: var(--green);
        }
        .decor-top-left .b3{
            position:absolute; left:20mm; top:0;
            width: 10mm; height: 52mm;
            background: var(--cyan);
        }

        /* ====== Decoración Inferior Derecha ====== */
        .decor-bottom-right{
            position:absolute;
            right:0;
            bottom:0;
            width: 50mm;
            height: 80mm;
        }
        .decor-bottom-right .c1{ /* azul oscuro */
            position:absolute;
            right:0;
            bottom:0;
            width: 15mm;
            height: 50mm;
            background: var(--blue-dark);
        }
        .decor-bottom-right .c2{ /* cyan */
            position:absolute;
            right:15mm;
            bottom:0;
            width: 14mm;
            height: 69mm;
            background: var(--green);
        }
        .decor-bottom-right .c3{ /* verde */
            position:absolute;
            right:29mm;
            bottom:0;
            width: 14mm;
            height: 55mm;
            background: var(--cyan);
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
    <div class="decor-top-left">
        <div class="b1"></div>
        <div class="b2"></div>
        <div class="b3"></div>
    </div>

    <div class="decor-bottom-right">
        <div class="c1"></div>
        <div class="c2"></div>
        <div class="c3"></div>
    </div>

    {{-- Encabezado --}}
    <div class="clinic-info">
        <h3>{{ $branch->name ?? 'Centro Médico' }}</h3>
        <p>{{ $branch->address ?? 'Dirección no disponible' }}</p>
        <p>Tel: {{ $branch->phone ?? 'N/A' }} | {{ $branch->email ?? 'N/A' }}</p>
    </div>

    <div class="logo"> @if(is_file(storage_path('/app/public/'.$logo))) <img src="{{storage_path('/app/public/'.$logo)}}"> @else LOGO @endif</div>

    <div class="top-right">
        <div>
            <span class="label">Fecha:</span>
            <span class="fill-sm">{{ \Carbon\Carbon::parse($medicalLeave->date ?? now())->translatedFormat('d \\d\\e F \\d\\e Y') }}</span>
        </div>
        <div>
            <span class="label">Paciente:</span>
            <span class="fill-sm">&nbsp;{{ $medicalLeave->patient_name ?? '' }}</span>
        </div>
    </div>

    {{-- Título --}}
    <div class="title-wrap">
        <div class="title">Certificado de Incapacidad</div>
    </div>

    {{-- Contenido --}}
    <div class="content">

        El suscrito médico certifica que
        <span class="fill-line fill-xl">&nbsp;{{ $medicalLeave->patient_name ?? '' }}</span>
        <br>

        con cédula No.
        <span class="fill-line fill-lg">&nbsp;{{ $patient->identifier_value ?? '' }}</span>,
        ha sido examinado (a)
        <br>

        y considera que está (ha estado) incapacitado (a) por
        <span class="fill-line fill-md">&nbsp;{{ $medicalLeave->total_days ?? '' }}</span>
        días /horas
        <br>

        para efectuar sus labores Hora:
        <span class="fill-line fill-sm">&nbsp;{{ $start_time ?? '' }}</span>;
        Día:
        <span class="fill-line fill-sm">&nbsp;{{ $start_day ?? '' }}</span>;
        Mes:
        <span class="fill-line fill-sm">&nbsp;{{ $start_month ?? '' }}</span>;
        Año:
        <span class="fill-line fill-sm">&nbsp;{{ $start_year ?? '' }}</span>
        hasta
        <br>

        Hora:
        <span class="fill-line fill-sm">&nbsp;{{ $end_time ?? '' }}</span>;
        Día:
        <span class="fill-line fill-sm">&nbsp;{{ $end_day ?? '' }}</span>;
        Mes:
        <span class="fill-line fill-sm">&nbsp;{{ $end_month ?? '' }}</span>;
        Año:
        <span class="fill-line fill-sm">&nbsp;{{ $end_year ?? '' }}</span>

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

