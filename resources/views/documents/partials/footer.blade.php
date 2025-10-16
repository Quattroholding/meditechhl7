{{-- Footer Section with Signature and Document Number --}}
@php
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
@endphp
<div class="footer-section">
    <!-- Document Number - Bottom Left in Red -->
    <div class="document-number-footer">
        {{ $documentNumber ?? '0001' }}
    </div>

    <div class="seal-section">
        @if($signatureDataUri)
            <img src="{{ $signatureDataUri }}" alt="Firma" class="doctor-signature">
            @if($sealDataUri)
                <img src="{{ $sealDataUri }}" alt="Sello" class="doctor-seal">
            @endif
        @endif
    </div>

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-line"></div>
        <div class="signature-text">Firma y Sello del Médico</div>
    </div>
</div>
