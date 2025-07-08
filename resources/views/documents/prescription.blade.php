<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receta Médica</title>
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
            color: #e74c3c;
            margin-top: 10px;
        }

        .prescription-number {
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
            border-left: 4px solid #3498db;
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
            color: #2980b9;
        }

        .medication-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .medication-table th {
            background-color: #34495e;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 11px;
        }

        .medication-table td {
            padding: 10px;
            border-bottom: 1px solid #ecf0f1;
            vertical-align: top;
        }

        .medication-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .medication-name {
            font-weight: bold;
            color: #2c3e50;
            font-size: 13px;
        }

        .dosage-instructions {
            color: #7f8c8d;
            font-size: 10px;
            margin-top: 3px;
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

        .prescription-notes {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 10px;
            border-radius: 5px;
            margin-top: 15px;
        }

        .validity {
            color: #e74c3c;
            font-weight: bold;
            font-size: 10px;
            text-align: center;
            margin-top: 10px;
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
    <!-- Header -->
    <div class="header">
        <div class="clinic-name">
            {{ $encounter->appointment->client->name ?? 'Centro Médico' }}
        </div>
        <div>{{ $encounter->appointment->client->address ?? '' }}</div>
        <div>Tel: {{ $encounter->appointment->client->whatsapp ?? '' }}</div>
        <div class="document-title">RECETA MÉDICA</div>
        <div class="prescription-number">No. {{ $prescriptionNumber }}</div>
    </div>

    <!-- Doctor Information -->
    <div class="section">
        <div class="section-title">Información del Médico</div>
        <div class="doctor-info">
            <div class="left-column">
                <div class="info-row">
                    <span class="label">Médico:</span> {{ $practitioner->name }}
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

    <!-- Medications -->
    <div class="section">
        <div class="section-title">Medicamentos Prescritos</div>
        <table class="medication-table">
            <thead>
                <tr>
                    <th style="width: 30%;">Medicamento</th>
                    <th style="width: 25%;">Instrucciones</th>
                    <th style="width: 10%;">Cantidad</th>
                    <th style="width: 10%;">Refills</th>
                </tr>
            </thead>
            <tbody>
                @foreach($medications as $medication)
                <tr>
                    <td>
                        <div class="medication-name">
                            {{ $medication->medicine->home_name ?? 'Medicamento no especificado' }}
                        </div>
                        @if($medication->medicine->concentration)
                            <div class="dosage-instructions">
                                Concentración: {{ $medication->medicine->concentration }}
                            </div>
                        @endif
                        @if($medication->medicine->type)
                            <div class="dosage-instructions">
                                Forma: {{ $medication->medicine->type }}
                            </div>
                        @endif
                    </td>
                    <td>
                        @if(is_array($medication->dosage_instruction) && isset($medication->dosage_instruction['dose']))
                            {{ $medication->dosage_instruction['dose'] }}
                        @else
                            {{ $medication->dosage_text ?? 'Ver instrucciones' }}
                        @endif
                    </td>

                    <td style="text-align: center;">
                        {{ $medication->quantity ?? 'N/A' }}
                    </td>
                    <td style="text-align: center;">
                        {{ $medication->refills ?? '0' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Notes -->
    @if($medications->where('note', '!=', null)->count() > 0)
    <div class="prescription-notes">
        <div style="font-weight: bold; margin-bottom: 5px;">Notas Adicionales:</div>
        @foreach($medications as $medication)
            @if($medication->note)
                <div style="margin-bottom: 3px;">• {{ $medication->note }}</div>
            @endif
        @endforeach
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
            Esta receta es válida por 30 días a partir de la fecha de emisión
        </div>
    </div>
</body>
</html>
