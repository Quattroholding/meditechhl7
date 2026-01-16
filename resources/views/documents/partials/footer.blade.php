{{-- Footer Section with Signature and Document Number --}}
@php
    $signatureDataUri = '';
    $sealDataUri = '';

    // First try doctorProfile if available
    if(isset($doctorProfile) && $doctorProfile) {
        // Get signature from doctorProfile
        if($doctorProfile->signature) {
            if($pdfService && method_exists($pdfService, 'isPrivateImage') && $pdfService->isPrivateImage($doctorProfile->signature)) {
                $signatureDataUri = $pdfService->getPrivateImageDataUri($doctorProfile->signature);
            } elseif(file_exists(public_path('storage/' . $doctorProfile->signature))) {
                $signatureDataUri = 'data:image/' . pathinfo($doctorProfile->signature, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents(public_path('storage/' . $doctorProfile->signature)));
            }
        }
        // Get seal from doctorProfile
        if($doctorProfile->seal) {
            if($pdfService && method_exists($pdfService, 'isPrivateImage') && $pdfService->isPrivateImage($doctorProfile->seal)) {
                $sealDataUri = $pdfService->getPrivateImageDataUri($doctorProfile->seal);
            } elseif(file_exists(public_path('storage/' . $doctorProfile->seal))) {
                $sealDataUri = 'data:image/' . pathinfo($doctorProfile->seal, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents(public_path('storage/' . $doctorProfile->seal)));
            }
        }
    }

    // Fallback: if no signature/seal from doctorProfile, try practitioner files
    if(empty($signatureDataUri) && isset($practitioner) && $practitioner) {
        // Get signature from practitioner files (table: files, type: signature)
        $signatureFile = $practitioner->signature();
        if($signatureFile && !empty($signatureFile->path)) {
            if(\Illuminate\Support\Facades\Storage::disk('local')->exists($signatureFile->path)) {
                $fileContents = \Illuminate\Support\Facades\Storage::disk('local')->get($signatureFile->path);
                $extension = pathinfo($signatureFile->path, PATHINFO_EXTENSION);
                $signatureDataUri = 'data:image/' . $extension . ';base64,' . base64_encode($fileContents);
            }
        }
    }

    if(empty($sealDataUri) && isset($practitioner) && $practitioner) {
        // Get seal from practitioner files (table: files, type: seal)
        $sealFile = $practitioner->seal();
        if($sealFile && !empty($sealFile->path)) {
            if(\Illuminate\Support\Facades\Storage::disk('local')->exists($sealFile->path)) {
                $fileContents = \Illuminate\Support\Facades\Storage::disk('local')->get($sealFile->path);
                $extension = pathinfo($sealFile->path, PATHINFO_EXTENSION);
                $sealDataUri = 'data:image/' . $extension . ';base64,' . base64_encode($fileContents);
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
