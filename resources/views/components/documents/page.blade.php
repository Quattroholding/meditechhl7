<div class="document-container">
    {{-- Header --}}
    @include('documents.partials.header', [
        'doctorProfile' => $doctorProfile,
        'practitioner' => $practitioner,
        'encounter' => $encounter
    ])

    {{-- Patient Info --}}
    @include('documents.partials.patient-info', [
        'patient' => $patient,
        'encounter' => $encounter
    ])

    {{-- Main Content (Slot) --}}
    {{ $content }}

    {{-- Diagnoses Section --}}
    <div class="content-section dx-section">
        <div class="section-header">Dx</div>
        <div class="section-content">
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

    {{-- Footer
    @include('documents.partials.footer', [
        'documentNumber' => $documentNumber,
        'pdfService'=>$pdfService])
        --}}

    @include('documents.partials.footer', [
        'documentNumber' => $documentNumber,
        'doctorProfile' => $doctorProfile ?? null,
        'practitioner' => $practitioner ?? null,
        'pdfService' => $pdfService ?? null,
    ])

</div>
