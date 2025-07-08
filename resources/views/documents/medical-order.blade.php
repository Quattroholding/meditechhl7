<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orden Médica</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .clinic-name {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .document-title {
            font-size: 16px;
            font-weight: bold;
            color: #27ae60;
            margin-top: 10px;
        }

        .order-number {
            font-size: 10px;
            color: #7f8c8d;
            margin-top: 5px;
        }

        .section {
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #2c3e50;
            border-bottom: 1px solid #bdc3c7;
            padding-bottom: 3px;
            margin-bottom: 10px;
        }

        .doctor-info, .patient-info {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .left-column, .right-column {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 10px;
        }

        .info-row {
            margin-bottom: 5px;
        }

        .label {
            font-weight: bold;
            color: #34495e;
        }

        .diagnosis-section {
            background-color: #f8f9fa;
            padding: 10px;
            border-left: 4px solid #e74c3c;
            margin-bottom: 20px;
        }

        .diagnosis-item {
            margin-bottom: 8px;
            padding: 5px;
            background-color: white;
            border-radius: 3px;
        }

        .diagnosis-title {
            font-weight: bold;
            color: #c0392b;
        }

        .service-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .service-table th {
            background-color: #27ae60;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 11px;
        }

        .service-table td {
            padding: 10px;
            border-bottom: 1px solid #ecf0f1;
            vertical-align: top;
        }

        .service-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .service-name {
            font-weight: bold;
            color: #2c3e50;
            font-size: 13px;
        }

        .service-code {
            color: #7f8c8d;
            font-size: 10px;
            margin-top: 3px;
        }

        .service-instructions {
            color: #555;
            font-size: 11px;
            margin-top: 3px;
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

        .footer {
            margin-top: 30px;
            border-top: 1px solid #bdc3c7;
            padding-top: 20px;
        }

        .signature-section {
            display: table;
            width: 100%;
            margin-top: 30px;
        }

        .signature-left, .signature-right {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: bottom;
            height: 80px;
        }

        .signature-line {
            border-bottom: 1px solid #2c3e50;
            margin-bottom: 5px;
            height: 40px;
        }

        .signature-label {
            font-size: 10px;
            color: #7f8c8d;
        }

        .date-issued {
            text-align: right;
            color: #7f8c8d;
            font-size: 10px;
            margin-top: 10px;
        }

        .order-notes {
            background-color: #d5f4e6;
            border: 1px solid #27ae60;
            padding: 10px;
            border-radius: 5px;
            margin-top: 15px;
        }

        .validity {
            color: #27ae60;
            font-weight: bold;
            font-size: 10px;
            text-align: center;
            margin-top: 10px;
        }

        .urgent-notice {
            background-color: #ffe6e6;
            border: 2px solid #e74c3c;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
            color: #c0392b;
            font-weight: bold;
        }

        .signature-image, .seal-image {
            text-align: center;
            margin-bottom: 10px;
        }

        .signature-image img, .seal-image img {
            max-height: 60px;
            max-width: 200px;
            object-fit: contain;
        }
    </style>
</head>
<body>
    <!-- Urgent Notice -->
    @if($serviceRequests->where('priority', 'stat')->count() > 0 || $serviceRequests->where('priority', 'urgent')->count() > 0)
    <div class="urgent-notice">
        ⚠️ ORDEN MÉDICA URGENTE - REQUIERE ATENCIÓN PRIORITARIA ⚠️
    </div>
    @endif

    <!-- Header -->
    <div class="header">
        <div class="clinic-name">
            {{ $encounter->appointment->client->name ?? 'Centro Médico' }}
        </div>
        <div>{{ $practitioner->user->clients->first()->address ?? '' }}</div>
        <div>Tel: {{ $encounter->appointment->client->whatsapp ?? '' }}</div>
        <div class="document-title">ORDEN MÉDICA</div>
        <div class="order-number">No. {{ $orderNumber }}</div>
    </div>

    <!-- Doctor Information -->
    <div class="section">
        <div class="section-title">Información del Médico</div>
        <div class="doctor-info">
            <div class="left-column">
                <div class="info-row">
                    <span class="label">Médico:</span>{{ $practitioner->name }}
                </div>
                <div class="info-row">
                    <span class="label">Especialidad:</span> {{ $encounter->appointment->medicalSpeciality->name ?? 'Medicina General' }}
                </div>
            </div>
            <div class="right-column">
                <div class="info-row">
                    <span class="label">Registro Médico:</span> {{ $practitioner->registry ?? 'N/A' }}
                </div>
                <div class="info-row">
                    <span class="label">Fecha:</span> {{ $date->format('d/m/Y') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Patient Information -->
    <div class="section">
        <div class="section-title">Información del Paciente</div>
        <div class="patient-info">
            <div class="left-column">
                <div class="info-row">
                    <span class="label">Paciente:</span> {{ $patient->name }}
                </div>
                <div class="info-row">
                    <span class="label">Edad:</span> {{ $patient->birth_date ? \Carbon\Carbon::parse($patient->birth_date)->age . ' años' : 'N/A' }}
                </div>
            </div>
            <div class="right-column">
                <div class="info-row">
                    <span class="label">Identificación:</span> {{ $patient->identifier }}
                </div>
                <div class="info-row">
                    <span class="label">Teléfono:</span> {{ $patient->phone }}
                </div>
            </div>
        </div>
    </div>

    <!-- Diagnoses -->
    @if($diagnoses->count() > 0)
    <div class="diagnosis-section">
        <div class="section-title">Diagnósticos / Indicaciones Clínicas</div>
        @foreach($diagnoses as $diagnosis)
        <div class="diagnosis-item">
            {{--}}
            <div class="diagnosis-title">{{ $diagnosis->use ?? 'Diagnóstico' }}:</div>
            {{--}}
            <div>{{ $diagnosis->condition->icd10Code->description_es ?? 'Diagnóstico no especificado' }}</div>
            @if($diagnosis->condition->icd10_code)
                <div style="font-size: 10px; color: #7f8c8d;">CIE-10: {{ $diagnosis->condition->icd10_code }}</div>
            @endif
            @if($diagnosis->note)
                <div style="font-size: 10px; color: #7f8c8d; margin-top: 3px;">{{ $diagnosis->note }}</div>
            @endif
        </div>
        @endforeach
    </div>
    @endif

    <!-- Service Requests -->
    <div class="section">
        <div class="section-title">Servicios y Estudios Solicitados</div>
        <table class="service-table">
            <thead>
                <tr>
                    <th style="width: 35%;">Servicio/Estudio</th>
                    <th style="width: 15%;">Código CPT</th>
                    <th style="width: 20%;">Indicaciones</th>
                    <th style="width: 10%;">Cantidad</th>
                    <th style="width: 10%;">Prioridad</th>
                    <th style="width: 10%;">Fecha</th>
                </tr>
            </thead>
            <tbody>
                @foreach($serviceRequests as $service)
                <tr>
                    <td>
                        <div class="service-name">
                            {{ $service->code_display ?? $service->cpt->description ?? 'Servicio no especificado' }}
                        </div>
                        @if($service->service_type)
                            <div class="service-code">
                                Tipo: {{ ucfirst($service->service_type) }}
                            </div>
                        @endif
                        @if($service->body_site && is_array($service->body_site))
                            <div class="service-code">
                                Sitio: {{ $service->body_site['display'] ?? $service->body_site['code'] ?? 'N/A' }}
                            </div>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        <strong>{{ $service->code }}</strong>
                        @if($service->cpt)
                            <div class="service-code">{{ $service->cpt->type ?? '' }}</div>
                        @endif
                    </td>
                    <td>
                        @if($service->note)
                            <div class="service-instructions">{{ $service->note }}</div>
                        @endif
                        @if($service->patient_instruction)
                            <div class="service-instructions">
                                <strong>Paciente:</strong> {{ $service->patient_instruction }}
                            </div>
                        @endif
                        @if($service->reason_code)
                            <div class="service-code">
                                Razón: {{ $service->reason_code }}
                            </div>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        {{ $service->quantity ?? '1' }}
                        @if($service->quantity_unit)
                            <div class="service-code">{{ $service->quantity_unit }}</div>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        <span class="priority-badge priority-{{ $service->priority ?? 'routine' }}">
                            {{ ucfirst($service->priority ?? 'Rutina') }}
                        </span>
                    </td>
                    <td style="text-align: center; font-size: 10px;">
                        @if($service->occurrence_start)
                            {{ \Carbon\Carbon::parse($service->occurrence_start)->format('d/m/Y') }}
                        @else
                            {{ $date->format('d/m/Y') }}
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Special Instructions -->
    @if($serviceRequests->where('patient_instruction', '!=', null)->count() > 0 || $serviceRequests->where('note', '!=', null)->count() > 0)
    <div class="order-notes">
        <div style="font-weight: bold; margin-bottom: 5px;">Instrucciones Especiales:</div>
        @foreach($serviceRequests as $service)
            @if($service->patient_instruction)
                <div style="margin-bottom: 3px;">• <strong>{{ $service->code_display ?? 'Servicio' }}:</strong> {{ $service->patient_instruction }}</div>
            @endif
            @if($service->note && $service->note != $service->patient_instruction)
                <div style="margin-bottom: 3px;">• <strong>Notas clínicas:</strong> {{ $service->note }}</div>
            @endif
        @endforeach
    </div>
    @endif

    <!-- Priority Services Notice -->
    @php
        $urgentServices = $serviceRequests->whereIn('priority', ['urgent', 'asap', 'stat']);
    @endphp
    @if($urgentServices->count() > 0)
    <div class="urgent-notice" style="margin-top: 15px;">
        ATENCIÓN: Esta orden contiene {{ $urgentServices->count() }} servicio(s) de prioridad alta que requiere(n) atención inmediata
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <div class="signature-section">
            <div class="signature-left">
                @if($practitioner->getSignaturePath())
                    <div class="signature-image">
                        <img src="{{ $practitioner->getSignaturePath() }}"
                             alt="Firma del médico"
                             style="max-height: 60px; max-width: 200px;">
                    </div>
                @else
                    <div class="signature-line"></div>
                @endif
                <div class="signature-label">Firma del Médico</div>
                <div class="signature-label">Dr(a). {{ $practitioner->user->name }}</div>
                <div class="signature-label">Reg. {{ $practitioner->license_number ?? 'N/A' }}</div>
            </div>
            <div class="signature-right">
                @if($practitioner->getSealPath())
                    <div class="seal-image">
                        <img src="{{ $practitioner->getSealPath() }}"
                             alt="Sello del médico"
                             style="max-height: 80px; max-width: 120px;">
                    </div>
                @else
                    <div class="signature-line"></div>
                @endif
                <div class="signature-label">Sello del Médico</div>
            </div>
        </div>

        <div class="date-issued">
            Emitida el: {{ $date->format('d/m/Y \a \l\a\s H:i') }}
        </div>

        <div class="validity">
            Esta orden médica es válida por 60 días a partir de la fecha de emisión
        </div>
    </div>
</body>
</html>
