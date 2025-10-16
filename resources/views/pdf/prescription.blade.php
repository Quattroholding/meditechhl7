@php
    // Dividir medicamentos en páginas de 5
    $allMedications = $prescription->activeMedications->toArray();
    $medicationsPerPage = 5;
    $medicationPages = array_chunk($allMedications, $medicationsPerPage);

    // Preparar datos para el componente
    $doctorProfile = $prescription->doctorProfile;
    $practitioner = (object)[
        'name' => $prescription->doctorProfile->user->first_name . ' ' . $prescription->doctorProfile->user->last_name
    ];
    $encounter = (object)[
        'appointment' => (object)[
            'medicalSpeciality' => (object)[
                'name' => $prescription->doctorProfile->speciality ?? ''
            ],
            'client' => null
        ],
        'created_at' => $prescription->prescription_date
    ];
    $patient = (object)[
        'name' => $prescription->patient_name,
        'age' => $prescription->patient_age ?? '',
        'identifier' => $prescription->patient_document ?? ''
    ];
    $diagnoses = [(object)[
        'condition' => (object)[
            'icd10Code' => null,
            'onset_info' => $prescription->diagnosis ?? ''
        ],
        'note' => $prescription->additional_notes ?? ''
    ]];
@endphp

<x-documents.base-layout
    :document-title="'Receta Médica - ' . $prescription->prescription_number"
    :doctor-profile="$doctorProfile"
    :pdf-service="$pdfService ?? null"
    :practitioner="$practitioner"
    :encounter="$encounter"
    :patient="$patient"
    :diagnoses="$diagnoses"
>
    <x-slot:additionalStyles>
        <style>
            /* Prescription-specific styles */
            .medication-item {
                margin-bottom: 15px;
                font-size: 11px;
            }

            .medication-name {
                font-weight: bold;
                margin-bottom: 3px;
            }

            .medication-details {
                margin-left: 10px;
                margin-bottom: 5px;
            }
        </style>
    </x-slot:additionalStyles>

    @foreach($medicationPages as $pageIndex => $pageMedications)
        <x-documents.page
            :doctor-profile="$doctorProfile"
            :practitioner="$practitioner"
            :encounter="$encounter"
            :patient="$patient"
            :diagnoses="$diagnoses"
            :document-number="$prescription->prescription_number"
            :pdf-service="$pdfService ?? null"
        >
            <x-slot:content>
                <!-- Rx Section (Medicamentos) - Max 5 per page -->
                <div class="content-section rx-section">
                    <div class="section-header">Rx</div>
                    <div class="section-content">
                        @foreach($pageMedications as $index => $medication)
                            @php $medication = (object) $medication; @endphp
                            <div class="medication-item">
                                <div class="medication-name">
                                    {{ ($pageIndex * $medicationsPerPage) + $index + 1 }}. {{ $medication->medication_name . '     ' . $medication->dosage . '    #' . $medication->quantity . ' ' . $medication->presentation }}
                                </div>
                                <div class="medication-details">
                                    <strong>Sg:</strong> {{ $medication->frequency . ' por ' . $medication->duration }}
                                </div>
                                @if($medication->instructions && $medication->instructions != 'Según indicación médica')
                                    <div class="medication-details">
                                        <strong>Instrucciones:</strong> {{ $medication->instructions }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </x-slot:content>
        </x-documents.page>
    @endforeach
</x-documents.base-layout>
