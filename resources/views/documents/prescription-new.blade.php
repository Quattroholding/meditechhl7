@php
// Preparar URLs de firmas y sellos para uso en todo el documento

// Dividir medicamentos en páginas de 4
$allMedications = $medications->toArray();
$medicationsPerPage = 4;
$medicationPages = array_chunk($allMedications, $medicationsPerPage);
@endphp

<x-documents.base-layout
    :document-title="'Receta Médica - ' . $prescriptionNumber"
    :doctor-profile="$doctorProfile ?? null"
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
            :doctor-profile="$doctorProfile ?? null"
            :practitioner="$practitioner"
            :encounter="$encounter"
            :patient="$patient"
            :diagnoses="$diagnoses"
            :document-number="$prescriptionNumber"
            :pdf-service="$pdfService"
        >
            <x-slot:content>
                <!-- Rx Section (Medicamentos) - Max 4 per page -->
                <div class="content-section rx-section">
                    <div class="section-header">Rx</div>
                    <div class="section-content">
                        @foreach($pageMedications as $index => $medication)
                            @php $medication = (object) $medication; @endphp
                            <div class="medication-item">
                                <div class="medication-name">
                                    @if($medication->medication_id)
                                        {{ ($pageIndex * $medicationsPerPage) + $index + 1 }}. {{ $medication->medicine['home_name'] .'     '.$medication->medicine['mgs'].' '.$medication->medicine['mgs_type'].'    #'.$medication->quantity.' '.$medication->medicine['type']}}
                                    @else
                                        {{ ($pageIndex * $medicationsPerPage) + $index + 1 }}. {{$medication->medication}}
                                    @endif
                                </div>
                                <div class="medication-details">
                                    <strong>Sg:</strong> {{ $medication->dosage_text ?? $medication->dosage_instruction }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </x-slot:content>
        </x-documents.page>
    @endforeach
</x-documents.base-layout>
