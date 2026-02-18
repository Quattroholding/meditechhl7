@php
    $allMedications = $medications->toArray();
    $medicationsPerPage = 5;
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
    $purple = '#6b4fa0';
    $midPurple = '#8b69c0';
    $lightPurple = '#f3eeff';
    $palePurple = '#f9f5ff';
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Receta Médica - {{ $prescriptionNumber }}</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11px; color: #2a2a2a; background: #fff; }
    @page { margin: 0; size: letter portrait; }

    /* ── PAGE (nested table sticky footer) ── */
    .page { width: 100%; height: 279mm; page-break-after: always; background: #fff; overflow: hidden; }
    .page:last-child { page-break-after: avoid; }
    .page-inner { display: table; width: 100%; height: 100%; }
    .page-top { display: table-row; height: 100%; }
    .page-top-cell { display: table-cell; vertical-align: top; padding: 0; }
    .page-bottom { display: table-row; }
    .page-bottom-cell { display: table-cell; vertical-align: bottom; padding: 0; }

    /* ── TWO-CELL HEADER ── */
    .header { display: table; width: 100%; border-collapse: collapse; }
    .header-white {
        display: table-cell; width: 52%;
        background: #fff;
        padding: 16px 20px;
        vertical-align: middle;
        border-right: 4px solid {{ $purple }};
    }
    .header-purple {
        display: table-cell; width: 48%;
        background: {{ $purple }};
        padding: 16px 20px;
        vertical-align: middle;
    }
    /* Logo area */
    .logo-facility { display: table; width: 100%; }
    .logo-cell { display: table-cell; width: 65px; vertical-align: middle; }
    .logo-cell img { max-width: 58px; max-height: 52px; }
    .facility-cell { display: table-cell; vertical-align: middle; padding-left: 10px; }
    .facility-name { font-size: 13px; font-weight: bold; color: {{ $purple }}; margin-bottom: 3px; }
    .facility-info { font-size: 8.5px; color: #888; line-height: 1.5; }

    /* Doctor area */
    .doctor-name { font-size: 15px; font-weight: bold; color: #fff; margin-bottom: 3px; }
    .doctor-specialty { font-size: 10px; color: rgba(255,255,255,0.85); margin-bottom: 2px; }
    .doctor-license { font-size: 9px; color: rgba(255,255,255,0.60); }

    /* Purple thin bar under header */
    .header-bar { height: 5px; background: {{ $midPurple }}; }

    /* ── PATIENT BAND ── */
    .patient-band { background: {{ $lightPurple }}; padding: 0; display: table; width: 100%; }
    .pb-rx { display: table-cell; width: 60px; background: {{ $purple }}; text-align: center; vertical-align: middle; }
    .pb-rx-text { font-size: 28px; font-weight: bold; font-style: italic; color: rgba(255,255,255,0.9); line-height: 1; }
    .pb-fields { display: table-cell; padding: 8px 16px; vertical-align: middle; }
    .pb-fields-inner { display: table; width: 100%; }
    .pbf { display: table-cell; vertical-align: top; padding-right: 14px; }
    .pbf-label { font-size: 7.5px; color: #9980c0; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 2px; }
    .pbf-val { font-size: 10.5px; font-weight: bold; color: #333; }
    .pb-meta { display: table-cell; width: 130px; background: {{ $purple }}; padding: 8px 12px; text-align: right; vertical-align: middle; }
    .pb-meta-num { font-size: 9px; color: rgba(255,255,255,0.60); margin-bottom: 2px; }
    .pb-meta-val { font-size: 10px; color: #fff; font-weight: bold; }

    /* ── BODY ── */
    .body { padding: 16px 24px 10px; background: #fff; }

    /* Diagnoses — two column table style */
    .dx-section { margin-bottom: 14px; }
    .dx-heading { font-size: 9px; font-weight: bold; color: {{ $purple }}; text-transform: uppercase; letter-spacing: 0.8px; padding-bottom: 4px; margin-bottom: 8px; border-bottom: 1.5px solid {{ $lightPurple }}; display: table; width: 100%; }
    .dx-heading::after { content: ''; display: table-cell; }
    .dx-table { display: table; width: 100%; border-collapse: collapse; }
    .dx-tr { display: table-row; }
    .dx-td-code { display: table-cell; width: 80px; vertical-align: top; padding: 3px 8px 3px 0; }
    .dx-code-pill { background: {{ $lightPurple }}; color: {{ $purple }}; font-size: 9px; font-weight: bold; padding: 2px 7px; border-radius: 3px; display: inline-block; border: 1px solid #ddd0f5; }
    .dx-td-desc { display: table-cell; vertical-align: top; padding: 3px 0; font-size: 10px; color: #333; }

    /* Medications — clean numbered list */
    .rx-section { }
    .rx-heading { display: table; width: 100%; margin-bottom: 10px; }
    .rx-heading-left { display: table-cell; vertical-align: middle; }
    .rx-heading-right { display: table-cell; text-align: right; vertical-align: middle; font-size: 8.5px; color: #bbb; }
    .rx-heading-label {
        display: inline-block;
        background: {{ $purple }}; color: #fff;
        font-size: 9px; font-weight: bold; font-style: italic;
        padding: 3px 12px; border-radius: 12px;
        margin-right: 6px; vertical-align: middle;
    }
    .rx-heading-title { font-size: 10px; font-weight: bold; color: #555; text-transform: uppercase; letter-spacing: 0.6px; vertical-align: middle; }

    .med-row { display: table; width: 100%; margin-bottom: 9px; }
    .med-n { display: table-cell; width: 30px; vertical-align: top; }
    .med-n-circle { width: 22px; height: 22px; background: {{ $lightPurple }}; border: 1.5px solid {{ $midPurple }}; border-radius: 50%; text-align: center; line-height: 20px; font-size: 9.5px; font-weight: bold; color: {{ $purple }}; display: block; }
    .med-content { display: table-cell; vertical-align: top; padding-left: 6px; border-left: 2px solid {{ $lightPurple }}; padding-top: 2px; }
    .med-name { font-size: 11px; font-weight: bold; color: #1a1a1a; margin-bottom: 3px; }
    .med-sig { font-size: 10px; color: #555; }
    .med-sig strong { color: {{ $purple }}; }

    /* Signature / footer bottom */
    .footer-sig { background: #fff; padding: 10px 24px 6px; text-align: center; }
    .sig-images { margin-bottom: 5px; }
    .sig-img { max-width: 180px; max-height: 90px; margin: 0 8px; vertical-align: middle; }
    .seal-img { max-width: 110px; max-height: 110px; margin: 0 8px; vertical-align: middle; }
    .sig-rule { border-top: 1px solid {{ $midPurple }}; width: 200px; margin: 4px auto 4px; }
    .sig-text { font-size: 8.5px; color: #999; }

    /* Footer bar */
    .footer { background: {{ $palePurple }}; border-top: 2px solid {{ $lightPurple }}; padding: 7px 24px; display: table; width: 100%; }
    .footer-left { display: table-cell; vertical-align: middle; font-size: 8px; color: #888; }
    .footer-center { display: table-cell; text-align: center; vertical-align: middle; font-size: 8px; color: {{ $midPurple }}; font-weight: bold; }
    .footer-right { display: table-cell; text-align: right; vertical-align: middle; font-size: 8px; color: #aaa; }
    .footer-purple-bar { height: 5px; background: {{ $purple }}; }
</style>
</head>
<body>

@foreach($medicationPages as $pageIndex => $pageMedications)
<div class="page">
<div class="page-inner">
<div class="page-top"><div class="page-top-cell">

    {{-- TWO-CELL HEADER --}}
    <div class="header">
        <div class="header-white">
            <div class="logo-facility">
                <div class="logo-cell">
                    @if($logoDataUri)
                        <img src="{{ $logoDataUri }}" alt="Logo">
                    @endif
                </div>
                <div class="facility-cell">
                    @if($facilityName)
                        <div class="facility-name">{{ $facilityName }}</div>
                    @else
                        <div class="facility-name">Receta Médica</div>
                    @endif
                    <div class="facility-info">
                        @if($facilityAddress){{ $facilityAddress }}<br>@endif
                        @if($facilityPhone)Tel: {{ $facilityPhone }}@endif
                    </div>
                </div>
            </div>
        </div>
        <div class="header-purple">
            <div class="doctor-name">{{ $doctorName }}</div>
            @if($doctorSpecialty) <div class="doctor-specialty">{{ $doctorSpecialty }}</div> @endif
            @if($doctorLicense) <div class="doctor-license">Cédula / Lic.: {{ $doctorLicense }}</div> @endif
        </div>
    </div>
    <div class="header-bar"></div>

    {{-- PATIENT BAND --}}
    <div class="patient-band">
        <div class="pb-rx"><div class="pb-rx-text">Rx</div></div>
        <div class="pb-fields">
            <div class="pb-fields-inner">
                <div class="pbf" style="width:40%">
                    <div class="pbf-label">Paciente</div>
                    <div class="pbf-val">{{ $patient->full_name ?? $patient->name ?? '—' }}</div>
                </div>
                <div class="pbf" style="width:22%">
                    <div class="pbf-label">F. Nacimiento</div>
                    <div class="pbf-val">
                        @if(!empty($patient->birth_date)) {{ \Carbon\Carbon::parse($patient->birth_date)->format('d/m/Y') }} @else — @endif
                    </div>
                </div>
                <div class="pbf" style="width:15%">
                    <div class="pbf-label">Edad</div>
                    <div class="pbf-val">
                        @if(!empty($patient->birth_date)) {{ \Carbon\Carbon::parse($patient->birth_date)->age }} a. @else — @endif
                    </div>
                </div>
                <div class="pbf">
                    <div class="pbf-label">Género</div>
                    <div class="pbf-val">{{ $patient->gender ?? '—' }}</div>
                </div>
            </div>
        </div>
        <div class="pb-meta">
            <div class="pb-meta-num">No. Receta</div>
            <div class="pb-meta-val">{{ $prescriptionNumber }}</div>
            <div class="pb-meta-num" style="margin-top:4px">Fecha</div>
            <div class="pb-meta-val" style="font-size:9px">{{ isset($encounter->end) ? \Carbon\Carbon::parse($encounter->end)->format('d/m/Y') : now()->format('d/m/Y') }}</div>
        </div>
    </div>

    {{-- BODY --}}
    <div class="body">

        @if($pageIndex === 0 && $diagnoses && $diagnoses->count() > 0)
            <div class="dx-section">
                <div class="dx-heading">Diagnósticos</div>
                <div class="dx-table">
                    @foreach($diagnoses as $d)
                        <div class="dx-tr">
                            <div class="dx-td-code">
                                @if($d->condition->icd10Code)
                                    <span class="dx-code-pill">{{ $d->condition->icd10Code->code ?? 'Dx' }}</span>
                                @else
                                    <span class="dx-code-pill">Dx</span>
                                @endif
                            </div>
                            <div class="dx-td-desc">
                                @if($d->condition->icd10Code)
                                    {{ strtoupper($d->condition->icd10Code->description_es ?? '') }}
                                @else
                                    {{ strtoupper($d->condition->onset_info ?? '') }}
                                @endif
                                @if(!empty($d->note)) <em style="color:#888;"> — {{ $d->note }}</em> @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Medications --}}
        <div class="rx-section">
            <div class="rx-heading">
                <div class="rx-heading-left">
                    <span class="rx-heading-label">Rx</span>
                    <span class="rx-heading-title">Medicamentos prescritos</span>
                </div>
                <div class="rx-heading-right">Pág. {{ $pageIndex + 1 }} / {{ count($medicationPages) }}</div>
            </div>

            @foreach($pageMedications as $index => $medication)
                @php $medication = (object) $medication; @endphp
                <div class="med-row">
                    <div class="med-n">
                        <span class="med-n-circle">{{ ($pageIndex * $medicationsPerPage) + $index + 1 }}</span>
                    </div>
                    <div class="med-content">
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

    </div>

</div></div>{{-- /page-top --}}
<div class="page-bottom"><div class="page-bottom-cell">

    {{-- SIGNATURE (last page only) --}}
    @if($pageIndex === count($medicationPages) - 1)
        <div class="footer-sig">
            @if($signatureDataUri || $sealDataUri)
                <div class="sig-images">
                    @if($signatureDataUri) <img src="{{ $signatureDataUri }}" class="sig-img" alt="Firma"> @endif
                    @if($sealDataUri) <img src="{{ $sealDataUri }}" class="seal-img" alt="Sello"> @endif
                </div>
            @endif
            <div class="sig-rule"></div>
            <div class="sig-text">Firma y Sello del Médico</div>
        </div>
    @endif

    {{-- FOOTER --}}
    <div class="footer">
        <div class="footer-left">{{ $facilityName ?: 'Hospital / Clínica' }}</div>
        <div class="footer-center">Documento confidencial de uso médico</div>
        <div class="footer-right">{{ now()->format('d/m/Y') }} | Pág. {{ $pageIndex + 1 }}</div>
    </div>
    <div class="footer-purple-bar"></div>

</div></div>{{-- /page-bottom --}}
</div>{{-- /page-inner --}}
</div>
@endforeach

</body>
</html>
