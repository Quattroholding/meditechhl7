<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orden Médica - {{ $orderNumber }}</title>
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

        .order-container {
            border: 2px solid #000;
            height: calc(100vh - 30mm);
            position: relative;
            padding: 10px;
            page-break-after: always;
            box-sizing: border-box;
        }

        .order-container:last-child {
            page-break-after: avoid;
        }

        .header-section {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .order-number {
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

        .service-item {
            margin-bottom: 15px;
            font-size: 11px;
        }

        .service-name {
            font-weight: bold;
            margin-bottom: 3px;
        }

        .service-details {
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

        .priority-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .priority-routine {
            background-color: #f1c40f;
            color: #000;
        }

        .priority-urgent {
            background-color: #e74c3c;
            color: white;
        }

        .priority-asap {
            background-color: #e67e22;
            color: white;
        }

        .priority-stat {
            background-color: #8e44ad;
            color: white;
        }

        @page {
            margin: 0;
        }
    </style>
</head>
<body>
    @php
        // Inicializar variables opcionales
        $doctorProfile = $doctorProfile ?? null;
        $pdfService = $pdfService ?? null;
        $serviceType = $serviceType ?? null;

        // Preparar URLs de firmas y sellos para uso en todo el documento
        $signatureDataUri = '';
        $sealDataUri = '';

        if($doctorProfile) {
            if($doctorProfile->signature) {
                if($pdfService && $pdfService->isPrivateImage($doctorProfile->signature)) {
                    $signatureDataUri = $pdfService->getPrivateImageDataUri($doctorProfile->signature);
                } elseif(file_exists(public_path('storage/' . $doctorProfile->signature))) {
                    $signatureDataUri = 'data:image/' . pathinfo($doctorProfile->signature, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents(public_path('storage/' . $doctorProfile->signature)));
                }
            }
            if($doctorProfile->seal) {
                if($pdfService && $pdfService->isPrivateImage($doctorProfile->seal)) {
                    $sealDataUri = $pdfService->getPrivateImageDataUri($doctorProfile->seal);
                } elseif(file_exists(public_path('storage/' . $doctorProfile->seal))) {
                    $sealDataUri = 'data:image/' . pathinfo($doctorProfile->seal, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents(public_path('storage/' . $doctorProfile->seal)));
                }
            }
        }

        // Filtrar servicios por tipo si se proporciona
        $filteredServices = $serviceType
            ? $serviceRequests->where('service_type', $serviceType)
            : $serviceRequests;

        // Dividir servicios en páginas de 4
        $allServices = $filteredServices->toArray();
        $servicesPerPage = 4;
        $totalPages = ceil(count($allServices) / $servicesPerPage);
        $servicePages = array_chunk($allServices, $servicesPerPage);

        // Obtener nombre del tipo de servicio para el título usando el Enum
        $serviceTypeTitle = \App\Enums\ServiceType::labelFromValue($serviceType);
    @endphp

    @foreach($servicePages as $pageIndex => $pageServices)
        <div class="order-container">
            <!-- Header Section -->
            <div class="header-section">
                <div class="order-number">
                    No.{{ $orderNumber ?? '0001' }}
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
                        {{ $doctorProfile->address ?? '' }}
                        Tel: {{ $doctorProfile->phone ?? '' }}
                    @elseif($encounter->appointment->client)
                        {{ $encounter->appointment->client->name ?? '' }}
                        {{ $encounter->appointment->client->address ?? '' }}<br>
                        Tel: {{ $encounter->appointment->client->whatsapp ?? '' }}
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

            <!-- Rx Section (Servicios Médicos) - Max 4 per page -->
            <div class="rx-section">
                <div class="rx-header">{{ $serviceTypeTitle }}</div>
                <div class="rx-content">
                    @foreach($pageServices as $index => $service)
                        @php $service = (object) $service; @endphp
                        <div class="service-item">
                            <div class="service-details">
                                {{ ($pageIndex * $servicesPerPage) + $index + 1 }}. {{ $service->code }} | {{ $service->cpt['description_es'] ?? 'Servicio no especificado' }}
                            </div>
                            @if($service->body_site && is_array($service->body_site))
                                <div class="service-details">
                                    <strong>Sitio Anatómico:</strong> {{ $service->body_site['display'] ?? $service->body_site['code'] ?? 'N/A' }}
                                </div>
                            @endif
                            @if($service->patient_instruction)
                                <div class="service-details">
                                    <strong>Instrucciones para el paciente:</strong> {{ $service->patient_instruction }}
                                </div>
                            @endif
                            @if($service->note)
                                <div class="service-details">
                                    <strong>Notas:</strong> {{ $service->note }}
                                </div>
                            @endif
                            @if($service->reason_code)
                                <div class="service-details">
                                    <strong>Razón:</strong> {{ $service->reason_code }}
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
                            {{ strtoupper($d->condition->icd10Code->description_es) }}
                        @else
                            {{ strtoupper($d->condition->onset_info) }}
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
