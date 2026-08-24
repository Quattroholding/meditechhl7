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

                    <div class="table-responsive">
                        <table class="table border-0 custom-table comman-table mb-0">
                            <thead>
                            <tr>
                                <th style="width: 50px;"></th>
                                <th>Paciente</th>
                                <th>Fecha Consulta</th>
                                @can('patients.see_conditions')
                                <th>Diagnóstico Principal</th>
                                @endcan
                                <th>Tipo de Cita</th>
                                <th>Medicamentos</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($data as $encounter)
                                <!-- Fila principal del Encounter -->
                                <tr class="encounter-row" data-encounter-id="{{ $encounter->id }}">
                                    <td>
                                        <button
                                            class="btn btn-sm btn-link toggle-medications p-0"
                                            onclick="toggleMedications({{ $encounter->id }})"
                                            data-bs-toggle="tooltip"
                                            title="Expandir/Contraer medicamentos"
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
                                    @can('patients.see_conditions')
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
                                    @endcan
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
                                            {{ $encounter->medicationRequests->count() }} medicamento(s)
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        @if($encounter->medicationRequests->count() > 0)
                                            <a
                                                href="{{ route('prescription.download', $encounter->id) }}"
                                                class="btn btn-sm btn-success"
                                                data-bs-toggle="tooltip"
                                                title="Descargar receta médica"
                                                target="_blank"
                                            >
                                                <i class="fas fa-prescription"></i> Descargar
                                            </a>
                                        @endif
                                    </td>
                                </tr>

                                <!-- Fila expandible con los Medication Requests -->
                                <tr class="medication-requests-row d-none" id="medications-{{ $encounter->id }}">
                                    <td colspan="7" class="p-0">
                                        <div class="bg-light p-3">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered mb-0">
                                                    <thead class="bg-white">
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Medicamento</th>
                                                            <th>Dosis</th>
                                                            {{--}}
                                                            <th>Vía</th>
                                                            <th>Frecuencia</th>
                                                            <th>Duración</th>
                                                            <th>Notas</th>

                                                            {{--}}
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($encounter->medicationRequests as $index => $medicationRequest)
                                                            <tr>
                                                                <td>{{ $index + 1 }}</td>
                                                                <td>
                                                                    <strong>
                                                                        @if($medicationRequest->medicine)
                                                                            {{ $medicationRequest->medicine->tradename }}
                                                                        @elseif($medicationRequest->medication2)
                                                                            {{ $medicationRequest->medication2->display }}
                                                                        @else
                                                                            {{ $medicationRequest->medication }}
                                                                        @endif
                                                                    </strong>
                                                                    @if($medicationRequest->medicine && $medicationRequest->medicine->genericname)
                                                                        <br><small class="text-muted">{{ $medicationRequest->medicine->genericname }}</small>
                                                                    @elseif($medicationRequest->medication2)
                                                                        <br><small class="text-muted">
                                                                            {{ $medicationRequest->medication2->ingredients->pluck('active_ingredient')->join(', ') }}
                                                                        </small>
                                                                    @endif
                                                                </td>
                                                                <td>{{ $medicationRequest->dosage_text ?? 'N/A' }}</td>
                                                                {{--}}
                                                                <td>{{ $medicationRequest->route ?? 'N/A' }}</td>
                                                                <td>{{ $medicationRequest->frequency ?? 'N/A' }}</td>
                                                                <td>
                                                                    @if($medicationRequest->duration)
                                                                        {{ $medicationRequest->duration }} días
                                                                    @else
                                                                        N/A
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if($medicationRequest->note)
                                                                        <span
                                                                            class="cursor-pointer text-primary"
                                                                            data-bs-toggle="tooltip"
                                                                            title="{{ $medicationRequest->note }}"
                                                                        >
                                                                            {{ \Str::limit($medicationRequest->note, 30) }}
                                                                        </span>
                                                                    @else
                                                                        <span class="text-muted">-</span>
                                                                    @endif
                                                                </td>
                                                                {{--}}
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
                                            <i class="fas fa-prescription-bottle-alt fa-3x mb-3"></i>
                                            <p>No se encontraron prescripciones registradas</p>
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
        function toggleMedications(encounterId) {
            const medicationsRow = document.getElementById('medications-' + encounterId);
            const icon = document.getElementById('icon-' + encounterId);

            if (medicationsRow.classList.contains('d-none')) {
                medicationsRow.classList.remove('d-none');
                icon.classList.remove('fa-chevron-right');
                icon.classList.add('fa-chevron-down');
            } else {
                medicationsRow.classList.add('d-none');
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
        .toggle-medications {
            color: #6c757d;
            text-decoration: none;
        }
        .toggle-medications:hover {
            color: #0d6efd;
        }
        .medication-requests-row .table {
            background-color: white;
        }
        .medication-requests-row {
            background-color: #f8f9fa;
        }
    </style>
</div>
