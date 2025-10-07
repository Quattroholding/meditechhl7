<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receta Médica - {{ $prescriptionNumber }}</title>
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

        if($doctorProfile) {
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
            /*
            // Fallback if pdfService is not available
            if($doctorProfile->signature && file_exists(public_path('storage/' . $doctorProfile->signature))) {
                $signatureDataUri = 'data:image/' . pathinfo($doctorProfile->signature, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents(public_path('storage/' . $doctorProfile->signature)));
            }
            if($doctorProfile->seal && file_exists(public_path('storage/' . $doctorProfile->seal))) {
                $sealDataUri = 'data:image/' . pathinfo($doctorProfile->seal, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents(public_path('storage/' . $doctorProfile->seal)));
            }
            */
        }

        // Dividir medicamentos en páginas de 5
        $allMedications = $medications->toArray();
        $medicationsPerPage = 4;
        $totalPages = ceil(count($allMedications) / $medicationsPerPage);
        $medicationPages = array_chunk($allMedications, $medicationsPerPage);
    @endphp

    @foreach($medicationPages as $pageIndex => $pageMedications)
        <div class="prescription-container">
            <!-- Header Section -->
            <div class="header-section">
                <div class="prescription-number">
                    No.{{ $prescriptionNumber ?? '0001' }}
                </div>

                <div class="logo-section">
                    @if($doctorProfile && file_exists(public_path('storage/' . $doctorProfile->logo)))
                    <div class="logo-box">
                       <img src="data:image/{{ pathinfo($doctorProfile->logo, PATHINFO_EXTENSION) }};base64,{{ base64_encode(file_get_contents(public_path('storage/' . $doctorProfile->logo))) }}" alt="Logo" class="doctor-logo">
                    </div>
                    @endif
                    <strong>{{ $practitioner->name }}</strong>
                </div>

                <div class="facility-info">
                    @if($doctorProfile)
                        {{ $doctorProfile->facility ?? '' }}
                        {{$doctorProfile->address?? ''}}
                        Tel: {{ $doctorProfile->phone?? ''}}
                    @elseif($encounter->appointment->client)
                        {{  $encounter->appointment->client->name ?? '' }}
                        {{ $encounter->appointment->client->address ?? ''}}<br>
                        Tel: {{ $encounter->appointment->client->whatsapp ?? ''}}
                    @endif

                </div>
            </div>

            <!-- Patient Information -->
            <div class="patient-info-section">
                <!-- Primera línea: Nombre y Edad -->
                <table class="patient-info-table">
                    <tr>
                        <td class="label" style="width: 5%;">Nombre</td>
                        <td class="value" style="width: 75%;">{{ $patient->name }}</td>
                        <td class="label" style="width: 5%;">Edad</td>
                        <td class="value" style="width: 15%;">{{ $patient->age }}</td>
                    </tr>
                </table>

                <!-- Segunda línea: Identificación, No. SS y Fecha -->
                <table class="patient-info-table">
                    <tr>
                        <td class="label" style="width: 8%;">Cédula</td>
                        <td class="value" style="width: 25%;">@if($patient->identifier_type<>'SS'){{ $patient->identifier ?? '' }}@endif</td>
                        <td class="label" style="width: 5%;">No. SS</td>
                        <td class="value" style="width: 29%;">@if($patient->identifier_type=='SS'){{ $patient->identifier ?? '' }}@endif</td>
                        <td class="label" style="width: 5%;">Fecha</td>
                        <td class="value" style="width: 28%;">{{ \Carbon\Carbon::parse($encounter->created_at)->format('d/m/Y') }}</td>
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
                                {{ ($pageIndex * $medicationsPerPage) + $index + 1 }}. {{ $medication->medication }}
                                @if($medication->medication_id)
                                    - {{ $medication->medicine->presentation }} {{ $medication->medicine->concentration }}
                                @else
                                    {{$medication->medication}}
                                @endif
                            </div>
                            <div class="medication-details">
                                <strong>Frecuencia:</strong> {{ $medication->frequency }}
                                @if($medication->duration)
                                    | <strong>Duración:</strong> {{ $medication->duration }}
                                @endif
                                @if($medication->quantity)
                                    | <strong>Cantidad:</strong> {{ $medication->quantity }} unidades
                                @endif
                            </div>
                            @if($medication->dosage_text)
                                <div class="medication-details">
                                    <strong>Instrucciones:</strong> {{ $medication->dosage_text }}
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
                    @foreach($diagnoses as $d)
                        @if($d->condition->icd10Code)
                            {{ strtoupper($d->condition->icd10Code->description_es)}}
                        @else
                            {{ strtoupper($d->condition->onset_info)}}
                        @endif
                        <br><br>
                        @if($d->note)
                        <strong>Notas adicionales:</strong><br>
                        {{ $d->note }}
                        @endif
                    @endforeach
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
