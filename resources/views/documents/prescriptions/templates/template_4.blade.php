@php
    $allMedications = $medications->toArray();
    $medicationsPerPage = 6;
    $medicationPages = array_chunk($allMedications, $medicationsPerPage);
    if (empty($medicationPages)) { $medicationPages = [[]]; }

    $logoDataUri = '';
    if ($doctorProfile && $doctorProfile->logo) {
        $logoPath = public_path('storage/' . $doctorProfile->logo);
        if (file_exists($logoPath) && is_file($logoPath)) {
            $ext = pathinfo($doctorProfile->logo, PATHINFO_EXTENSION);
            $logoDataUri = 'data:image/' . $ext . ';base64,' . base64_encode(file_get_contents($logoPath));
        }
    }
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
    $facilityName = $doctorProfile->facility ?? '';
    $facilityAddress = $doctorProfile->address ?? '';
    $facilityPhone = $doctorProfile->phone ?? '';
    if (empty($facilityName) && isset($client) && $client) {
        $facilityName    = $client->name ?? '';
        $facilityAddress = $client->address ?? '';
        $facilityPhone   = $client->whatsapp ?? $client->phone ?? '';
    }
    $doctorName      = $practitioner->name ?? '';
    $doctorSpecialty = $encounter->appointment->medicalSpeciality->name ?? '';
    $doctorLicense   = '';
    if (isset($practitioner->qualifications) && $practitioner->qualifications && method_exists($practitioner->qualifications, 'isNotEmpty') && $practitioner->qualifications->isNotEmpty()) {
        $doctorLicense = $practitioner->qualifications->first()->identifier ?? '';
    }
    $blue = '#1a6fad';
    $darkBlue = '#144f7a';
    $lightBlue = '#e8f4fc';
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

    /* ── LAYOUT: sidebar left + main right ── */
    /* Explicit height forces table-cells to stretch full page */
    .page { width: 100%; height: 279mm; display: table; table-layout: fixed; page-break-after: always; }
    .page:last-child { page-break-after: avoid; }

    .sidebar {
        display: table-cell;
        width: 25%;
        background: {{ $blue }};
        vertical-align: top;
        padding: 0;
        overflow: hidden;
    }
    /* Inner wrapper fills full cell height using nested table */
    .sidebar-inner {
        display: table;
        width: 100%;
        height: 100%;
    }
    .sidebar-inner-top {
        display: table-row;
        height: 100%;        /* greedy: takes all remaining space */
    }
    .sidebar-inner-top-cell {
        display: table-cell;
        vertical-align: top;
        padding: 0;
    }

    .sidebar-top {
        background: {{ $darkBlue }};
        padding: 20px 14px 16px;
        text-align: center;
    }
    .sidebar-logo { margin-bottom: 10px; }
    .sidebar-logo img { max-width: 70px; max-height: 60px; }
    .sidebar-rx { font-size: 50px; font-weight: bold; font-style: italic; color: rgba(255,255,255,0.25); line-height: 1; }

    .sidebar-body { padding: 16px 14px; position: relative; z-index: 1; }
    .sb-section-title {
        font-size: 7.5px; color: rgba(255,255,255,0.5);
        text-transform: uppercase; letter-spacing: 1px;
        border-bottom: 1px solid rgba(255,255,255,0.15);
        padding-bottom: 4px; margin-bottom: 8px;
    }
    .sb-doctor-name { color: #fff; font-size: 13px; font-weight: bold; line-height: 1.3; margin-bottom: 4px; }
    .sb-doctor-specialty { color: rgba(255,255,255,0.75); font-size: 9.5px; margin-bottom: 3px; }
    .sb-doctor-license { color: rgba(255,255,255,0.55); font-size: 9px; }

    .sb-divider { height: 1px; background: rgba(255,255,255,0.12); margin: 12px 0; }

    .sb-label { font-size: 7.5px; color: rgba(255,255,255,0.45); text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 2px; }
    .sb-value { font-size: 9.5px; color: rgba(255,255,255,0.80); margin-bottom: 6px; line-height: 1.35; }


    /* ── MAIN CONTENT ── */
    .main { display: table-cell; vertical-align: top; padding: 0; }
    /* Inner wrapper to pin footer to bottom */
    .main-inner { display: table; width: 100%; height: 100%; }
    .main-inner-top { display: table-row; height: 100%; }
    .main-inner-top-cell { display: table-cell; vertical-align: top; padding: 0; }
    .main-inner-bottom { display: table-row; }
    .main-inner-bottom-cell { display: table-cell; vertical-align: bottom; padding: 0; }

    .main-header {
        background: {{ $lightBlue }};
        border-bottom: 2px solid {{ $blue }};
        padding: 14px 20px;
        display: table; width: 100%;
    }
    .mh-left { display: table-cell; vertical-align: middle; }
    .mh-right { display: table-cell; text-align: right; vertical-align: middle; }
    .page-title { font-size: 14px; font-weight: bold; color: {{ $blue }}; }
    .page-subtitle { font-size: 9px; color: #888; margin-top: 2px; }
    .rx-number-badge {
        background: {{ $blue }}; color: #fff;
        font-size: 9px; font-weight: bold;
        padding: 3px 10px; border-radius: 12px;
        display: inline-block;
    }

    .main-body { padding: 14px 20px; }

    /* patient strip */
    .patient-strip {
        display: table; width: 100%;
        border: 1px solid #dde8f4;
        margin-bottom: 14px;
    }
    .ps-field { display: table-cell; padding: 8px 12px; border-right: 1px solid #dde8f4; vertical-align: top; }
    .ps-field:last-child { border-right: none; }
    .ps-label { font-size: 7.5px; color: #aaa; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 2px; }
    .ps-val { font-size: 10.5px; font-weight: bold; color: #222; }

    /* Dx */
    .dx-box { background: {{ $lightBlue }}; border-left: 3px solid {{ $blue }}; padding: 8px 12px; margin-bottom: 14px; }
    .dx-title { font-size: 9px; font-weight: bold; color: {{ $blue }}; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 5px; }
    .dx-item { font-size: 10px; color: #333; margin-bottom: 3px; }
    .dx-item strong { color: {{ $darkBlue }}; }

    /* Medications */
    .meds-title { font-size: 9px; font-weight: bold; color: {{ $blue }}; text-transform: uppercase; letter-spacing: 0.7px; border-bottom: 1px solid {{ $lightBlue }}; padding-bottom: 4px; margin-bottom: 10px; }
    .med-row { display: table; width: 100%; margin-bottom: 9px; padding-bottom: 9px; border-bottom: 1px solid #eef0f4; }
    .med-row:last-child { border-bottom: none; }
    .med-num-col { display: table-cell; width: 24px; vertical-align: top; padding-top: 1px; }
    .med-num { background: {{ $blue }}; color: #fff; font-size: 9px; font-weight: bold; width: 20px; height: 20px; line-height: 20px; text-align: center; border-radius: 3px; display: block; }
    .med-content-col { display: table-cell; vertical-align: top; padding-left: 6px; }
    .med-name { font-size: 11px; font-weight: bold; color: #1a1a1a; }
    .med-sig { font-size: 10px; color: #555; margin-top: 2px; }
    .med-sig strong { color: {{ $blue }}; }

    /* Footer — white sig area + blue bar */
    .footer-sig-area {
        background: #fff;
        padding: 10px 20px 8px;
        text-align: center;
        border-top: 1px solid {{ $lightBlue }};
    }
    .footer-sig-images { margin-bottom: 4px; }
    .footer-sig-img { max-width: 180px; max-height: 90px; margin: 0 8px; vertical-align: middle; }
    .footer-seal-img { max-width: 110px; max-height: 110px; margin: 0 8px; vertical-align: middle; }
    .footer-sig-rule { border-top: 1px solid {{ $blue }}; width: 200px; margin: 4px auto 3px; }
    .footer-sig-label { font-size: 8.5px; color: #666; }

    .main-footer { background: {{ $blue }}; padding: 7px 20px; display: table; width: 100%; }
    .mf-left { display: table-cell; color: rgba(255,255,255,0.7); font-size: 8px; vertical-align: middle; }
    .mf-right { display: table-cell; text-align: right; color: rgba(255,255,255,0.55); font-size: 8px; vertical-align: middle; }
</style>
</head>
<body>

@foreach($medicationPages as $pageIndex => $pageMedications)
<div class="page">

    {{-- SIDEBAR --}}
    <div class="sidebar">
        <div class="sidebar-inner">
            {{-- Top row: content (greedy, takes all available height) --}}
            <div class="sidebar-inner-top">
                <div class="sidebar-inner-top-cell">
                    <div class="sidebar-top">
                        @if($logoDataUri)
                            <div class="sidebar-logo"><img src="{{ $logoDataUri }}" alt="Logo"></div>
                        @endif
                        <div class="sidebar-rx">Rx</div>
                    </div>

                    <div class="sidebar-body">
                        <div class="sb-section-title">Médico tratante</div>
                        <div class="sb-doctor-name">{{ $doctorName }}</div>
                        @if($doctorSpecialty)
                            <div class="sb-doctor-specialty">{{ $doctorSpecialty }}</div>
                        @endif
                        @if($doctorLicense)
                            <div class="sb-doctor-license">Cédula: {{ $doctorLicense }}</div>
                        @endif

                        @if($facilityName || $facilityAddress || $facilityPhone)
                            <div class="sb-divider"></div>
                            <div class="sb-section-title">Establecimiento</div>
                            @if($facilityName)
                                <div class="sb-value" style="color:#fff;font-weight:bold;">{{ $facilityName }}</div>
                            @endif
                            @if($facilityAddress)
                                <div class="sb-label">Dirección</div>
                                <div class="sb-value">{{ $facilityAddress }}</div>
                            @endif
                            @if($facilityPhone)
                                <div class="sb-label">Teléfono</div>
                                <div class="sb-value">{{ $facilityPhone }}</div>
                            @endif
                        @endif

                        <div class="sb-divider"></div>
                        <div class="sb-section-title">Receta</div>
                        <div class="sb-label">Número</div>
                        <div class="sb-value">{{ $prescriptionNumber }}</div>
                        <div class="sb-label">Fecha</div>
                        <div class="sb-value">{{ isset($encounter->end) ? \Carbon\Carbon::parse($encounter->end)->format('d/m/Y') : now()->format('d/m/Y') }}</div>
                        <div class="sb-label">Página</div>
                        <div class="sb-value">{{ $pageIndex + 1 }} / {{ count($medicationPages) }}</div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- MAIN --}}
    <div class="main">
        <div class="main-inner">
            {{-- Top row: header + content (takes all available space) --}}
            <div class="main-inner-top">
                <div class="main-inner-top-cell">
                    <div class="main-header">
                        <div class="mh-left">
                            <div class="page-title">Receta Médica</div>
                            <div class="page-subtitle">Documento confidencial — uso exclusivo médico</div>
                        </div>
                        <div class="mh-right">
                            <span class="rx-number-badge">{{ $prescriptionNumber }}</span>
                        </div>
                    </div>

                    <div class="main-body">
            {{-- Patient --}}
            <div class="patient-strip">
                <div class="ps-field" style="width:45%">
                    <div class="ps-label">Paciente</div>
                    <div class="ps-val">{{ $patient->full_name ?? $patient->name ?? '—' }}</div>
                </div>
                <div class="ps-field" style="width:20%">
                    <div class="ps-label">Edad</div>
                    <div class="ps-val">
                        @if(!empty($patient->birth_date)) {{ \Carbon\Carbon::parse($patient->birth_date)->age }} a. @else — @endif
                    </div>
                </div>
                <div class="ps-field" style="width:20%">
                    <div class="ps-label">Género</div>
                    <div class="ps-val">{{ $patient->gender ?? '—' }}</div>
                </div>
                <div class="ps-field" style="width:15%">
                    <div class="ps-label">F. Nac.</div>
                    <div class="ps-val">
                        @if(!empty($patient->birth_date)) {{ \Carbon\Carbon::parse($patient->birth_date)->format('d/m/Y') }} @else — @endif
                    </div>
                </div>
            </div>

            {{-- Dx --}}
            @if($pageIndex === 0 && $diagnoses && $diagnoses->count() > 0)
                <div class="dx-box">
                    <div class="dx-title">Diagnósticos</div>
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
            <div class="meds-title">Medicamentos prescritos</div>
            @foreach($pageMedications as $index => $medication)
                @php $medication = (object) $medication; @endphp
                <div class="med-row">
                    <div class="med-num-col">
                        <span class="med-num">{{ ($pageIndex * $medicationsPerPage) + $index + 1 }}</span>
                    </div>
                    <div class="med-content-col">
                        <div class="med-name">
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
                        </div>
                        <div class="med-sig">
                            <strong>Sig:</strong> {{ $medication->dosage_text ?? $medication->dosage_instruction ?? '' }}
                        </div>
                    </div>
                </div>
            @endforeach

                    </div>{{-- /main-body --}}
                </div>{{-- /main-inner-top-cell --}}
            </div>{{-- /main-inner-top --}}

            {{-- Bottom row: footer pinned to bottom --}}
            <div class="main-inner-bottom">
                <div class="main-inner-bottom-cell">
                    {{-- White area: signature + seal --}}
                    <div class="footer-sig-area">
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
                    {{-- Blue bar: facility + confidential + date --}}
                    <div class="main-footer">
                        <div class="mf-left">{{ $facilityName ?: 'Hospital / Clínica' }} — Documento confidencial</div>
                        <div class="mf-right">{{ now()->format('d/m/Y') }} | Pág. {{ $pageIndex + 1 }}</div>
                    </div>
                </div>{{-- /main-inner-bottom-cell --}}
            </div>{{-- /main-inner-bottom --}}
        </div>{{-- /main-inner --}}
    </div>{{-- /main --}}

</div>
@endforeach

</body>
</html>
