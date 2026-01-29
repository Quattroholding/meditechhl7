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

        /* ====== Lines / frames ====== */
        .frame-top-left{
            position:absolute;
            left: 10mm;
            top: 10mm;
            width: 95mm;
            height: 95mm;
        }
        .frame-top-left:before{
            content:"";
            position:absolute;
            left:0; top:0;
            width: 100%;
            height: 100%;
            border-left: 1mm solid #000;
            border-top: 1mm solid #000;
        }

        .frame-bottom-right{
            position:absolute;
            right: 12mm;
            bottom: 12mm;
            width: 95mm;
            height: 95mm;
        }
        .frame-bottom-right:before{
            content:"";
            position:absolute;
            right:0; bottom:0;
            width: 100%;
            height: 100%;
            border-right: 1mm solid #000;
            border-bottom: 1mm solid #000;
        }

        /* Línea vertical derecha (mitad) */
        .right-mid-line{
            position:absolute;
            right: 18mm;
            top: 110mm;
            width: 2mm;
            height: 38mm;
            background:#000;
        }

        /* ====== decoración arriba derecha (cuadrados) ====== */
        .squares{
            position:absolute;
            right: 22mm;
            top: 16mm;
            width: 45mm;
            height: 45mm;
        }
        .squares .sq{
            position:absolute;
            width: 30mm;
            height: 30mm;
            border: 1px solid rgba(0,0,0,.45);
        }
        .squares .sq.one{ right: 0; top: 0; }
        .squares .sq.two{ right: 8mm; top: 10mm; }

        .squares .solid{
            position:absolute;
            width: 12mm;
            height: 12mm;
            background:#000;
            right: 8mm;
            top: -2mm;
        }

        /* ====== Header ====== */
        .logo{
            position:absolute;
            top: 22mm;
            left: 20mm;
            font-size: 42pt;
            font-weight: 900;
            color:#000;
        }

        .clinic-info{
            position:absolute;
            top: 15mm;
            left: 85mm;
            font-size: 12pt;
            line-height: 1.35;
            color:#6b7280;
        }

        /* ====== Title bar ====== */
        .title{
            position:absolute;
            top: 65mm;
            left: 20mm;
            width: 195mm;
            background:#000;
            color:#fff;
            font-weight: 900;
            font-size: 32pt;
            padding: 1mm 12mm;
        }

        /* ====== Body ====== */
        .content{
            position:absolute;
            top: 85mm;
            left: 20mm;
            right: 20mm;
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
        .md{ min-width: 55mm; }
        .sm{ min-width: 30mm; }

        /* ===== Footer doctor ===== */
        .doctor{
            position:absolute;
            bottom: 22mm;
            left: 20mm;
            font-size: 20pt;
            font-weight: 900;
            color:#000;
            white-space: nowrap;
        }

    </style>
</head>
<body>

<div class="page">

    {{-- Marcos --}}
    <div class="frame-top-left"></div>
    <div class="frame-bottom-right"></div>

    {{-- Decoración cuadrados --}}
    <div class="squares">
        <div class="solid"></div>
        <div class="sq one"></div>
        <div class="sq two"></div>
    </div>

    {{-- Línea vertical derecha --}}


    {{-- Header --}}
    <div class="logo">@if(is_file(storage_path('/app/public/'.$logo))) <img src="{{storage_path('/app/public/'.$logo)}}"> @else LOGO @endif</div>

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

</div>

</body>
</html>

