@php
    // Dividir referrals en páginas de 3
    $allReferrals = $referrals->toArray();
    $referralsPerPage = 3;
    $referralPages = array_chunk($allReferrals, $referralsPerPage);
@endphp

<x-documents.base-layout
    :document-title="'Orden de Referencia - ' . $orderNumber"
    :doctor-profile="$doctorProfile ?? null"
    :pdf-service="$pdfService ?? null"
    :practitioner="$practitioner"
    :encounter="$encounter"
    :patient="$patient"
    :diagnoses="$diagnoses"
>
    <x-slot:additionalStyles>
        <style>
            /* Referral Order specific styles */
            .referral-item {
                margin-bottom: 20px;
                padding: 10px;
                /*border: 1px solid #ddd;
                border-radius: 5px;*/
                font-size: 11px;
                background-color: #fff;
            }

            .referral-header {
                font-weight: bold;
                font-size: 12px;
                margin-bottom: 8px;
                color: #2c3e50;
            }

            .referral-details {
                margin-left: 10px;
                margin-bottom: 5px;
                line-height: 1.6;
            }

            .priority-badge {
                display: inline-block;
                padding: 2px 8px;
                border-radius: 3px;
                font-size: 9px;
                font-weight: bold;
                text-transform: uppercase;
            }

            .priority-routine {
                background-color: #3498db;
                color: white;
            }

            .priority-urgent {
                background-color: #e74c3c;
                color: white;
            }

            .status-badge {
                display: inline-block;
                padding: 2px 8px;
                border-radius: 3px;
                font-size: 9px;
                font-weight: bold;
                text-transform: uppercase;
                background-color: #27ae60;
                color: white;
            }
        </style>
    </x-slot:additionalStyles>


    @foreach($referralPages as $pageIndex => $pageReferrals)
        <x-documents.page
            :doctor-profile="$doctorProfile ?? null"
            :practitioner="$practitioner"
            :encounter="$encounter"
            :patient="$patient"
            :diagnoses="$diagnoses"
            :document-number="$orderNumber"
            :pdf-service="$pdfService"
        >
            <x-slot:content>
                <!-- Referrals Section - Max 3 per page -->
                <div class="content-section rx-section">
                    <div class="section-header">Referencia a Especialista </div>
                    <div class="section-content">
                        @foreach($pageReferrals as $index => $referral)
                            @php
                                $referral = (object) $referral;

                                // Determinar si es especialista interno o externo
                                $isExternal = !empty($referral->external_specialist_name);

                                if ($isExternal) {
                                    // Especialista externo
                                    $specialistName = $referral->external_specialist_name;
                                    $speciality = $referral->external_specialist_specialty ?? 'Especialista';
                                    $license = $referral->external_specialist_license ?? null;
                                    $phone = $referral->external_specialist_phone ?? null;
                                    $clinic = $referral->external_specialist_clinic ?? null;
                                } else {
                                    // Especialista interno (del sistema)
                                    $referredToPractitioner = $referral->referred_to ?? null;
                                    $specialistName = $referredToPractitioner['name'] ?? null;
                                    $speciality = $referral->supporting_info['speciality_name'] ?? 'Especialista';
                                    $license = $referredToPractitioner['licence_code'] ?? null;
                                    $phone = null;
                                    $clinic = null;
                                }
                            @endphp
                            <div class="referral-item">
                                <div class="referral-header">
                                    {{ ($pageIndex * $referralsPerPage) + $index + 1 }}. {{$referral->description}}
                                </div>

                                @if($specialistName)
                                    <div class="referral-details">
                                        <strong>Médico Referido:</strong> {{ $specialistName }}
                                        @if($license)
                                            - <em>Licencia: {{ $license }}</em>
                                        @endif
                                    </div>
                                @endif

                                @if($clinic)
                                    <div class="referral-details">
                                        <strong>Clínica/Consultorio:</strong> {{ $clinic }}
                                    </div>
                                @endif

                                @if($phone)
                                    <div class="referral-details">
                                        <strong>Teléfono:</strong> {{ $phone }}
                                    </div>
                                @endif

                                @if($referral->reason)
                                    <div class="referral-details">
                                        <strong>Motivo de Referencia:</strong><br>
                                        {{ $referral->reason }}
                                    </div>
                                @endif

                                @if($referral->occurrence_date)
                                    <div class="referral-details">
                                        <strong>Fecha Sugerida:</strong> {{ \Carbon\Carbon::parse($referral->occurrence_date)->format('d/m/Y') }}
                                    </div>
                                @endif

                            </div>

                        @endforeach
                    </div>
                </div>
                {{--}}
                <!-- Notas adicionales -->
                <div class="content-section" style="margin-top: 20px;">
                    <div class="section-content" style="font-size: 10px; color: #7f8c8d;">
                        <p><strong>Nota:</strong> Esta orden de referencia debe ser presentada al especialista indicado. El paciente debe coordinar la cita según disponibilidad.</p>
                    </div>
                </div>
                {{--}}
            </x-slot:content>
        </x-documents.page>
    @endforeach
</x-documents.base-layout>
