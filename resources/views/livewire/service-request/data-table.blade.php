<div>
    <div class="row">
        <div class="col-sm-12">
            <div class="card card-table show-entire">
                <div class="card-body">
                    <!-- Table Header -->
                    @component('components.table-header')
                        @slot('title')

                        @endslot
                        @slot('li_1')
                        @endslot
                        @slot('show_create', false)
                    @endcomponent
                    <!-- /Table Header -->

                    <!-- Modals -->
                    <livewire:service-request.upload-result />
                    <livewire:service-request.view-results />
                    <livewire:service-request.status-manager />

                    <div class="table-responsive">
                        <table class="table border-0 custom-table comman-table mb-0">
                            <thead>
                            <tr>
                                <th style="width: 50px;"></th>
                                <th>Paciente</th>
                                <th>Fecha Consulta</th>
                                <th>Diagnóstico Principal</th>
                                <th>Tipo de Cita</th>
                                <th>Servicios</th>
                                <th class="text-end">Resultados</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($data as $encounter)
                                <!-- Fila principal del Encounter -->
                                <tr class="encounter-row" data-encounter-id="{{ $encounter->id }}">
                                    <td>
                                        <button
                                            class="btn btn-sm btn-link toggle-services p-0"
                                            onclick="toggleServices({{ $encounter->id }})"
                                            data-bs-toggle="tooltip"
                                            title="Expandir/Contraer servicios"
                                        >
                                            <i class="fas fa-chevron-right" id="icon-{{ $encounter->id }}"></i>
                                        </button>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <strong>{{ $encounter->patient->name }}</strong><br>
                                                <small class="text-muted">{{ $encounter->patient->id_number }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        {{ $encounter->start ? $encounter->start->format('d/m/Y H:i') : 'N/A' }}
                                    </td>
                                    <td>
                                        @if($encounter->diagnoses->isNotEmpty())
                                            @php
                                                $primaryDiagnosis = $encounter->diagnoses->firstWhere('rank', 1) ?? $encounter->diagnoses->first();
                                            @endphp
                                            @if($primaryDiagnosis->condition)
                                            <span
                                                class="text-primary cursor-pointer"
                                                data-bs-toggle="tooltip"
                                                title="{{ $primaryDiagnosis->condition->code_display ?? $primaryDiagnosis->condition->onset_info }}"
                                            >
                                                {{ \Str::limit($primaryDiagnosis->condition->code_display ?? $primaryDiagnosis->condition->onset_info, 40) }}
                                            </span>
                                            @else
                                                <span class="text-muted">Sin diagnóstico</span>
                                            @endif
                                        @else
                                            <span class="text-muted">Sin diagnóstico</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($encounter->appointment && $encounter->appointment->service_type)
                                            <span class="badge bg-info">
                                                {{ ucfirst($encounter->appointment->service_type) }}
                                            </span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">
                                            {{ $encounter->serviceRequests->count() }} servicio(s)
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-secondary">
                                            {{ $encounter->serviceRequests->sum('results_count') }} resultado(s)
                                        </span>
                                    </td>
                                </tr>

                                <!-- Fila expandible con los Service Requests -->
                                <tr class="service-requests-row d-none" id="services-{{ $encounter->id }}">
                                    <td colspan="7" class="p-0">
                                        <div class="bg-light p-3">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered mb-0">
                                                    <thead class="bg-white">
                                                        <tr>
                                                            <th>Código</th>
                                                            <th>Tipo</th>
                                                            <th>Descripción</th>
                                                            <th>Estado</th>
                                                            <th>Fecha</th>
                                                            <th class="text-end">Acciones</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($encounter->serviceRequests as $serviceRequest)
                                                            <tr>
                                                                <td>{{ $serviceRequest->code }}</td>
                                                                <td>
                                                                    <span class="badge bg-secondary">
                                                                        {{ __('service_request.'.$serviceRequest->service_type) }}
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    @php
                                                                        $fullDescription = $serviceRequest->cpt?->description_es ?? $serviceRequest->code_display ?? 'N/A';
                                                                        $shortDescription = \Str::limit($fullDescription, 50);
                                                                    @endphp
                                                                    <span
                                                                        class="{{ strlen($fullDescription) > 50 ? 'cursor-pointer text-primary' : '' }}"
                                                                        @if(strlen($fullDescription) > 50)
                                                                            data-bs-toggle="tooltip"
                                                                            title="{{ $fullDescription }}"
                                                                        @endif
                                                                    >
                                                                        {{ $shortDescription }}
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    @php
                                                                        $statusEnum = \App\Enums\ServiceRequestStatus::fromString($serviceRequest->status);
                                                                    @endphp
                                                                    @if($statusEnum)
                                                                        <span class="badge {{ $statusEnum->badgeClass() }}">
                                                                            {{ $statusEnum->label() }}
                                                                        </span>
                                                                    @else
                                                                        <span class="badge bg-secondary">
                                                                            {{ ucfirst($serviceRequest->status ?? 'N/A') }}
                                                                        </span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    {{ $serviceRequest->occurrence_start ? $serviceRequest->occurrence_start : 'N/A' }}
                                                                </td>
                                                                <td class="text-end">
                                                                    <div class="btn-group btn-group-sm">
                                                                        <button
                                                                            type="button"
                                                                            class="btn btn-secondary btn-sm"
                                                                            title="Ver resultados ({{ $serviceRequest->results_count }})"
                                                                            wire:click="openViewResultsModal({{ $serviceRequest->id }})"
                                                                        >
                                                                            <i class="fa-solid fa-eye"></i>
                                                                            <span class="ms-1 badge bg-white text-dark">{{ $serviceRequest->results_count }}</span>
                                                                        </button>
                                                                        <button
                                                                            type="button"
                                                                            class="btn btn-warning btn-sm"
                                                                            title="Cambiar estado"
                                                                            wire:click="openStatusModal({{ $serviceRequest->id }})"
                                                                        >
                                                                            <i class="fas fa-exchange-alt"></i>
                                                                        </button>
                                                                        <button
                                                                            type="button"
                                                                            class="btn btn-primary btn-sm"
                                                                            title="Subir resultado"
                                                                            wire:click="openUploadModal({{ $serviceRequest->id }})"
                                                                        >
                                                                            <i class="fas fa-upload"></i>
                                                                        </button>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3"></i>
                                            <p>No se encontraron consultas con solicitudes de servicio</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                    @include('partials.pagination',['data'=>$data])
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleServices(encounterId) {
            const servicesRow = document.getElementById('services-' + encounterId);
            const icon = document.getElementById('icon-' + encounterId);

            if (servicesRow.classList.contains('d-none')) {
                servicesRow.classList.remove('d-none');
                icon.classList.remove('fa-chevron-right');
                icon.classList.add('fa-chevron-down');
            } else {
                servicesRow.classList.add('d-none');
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-right');
            }
        }

        // Inicializar tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        // Reinicializar tooltips después de actualizar Livewire
        document.addEventListener('livewire:navigated', function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>

    <style>
        .cursor-pointer {
            cursor: pointer;
        }
        .encounter-row:hover {
            background-color: #f8f9fa;
        }
        .toggle-services {
            color: #6c757d;
            text-decoration: none;
        }
        .toggle-services:hover {
            color: #0d6efd;
        }
        .service-requests-row .table {
            background-color: white;
        }
        .service-requests-row {
            background-color: #f8f9fa;
        }
    </style>
</div>
