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
            color: {{$colors['fontColor']}};
            background: #{{ $doctorProfile->recepy_background_color ?? 'ffffff' }};
            margin: 0;
            padding: 5mm;
            height: 150vh;
        }

        .prescription-container {
            border: 3px solid {{$colors['outBorderColor']}};
            border-radius: 20px;
            height: calc(110vh - 30mm);
            position: relative;
            padding: 10px;
            page-break-after: always;
            box-sizing: border-box;
        }

        .prescription-container:last-child {
            page-break-after: avoid;
        }

        /* Header Styles */
        .header-section {
            display: table;
            width: 100%;
            margin-bottom: 15px;
            min-height: 110px;
        }

        .logo-section {
            display: table-cell;
            width: 25%;
            text-align: center;
            vertical-align: middle;
            padding: 5px;
        }

        .facility-logo {
            max-width: 90px;
            max-height: 90px;
        }

        .practitioner-info {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: middle;
            padding: 10px;
            font-size: 12px;
        }

        .facility-info {
            display: table-cell;
            width: 25%;
            vertical-align: middle;
            padding: 5px;
            font-size: 11px;
            text-align: right;
        }

        .patient-info-section {
            margin-bottom: 15px;
            font-size: 12px;
        }

        .patient-info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }

        .patient-info-table td {
            padding: 2px 5px;
            vertical-align: bottom;
        }

        .patient-info-table td.label {
            font-weight: bold;
            white-space: nowrap;
        }

        .patient-info-table td.value {
            border-bottom: 1px solid  {{$colors['linesColor']}};
            padding-bottom: 1px;
        }

        .rx-section {
            border: 2px solid {{$colors['innerBorderColor']}};
            margin-bottom: 20px;
            position: relative;
            min-height: 440px;
            border-radius: 20px;
        }

        .rx-header {
            position: absolute;
            color:{{$colors['innerBorderColor']}};
            top: 5px;
            left: 10px;
            background: {{ '#' . ($doctorProfile->recepy_background_color ?? 'ffffff') }};
            padding: 0 5px;
            font-weight: bold;
            font-size: 14px;
        }

        .rx-content {
            padding-top: 25px;
            padding-left: 35px;
        }

        .medication-item {
            margin-bottom: 15px;
            font-size: 11px;
        }

        .medication-name {
            font-weight: bold;
            margin-bottom: 3px;
        }

        .medication-details {
            margin-left: 10px;
            margin-bottom: 5px;
        }

        .dx-section {
            border: 2px solid {{$colors['innerBorderColor']}};
            margin-bottom: 15px;
            position: relative;
            min-height: 260px;
            border-radius: 20px;
        }

        .dx-header {
            position: absolute;
            color:{{$colors['innerBorderColor']}};
            top: 5px;
            left: 10px;
            background: {{ '#' . ($doctorProfile->recepy_background_color ?? 'ffffff') }};
            padding: 0 5px;
            font-weight: bold;
            font-size: 14px;
        }

        .dx-content {
          padding-left: 35px;
          padding-top: 25px;
          font-size: 11px;
        }

        /* Footer Styles */
        .footer-section {
            position: relative;
            width: 100%;
            margin-top: 20px;
            min-height: 80px;
        }

        .document-number-footer {
            position: absolute;
            left: 10px;
            top: 50px;
            color: red;
            font-weight: bold;
            font-size: 16px;
        }

        .seal-section {
            position: initial;
            bottom: 100px;
            right: 10px;
            text-align: right;
            z-index: 10;
        }

        .signature-section {
            position: initial;
            bottom: 40px;
            right: 10px;
            text-align: center;
            z-index: 10;
        }

        .signature-line {
            border-top: 2px solid {{$colors['linesColor']}};
            width: 350px;
            margin-bottom: 5px;
            margin-top: 20px;
            margin-left: 380px;
        }

        .signature-text {
            font-size: 10px;
            font-weight: bold;
            margin-left: 330px;
        }

        .doctor-signature {
            max-width: 200px;
            max-height: 60px;
            margin-bottom: 5px;
        }

        .doctor-seal {
            max-width: 200px;
            max-height: 60px;
            margin-left: 10px;
            vertical-align: top;
        }


        @page {
            margin: 0;
        }
    </style>
</head>
<body>
@php
    // Preparar URLs de firmas y sellos para uso en todo el documento
    $signatureDataUri = '';
    $sealDataUri = '';

    if(!empty($pdfService)) {
        if($doctorProfile->signature) {
            if($pdfService->isPrivateImage($doctorProfile->signature)) {

                $signatureDataUri = $pdfService->getPrivateImageDataUri($doctorProfile->signature);
            } elseif(file_exists(public_path('storage/' . $doctorProfile->signature))) {
                $signatureDataUri = 'data:image/' . pathinfo($doctorProfile->signature, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents(public_path('storage/' . $doctorProfile->signature)));
            }
        }
        if($doctorProfile->seal) {
            if($pdfService->isPrivateImage($doctorProfile->seal)) {
                $sealDataUri = $pdfService->getPrivateImageDataUri($doctorProfile->seal);
            } elseif(file_exists(public_path('storage/' . $doctorProfile->seal))) {
                $sealDataUri = 'data:image/' . pathinfo($doctorProfile->seal, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents(public_path('storage/' . $doctorProfile->seal)));
            }
        }
    } else {
        // Fallback if pdfService is not available
        if($doctorProfile->signature && file_exists(public_path('storage/' . $doctorProfile->signature))) {
            $signatureDataUri = 'data:image/' . pathinfo($doctorProfile->signature, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents(public_path('storage/' . $doctorProfile->signature)));
        }
        if($doctorProfile->seal && file_exists(public_path('storage/' . $doctorProfile->seal))) {
            $sealDataUri = 'data:image/' . pathinfo($doctorProfile->seal, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents(public_path('storage/' . $doctorProfile->seal)));
        }
    }

    // Dividir medicamentos en páginas de 5
    $allMedications = $prescription->activeMedications->toArray();
    $medicationsPerPage = 5;
    $totalPages = ceil(count($allMedications) / $medicationsPerPage);
    $medicationPages = array_chunk($allMedications, $medicationsPerPage);
@endphp

@foreach($medicationPages as $pageIndex => $pageMedications)
    <div class="prescription-container">

        {{-- Header Section with 3 columns: Logo (25%) | Practitioner Info (50%) | Facility Info (25%) --}}
        <div class="header-section">
            <!-- Column 1: Logo (25%) -->
            <div class="logo-section">
                @php
                    $logoPath = public_path('storage/' . $doctorProfile->logo);
                @endphp
                @if(file_exists($logoPath) && is_file($logoPath))
                    <img src="data:image/{{ pathinfo($doctorProfile->logo, PATHINFO_EXTENSION) }};base64,{{ base64_encode(file_get_contents(public_path('storage/' . $doctorProfile->logo))) }}" alt="Logo" class="facility-logo">
                @else

                @endif
            </div>

            <!-- Column 2: Practitioner Info (50%) -->
            <div class="practitioner-info">
                <strong>{{ $doctorProfile->user->full_name }}</strong><br>
                <strong style="font-size: 18px">{{ $doctorProfile->speciality ?? '' }}</strong>
            </div>

            <!-- Column 3: Facility Info (25%) -->
            <div class="facility-info">
                @if($doctorProfile->facility)
                    {{ $doctorProfile->facility }}<br>
                @endif
                @if($doctorProfile->address)
                    {{ $doctorProfile->address }}<br>
                @endif
                @if($doctorProfile->phone)
                    Tel: {{ $doctorProfile->phone }}
                @endif
            </div>
        </div>


        {{-- Patient Information Section --}}
        <div class="patient-info-section">
            <!-- Primera línea: Nombre y Edad -->
            <table class="patient-info-table">
                <tr>
                    <td class="label" style="width: 8%;">Nombre :</td>
                    <td class="value" style="width: 75%;">{{ $prescription->patient_name }}</td>
                    <td class="label" style="width: 5%;">Edad :</td>
                    <td class="value" style="width: 13%;"> @if($prescription->patient_age)  {{ $prescription->patient_age }} @endif</td>
                </tr>
            </table>

            <!-- Segunda línea: Identificación y Fecha -->
            <table class="patient-info-table">
                <tr>
                    <td class="label" style="width: 5%;">No. de ID :</td>
                    <td class="value" style="width: 28%;">{{ $prescription->patient_document ?? '' }}</td>
                    <td class="label" style="width: 5%;">Fecha :</td>
                    <td class="value" style="width: 22%;">{{\Carbon\Carbon::parse($prescription->prescription_date)->format('d/m/Y') }}</td>
                </tr>
            </table>
        </div>

        <!-- Rx Section (Medicamentos) - Max 5 per page -->
        <div class="rx-section">
            <div class="rx-header">Rx</div>
            <div class="rx-content">
                @foreach($pageMedications as $index => $medication)
                    @php $medication = (object) $medication; @endphp
                    <div class="medication-item">
                        <div class="medication-name">
                            {{ ($pageIndex * $medicationsPerPage) + $index + 1 }}. {{$medication->medication_name.'     '.$medication->dosage.'    #'.$medication->quantity.' '.$medication->presentation}}
                        </div>
                        <div class="medication-details">
                            <strong>Sg:</strong> {{ $medication->frequency.' por '.$medication->duration }}
                        </div>
                        @if($medication->instructions && $medication->instructions<>'Según indicación médica')
                            <div class="medication-details">
                                <strong>Instrucciones:</strong> {{ $medication->instructions }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Dx Section (Diagnósticos) - Appears on every page -->
        <div class="dx-section">
            <div class="dx-header">Dx</div>
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

        <div class="footer-section">
            <!-- Document Number - Bottom Left in Red -->
            <div class="document-number-footer">
                {{ $prescription->prescription_number ?? '0001' }}
            </div>

            <div class="seal-section">
                @if($signatureDataUri)
                    <img src="{{ $signatureDataUri }}" alt="Firma" class="doctor-signature">
                    @if($sealDataUri)
                        <img src="{{ $sealDataUri }}" alt="Sello" class="doctor-seal">
                    @endif
                @endif
            </div>

            <!-- Signature Section -->
            <div class="signature-section">
                <div class="signature-line"></div>
                <div class="signature-text">Firma y Sello del Médico</div>
            </div>
        </div>


    </div>
@endforeach
</body>
</html>
