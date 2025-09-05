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
            color: #333;
            background: white;
        }

        .prescription-header {
            border-bottom: 2px solid #2c5aa0;
            padding-bottom: 20px;
            margin-bottom: 20px;
            display: table;
            width: 100%;
        }

        .header-left {
            display: table-cell;
            width: 70%;
            vertical-align: top;
        }

        .header-right {
            display: table-cell;
            width: 30%;
            vertical-align: top;
            text-align: right;
        }

        .doctor-logo {
            max-width: 80px;
            max-height: 80px;
            margin-bottom: 10px;
        }

        .doctor-info h2 {
            color: #2c5aa0;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .doctor-info p {
            margin-bottom: 3px;
            font-size: 11px;
        }

        .prescription-number {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 10px;
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .patient-section {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 15px;
            margin-bottom: 20px;
        }

        .patient-section h3 {
            color: #2c5aa0;
            font-size: 14px;
            margin-bottom: 10px;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 5px;
        }

        .patient-info {
            display: table;
            width: 100%;
        }

        .patient-left, .patient-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .patient-right {
            padding-left: 20px;
        }

        .info-row {
            margin-bottom: 5px;
        }

        .info-label {
            font-weight: bold;
            display: inline-block;
            min-width: 60px;
        }

        .prescription-date {
            text-align: right;
            margin-bottom: 20px;
            font-size: 11px;
        }

        .medications-section {
            margin-bottom: 30px;
        }

        .medications-section h3 {
            color: #2c5aa0;
            font-size: 14px;
            margin-bottom: 15px;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .medication-item {
            border: 1px solid #dee2e6;
            margin-bottom: 15px;
            padding: 12px;
            background: white;
        }

        .medication-header {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }

        .medication-number {
            display: table-cell;
            width: 30px;
            font-weight: bold;
            font-size: 16px;
            color: #2c5aa0;
            vertical-align: top;
        }

        .medication-name {
            display: table-cell;
            font-weight: bold;
            font-size: 13px;
            vertical-align: top;
        }

        .medication-details {
            margin-left: 30px;
            margin-top: 5px;
        }

        .medication-line {
            margin-bottom: 3px;
        }

        .medication-instructions {
            background: #f8f9fa;
            padding: 8px;
            margin-top: 8px;
            margin-left: 30px;
            border-left: 3px solid #2c5aa0;
            font-style: italic;
        }

        .diagnosis-section {
            margin-bottom: 20px;
        }

        .diagnosis-section h4 {
            color: #2c5aa0;
            font-size: 12px;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .diagnosis-content {
            background: #f8f9fa;
            padding: 10px;
            border-left: 3px solid #2c5aa0;
        }

        .notes-section {
            margin-bottom: 30px;
        }

        .notes-section h4 {
            color: #2c5aa0;
            font-size: 12px;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .notes-content {
            background: #f8f9fa;
            padding: 10px;
            border-left: 3px solid #28a745;
        }

        .signature-section {
            margin-top: 40px;
            text-align: center;
        }

        .doctor-signature {
            max-width: 150px;
            max-height: 80px;
            margin-bottom: 10px;
        }

        .doctor-seal {
            max-width: 80px;
            max-height: 80px;
            margin-left: 20px;
            vertical-align: top;
        }

        .signature-line {
            border-top: 1px solid #333;
            width: 200px;
            margin: 20px auto 5px;
        }

        .doctor-name {
            font-weight: bold;
            margin-bottom: 2px;
        }

        .license-number {
            font-size: 10px;
            color: #666;
        }

        .footer {
            position: fixed;
            bottom: 20px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
        }

        @page {
            margin: 25mm;
        }

        .page-break {
            page-break-after: always;
        }

        .no-break {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="prescription-header">
        <div class="header-left">
            @if($prescription->doctorProfile->logo && file_exists(public_path('storage/' . $prescription->doctorProfile->logo)))
                <img src="{{ public_path('storage/' . $prescription->doctorProfile->logo) }}" alt="Logo" class="doctor-logo">
            @endif
            <div class="doctor-info">
                <h2>{{ $prescription->doctorProfile->user->first_name }} {{ $prescription->doctorProfile->user->last_name }}</h2>
                @if($prescription->doctorProfile->speciality)
                    <p><strong>{{ $prescription->doctorProfile->speciality }}</strong></p>
                @endif
                @if($prescription->doctorProfile->facility)
                    <p>{{ $prescription->doctorProfile->facility }}</p>
                @endif
                @if($prescription->doctorProfile->address)
                    <p>📍 {{ $prescription->doctorProfile->address }}</p>
                @endif
                @if($prescription->doctorProfile->phone)
                    <p>📞 {{ $prescription->doctorProfile->phone }}</p>
                @endif
                @if($prescription->doctorProfile->email)
                    <p>✉️ {{ $prescription->doctorProfile->email }}</p>
                @endif
            </div>
        </div>
        <div class="header-right">
            @if($prescription->doctorProfile->medical_license_number)
                <p><strong>Reg. Médico:</strong></p>
                <p>{{ $prescription->doctorProfile->medical_license_number }}</p>
            @endif
        </div>
    </div>

    <!-- Prescription Number -->
    <div class="prescription-number">
        RECETA MÉDICA N° {{ $prescription->prescription_number }}
    </div>

    <!-- Date -->
    <div class="prescription-date">
        {{ $prescription->doctorProfile->address ? explode(',', $prescription->doctorProfile->address)[0] ?? 'Caracas' : 'Caracas' }}, 
        {{ \Carbon\Carbon::parse($prescription->prescription_date)->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}
    </div>

    <!-- Patient Information -->
    <div class="patient-section no-break">
        <h3>INFORMACIÓN DEL PACIENTE</h3>
        <div class="patient-info">
            <div class="patient-left">
                <div class="info-row">
                    <span class="info-label">Nombre:</span>
                    <strong>{{ $prescription->patient_name }}</strong>
                </div>
                @if($prescription->patient_document)
                <div class="info-row">
                    <span class="info-label">Cédula:</span>
                    {{ $prescription->patient_document }}
                </div>
                @endif
                @if($prescription->patient_birth_date)
                <div class="info-row">
                    <span class="info-label">Edad:</span>
                    {{ \Carbon\Carbon::parse($prescription->patient_birth_date)->age }} años
                </div>
                @endif
            </div>
            <div class="patient-right">
                @if($prescription->patient_gender)
                <div class="info-row">
                    <span class="info-label">Sexo:</span>
                    {{ $prescription->patient_gender == 'M' ? 'Masculino' : ($prescription->patient_gender == 'F' ? 'Femenino' : 'Otro') }}
                </div>
                @endif
                @if($prescription->patient_phone)
                <div class="info-row">
                    <span class="info-label">Teléfono:</span>
                    {{ $prescription->patient_phone }}
                </div>
                @endif
                @if($prescription->patient_address)
                <div class="info-row">
                    <span class="info-label">Dirección:</span>
                    {{ $prescription->patient_address }}
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Diagnosis -->
    @if($prescription->diagnosis)
    <div class="diagnosis-section no-break">
        <h4>DIAGNÓSTICO:</h4>
        <div class="diagnosis-content">
            {{ $prescription->diagnosis }}
        </div>
    </div>
    @endif

    <!-- Medications -->
    <div class="medications-section">
        <h3>Rp/ MEDICACIÓN PRESCRITA</h3>
        
        @foreach($prescription->activeMedications as $index => $medication)
        <div class="medication-item no-break">
            <div class="medication-header">
                <div class="medication-number">{{ $index + 1 }}.</div>
                <div class="medication-name">
                    {{ $medication->medication_name }}
                    @if($medication->presentation || $medication->concentration)
                        <span style="font-weight: normal;">
                            @if($medication->presentation) - {{ $medication->presentation }}@endif
                            @if($medication->concentration) {{ $medication->concentration }}@endif
                        </span>
                    @endif
                </div>
            </div>
            
            <div class="medication-details">
                <div class="medication-line">
                    <strong>Dosis:</strong> {{ $medication->dosage }}
                </div>
                <div class="medication-line">
                    <strong>Frecuencia:</strong> {{ $medication->frequency }}
                </div>
                @if($medication->duration)
                <div class="medication-line">
                    <strong>Duración:</strong> {{ $medication->duration }}
                </div>
                @endif
                @if($medication->quantity)
                <div class="medication-line">
                    <strong>Cantidad:</strong> {{ $medication->quantity }} unidades
                </div>
                @endif
            </div>
            
            @if($medication->instructions)
            <div class="medication-instructions">
                <strong>Instrucciones:</strong> {{ $medication->instructions }}
            </div>
            @endif
        </div>
        @endforeach
    </div>

    <!-- Additional Notes -->
    @if($prescription->additional_notes)
    <div class="notes-section no-break">
        <h4>NOTAS ADICIONALES:</h4>
        <div class="notes-content">
            {{ $prescription->additional_notes }}
        </div>
    </div>
    @endif

    <!-- Signature -->
    <div class="signature-section no-break">
        @if($prescription->doctorProfile->signature && file_exists(public_path('storage/' . $prescription->doctorProfile->signature)))
            <img src="{{ public_path('storage/' . $prescription->doctorProfile->signature) }}" alt="Firma" class="doctor-signature">
            @if($prescription->doctorProfile->seal && file_exists(public_path('storage/' . $prescription->doctorProfile->seal)))
                <img src="{{ public_path('storage/' . $prescription->doctorProfile->seal) }}" alt="Sello" class="doctor-seal">
            @endif
        @else
            <div class="signature-line"></div>
        @endif
        
        <div class="doctor-name">
            {{ $prescription->doctorProfile->user->first_name }} {{ $prescription->doctorProfile->user->last_name }}
        </div>
        @if($prescription->doctorProfile->speciality)
            <div style="font-size: 10px; margin-bottom: 2px;">
                {{ $prescription->doctorProfile->speciality }}
            </div>
        @endif
        @if($prescription->doctorProfile->medical_license_number)
            <div class="license-number">
                Registro Médico: {{ $prescription->doctorProfile->medical_license_number }}
            </div>
        @endif
    </div>

    <!-- Footer -->
    <div class="footer">
        Esta receta médica fue generada digitalmente el {{ now()->format('d/m/Y H:i') }} | 
        Receta N° {{ $prescription->prescription_number }}
    </div>
</body>
</html>