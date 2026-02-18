@php
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
                <div class="content-section rx-section">
                    <div class="section-header">Rx</div>
                    <div class="section-content">
                        @foreach($pageMedications as $index => $medication)
                            @php $medication = (object) $medication; @endphp
                            <div class="medication-item">
                                <div class="medication-name">
                                    @if(!empty($medication->medication_id2) && !empty($medication->medication2))
                                        @php
                                            $med2 = (object) $medication->medication2;
                                            $ingredient = isset($med2->ingredients) && count($med2->ingredients) > 0 ? (object) $med2->ingredients[0] : null;
                                            $strength = $ingredient ? $ingredient->strength_value . ' ' . $ingredient->strength_unit : '';
                                        @endphp
                                        {{ ($pageIndex * $medicationsPerPage) + $index + 1 }}. {{ $med2->display ?? $med2->home_name }}  {{ $strength }}  #{{ $medication->quantity }} {{ $med2->form }}
                                    @elseif(!empty($medication->medication_id) && !empty($medication->medicine))
                                        {{ ($pageIndex * $medicationsPerPage) + $index + 1 }}. {{ $medication->medicine['home_name'] . '  ' . $medication->medicine['mgs'] . ' ' . $medication->medicine['mgs_type'] . '  #' . $medication->quantity . ' ' . $medication->medicine['type'] }}
                                    @else
                                        {{ ($pageIndex * $medicationsPerPage) + $index + 1 }}. {{ $medication->medication }}
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
