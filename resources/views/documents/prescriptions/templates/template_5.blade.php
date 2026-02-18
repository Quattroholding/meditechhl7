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
    $teal = '#2a9d8f';
    $darkTeal = '#1f7267';
    $lightTeal = '#e8f7f5';
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Receta Médica - {{ $prescriptionNumber }}</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11px; color: #222; background: {{ $teal }}; }
    @page { margin: 0; size: letter portrait; }

    /* Full teal background page, white card floated inside */
    .page {
        width: 100%;
        min-height: 100vh;
        background: {{ $teal }};
        position: relative;
        page-break-after: always;
        overflow: hidden;
    }
    .page:last-child { page-break-after: avoid; }

    /* Background texture dots */
    .bg-dots {
        position: absolute; top: 0; left: 0; right: 0; bottom: 0;
        background-image: radial-gradient(circle, rgba(255,255,255,0.08) 1px, transparent 1px);
        background-size: 18px 18px;
    }
    /* Large circle accent */
    .bg-circle {
        position: absolute; top: -100px; right: -100px;
        width: 350px; height: 350px; border-radius: 50%;
        background: rgba(255,255,255,0.06);
    }

    /* White card */
    .card {
        position: relative; z-index: 2;
        margin: 18px 18px 18px 18px;
        background: #fff;
        border-radius: 6px;
        overflow: hidden;
        min-height: calc(100vh - 36px);
        display: flex;
        flex-direction: column;
    }

    /* Card header — teal top strip + doctor row */
    .card-header-strip {
        background: {{ $darkTeal }};
        padding: 14px 20px;
        display: table; width: 100%;
    }
    .ch-logo-cell { display: table-cell; width: 65px; vertical-align: middle; }
    .ch-logo-cell img { max-width: 55px; max-height: 50px; }
    .ch-doctor-cell { display: table-cell; vertical-align: middle; padding-left: 10px; }
    .ch-rx-cell { display: table-cell; width: 90px; vertical-align: middle; text-align: right; }
    .doctor-name { color: #fff; font-size: 15px; font-weight: bold; margin-bottom: 3px; }
    .doctor-specialty { color: rgba(255,255,255,0.80); font-size: 10px; margin-bottom: 2px; }
    .doctor-license { color: rgba(255,255,255,0.60); font-size: 9px; }
    .rx-symbol { font-size: 44px; font-weight: bold; font-style: italic; color: rgba(255,255,255,0.30); line-height: 1; }

    /* Teal thin divider */
    .card-divider { height: 4px; background: {{ $teal }}; }

    /* Facility bar */
    .facility-bar { background: {{ $lightTeal }}; padding: 6px 20px; display: table; width: 100%; }
    .fb-name { display: table-cell; font-size: 10px; font-weight: bold; color: {{ $darkTeal }}; vertical-align: middle; }
    .fb-right { display: table-cell; text-align: right; font-size: 9px; color: #777; vertical-align: middle; }

    /* Card body */
    .card-body { flex: 1; padding: 16px 20px; }

    /* Patient info — horizontal fields in a teal-bordered box */
    .patient-box {
        border: 1.5px solid {{ $teal }};
        border-radius: 4px;
        margin-bottom: 14px;
        overflow: hidden;
    }
    .patient-box-title {
        background: {{ $teal }};
        color: #fff;
        font-size: 8.5px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        padding: 4px 12px;
    }
    .patient-box-fields { display: table; width: 100%; padding: 10px 12px; }
    .pbf { display: table-cell; vertical-align: top; padding-right: 10px; }
    .pbf-label { font-size: 7.5px; color: #aaa; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 2px; }
    .pbf-val { font-size: 10.5px; font-weight: bold; color: #222; }

    /* Diagnoses */
    .dx-area { margin-bottom: 14px; }
    .dx-title {
        font-size: 8.5px; font-weight: bold; color: {{ $teal }};
        text-transform: uppercase; letter-spacing: 0.8px;
        margin-bottom: 6px; display: table; width: 100%;
    }
    .dx-title::after {
        content: ''; display: table-cell;
        border-bottom: 1px dashed {{ $teal }};
        vertical-align: middle; padding-left: 8px;
    }
    .dx-row { display: table; width: 100%; margin-bottom: 4px; }
    .dx-badge { display: table-cell; width: 60px; vertical-align: top; }
    .dx-badge span { background: {{ $teal }}; color: #fff; font-size: 8.5px; padding: 1px 6px; border-radius: 3px; display: inline-block; }
    .dx-desc { display: table-cell; vertical-align: top; padding-left: 8px; font-size: 10px; color: #333; }

    /* Medications */
    .rx-title-row { display: table; width: 100%; margin-bottom: 10px; border-bottom: 2px solid {{ $lightTeal }}; padding-bottom: 6px; }
    .rx-title-left { display: table-cell; vertical-align: middle; }
    .rx-italic { font-size: 20px; font-style: italic; font-weight: bold; color: {{ $teal }}; display: inline-block; vertical-align: middle; margin-right: 5px; }
    .rx-label { font-size: 10px; font-weight: bold; color: #555; text-transform: uppercase; letter-spacing: 0.6px; display: inline-block; vertical-align: middle; }
    .rx-title-right { display: table-cell; text-align: right; vertical-align: middle; font-size: 8.5px; color: #bbb; }

    .med-item { margin-bottom: 10px; padding: 9px 12px; border: 1px solid #e8f7f5; border-left: 3px solid {{ $teal }}; border-radius: 0 4px 4px 0; }
    .med-name { font-size: 11px; font-weight: bold; color: #1a1a1a; margin-bottom: 3px; }
    .med-sig { font-size: 10px; color: #555; }
    .med-sig em { font-style: normal; font-weight: bold; color: {{ $teal }}; }
    .med-number { display: inline-block; background: {{ $teal }}; color: #fff; font-size: 8.5px; font-weight: bold; width: 18px; height: 18px; line-height: 18px; text-align: center; border-radius: 50%; margin-right: 5px; vertical-align: middle; }

    /* Signature */
    .sig-area { margin-top: 20px; text-align: center; padding-top: 14px; border-top: 1px dashed #ccc; }
    .sig-images { margin-bottom: 6px; }
    .sig-img { max-width: 180px; max-height: 90px; margin: 0 8px; }
    .seal-img { max-width: 110px; max-height: 110px; margin: 0 8px; }
    .sig-line { display: block; border-top: 1px solid {{ $teal }}; width: 160px; margin: 4px auto 3px; }
    .sig-text { font-size: 8.5px; color: #999; }

    /* Card footer */
    .card-footer { background: {{ $darkTeal }}; padding: 8px 20px; display: table; width: 100%; }
    .cf-left { display: table-cell; color: rgba(255,255,255,0.65); font-size: 8px; vertical-align: middle; }
    .cf-right { display: table-cell; text-align: right; color: rgba(255,255,255,0.50); font-size: 8px; vertical-align: middle; }
</style>
</head>
<body>

@foreach($medicationPages as $pageIndex => $pageMedications)
<div class="page">
    <div class="bg-dots"></div>
    <div class="bg-circle"></div>

    <div class="card">

        {{-- Card header --}}
        <div class="card-header-strip">
            <div class="ch-logo-cell">
                @if($logoDataUri)
                    <img src="{{ $logoDataUri }}" alt="Logo">
                @endif
            </div>
            <div class="ch-doctor-cell">
                <div class="doctor-name">{{ $doctorName }}</div>
                @if($doctorSpecialty) <div class="doctor-specialty">{{ $doctorSpecialty }}</div> @endif
                @if($doctorLicense) <div class="doctor-license">Cédula / Lic.: {{ $doctorLicense }}</div> @endif
            </div>
            <div class="ch-rx-cell">
                <div class="rx-symbol">Rx</div>
            </div>
        </div>
        <div class="card-divider"></div>

        {{-- Facility bar --}}
        <div class="facility-bar">
            <div class="fb-name">{{ $facilityName ?: 'Receta Médica' }}</div>
            <div class="fb-right">
                @if($facilityPhone) Tel: {{ $facilityPhone }} &nbsp;|&nbsp; @endif
                {{ isset($encounter->end) ? \Carbon\Carbon::parse($encounter->end)->format('d/m/Y') : now()->format('d/m/Y') }}
                &nbsp;|&nbsp; No. {{ $prescriptionNumber }}
            </div>
        </div>

        {{-- Body --}}
        <div class="card-body">

            @if($pageIndex === 0)
            {{-- Patient --}}
            <div class="patient-box">
                <div class="patient-box-title">Datos del paciente</div>
                <div class="patient-box-fields">
                    <div class="pbf" style="width:40%">
                        <div class="pbf-label">Nombre completo</div>
                        <div class="pbf-val">{{ $patient->full_name ?? $patient->name ?? '—' }}</div>
                    </div>
                    <div class="pbf" style="width:20%">
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
                    <div class="pbf" style="width:25%">
                        <div class="pbf-label">Género</div>
                        <div class="pbf-val">{{ $patient->gender ?? '—' }}</div>
                    </div>
                </div>
            </div>

            {{-- Dx --}}
            @if($diagnoses && $diagnoses->count() > 0)
                <div class="dx-area">
                    <div class="dx-title">Diagnósticos</div>
                    @foreach($diagnoses as $d)
                        <div class="dx-row">
                            <div class="dx-badge">
                                @if($d->condition->icd10Code)
                                    <span>{{ $d->condition->icd10Code->code ?? 'Dx' }}</span>
                                @else
                                    <span>Dx</span>
                                @endif
                            </div>
                            <div class="dx-desc">
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
            @endif
            @endif

            {{-- Medications --}}
            <div class="rx-title-row">
                <div class="rx-title-left">
                    <span class="rx-italic">Rx</span>
                    <span class="rx-label">Medicamentos</span>
                </div>
                <div class="rx-title-right">Pág. {{ $pageIndex + 1 }} / {{ count($medicationPages) }}</div>
            </div>

            @foreach($pageMedications as $index => $medication)
                @php $medication = (object) $medication; @endphp
                <div class="med-item">
                    <div class="med-name">
                        <span class="med-number">{{ ($pageIndex * $medicationsPerPage) + $index + 1 }}</span>
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
                        <em>Sig:</em> {{ $medication->dosage_text ?? $medication->dosage_instruction ?? '' }}
                    </div>
                </div>
            @endforeach

            @if($pageIndex === count($medicationPages) - 1)
                <div class="sig-area">
                    @if($signatureDataUri || $sealDataUri)
                        <div class="sig-images">
                            @if($signatureDataUri) <img src="{{ $signatureDataUri }}" class="sig-img" alt="Firma"> @endif
                            @if($sealDataUri) <img src="{{ $sealDataUri }}" class="seal-img" alt="Sello"> @endif
                        </div>
                    @endif
                    <span class="sig-line"></span>
                    <div class="sig-text">Firma y Sello del Médico</div>
                </div>
            @endif

        </div>

        {{-- Card footer --}}
        <div class="card-footer">
            <div class="cf-left">{{ $facilityName ?: 'Hospital / Clínica' }} — Documento confidencial</div>
            <div class="cf-right">{{ now()->format('d/m/Y') }}</div>
        </div>

    </div>
</div>
@endforeach

</body>
</html>
