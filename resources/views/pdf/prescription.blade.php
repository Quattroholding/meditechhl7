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
            color: #000;
            background: #{{ $doctorProfile->recepy_background_color ?? 'ffffff' }};
            margin: 0;
            padding: 15mm;
            height: 150vh;
        }

        .prescription-container {
            border: 2px solid #000;
            height: calc(100vh - 30mm);
            position: relative;
            padding: 10px;
            page-break-after: always;
            box-sizing: border-box;
        }

        .prescription-container:last-child {
            page-break-after: avoid;
        }

        .header-section {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .prescription-number {
            display: table-cell;
            width: 35%;
            vertical-align: top;
            font-weight: bold;
            font-size: 12px;
        }

        .logo-section {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: top;
            border: 0px solid #000;
            padding: 10px;
            position: relative;
        }

        .logo-box {
            border: 0px solid #000;
            padding: 20px;
            margin-bottom: 5px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .doctor-logo {
            max-width: 100px;
            max-height: 100px;
        }

        .facility-info {
            display: table-cell;
            width: 35%;
            vertical-align: top;
            padding-left: 15px;
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
            border-bottom: 1px solid #000;
            padding-bottom: 1px;
        }

        .rx-section {
            border: 2px solid #000;
            margin-bottom: 15px;
            position: relative;
            min-height: 360px;
        }

        .rx-header {
            position: absolute;
            top: -8px;
            left: 10px;
            background: {{ '#' . ($doctorProfile->recepy_background_color ?? 'ffffff') }};
            padding: 0 5px;
            font-weight: bold;
            font-size: 14px;
        }

        .rx-content {
            padding: 20px 15px 15px 15px;
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
            border: 2px solid #000;
            margin-bottom: 15px;
            position: relative;
            min-height: 130px;
        }

        .dx-header {
            position: absolute;
            top: -8px;
            left: 10px;
            background: {{ '#' . ($doctorProfile->recepy_background_color ?? 'ffffff') }};
            padding: 0 5px;
            font-weight: bold;
            font-size: 14px;
        }

        .dx-content {
            padding: 20px 15px 15px 15px;
            font-size: 11px;
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
            border-top: 1px solid #000;
            width: 300px;
            margin-bottom: 5px;
            margin-top: 30px;
            margin-left: 350px;
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
            if($prescription->doctorProfile->signature) {
                if($pdfService->isPrivateImage($prescription->doctorProfile->signature)) {

                    $signatureDataUri = $pdfService->getPrivateImageDataUri($prescription->doctorProfile->signature);
                } elseif(file_exists(public_path('storage/' . $prescription->doctorProfile->signature))) {
                    $signatureDataUri = 'data:image/' . pathinfo($prescription->doctorProfile->signature, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents(public_path('storage/' . $prescription->doctorProfile->signature)));
                }
            }
            if($prescription->doctorProfile->seal) {
                if($pdfService->isPrivateImage($prescription->doctorProfile->seal)) {
                    $sealDataUri = $pdfService->getPrivateImageDataUri($prescription->doctorProfile->seal);
                } elseif(file_exists(public_path('storage/' . $prescription->doctorProfile->seal))) {
                    $sealDataUri = 'data:image/' . pathinfo($prescription->doctorProfile->seal, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents(public_path('storage/' . $prescription->doctorProfile->seal)));
                }
            }
        } else {
            // Fallback if pdfService is not available
            if($prescription->doctorProfile->signature && file_exists(public_path('storage/' . $prescription->doctorProfile->signature))) {
                $signatureDataUri = 'data:image/' . pathinfo($prescription->doctorProfile->signature, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents(public_path('storage/' . $prescription->doctorProfile->signature)));
            }
            if($prescription->doctorProfile->seal && file_exists(public_path('storage/' . $prescription->doctorProfile->seal))) {
                $sealDataUri = 'data:image/' . pathinfo($prescription->doctorProfile->seal, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents(public_path('storage/' . $prescription->doctorProfile->seal)));
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
            <!-- Header Section -->
            <div class="header-section">
                <div class="prescription-number">
                    No.{{ $prescription->prescription_number ?? '0001' }}
                </div>

                <div class="logo-section">
                    <div class="logo-box">
                        @if(file_exists(public_path('storage/' . $prescription->doctorProfile->logo)))
                            <img src="data:image/{{ pathinfo($prescription->doctorProfile->logo, PATHINFO_EXTENSION) }};base64,{{ base64_encode(file_get_contents(public_path('storage/' . $prescription->doctorProfile->logo))) }}" alt="Logo" class="doctor-logo">
                        @else
                            LOGO CLINICA
                        @endif
                    </div>
                    <strong>{{ $prescription->doctorProfile->user->first_name }} {{ $prescription->doctorProfile->user->last_name }}</strong>
                </div>

                <div class="facility-info">
                    @if($prescription->doctorProfile->facility)
                        {{ $prescription->doctorProfile->facility }}<br>
                    @endif
                    @if($prescription->doctorProfile->address)
                        {{ $prescription->doctorProfile->address }}<br>
                    @endif
                    @if($prescription->doctorProfile->phone)
                        Tel: {{ $prescription->doctorProfile->phone }}
                    @endif
                </div>
            </div>

            <!-- Patient Information -->
            <div class="patient-info-section">
                <!-- Primera línea: Nombre y Edad -->
                <table class="patient-info-table">
                    <tr>
                        <td class="label" style="width: 5%;">Nombre</td>
                        <td class="value" style="width: 75%;">{{ $prescription->patient_name }}</td>
                        <td class="label" style="width: 5%;">Edad</td>
                        <td class="value" style="width: 15%;">
                            @if($prescription->patient_age)
                                {{ $prescription->patient_age }}
                            @endif
                        </td>
                    </tr>
                </table>

                <!-- Segunda línea: Identificación, No. SS y Fecha -->
                <table class="patient-info-table">
                    <tr>
                        <td class="label" style="width: 8%;">Cédula</td>
                        <td class="value" style="width: 25%;">{{ $prescription->patient_document ?? '' }}</td>
                        <td class="label" style="width: 5%;">No. SS</td>
                        <td class="value" style="width: 29%;"></td>
                        <td class="label" style="width: 5%;">Fecha</td>
                        <td class="value" style="width: 28%;">{{ \Carbon\Carbon::parse($prescription->prescription_date)->format('d/m/Y') }}</td>
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
                               {{$medication->medication_name.'     '.$medication->dosage.'    #'.$medication->quantity.' '.$medication->presentation}}
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

            <div class="seal-section">
                @if($signatureDataUri)
                    <img src="{{ $signatureDataUri }}" alt="Firma" class="doctor-signature">
                    @if($sealDataUri)
                        <img src="{{ $sealDataUri }}" alt="Sello" class="doctor-seal">
                    @endif
                @endif
            </div>

            <!-- Signature Section - Appears on every page -->
            <div class="signature-section">

                <div class="signature-line"></div>
                <div class="signature-text">Firma y Sello del Médico</div>
            </div>
        </div>
    @endforeach
</body>
</html>
