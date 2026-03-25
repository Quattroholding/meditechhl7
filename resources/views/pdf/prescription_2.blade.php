<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receta Médica - {{ $prescription->prescription_number }}</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #1a2a4a;
            background: #ffffff;
            margin: 0;
            padding: 0;
        }

        /* ── PAGE CONTAINER ── */
        .page {
            width: 100%;
            min-height: 270mm;
            position: relative;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            page-break-after: always;
        }

        .page:last-child {
            page-break-after: avoid;
        }

        /* ── HEADER ── */
        .header {
            background: {{ $colors['outBorderColor'] }};
            padding: 16px 28px 28px 28px;
            position: relative;
            overflow: hidden;
        }

        /* Curva inferior del header */
        .header::after {
            content: '';
            position: absolute;
            bottom: -14px;
            left: -5%;
            width: 110%;
            height: 28px;
            background: #ffffff;
            border-radius: 50% 50% 0 0 / 100% 100% 0 0;
        }

        .header-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 14px;
            flex: 1;
        }

        .header-logo-wrap {
            width: 58px;
            height: 58px;
            border-radius: 50%;
            overflow: hidden;
            background: rgba(255,255,255,0.18);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .header-logo-wrap img {
            max-width: 54px;
            max-height: 54px;
            object-fit: contain;
        }

        .header-text h1 {
            font-size: 22px;
            font-weight: bold;
            color: #ffffff;
            line-height: 1.15;
        }

        .header-text p {
            font-size: 10px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.80);
            margin-top: 3px;
        }

        /* Caduceo SVG en header */
        .header-caduceo {
            width: 52px;
            height: 62px;
            opacity: 0.72;
            flex-shrink: 0;
        }

        /* ── SUBHEADER (certificación) ── */
        .subheader {
            background: #ddeeff;
            padding: 6px 28px;
            font-size: 9.5px;
            color: #2c5282;
            border-bottom: 1px solid #bee3f8;
            margin-top: 16px;
        }

        /* ── BODY ── */
        .body {
            flex: 1;
            padding: 18px 28px 10px 28px;
            position: relative;
        }

        /* Marca de agua */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -55%) rotate(-5deg);
            opacity: 0.035;
            pointer-events: none;
            z-index: 0;
        }

        .watermark svg {
            width: 260px;
            height: 300px;
        }

        .body-content {
            position: relative;
            z-index: 1;
        }

        /* ── PATIENT INFO ── */
        .patient-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }

        .patient-table td {
            padding: 3px 5px;
            vertical-align: bottom;
            font-size: 11px;
        }

        .patient-table td.lbl {
            font-weight: bold;
            color: {{ $colors['outBorderColor'] }};
            white-space: nowrap;
            width: 1%;
        }

        .patient-table td.val {
            border-bottom: 1px solid {{ $colors['linesColor'] }};
        }

        /* ── Rx ── */
        .rx-symbol {
            font-size: 46px;
            font-weight: bold;
            color: {{ $colors['innerBorderColor'] }};
            line-height: 1;
            margin: 14px 0 10px 0;
            font-style: italic;
        }

        .rx-symbol sup {
            font-size: 24px;
            vertical-align: super;
            font-style: italic;
        }

        /* ── MEDICATIONS ── */
        .rx-box {
            border: 1.5px solid {{ $colors['innerBorderColor'] }};
            border-radius: 10px;
            padding: 14px 16px 10px 16px;
            margin-bottom: 14px;
            position: relative;
            min-height: 240px;
        }

        .box-label {
            position: absolute;
            top: -10px;
            left: 14px;
            background: #ffffff;
            padding: 0 6px;
            font-weight: bold;
            font-size: 14px;
            color: {{ $colors['innerBorderColor'] }};
        }

        .medication-item {
            margin-bottom: 14px;
            font-size: 11px;
            padding-left: 8px;
            border-left: 3px solid {{ $colors['linesColor'] }};
        }

        .medication-name {
            font-weight: bold;
            color: #1a2a4a;
            margin-bottom: 2px;
        }

        .medication-detail {
            color: #4a5568;
            margin-left: 4px;
            line-height: 1.5;
        }

        /* ── DX BOX ── */
        .dx-box {
            border: 1.5px solid {{ $colors['innerBorderColor'] }};
            border-radius: 10px;
            padding: 14px 16px 10px 16px;
            margin-bottom: 14px;
            position: relative;
            min-height: 80px;
        }

        .dx-content {
            font-size: 11px;
            color: #2d3748;
            line-height: 1.6;
        }

        /* ── SIGNATURE AREA ── */
        .signature-area {
            padding: 6px 28px 0 28px;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
        }

        .doc-number {
            color: #e53e3e;
            font-weight: bold;
            font-size: 13px;
        }

        .signature-block {
            text-align: center;
        }

        .sig-images {
            display: flex;
            align-items: flex-end;
            justify-content: center;
            gap: 8px;
            min-height: 46px;
            margin-bottom: 4px;
        }

        .sig-img {
            max-width: 110px;
            max-height: 46px;
        }

        .sig-line {
            border-top: 1.5px solid {{ $colors['linesColor'] }};
            width: 200px;
            margin: 0 auto 3px;
        }

        .sig-text {
            font-size: 9px;
            font-weight: bold;
            color: #4a6fa5;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ── FOOTER BAR ── */
        .footer-bar {
            background: {{ $colors['outBorderColor'] }};
            margin-top: 14px;
            display: flex;
            align-items: stretch;
        }

        .footer-brand {
            background: rgba(0,0,0,0.18);
            padding: 9px 18px;
            min-width: 120px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .footer-brand-name {
            font-size: 15px;
            font-weight: bold;
            color: #ffffff;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .footer-brand-slogan {
            font-size: 8px;
            color: rgba(255,255,255,0.75);
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-top: 2px;
        }

        .footer-contacts {
            flex: 1;
            padding: 9px 18px;
            display: flex;
            flex-wrap: wrap;
            gap: 5px 20px;
            align-items: center;
        }

        .fc-item {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 10px;
            color: rgba(255,255,255,0.88);
        }

        .fc-icon {
            font-size: 11px;
            opacity: 0.8;
        }

        @page {
            margin: 0;
            size: letter;
        }
    </style>
</head>
<body>

@php
    // Preparar imágenes de firma y sello
    $signatureDataUri = '';
    $sealDataUri      = '';

    if (! empty($pdfService)) {
        if ($doctorProfile->signature) {
            if ($pdfService->isPrivateImage($doctorProfile->signature)) {
                $signatureDataUri = $pdfService->getPrivateImageDataUri($doctorProfile->signature);
            } elseif (file_exists(public_path('storage/' . $doctorProfile->signature))) {
                $signatureDataUri = 'data:image/' . pathinfo($doctorProfile->signature, PATHINFO_EXTENSION)
                    . ';base64,' . base64_encode(file_get_contents(public_path('storage/' . $doctorProfile->signature)));
            }
        }
        if ($doctorProfile->seal) {
            if ($pdfService->isPrivateImage($doctorProfile->seal)) {
                $sealDataUri = $pdfService->getPrivateImageDataUri($doctorProfile->seal);
            } elseif (file_exists(public_path('storage/' . $doctorProfile->seal))) {
                $sealDataUri = 'data:image/' . pathinfo($doctorProfile->seal, PATHINFO_EXTENSION)
                    . ';base64,' . base64_encode(file_get_contents(public_path('storage/' . $doctorProfile->seal)));
            }
        }
    } else {
        if ($doctorProfile->signature && file_exists(public_path('storage/' . $doctorProfile->signature))) {
            $signatureDataUri = 'data:image/' . pathinfo($doctorProfile->signature, PATHINFO_EXTENSION)
                . ';base64,' . base64_encode(file_get_contents(public_path('storage/' . $doctorProfile->signature)));
        }
        if ($doctorProfile->seal && file_exists(public_path('storage/' . $doctorProfile->seal))) {
            $sealDataUri = 'data:image/' . pathinfo($doctorProfile->seal, PATHINFO_EXTENSION)
                . ';base64,' . base64_encode(file_get_contents(public_path('storage/' . $doctorProfile->seal)));
        }
    }

    // Paginar medicamentos (5 por página)
    $allMedications     = $prescription->activeMedications->toArray();
    $medicationsPerPage = 5;
    $medicationPages    = array_chunk($allMedications, $medicationsPerPage);

    // Datos de contacto
    $facilityName   = $doctorProfile->facility ?? '';
    $facilitySlogan = $doctorProfile->slogan   ?? '';
    $phone          = $doctorProfile->phone    ?? '';
    $phone2         = $doctorProfile->phone2   ?? '';
    $emailContact   = $doctorProfile->email    ?? '';
    $addressContact = $doctorProfile->address  ?? '';
    $website        = $doctorProfile->website  ?? '';
    $licenseNumber  = $doctorProfile->license_number ?? '';
@endphp

@foreach($medicationPages as $pageIndex => $pageMedications)
<div class="page">

    {{-- ── HEADER ── --}}
    <div class="header">
        <div class="header-inner">
            <div class="header-left">
                {{-- Logo --}}
                @php $logoPath = public_path('storage/' . $doctorProfile->logo); @endphp
                @if($doctorProfile->logo && file_exists($logoPath) && is_file($logoPath))
                    <div class="header-logo-wrap">
                        <img src="data:image/{{ pathinfo($doctorProfile->logo, PATHINFO_EXTENSION) }};base64,{{ base64_encode(file_get_contents($logoPath)) }}" alt="Logo">
                    </div>
                @endif
                <div class="header-text">
                    <h1>{{ $doctorProfile->user->full_name }}</h1>
                    <p>{{ $doctorProfile->speciality ?? '' }}</p>
                </div>
            </div>

            {{-- Caduceo SVG --}}
            <svg class="header-caduceo" viewBox="0 0 60 75" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Vara central -->
                <line x1="30" y1="6" x2="30" y2="68" stroke="white" stroke-width="3.5" stroke-linecap="round"/>
                <!-- Orbe superior -->
                <circle cx="30" cy="10" r="8" stroke="white" stroke-width="2.5" fill="none"/>
                <!-- Alas -->
                <path d="M30 18 Q14 14 10 22 Q14 26 30 22" stroke="white" stroke-width="2" fill="none" stroke-linejoin="round"/>
                <path d="M30 18 Q46 14 50 22 Q46 26 30 22" stroke="white" stroke-width="2" fill="none" stroke-linejoin="round"/>
                <!-- Serpiente izquierda -->
                <path d="M26 22 Q14 30 20 38 Q28 44 24 52" stroke="white" stroke-width="2.2" fill="none" stroke-linecap="round"/>
                <!-- Serpiente derecha -->
                <path d="M34 22 Q46 30 40 38 Q32 44 36 52" stroke="white" stroke-width="2.2" fill="none" stroke-linecap="round"/>
                <!-- Base -->
                <line x1="22" y1="68" x2="38" y2="68" stroke="white" stroke-width="2.5" stroke-linecap="round"/>
                <line x1="25" y1="63" x2="35" y2="63" stroke="white" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </div>
    </div>

    {{-- ── SUBHEADER ── --}}
    <div class="subheader">
        @if($licenseNumber)
            Certificación: <strong>{{ $licenseNumber }}</strong>
            @if($facilityName) &nbsp;&nbsp;|&nbsp;&nbsp; @endif
        @endif
        @if($facilityName){{ $facilityName }}@endif
    </div>

    {{-- ── BODY ── --}}
    <div class="body">

        {{-- Marca de agua caduceo --}}
        <div class="watermark">
            <svg viewBox="0 0 120 150" fill="none" xmlns="http://www.w3.org/2000/svg">
                <line x1="60" y1="10" x2="60" y2="138" stroke="#2b6cb0" stroke-width="7" stroke-linecap="round"/>
                <circle cx="60" cy="20" r="16" stroke="#2b6cb0" stroke-width="5" fill="none"/>
                <path d="M60 36 Q28 28 20 44 Q28 52 60 44" stroke="#2b6cb0" stroke-width="4" fill="none"/>
                <path d="M60 36 Q92 28 100 44 Q92 52 60 44" stroke="#2b6cb0" stroke-width="4" fill="none"/>
                <path d="M52 44 Q28 60 40 76 Q56 88 48 104" stroke="#2b6cb0" stroke-width="4.5" fill="none" stroke-linecap="round"/>
                <path d="M68 44 Q92 60 80 76 Q64 88 72 104" stroke="#2b6cb0" stroke-width="4.5" fill="none" stroke-linecap="round"/>
                <line x1="44" y1="136" x2="76" y2="136" stroke="#2b6cb0" stroke-width="5" stroke-linecap="round"/>
                <line x1="50" y1="126" x2="70" y2="126" stroke="#2b6cb0" stroke-width="4" stroke-linecap="round"/>
            </svg>
        </div>

        <div class="body-content">

            {{-- ── PATIENT INFO ── --}}
            <table class="patient-table">
                <tr>
                    <td class="lbl">Nombre :</td>
                    <td class="val" style="min-width:200px;">{{ $prescription->patient_name }}</td>
                    <td style="width:16px;"></td>
                    <td class="lbl">Edad :</td>
                    <td class="val" style="min-width:60px;">{{ $prescription->patient_age ?? '' }}</td>
                </tr>
            </table>
            <table class="patient-table">
                <tr>
                    <td class="lbl">Dirección :</td>
                    <td class="val" style="min-width:200px;" colspan="4">{{ $prescription->patient_address ?? '' }}</td>
                </tr>
            </table>
            <table class="patient-table">
                <tr>
                    <td class="lbl">No. de ID :</td>
                    <td class="val" style="min-width:160px;">{{ $prescription->patient_document ?? '' }}</td>
                    <td style="width:16px;"></td>
                    <td class="lbl">Fecha :</td>
                    <td class="val" style="min-width:90px;">{{ \Carbon\Carbon::parse($prescription->prescription_date)->format('d/m/Y') }}</td>
                </tr>
            </table>
            <table class="patient-table" style="margin-bottom:0;">
                <tr>
                    <td class="lbl">Diagnóstico :</td>
                    <td class="val" colspan="4">{{ $prescription->diagnosis ?? '' }}</td>
                </tr>
            </table>

            {{-- ── Rx ── --}}
            <div class="rx-symbol">R<sup>x</sup></div>

            {{-- ── MEDICATIONS BOX ── --}}
            <div class="rx-box">
                <span class="box-label">Rx</span>
                @foreach($pageMedications as $index => $medication)
                    @php $medication = (object) $medication; @endphp
                    <div class="medication-item">
                        <div class="medication-name">
                            {{ ($pageIndex * $medicationsPerPage) + $index + 1 }}.
                            {{ $medication->medication_name }}
                            @if($medication->dosage) &mdash; {{ $medication->dosage }} @endif
                            @if($medication->presentation) &nbsp;{{ $medication->presentation }} @endif
                            @if($medication->quantity) &nbsp;#{{ $medication->quantity }} @endif
                        </div>
                        <div class="medication-detail">
                            <strong>Sg:</strong> {{ $medication->frequency }} por {{ $medication->duration }}
                        </div>
                        @if($medication->instructions && $medication->instructions !== 'Según indicación médica')
                            <div class="medication-detail">
                                <strong>Instrucciones:</strong> {{ $medication->instructions }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- ── DX BOX ── --}}
            <div class="dx-box">
                <span class="box-label">Dx</span>
                <div class="dx-content">
                    @if($prescription->diagnosis)
                        {{ $prescription->diagnosis }}
                    @endif
                    @if($prescription->additional_notes)
                        <br><br>
                        <strong>Notas adicionales:</strong><br>
                        {{ $prescription->additional_notes }}
                    @endif
                </div>
            </div>

        </div>{{-- /body-content --}}
    </div>{{-- /body --}}

    {{-- ── SIGNATURE AREA ── --}}
    <div class="signature-area">
        <div class="doc-number">{{ $prescription->prescription_number ?? '' }}</div>
        <div class="signature-block">
            @if($signatureDataUri || $sealDataUri)
                <div class="sig-images">
                    @if($signatureDataUri)
                        <img src="{{ $signatureDataUri }}" alt="Firma" class="sig-img">
                    @endif
                    @if($sealDataUri)
                        <img src="{{ $sealDataUri }}" alt="Sello" class="sig-img">
                    @endif
                </div>
            @else
                <div style="min-height:46px;"></div>
            @endif
            <div class="sig-line"></div>
            <div class="sig-text">Firma y Sello del Médico</div>
        </div>
    </div>

    {{-- ── FOOTER BAR ── --}}
    <div class="footer-bar">
        <div class="footer-brand">
            <div class="footer-brand-name">{{ $facilityName ?: 'HOSPITAL' }}</div>
            @if($facilitySlogan)
                <div class="footer-brand-slogan">{{ $facilitySlogan }}</div>
            @endif
        </div>
        <div class="footer-contacts">
            @if($phone)
                <div class="fc-item">
                    <span class="fc-icon">&#9742;</span>
                    {{ $phone }}
                </div>
            @endif
            @if($phone2)
                <div class="fc-item">
                    <span class="fc-icon">&#9742;</span>
                    {{ $phone2 }}
                </div>
            @endif
            @if($emailContact)
                <div class="fc-item">
                    <span class="fc-icon">&#9993;</span>
                    {{ $emailContact }}
                </div>
            @endif
            @if($addressContact)
                <div class="fc-item">
                    <span class="fc-icon">&#9679;</span>
                    {{ $addressContact }}
                </div>
            @endif
            @if($website)
                <div class="fc-item">
                    <span class="fc-icon">&#9675;</span>
                    {{ $website }}
                </div>
            @endif
        </div>
    </div>

</div>{{-- /page --}}
@endforeach

</body>
</html>
