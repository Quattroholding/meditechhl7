<?php

namespace App\View\Components\Documents;

use Illuminate\View\Component;

class Page extends Component
{
    public $doctorProfile;

    public $practitioner;

    public $encounter;

    public $patient;

    public $diagnoses;

    public $documentNumber;

    public string $signatureDataUri = '';

    public string $sealDataUri = '';

    public $pdfService;

    public function __construct(
        $doctorProfile = null,
        $practitioner = null,
        $encounter = null,
        $patient = null,
        $diagnoses = null,
        $documentNumber = null,
        $signatureDataUri = '',
        $sealDataUri = '',
        $pdfService = null
    ) {
        $this->doctorProfile = $doctorProfile;
        $this->practitioner = $practitioner;
        $this->encounter = $encounter;
        $this->patient = $patient;
        $this->diagnoses = $diagnoses;
        $this->documentNumber = $documentNumber;
        $this->signatureDataUri = $signatureDataUri;
        $this->sealDataUri = $sealDataUri;
        $this->pdfService = $pdfService;

        // Preparar URLs de firmas y sellos
        $this->prepareSignatureAndSeal();
    }

    protected function prepareSignatureAndSeal(): void
    {
        if ($this->doctorProfile) {
            if ($this->doctorProfile->signature) {
                if ($this->pdfService && $this->pdfService->isPrivateImage($this->doctorProfile->signature)) {
                    $this->signatureDataUri = $this->pdfService->getPrivateImageDataUri($this->doctorProfile->signature);
                } elseif (file_exists(public_path('storage/'.$this->doctorProfile->signature))) {
                    $this->signatureDataUri = 'data:image/'.pathinfo($this->doctorProfile->signature, PATHINFO_EXTENSION).';base64,'.base64_encode(file_get_contents(public_path('storage/'.$this->doctorProfile->signature)));
                }
            }

            if ($this->doctorProfile->seal) {
                if ($this->pdfService && $this->pdfService->isPrivateImage($this->doctorProfile->seal)) {
                    $this->sealDataUri = $this->pdfService->getPrivateImageDataUri($this->doctorProfile->seal);
                } elseif (file_exists(public_path('storage/'.$this->doctorProfile->seal))) {
                    $this->sealDataUri = 'data:image/'.pathinfo($this->doctorProfile->seal, PATHINFO_EXTENSION).';base64,'.base64_encode(file_get_contents(public_path('storage/'.$this->doctorProfile->seal)));
                }
            }
        }
    }

    public function render()
    {
        return view('components.documents.page');
    }
}
