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
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
            color: #111;
        }

        /* Tamaño A4 horizontal */
        .page{
            position: relative;
            width: 297mm;
            height: 210mm;
            overflow: hidden;
            background: #fff;
        }

        /* Bloque negro esquina arriba derecha */
        .corner-top-right{
            position: absolute;
            right: 0;
            top: 0;
            width: 15mm;
            height: 70mm;
            background: #000;
        }

        /* Header */
        .logo{
            position: absolute;
            top: 18mm;
            left: 20mm;
            font-size: 40pt;
            font-weight: 800;
            color: #000;
            letter-spacing: 1px;
        }

        .clinic-info{
            position: absolute;
            top: 18mm;
            left: 85mm;
            font-size: 12pt;
            color: #4b5563;
            line-height: 1.35;
        }

        /* Barra del título */
        .title{
            position: absolute;
            top: 65mm;
            left: 20mm;
            background: #000;
            color: #fff;
            font-weight: 800;
            font-size: 28pt;
            padding: 1mm 5mm;
            width: 150mm;
        }

        /* Contenido */
        .content{
            position: absolute;
            top: 90mm;
            left: 20mm;
            right: 20mm;
            font-size: 18pt;
            line-height: 1.8;
        }

        .fill-line{
            display: inline-block;
            border-bottom: 2px solid #000;
            height: 9.5mm;
            vertical-align: baseline;
            min-width: 120mm;
        }

        .fill-line.md{ min-width: 105mm; }
        .fill-line.sm{ min-width: 30mm; }
        .fill-line.xs{ min-width: 22mm; }

        /* Footer doctor */
        .doctor{
            position: absolute;
            bottom: 26mm;
            left: 120mm;
            font-size: 20pt;
            font-weight: 800;
            color: #000;
            text-align: center;
            white-space: nowrap;
        }

        /* Puntos decorativos abajo izquierda */
        .dots{
            position: absolute;
            left: 18mm;
            bottom: 18mm;
            width: 25mm;
            height: 25mm;
        }

        .dots span{
            position: absolute;
            width: 1.5mm;
            height: 1.5mm;
            background: #000;
        }

        /* Genera una matriz 4x4 */
        .dots span:nth-child(1){ left:0mm; top:0mm; }
        .dots span:nth-child(2){ left:8mm; top:0mm; }
        .dots span:nth-child(3){ left:16mm; top:0mm; }
        .dots span:nth-child(4){ left:24mm; top:0mm; }

        .dots span:nth-child(5){ left:0mm; top:8mm; }
        .dots span:nth-child(6){ left:8mm; top:8mm; }
        .dots span:nth-child(7){ left:16mm; top:8mm; }
        .dots span:nth-child(8){ left:24mm; top:8mm; }

        .dots span:nth-child(9){ left:0mm; top:16mm; }
        .dots span:nth-child(10){ left:8mm; top:16mm; }
        .dots span:nth-child(11){ left:16mm; top:16mm; }
        .dots span:nth-child(12){ left:24mm; top:16mm; }

        .dots span:nth-child(13){ left:0mm; top:24mm; }
        .dots span:nth-child(14){ left:8mm; top:24mm; }
        .dots span:nth-child(15){ left:16mm; top:24mm; }
        .dots span:nth-child(16){ left:24mm; top:24mm; }
    </style>
</head>

<body>
<div class="page">

    {{-- esquina negra --}}
    <div class="corner-top-right"></div>

    {{-- header --}}
    <div class="logo"> @if(is_file(storage_path('/app/public/'.$logo))) <img src="{{storage_path('/app/public/'.$logo)}}"> @else LOGO @endif</div>

    <div class="clinic-info">
        <h3>{{ $branch->name ?? 'Centro Médico' }}</h3>
        <p>{{ $branch->address ?? 'Dirección no disponible' }}</p>
        <p>Tel: {{ $branch->phone ?? 'N/A' }} | {{ $branch->email ?? 'N/A' }}</p>
    </div>

    {{-- título --}}
    <div class="title">
        Certificado de Incapacidad
    </div>

    {{-- contenido --}}
    <div class="content">

        El suscrito médico certifica que
        <span class="fill-line">
            &nbsp;{{ $medicalLeave->patient_name ?? '' }}
        </span>
        <br>

        con cédula No.
        <span class="fill-line md">
            &nbsp; {{ $patient->identifier_value ?? '' }}
        </span>,
        ha sido examinado (a)
        <br>

        y considera que está (ha estado) incapacitado (a) por
        <span class="fill-line sm">
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
        hasta
        <br>

        Hora:
        <span class="fill-line xs">&nbsp;{{ $end_time ?? '' }}</span>;
        Día:
        <span class="fill-line xs">&nbsp;{{ $end_day ?? '' }}</span>;
        Mes:
        <span class="fill-line xs">&nbsp;{{ $end_month ?? '' }}</span>;
        Año:
        <span class="fill-line xs">&nbsp;{{ $end_year ?? '' }}</span>

    </div>

    {{-- footer --}}
    <div class="doctor">
        {{ $medicalLeave->practitioner_name ?? 'Nombre del Médico aquí' }} –
        Registro No.{{ $medicalLeave->practitioner_license_number ?? '0000000' }}
    </div>

    {{-- puntos decorativos --}}
    <div class="dots">
        @for($i=0;$i<16;$i++)
            <span></span>
        @endfor
    </div>

</div>
</body>
</html>
