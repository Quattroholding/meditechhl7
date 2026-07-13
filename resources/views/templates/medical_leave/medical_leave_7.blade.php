<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Certificado de Incapacidad</title>

    <style>
        @page{
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
            overflow:hidden;

            /* Fondo con diagonales muy suaves */
            background:
                repeating-linear-gradient(
                    135deg,
                    rgba(0,0,0,0.03) 0,
                    rgba(0,0,0,0.03) 16mm,
                    transparent 16mm,
                    transparent 32mm
                ),
                #fff;
        }

        :root{
            --blue:#176ad6;
            --blue-dark:#0b3d9c;
            --blue-soft:#1d73ea;
            --muted:#6b7280;
        }

        /* ====== barra superior (degradada azul) ====== */
        .top-bar{
            position:absolute;
            left: 140mm;
            top:0;
            width: 167mm;
            height: 15mm;
            background: var(--blue);
        }

        /* ====== barra inferior (azul) ====== */
        .bottom-bar{
            position:absolute;
            left:0;
            bottom:0;
            width: 297mm;
            height: 20mm;
            background: var(--blue);
        }

        /* ====== zona derecha (foto) ====== */
        .photo-area{
            position:absolute;
            right:0;
            top: 22mm;
            width: 90mm;
            height: 170mm;
            overflow:hidden;
            z-index: 2;
        }

        /* Si tienes foto PNG/JPG del doctor */
        .photo-area img{
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            display:block;
        }

        /* ====== header ====== */
        .logo{
            position:absolute;
            top: 18mm;
            left: 20mm;
            font-size: 44pt;
            font-weight: 900;
            color: var(--blue);
        }

        .clinic-info{
            position:absolute;
            top: 16mm;
            left: 85mm;
            font-size: 12pt;
            color: var(--muted);
            line-height: 1.35;
        }

        /* ====== título ====== */
        .title{
            position:absolute;
            top: 60mm;
            left: 20mm;
            width: 180mm;
            background: var(--blue);
            color:#fff;
            font-weight: 900;
            font-size: 32pt;
            padding: 1mm 12mm;
        }

        /* ====== body ====== */
        .content{
            position:absolute;
            top: 82mm;
            left: 20mm;
            right: 45mm; /* para no montarse sobre la foto */
            font-size: 18pt;
            line-height: 1.9;
            z-index: 3;
        }

        .fill-line{
            display:inline-block;
            border-bottom: 2px solid #000;
            height: 10mm;
            vertical-align: baseline;
        }

        .xl{ min-width: 125mm; }
        .lg{ min-width: 110mm; }
        .md{ min-width: 45mm; }
        .sm{ min-width: 28mm; }

        /* ===== footer doctor ===== */
        .doctor{
            position:absolute;
            left: 20mm;
            bottom: 34mm;
            font-size: 20pt;
            font-weight: 900;
            color: var(--blue);
            z-index: 3;
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

    {{-- barra superior --}}
    <div class="top-bar"></div>

    {{-- foto derecha (opcional) --}}
    <div class="photo-area">
        {{-- si tienes la foto la pones aqui --}}
        {{-- <img src="{{ public_path('images/doctor.png') }}" alt="Doctor"> --}}
    </div>

    {{-- header --}}
    <div class="logo">@if(is_file(storage_path('/app/public/'.$logo))) <img src="{{storage_path('/app/public/'.$logo)}}"> @else LOGO @endif</div>

    <div class="clinic-info">
        <h3>{{ $branch->name ?? 'Centro Médico' }}</h3>
        <p>{{ $branch->address ?? 'Dirección no disponible' }}</p>
        <p>Tel: {{ $branch->phone ?? 'N/A' }} | {{ $branch->email ?? 'N/A' }}</p>
    </div>

    {{-- titulo --}}
    <div class="title">Certificado de Incapacidad</div>

    {{-- contenido --}}
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

    {{-- footer --}}
    <div class="doctor">
        {{ $medicalLeave->practitioner_name ?? 'Nombre del Médico aquí' }} – Registro No.{{ $medicalLeave->practitioner_license_number ?? '0000000' }}
    </div>

    {{-- barra inferior --}}
    <div class="bottom-bar"></div>

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

