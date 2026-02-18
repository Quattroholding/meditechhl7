@php
    $allMedications = $medications->toArray();
    $medicationsPerPage = 5;
    $medicationPages = array_chunk($allMedications, $medicationsPerPage);
    if (empty($medicationPages)) { $medicationPages = [[]]; }

    // Logo
    $logoDataUri = '';
    if ($doctorProfile && $doctorProfile->logo) {
        $logoPath = public_path('storage/' . $doctorProfile->logo);
        if (file_exists($logoPath) && is_file($logoPath)) {
            $ext = pathinfo($doctorProfile->logo, PATHINFO_EXTENSION);
            $logoDataUri = 'data:image/' . $ext . ';base64,' . base64_encode(file_get_contents($logoPath));
        }
    }

    // Firma y sello
    $signatureDataUri = '';
    $sealDataUri = '';
    if ($doctorProfile && $doctorProfile->signature && file_exists(public_path('storage/' . $doctorProfile->signature))) {
        $ext = pathinfo($doctorProfile->signature, PATHINFO_EXTENSION);
        $signatureDataUri = 'data:image/' . $ext . ';base64,' . base64_encode(file_get_contents(public_path('storage/' . $doctorProfile->signature)));
    }
    if ($doctorProfile && $doctorProfile->seal && file_exists(public_path('storage/' . $doctorProfile->seal))) {
        $ext = pathinfo($doctorProfile->seal, PATHINFO_EXTENSION);
        $sealDataUri = 'data:image/' . $ext . ';base64,' . base64_encode(file_get_contents(public_path('storage/' . $doctorProfile->seal)));
    }
    if (empty($signatureDataUri) && isset($practitioner) && method_exists($practitioner, 'signature')) {
        $sigFile = $practitioner->signature();
        if ($sigFile && !empty($sigFile->path) && \Illuminate\Support\Facades\Storage::disk('local')->exists($sigFile->path)) {
            $ext = pathinfo($sigFile->path, PATHINFO_EXTENSION);
            $signatureDataUri = 'data:image/' . $ext . ';base64,' . base64_encode(\Illuminate\Support\Facades\Storage::disk('local')->get($sigFile->path));
        }
    }
    if (empty($sealDataUri) && isset($practitioner) && method_exists($practitioner, 'seal')) {
        $sealFile = $practitioner->seal();
        if ($sealFile && !empty($sealFile->path) && \Illuminate\Support\Facades\Storage::disk('local')->exists($sealFile->path)) {
            $ext = pathinfo($sealFile->path, PATHINFO_EXTENSION);
            $sealDataUri = 'data:image/' . $ext . ';base64,' . base64_encode(\Illuminate\Support\Facades\Storage::disk('local')->get($sealFile->path));
        }
    }

    // Facility
    $facilityName    = $doctorProfile->facility ?? '';
    $facilityAddress = $doctorProfile->address ?? '';
    $facilityPhone   = $doctorProfile->phone ?? '';
    if (empty($facilityName) && isset($client) && $client) {
        $facilityName    = $client->name ?? '';
        $facilityAddress = $client->address ?? '';
        $facilityPhone   = $client->whatsapp ?? $client->phone ?? '';
    }

    // Doctor data
    $doctorName      = $practitioner->name ?? '';
    $doctorSpecialty = $encounter->appointment->medicalSpeciality->name ?? '';
    $doctorLicense   = '';
    if (isset($practitioner->qualifications) && $practitioner->qualifications && method_exists($practitioner->qualifications, 'isNotEmpty') && $practitioner->qualifications->isNotEmpty()) {
        $doctorLicense = $practitioner->qualifications->first()->identifier ?? '';
    }

    $primary   = '#243d84';
    $secondary = '#0c9547';
    $lightP    = '#e8edf7';
    $lightS    = '#e6f7ed';
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Receta Médica - {{ $prescriptionNumber }}</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11px; color: #222; background: #fff; }
    @page { margin: 0; size: letter portrait; }

    .page { width: 100%; height: 279mm; position: relative; page-break-after: always; }
    .page:last-child { page-break-after: avoid; }
    .page-inner { display: table; width: 100%; height: 100%; }
    .page-top { display: table-row; height: 100%; }
    .page-top-cell { display: table-cell; vertical-align: top; padding: 0; }
    .page-bottom { display: table-row; }
    .page-bottom-cell { display: table-cell; vertical-align: bottom; padding: 0; }

    /* HEADER */
    .header { background: {{ $primary }}; position: relative; overflow: hidden; }
    .header-inner { display: table; width: 100%; }
    .header-logo-cell { display: table-cell; width: 80px; vertical-align: middle; padding: 14px 16px; }
    .header-logo-cell img { max-width: 70px; max-height: 60px; }
    .header-doctor-cell { display: table-cell; vertical-align: middle; padding: 14px 8px; }
    .header-rx-cell { display: table-cell; width: 110px; vertical-align: middle; text-align: center; position: relative; }
    /* green diagonal */
    .header-rx-bg { position: absolute; top: 0; bottom: 0; right: 0; left: 0; background: {{ $secondary }}; clip-path: polygon(22% 0%, 100% 0%, 100% 100%, 0% 100%); }
    .header-rx-content { position: relative; z-index: 2; }
    .doctor-name { color: #fff; font-size: 16px; font-weight: bold; margin-bottom: 3px; }
    .doctor-specialty { color: rgba(255,255,255,0.85); font-size: 10.5px; margin-bottom: 2px; }
    .doctor-license { color: rgba(255,255,255,0.65); font-size: 9.5px; }
    .rx-big { font-size: 40px; font-weight: bold; font-style: italic; color: #fff; line-height: 1; }
    .rx-num { font-size: 8.5px; color: rgba(255,255,255,0.8); margin-top: 2px; }

    /* green ribbon */
    .ribbon { background: {{ $secondary }}; height: 5px; }

    /* BODY */
    .body { padding: 16px 24px 16px; }

    /* Patient bar */
    .patient-bar { background: {{ $lightP }}; border-left: 4px solid {{ $primary }}; padding: 10px 14px; margin-bottom: 14px; display: table; width: 100%; }
    .patient-col { display: table-cell; width: 33.33%; vertical-align: top; }
    .p-label { font-size: 8px; color: #888; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 2px; }
    .p-val { font-size: 11px; font-weight: bold; color: {{ $primary }}; }

    /* Two-column */
    .two-col { display: table; width: 100%; }
    .col-left { display: table-cell; width: 60%; vertical-align: top; padding-right: 14px; }
    .col-right { display: table-cell; width: 40%; vertical-align: top; }

    /* Dx box */
    .dx-box { background: {{ $lightS }}; border-top: 3px solid {{ $secondary }}; padding: 9px 12px; margin-bottom: 14px; }
    .dx-title { font-size: 9px; font-weight: bold; color: {{ $secondary }}; text-transform: uppercase; letter-spacing: 0.7px; margin-bottom: 6px; }
    .dx-item { font-size: 10px; color: #333; padding-left: 10px; position: relative; margin-bottom: 3px; }
    .dx-item::before { content: '▸'; position: absolute; left: 0; color: {{ $secondary }}; }

    /* Rx section */
    .rx-section-title { font-size: 9.5px; font-weight: bold; color: {{ $primary }}; text-transform: uppercase; letter-spacing: 0.8px; border-bottom: 2px solid {{ $primary }}; padding-bottom: 4px; margin-bottom: 10px; }
    .med-item { margin-bottom: 11px; padding-bottom: 11px; border-bottom: 1px dashed #d0d0d0; }
    .med-item:last-child { border-bottom: none; }
    .med-num { display: inline-block; background: {{ $primary }}; color: #fff; font-size: 9px; font-weight: bold; width: 17px; height: 17px; line-height: 17px; text-align: center; border-radius: 50%; margin-right: 5px; vertical-align: middle; }
    .med-name { font-size: 11px; font-weight: bold; color: #1a1a1a; display: inline; vertical-align: middle; }
    .med-sig { font-size: 10px; color: #444; margin-top: 3px; padding-left: 22px; }
    .med-sig strong { color: {{ $secondary }}; }

    /* Info panel */
    .info-panel { background: #f8f9fc; border: 1px solid #dde3f0; padding: 12px; }
    .info-title { font-size: 9px; font-weight: bold; color: {{ $primary }}; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 8px; padding-bottom: 4px; border-bottom: 1px solid #dde3f0; }
    .info-row { margin-bottom: 6px; }
    .info-label { font-size: 8px; color: #888; text-transform: uppercase; margin-bottom: 1px; }
    .info-val { font-size: 10px; color: #222; }

    /* Footer pinned to bottom via nested table row */
    .footer-wrap { }
    /* White sig area */
    .footer-sig { background: #fff; padding: 10px 24px 8px; text-align: center; border-top: 1px solid {{ $lightP }}; }
    .footer-sig-images { margin-bottom: 4px; }
    .footer-sig-img { max-width: 180px; max-height: 90px; margin: 0 8px; vertical-align: middle; }
    .footer-seal-img { max-width: 110px; max-height: 110px; margin: 0 8px; vertical-align: middle; }
    .footer-sig-rule { border-top: 1px solid {{ $primary }}; width: 200px; margin: 4px auto 3px; }
    .footer-sig-label { font-size: 8.5px; color: #666; }
    /* Blue bar */
    .footer { background: {{ $primary }}; padding: 8px 24px; display: table; width: 100%; }
    .footer-left { display: table-cell; vertical-align: middle; color: rgba(255,255,255,0.7); font-size: 8.5px; }
    .footer-right { display: table-cell; text-align: right; vertical-align: middle; color: rgba(255,255,255,0.55); font-size: 8px; }
    .footer-green { height: 4px; background: {{ $secondary }}; }
</style>
</head>
<body>

@foreach($medicationPages as $pageIndex => $pageMedications)
<div class="page">
<div class="page-inner">
<div class="page-top"><div class="page-top-cell">

    {{-- HEADER --}}
    <div class="header">
        <div class="header-inner">
            <div class="header-logo-cell">
                @if($logoDataUri)
                    <img src="{{ $logoDataUri }}" alt="Logo">
                @endif
            </div>
            <div class="header-doctor-cell">
                <div class="doctor-name">{{ $doctorName }}</div>
                @if($doctorSpecialty)
                    <div class="doctor-specialty">{{ $doctorSpecialty }}</div>
                @endif
                @if($doctorLicense)
                    <div class="doctor-license">Cédula / Lic.: {{ $doctorLicense }}</div>
                @endif
                @if($facilityName)
                    <div class="doctor-license" style="margin-top:6px;">{{ $facilityName }}</div>
                @endif
                @if($facilityAddress)
                    <div class="doctor-license">{{ $facilityAddress }}</div>
                @endif
                @if($facilityPhone)
                    <div class="doctor-license">Tel: {{ $facilityPhone }}</div>
                @endif
            </div>
            <div class="header-rx-cell">
                <div class="header-rx-bg"></div>
                <div class="header-rx-content">
                    <div class="rx-big">Rx</div>
                    <div class="rx-num">{{ $prescriptionNumber }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="ribbon"></div>

    {{-- BODY --}}
    <div class="body">

        {{-- Patient bar --}}
        <div class="patient-bar">
            <div class="patient-col">
                <div class="p-label">Paciente</div>
                <div class="p-val">{{ $patient->full_name ?? $patient->name ?? '—' }}</div>
            </div>
            <div class="patient-col">
                <div class="p-label">Fecha</div>
                <div class="p-val">{{ isset($encounter->end) ? \Carbon\Carbon::parse($encounter->end)->format('d/m/Y') : now()->format('d/m/Y') }}</div>
            </div>
            <div class="patient-col">
                <div class="p-label">Edad</div>
                <div class="p-val">
                    @if(!empty($patient->birth_date)) {{ \Carbon\Carbon::parse($patient->birth_date)->age }} años @else — @endif
                </div>
            </div>
        </div>

        <div class="two-col">
            <div class="col-left">

                {{-- Diagnoses --}}
                @if($pageIndex === 0 && $diagnoses && $diagnoses->count() > 0)
                    <div class="dx-box">
                        <div class="dx-title">Dx — Diagnósticos</div>
                        @foreach($diagnoses as $d)
                            <div class="dx-item">
                                @if($d->condition->icd10Code)
                                    <strong>{{ $d->condition->icd10Code->code ?? '' }}</strong>
                                    {{ strtoupper($d->condition->icd10Code->description_es ?? '') }}
                                @else
                                    {{ strtoupper($d->condition->onset_info ?? '') }}
                                @endif
                                @if(!empty($d->note)) — <em>{{ $d->note }}</em> @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Medications --}}
                <div class="rx-section-title">Medicamentos prescritos</div>
                @foreach($pageMedications as $index => $medication)
                    @php $medication = (object) $medication; @endphp
                    <div class="med-item">
                        <span class="med-num">{{ ($pageIndex * $medicationsPerPage) + $index + 1 }}</span>
                        <span class="med-name">
                            @if(!empty($medication->medication_id2) && !empty($medication->medication2))
                                @php
                                    $med2 = (object) $medication->medication2;
                                    $ingredient = isset($med2->ingredients) && count($med2->ingredients) > 0 ? (object) $med2->ingredients[0] : null;
                                    $strength = $ingredient ? $ingredient->strength_value . ' ' . $ingredient->strength_unit : '';
                                @endphp
                                {{ $med2->display ?? $med2->home_name }} {{ $strength }} #{{ $medication->quantity }} {{ $med2->form ?? '' }}
                            @elseif(!empty($medication->medication_id) && !empty($medication->medicine))
                                {{ $medication->medicine['home_name'] }} {{ $medication->medicine['mgs'] }} {{ $medication->medicine['mgs_type'] }} #{{ $medication->quantity }} {{ $medication->medicine['type'] }}
                            @else
                                {{ $medication->medication }}
                            @endif
                        </span>
                        <div class="med-sig">
                            <strong>Sig:</strong> {{ $medication->dosage_text ?? $medication->dosage_instruction ?? '' }}
                        </div>
                    </div>
                @endforeach

            </div>

            <div class="col-right">
                <div class="info-panel">
                    <div class="info-title">Información clínica</div>
                    <div class="info-row">
                        <div class="info-label">Médico</div>
                        <div class="info-val">{{ $doctorName }}</div>
                    </div>
                    @if($doctorSpecialty)
                        <div class="info-row">
                            <div class="info-label">Especialidad</div>
                            <div class="info-val">{{ $doctorSpecialty }}</div>
                        </div>
                    @endif
                    @if($doctorLicense)
                        <div class="info-row">
                            <div class="info-label">Cédula / Lic.</div>
                            <div class="info-val">{{ $doctorLicense }}</div>
                        </div>
                    @endif
                    @if(!empty($patient->birth_date))
                        <div class="info-row">
                            <div class="info-label">F. Nacimiento</div>
                            <div class="info-val">{{ \Carbon\Carbon::parse($patient->birth_date)->format('d/m/Y') }}</div>
                        </div>
                    @endif
                    @if(!empty($patient->gender))
                        <div class="info-row">
                            <div class="info-label">Género</div>
                            <div class="info-val">{{ $patient->gender }}</div>
                        </div>
                    @endif
                    <div class="info-row">
                        <div class="info-label">No. Receta</div>
                        <div class="info-val" style="color:{{ $primary }};font-weight:bold;">{{ $prescriptionNumber }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Página</div>
                        <div class="info-val">{{ $pageIndex + 1 }} / {{ count($medicationPages) }}</div>
                    </div>
                </div>

            </div>
        </div>

    </div>

</div></div>{{-- /page-top-cell /page-top --}}
<div class="page-bottom"><div class="page-bottom-cell">
    <div class="footer-wrap">
        {{-- White: signature + seal --}}
        <div class="footer-sig">
            @if($signatureDataUri || $sealDataUri)
                <div class="footer-sig-images">
                    @if($signatureDataUri)
                        <img src="{{ $signatureDataUri }}" class="footer-sig-img" alt="Firma">
                    @endif
                    @if($sealDataUri)
                        <img src="{{ $sealDataUri }}" class="footer-seal-img" alt="Sello">
                    @endif
                </div>
            @endif
            <div class="footer-sig-rule"></div>
            <div class="footer-sig-label">Firma y Sello del Médico</div>
        </div>
        {{-- Blue bar --}}
        <div class="footer">
            <div class="footer-left">
                {{ $facilityName ?: 'Hospital / Clínica' }}
                @if($facilityPhone) — Tel: {{ $facilityPhone }} @endif
                — Documento confidencial
            </div>
            <div class="footer-right">{{ now()->format('d/m/Y') }} | Pág. {{ $pageIndex + 1 }}</div>
        </div>
        <div class="footer-green"></div>
    </div>
</div></div>{{-- /page-bottom-cell /page-bottom --}}
</div>{{-- /page-inner --}}

</div>
@endforeach

</body>
</html>
