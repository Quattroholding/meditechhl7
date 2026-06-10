@php
    // Inicializar variables opcionales
    $serviceType = $serviceType ?? null;

    // Filtrar servicios por tipo si se proporciona
    $filteredServices = $serviceType
        ? $serviceRequests->where('service_type', $serviceType)
        : $serviceRequests;

    // Dividir servicios en páginas de 4
    $allServices = $filteredServices->toArray();
    $servicesPerPage = 4;
    $servicePages = array_chunk($allServices, $servicesPerPage);

    // Obtener nombre del tipo de servicio para el título usando el Enum
    $serviceTypeTitle = \App\Enums\ServiceType::labelFromValue($serviceType);
@endphp

<x-documents.base-layout
    :document-title="'Orden Médica - ' . $orderNumber"
    :doctor-profile="$doctorProfile ?? null"
    :pdf-service="$pdfService ?? null"
    :practitioner="$practitioner"
    :encounter="$encounter"
    :patient="$patient"
    :diagnoses="$diagnoses"
>
    <x-slot:additionalStyles>
        <style>
            /* Medical Order specific styles */
            .service-item {
                margin-bottom: 15px;
                font-size: 11px;
            }

            .service-name {
                font-weight: bold;
                margin-bottom: 3px;
            }

            .service-details {
                margin-left: 10px;
                margin-bottom: 5px;
            }

            .priority-badge {
                display: inline-block;
                padding: 2px 6px;
                border-radius: 3px;
                font-size: 9px;
                font-weight: bold;
                text-transform: uppercase;
            }

            .priority-routine {
                background-color: #f1c40f;
                color: #000;
            }

            .priority-urgent {
                background-color: #e74c3c;
                color: white;
            }

            .priority-asap {
                background-color: #e67e22;
                color: white;
            }

            .priority-stat {
                background-color: #8e44ad;
                color: white;
            }
        </style>
    </x-slot:additionalStyles>
    @foreach($servicePages as $pageIndex => $pageServices)
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
                <!-- Services Section - Max 4 per page -->
                <div class="content-section rx-section">
                    <div class="section-header">{{ $serviceTypeTitle }}</div>
                    <div class="section-content">

                        @foreach($pageServices as $index => $service)
                            @php $service = (object) $service; @endphp
                            <div class="service-item">
                                <div class="service-details">
                                    {{ ($pageIndex * $servicesPerPage) + $index + 1 }}. {{ $service->code }} | {{ $service->cpt['description_es'] ?? 'Servicio no especificado' }}<br/>
                                </div>
                                @if($service->body_site && is_array($service->body_site))
                                    <div class="service-details">
                                        <strong>Sitio Anatómico:</strong> {{ $service->body_site['display'] ?? $service->body_site['code'] ?? 'N/A' }}<br/>
                                    </div>
                                @endif
                                @if($service->patient_instruction)
                                    <div class="service-details">
                                        <strong>Instrucciones para el paciente:</strong> {{ $service->patient_instruction }}<br/>
                                    </div>
                                @endif
                                @if($service->note)
                                    <div class="service-details">
                                        <strong>Notas:</strong> {{ $service->note }}<br/><br/>
                                    </div>
                                @endif
                                @if($service->reason_code)
                                    <div class="service-details">
                                        <strong>Razón:</strong> {{ $service->reason_code }}
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
