<div>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-history me-2"></i>
                Historial de Cambios de la Consulta
            </h5>
        </div>
        <div class="card-body">
            @if($snapshots->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Versión</th>
                                <th>Tipo</th>
                                <th>Fecha/Hora</th>
                                <th>Creado Por</th>
                                <th>Resumen</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($snapshots as $snapshot)
                                <tr>
                                    <td>
                                        <span class="badge bg-primary">v{{ $snapshot->version }}</span>
                                    </td>
                                    <td>
                                        @if($snapshot->snapshot_type === 'initial_finish')
                                            <span class="badge bg-success">
                                                <i class="fas fa-flag-checkered me-1"></i>
                                                Finalización Inicial
                                            </span>
                                        @else
                                            <span class="badge bg-warning">
                                                <i class="fas fa-edit me-1"></i>
                                                Modificación
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $snapshot->created_at->format('d/m/Y H:i:s') }}</small>
                                    </td>
                                    <td>
                                        {{ $snapshot->creator->first_name ?? 'Sistema' }}
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $snapshot->change_summary ?? 'Sin descripción' }}
                                        </small>
                                    </td>
                                    <td>
                                        <button
                                            wire:click="viewSnapshot({{ $snapshot->id }})"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i>
                                            Ver Detalles
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle me-2"></i>
                    Total de versiones guardadas: <strong>{{ $snapshots->count() }}</strong>
                </div>
            @else
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    No hay historial de cambios disponible para esta consulta.
                </div>
            @endif
        </div>
    </div>

    <!-- Modal para ver detalles del snapshot -->
    @if($showModal)
        @teleport('body')
        <div class="modal-overlay" wire:click="closeModal" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 10000; display: flex; align-items: center; justify-content: center;">
            <div class="modal-content" wire:click.stop style="position: relative; max-width: 1200px; width: 95%; max-height: 90vh; overflow-y: auto; background: white; border-radius: 8px; padding: 0;">
                <div class="modal-header" style="padding: 20px; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center;">
                    <h5 class="modal-title" style="margin: 0; font-size: 1.25rem; font-weight: 600;">
                        @if($selectedSnapshot)
                            Detalles del Snapshot - Versión {{ $selectedSnapshot->version }}
                            <span class="badge bg-secondary ms-2">
                                {{ $selectedSnapshot->created_at->format('d/m/Y H:i') }}
                            </span>
                        @endif
                    </h5>
                    <button wire:click="closeModal" style="background: none; border: none; font-size: 28px; cursor: pointer; line-height: 1; padding: 0; width: 30px; height: 30px;">&times;</button>
                </div>
                <div class="modal-body" style="padding: 20px;">
                    @if($selectedSnapshotData)
                        <!-- Información del Encounter -->
                        <div class="card mb-3">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0">Información de la Consulta</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Estado:</strong> {{ ucfirst($selectedSnapshotData['encounter']['status'] ?? 'N/A') }}</p>
                                        <p><strong>Motivo:</strong> {{ $selectedSnapshotData['encounter']['reason'] ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Inicio:</strong> {{ \Carbon\Carbon::parse($selectedSnapshotData['encounter']['start'])->format('d-m-Y H:i') ?? 'N/A' }}</p>
                                        <p><strong>Fin:</strong> {{ \Carbon\Carbon::parse($selectedSnapshotData['encounter']['end'])->format('d-m-Y H:i') ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                @if(!empty($selectedSnapshotData['encounter']['general_note']))
                                    <div class="mt-2">
                                        <strong>Nota General:</strong>
                                        <p class="text-muted">{{ $selectedSnapshotData['encounter']['general_note'] }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Diagnósticos -->
                        @if(!empty($selectedSnapshotData['diagnoses']) && count($selectedSnapshotData['diagnoses']) > 0)
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0  text-white">
                                        <i class="fas fa-diagnoses me-2"></i>
                                        Diagnósticos ({{ count($selectedSnapshotData['diagnoses']) }})
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group">
                                        @foreach($selectedSnapshotData['diagnoses'] as $diagnosis)
                                            <li class="list-group-item">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <strong>{{ $diagnosis['condition']['code'] ?? 'N/A' }}</strong>
                                                        @if(!empty($diagnosis['condition']['description_es']))
                                                            - {{ $diagnosis['condition']['description_es'] }}
                                                        @endif
                                                        <br>
                                                        <small class="text-muted">
                                                            <strong>Estado Clínico:</strong>
                                                            <span class="badge bg-{{ $diagnosis['condition']['clinical_status'] === 'active' ? 'success' : 'secondary' }}">
                                                                {{ ucfirst($diagnosis['condition']['clinical_status'] ?? 'N/A') }}
                                                            </span>
                                                        </small>
                                                        @if(!empty($diagnosis['condition']['severity']))
                                                            <br><small><strong>Severidad:</strong> {{ __('present_illness.' . $diagnosis['condition']['severity']) }}</small>
                                                        @endif
                                                        @if(!empty($diagnosis['condition']['note']))
                                                            <br><small><strong>Nota:</strong> {{ $diagnosis['condition']['note'] }}</small>
                                                        @endif
                                                    </div>
                                                    @if(!empty($diagnosis['condition']['onset_date']))
                                                        <small class="text-muted">{{ \Carbon\Carbon::parse($diagnosis['condition']['onset_date'])->format('d-m-Y H:i') }}</small>
                                                    @endif
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif

                        <!-- Signos Vitales -->
                        @if(!empty($selectedSnapshotData['vital_signs']) && count($selectedSnapshotData['vital_signs']) > 0)
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0  text-white">
                                        <i class="fas fa-heartbeat me-2"></i>
                                        Signos Vitales ({{ count($selectedSnapshotData['vital_signs']) }})
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @foreach($selectedSnapshotData['vital_signs'] as $vital)
                                            <div class="col-md-4 mb-2">
                                                <div class="border p-2 rounded">
                                                    <strong>
                                                        {{ $vital['name'] ?? $vital['code'] ?? 'N/A' }}
                                                    </strong>
                                                    @if(!empty($vital['name']) && !empty($vital['code']))
                                                        <br><small class="text-muted">{{ $vital['code'] }}</small>
                                                    @endif
                                                    <br>
                                                    <span class="text-primary fw-bold">{{ $vital['value'] ?? 'N/A' }} {{ $vital['unit'] ?? '' }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Medicamentos -->
                        @if(!empty($selectedSnapshotData['medication_requests']) && count($selectedSnapshotData['medication_requests']) > 0)
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0  text-white">
                                        <i class="fas fa-pills me-2"></i>
                                        Medicamentos ({{ count($selectedSnapshotData['medication_requests']) }})
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group">
                                        @foreach($selectedSnapshotData['medication_requests'] as $med)
                                            <li class="list-group-item">
                                                <strong>{{ $med['medication'] ?? 'N/A' }}</strong><br>
                                                <small>
                                                    Dosis: {{ $med['dosage_text'] ?? 'N/A' }} |
                                                    Frecuencia: {{ $med['frequency'] ?? 'N/A' }} |
                                                    Cantidad: {{ $med['quantity'] ?? 'N/A' }}
                                                </small>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif

                        <!-- Enfermedad Actual / Present Illness -->
                        @if(!empty($selectedSnapshotData['present_illness']))
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0 text-white">
                                        <i class="fas fa-file-medical-alt me-2"></i>
                                        Enfermedad Actual
                                    </h6>
                                </div>
                                <div class="card-body">
                                    @if(!empty($selectedSnapshotData['present_illness']['description']))
                                        <div class="mb-2">
                                            <strong>Descripción:</strong>
                                            <p class="mb-1">{{ $selectedSnapshotData['present_illness']['description'] }}</p>
                                        </div>
                                    @endif
                                    <div class="row">
                                        @if(!empty($selectedSnapshotData['present_illness']['location']))
                                            <div class="col-md-3">
                                                <strong>Localización:</strong><br>
                                                <span class="badge bg-info">
                                                    {{ __('present_illness.' . $selectedSnapshotData['present_illness']['location']) }}
                                                </span>
                                            </div>
                                        @endif
                                        @if(!empty($selectedSnapshotData['present_illness']['severity']))
                                            <div class="col-md-3">
                                                <strong>Severidad:</strong><br>
                                                <span class="badge bg-{{ $selectedSnapshotData['present_illness']['severity'] === 'disabling' ? 'danger' : ($selectedSnapshotData['present_illness']['severity'] === 'severe' ? 'warning' : 'info') }}">
                                                    {{ __('present_illness.' . $selectedSnapshotData['present_illness']['severity']) }}
                                                </span>
                                            </div>
                                        @endif
                                        @if(!empty($selectedSnapshotData['present_illness']['duration']))
                                            <div class="col-md-3">
                                                <strong>Duración:</strong><br>
                                                {{ __('present_illness.' . $selectedSnapshotData['present_illness']['duration']) }}
                                            </div>
                                        @endif
                                        @if(!empty($selectedSnapshotData['present_illness']['timing']))
                                            <div class="col-md-3">
                                                <strong>Temporalidad:</strong><br>
                                                {{ __('present_illness.' . $selectedSnapshotData['present_illness']['timing']) }}
                                            </div>
                                        @endif
                                    </div>
                                    @if(!empty($selectedSnapshotData['present_illness']['aggravating_factors']))
                                        <div class="mt-2">
                                            <strong>Factores agravantes:</strong>
                                            <p class="mb-1">{{ $selectedSnapshotData['present_illness']['aggravating_factors'] }}</p>
                                        </div>
                                    @endif
                                    @if(!empty($selectedSnapshotData['present_illness']['alleviating_factors']))
                                        <div class="mt-2">
                                            <strong>Factores aliviantes:</strong>
                                            <p class="mb-1">{{ $selectedSnapshotData['present_illness']['alleviating_factors'] }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Exámenes Físicos -->
                        @if(!empty($selectedSnapshotData['physical_exams']) && count($selectedSnapshotData['physical_exams']) > 0)
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0 text-white">
                                        <i class="fas fa-stethoscope me-2"></i>
                                        Exámenes Físicos ({{ count($selectedSnapshotData['physical_exams']) }})
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group">
                                        @foreach($selectedSnapshotData['physical_exams'] as $exam)
                                            <li class="list-group-item">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <strong>{{ $exam['code'] ?? 'N/A' }}</strong>
                                                        @if(!empty($exam['category']))
                                                            <span class="badge bg-secondary ms-2">{{ $exam['category'] }}</span>
                                                        @endif
                                                        @if(!empty($exam['description']))
                                                            <p class="mb-1 mt-1">{{ $exam['description'] }}</p>
                                                        @endif
                                                        @if(!empty($exam['conclusion']))
                                                            <p class="mb-0 text-muted"><strong>Conclusión:</strong> {{ $exam['conclusion'] }}</p>
                                                        @endif
                                                    </div>
                                                    @if(!empty($exam['effective_date']))
                                                        <small class="text-muted">{{ \Carbon\Carbon::parse($exam['effective_date'])->format('d-m-Y H:i') }}</small>
                                                    @endif
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif

                        <!-- Solicitudes de Servicio / Service Requests -->
                        @if(!empty($selectedSnapshotData['service_requests']) && count($selectedSnapshotData['service_requests']) > 0)
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0 text-white">
                                        <i class="fas fa-file-prescription me-2"></i>
                                        Solicitudes de Servicio ({{ count($selectedSnapshotData['service_requests']) }})
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group">
                                        @foreach($selectedSnapshotData['service_requests'] as $service)
                                            <li class="list-group-item">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div class="flex-grow-1">
                                                        <strong>{{ $service['code_display'] ?? $service['code'] ?? 'Solicitud de Servicio' }}</strong>
                                                        @if(!empty($service['service_type']))
                                                            <span class="badge bg-primary ms-2">{{ $service['service_type'] }}</span>
                                                        @endif
                                                        @if(!empty($service['status']))
                                                            <span class="badge bg-{{ $service['status'] === 'completed' ? 'success' : ($service['status'] === 'active' ? 'info' : 'secondary') }} ms-1">
                                                                {{ ucfirst($service['status']) }}
                                                            </span>
                                                        @endif
                                                        @if(!empty($service['priority']))
                                                            <span class="badge bg-warning ms-1">{{ ucfirst($service['priority']) }}</span>
                                                        @endif
                                                        <br>
                                                        @if(!empty($service['note']))
                                                            <small class="text-muted">Nota: {{ $service['note'] }}</small>
                                                        @endif
                                                        @if(!empty($service['patient_instruction']))
                                                            <br><small><strong>Instrucciones:</strong> {{ $service['patient_instruction'] }}</small>
                                                        @endif
                                                        @if(!empty($service['quantity']))
                                                            <br><small><strong>Cantidad:</strong> {{ $service['quantity'] }}</small>
                                                        @endif

                                                        <!-- Resultados del servicio -->
                                                        @if(!empty($service['results']) && count($service['results']) > 0)
                                                            <div class="mt-2">
                                                                <strong class="text-success">Resultados ({{ count($service['results']) }}):</strong>
                                                                <ul class="list-unstyled ms-3 mt-1">
                                                                    @foreach($service['results'] as $result)
                                                                        <li class="mb-1">
                                                                            <i class="fas fa-check-circle text-success me-1"></i>
                                                                            {{ $result['code'] ?? $result['result_type'] ?? 'Resultado' }}
                                                                            @if(!empty($result['file_name']))
                                                                                <small class="text-muted">({{ $result['file_name'] }})</small>
                                                                            @endif
                                                                            @if(!empty($result['interpretation']))
                                                                                <span class="badge bg-secondary ms-1">{{ $result['interpretation'] }}</span>
                                                                            @endif
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif

                        <!-- Metadata del Snapshot -->
                        @if(!empty($selectedSnapshotData['snapshot_metadata']))
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Resumen del Snapshot
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <p class="mb-0">
                                                <strong>Diagnósticos:</strong><br>
                                                <span class="badge bg-info">{{ $selectedSnapshotData['snapshot_metadata']['total_diagnoses'] ?? 0 }}</span>
                                            </p>
                                        </div>
                                        <div class="col-md-3">
                                            <p class="mb-0">
                                                <strong>Signos Vitales:</strong><br>
                                                <span class="badge bg-info">{{ $selectedSnapshotData['snapshot_metadata']['total_vital_signs'] ?? 0 }}</span>
                                            </p>
                                        </div>
                                        <div class="col-md-3">
                                            <p class="mb-0">
                                                <strong>Medicamentos:</strong><br>
                                                <span class="badge bg-info">{{ $selectedSnapshotData['snapshot_metadata']['total_medications'] ?? 0 }}</span>
                                            </p>
                                        </div>
                                        <div class="col-md-3">
                                            <p class="mb-0">
                                                <strong>Solicitudes:</strong><br>
                                                <span class="badge bg-info">{{ $selectedSnapshotData['snapshot_metadata']['total_service_requests'] ?? 0 }}</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
                <div class="modal-footer" style="padding: 15px 20px; border-top: 1px solid #dee2e6; display: flex; justify-content: flex-end;">
                    <button type="button" class="btn btn-secondary" wire:click="closeModal">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
        @endteleport
    @endif
</div>
