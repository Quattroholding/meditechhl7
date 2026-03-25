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

    public function __construct(
        $doctorProfile = null,
        $practitioner = null,
        $encounter = null,
        $patient = null,
        $diagnoses = null,
        $documentNumber = null,
        $signatureDataUri = '',
        $sealDataUri = ''
    ) {
        $this->doctorProfile = $doctorProfile;
        $this->practitioner = $practitioner;
        $this->encounter = $encounter;
        $this->patient = $patient;
        $this->diagnoses = $diagnoses;
        $this->documentNumber = $documentNumber;
        $this->signatureDataUri = $signatureDataUri;
        $this->sealDataUri = $sealDataUri;
    }

    public function render()
    {
        return view('components.documents.page');
    }
}
