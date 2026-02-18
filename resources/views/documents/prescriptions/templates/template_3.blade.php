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

    $sky   = '#08a0d0';
    $teal  = '#41bfa2';
    $darkSky  = '#0681a8';
    $lightSky = '#e0f5fb';
    $lightTeal = '#e6f7f4';
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Receta Médica - {{ $prescriptionNumber }}</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11px; color: #2a2a2a; background: #f4fbfd; }
    @page { margin: 0; size: letter portrait; }

    .page { width: 100%; height: 279mm; background: #f4fbfd; position: relative; page-break-after: always; overflow: hidden; }
    .page:last-child { page-break-after: avoid; }
    .page-inner { display: table; width: 100%; height: 100%; }
    .page-top { display: table-row; height: 100%; }
    .page-top-cell { display: table-cell; vertical-align: top; padding: 0; }
    .page-bottom { display: table-row; }
    .page-bottom-cell { display: table-cell; vertical-align: bottom; padding: 0; }

    /* Background decorations */
    .bg-top-right { position: absolute; top: -70px; right: -70px; width: 220px; height: 220px; border-radius: 50%; background: {{ $sky }}; opacity: 0.07; }
    .bg-bot-left { position: absolute; bottom: 60px; left: -80px; width: 270px; height: 270px; border-radius: 50%; background: {{ $teal }}; opacity: 0.07; }

    /* HEADER */
    .header { background: #fff; border-bottom: 3px solid {{ $sky }}; position: relative; z-index: 1; }
    .header-top { background: {{ $sky }}; height: 7px; }
    .header-body { display: table; width: 100%; padding: 14px 24px 12px; }
    .hb-logo { display: table-cell; width: 75px; vertical-align: middle; }
    .hb-logo img { max-width: 65px; max-height: 58px; }
    .hb-doctor { display: table-cell; vertical-align: middle; padding-left: 10px; }
    .hb-rxcircle { display: table-cell; width: 75px; vertical-align: middle; text-align: center; }
    .hb-clinic { display: table-cell; width: 30%; vertical-align: middle; text-align: right; padding-left: 14px; border-left: 2px solid {{ $lightSky }}; }
    .doctor-name { font-size: 16px; font-weight: bold; color: {{ $darkSky }}; margin-bottom: 3px; }
    .doctor-specialty { font-size: 10.5px; color: {{ $teal }}; font-weight: bold; margin-bottom: 2px; }
    .doctor-license { font-size: 9.5px; color: #777; }
    .rx-circle { width: 52px; height: 52px; background: {{ $sky }}; border-radius: 50%; margin: 0 auto; display: table; }
    .rx-circle-inner { display: table-cell; vertical-align: middle; text-align: center; color: #fff; font-size: 26px; font-weight: bold; font-style: italic; line-height: 1; }
    .clinic-name { font-size: 11px; font-weight: bold; color: {{ $darkSky }}; margin-bottom: 3px; }
    .clinic-info { font-size: 9px; color: #777; line-height: 1.5; }

    /* Teal sub-bar */
    .sub-bar { background: {{ $teal }}; padding: 5px 24px; display: table; width: 100%; position: relative; z-index: 1; }
    .sb-left { display: table-cell; color: #fff; font-size: 9px; }
    .sb-left strong { font-size: 10px; }
    .sb-right { display: table-cell; text-align: right; color: rgba(255,255,255,0.85); font-size: 9px; }
    .sb-right strong { font-size: 10px; }

    /* BODY */
    .body { padding: 14px 24px 14px; position: relative; z-index: 1; }

    /* Patient card */
    .patient-card { background: #fff; border-top: 3px solid {{ $sky }}; border-radius: 4px; box-shadow: 0 1px 4px rgba(8,160,208,0.10); padding: 10px 14px; margin-bottom: 13px; display: table; width: 100%; }
    .pf { display: table-cell; width: 25%; vertical-align: top; padding-right: 8px; }
    .pf-label { font-size: 7.5px; color: #aaa; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 2px; }
    .pf-val { font-size: 11px; font-weight: bold; color: #2a2a2a; }

    /* Dx strip */
    .dx-strip { background: {{ $lightTeal }}; border-left: 4px solid {{ $teal }}; padding: 9px 14px; margin-bottom: 13px; border-radius: 0 4px 4px 0; }
    .dx-strip-title { font-size: 9px; font-weight: bold; color: {{ $teal }}; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 6px; }
    .dx-item { font-size: 10px; color: #333; margin-bottom: 3px; padding-left: 10px; position: relative; }
    .dx-item::before { content: '▸'; position: absolute; left: 0; color: {{ $teal }}; }

    /* Rx header */
    .rx-head { display: table; width: 100%; margin-bottom: 10px; border-bottom: 1px solid {{ $sky }}; padding-bottom: 5px; }
    .rx-head-left { display: table-cell; vertical-align: middle; }
    .rx-big { font-size: 24px; font-weight: bold; font-style: italic; color: {{ $sky }}; line-height: 1; display: inline-block; vertical-align: middle; margin-right: 6px; }
    .rx-subtitle { font-size: 9.5px; font-weight: bold; color: #555; text-transform: uppercase; letter-spacing: 0.7px; display: inline-block; vertical-align: middle; }
    .rx-head-right { display: table-cell; text-align: right; vertical-align: middle; font-size: 8.5px; color: #aaa; }

    /* Med cards */
    .med-card { background: #fff; margin-bottom: 8px; display: table; width: 100%; border-left: 4px solid {{ $sky }}; box-shadow: 0 1px 3px rgba(0,0,0,0.06); border-radius: 0 4px 4px 0; }
    .med-card:nth-child(even) { border-left-color: {{ $teal }}; }
    .med-num-cell { display: table-cell; width: 32px; background: {{ $lightSky }}; text-align: center; vertical-align: middle; font-size: 13px; font-weight: bold; color: {{ $sky }}; padding: 0 6px; }
    .med-card:nth-child(even) .med-num-cell { background: {{ $lightTeal }}; color: {{ $teal }}; }
    .med-body-cell { display: table-cell; padding: 8px 12px; vertical-align: middle; }
    .med-name { font-size: 11px; font-weight: bold; color: #1a1a1a; margin-bottom: 3px; }
    .med-sig { font-size: 10px; color: #555; }
    .med-sig strong { color: {{ $teal }}; }

    /* Signature */
    /* Footer pinned to bottom via nested table row */
    .footer-wrap { }
    /* White sig area */
    .footer-sig { background: #fff; padding: 10px 24px 8px; text-align: center; border-top: 1px solid #d0eef8; }
    .footer-sig-images { margin-bottom: 4px; }
    .footer-sig-img { max-width: 180px; max-height: 90px; margin: 0 8px; vertical-align: middle; }
    .footer-seal-img { max-width: 110px; max-height: 110px; margin: 0 8px; vertical-align: middle; }
    .footer-sig-rule { border-top: 1px solid {{ $sky }}; width: 200px; margin: 4px auto 3px; }
    .footer-sig-label { font-size: 8.5px; color: #666; }
    /* White content bar */
    .footer-content { background: #fff; border-top: 3px solid {{ $teal }}; padding: 7px 24px; display: table; width: 100%; }
    .footer-left { display: table-cell; vertical-align: middle; font-size: 8.5px; color: #888; }
    .footer-right { display: table-cell; text-align: right; vertical-align: middle; font-size: 8.5px; color: #aaa; }
    .footer-sky { background: {{ $sky }}; height: 5px; }
</style>
</head>
<body>

@foreach($medicationPages as $pageIndex => $pageMedications)
<div class="page">
<div class="page-inner">
<div class="page-top"><div class="page-top-cell">
    <div class="bg-top-right"></div>
    <div class="bg-bot-left"></div>

    {{-- HEADER --}}
    <div class="header">
        <div class="header-top"></div>
        <div class="header-body">
            <div class="hb-logo">
                @if($logoDataUri)
                    <img src="{{ $logoDataUri }}" alt="Logo">
                @endif
            </div>
            <div class="hb-doctor">
                <div class="doctor-name">{{ $doctorName }}</div>
                @if($doctorSpecialty)
                    <div class="doctor-specialty">{{ $doctorSpecialty }}</div>
                @endif
                @if($doctorLicense)
                    <div class="doctor-license">Cédula / Lic.: {{ $doctorLicense }}</div>
                @endif
            </div>
            <div class="hb-rxcircle">
                <div class="rx-circle">
                    <div class="rx-circle-inner">℞</div>
                </div>
            </div>
            <div class="hb-clinic">
                @if($facilityName)
                    <div class="clinic-name">{{ $facilityName }}</div>
                @else
                    <div class="clinic-name">Receta Médica</div>
                @endif
                <div class="clinic-info">
                    @if($facilityAddress){{ $facilityAddress }}<br>@endif
                    @if($facilityPhone)Tel: {{ $facilityPhone }}@endif
                </div>
            </div>
        </div>
    </div>

    {{-- TEAL SUB-BAR --}}
    <div class="sub-bar">
        <div class="sb-left">
            Paciente: <strong>{{ $patient->full_name ?? $patient->name ?? '—' }}</strong>
        </div>
        <div class="sb-right">
            No. <strong>{{ $prescriptionNumber }}</strong> &nbsp;|&nbsp;
            {{ isset($encounter->end) ? \Carbon\Carbon::parse($encounter->end)->format('d/m/Y') : now()->format('d/m/Y') }}
            &nbsp;|&nbsp; Pág. {{ $pageIndex + 1 }}/{{ count($medicationPages) }}
        </div>
    </div>

    {{-- BODY --}}
    <div class="body">

        @if($pageIndex === 0)
        {{-- Patient card --}}
        <div class="patient-card">
            <div class="pf">
                <div class="pf-label">Nombre completo</div>
                <div class="pf-val">{{ $patient->full_name ?? $patient->name ?? '—' }}</div>
            </div>
            <div class="pf">
                <div class="pf-label">F. Nacimiento</div>
                <div class="pf-val">
                    @if(!empty($patient->birth_date)) {{ \Carbon\Carbon::parse($patient->birth_date)->format('d/m/Y') }} @else — @endif
                </div>
            </div>
            <div class="pf">
                <div class="pf-label">Edad</div>
                <div class="pf-val">
                    @if(!empty($patient->birth_date)) {{ \Carbon\Carbon::parse($patient->birth_date)->age }} años @else — @endif
                </div>
            </div>
            <div class="pf">
                <div class="pf-label">Género</div>
                <div class="pf-val">{{ $patient->gender ?? '—' }}</div>
            </div>
        </div>

        {{-- Diagnoses --}}
        @if($diagnoses && $diagnoses->count() > 0)
            <div class="dx-strip">
                <div class="dx-strip-title">Dx — Diagnósticos</div>
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
        @endif

        {{-- Medications --}}
        <div class="rx-head">
            <div class="rx-head-left">
                <span class="rx-big">Rx</span>
                <span class="rx-subtitle">Medicamentos prescritos</span>
            </div>
            <div class="rx-head-right">{{ count($pageMedications) }} medicamento(s)</div>
        </div>

        @foreach($pageMedications as $index => $medication)
            @php $medication = (object) $medication; @endphp
            <div class="med-card">
                <div class="med-num-cell">{{ ($pageIndex * $medicationsPerPage) + $index + 1 }}</div>
                <div class="med-body-cell">
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

    </div>

    </div></div>{{-- /page-top-cell /page-top --}}
    <div class="page-bottom"><div class="page-bottom-cell">
        {{-- FOOTER --}}
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
            {{-- Content bar --}}
            <div class="footer-content">
                <div class="footer-left">
                    {{ $facilityName ?: 'Hospital / Clínica' }}
                    @if($facilityPhone) — Tel: {{ $facilityPhone }} @endif
                    — Documento confidencial de uso médico
                </div>
                <div class="footer-right">{{ now()->format('d/m/Y') }} | Pág. {{ $pageIndex + 1 }}</div>
            </div>
            <div class="footer-sky"></div>
        </div>
    </div></div>{{-- /page-bottom-cell /page-bottom --}}
    </div>{{-- /page-inner --}}

</div>
@endforeach

</body>
</html>
