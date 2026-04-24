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
                        <!-- Estilos para resaltar cambios -->
                        <style>
                            .change-added {
                                background-color: #d4edda !important;
                                border-left: 4px solid #28a745 !important;
                            }
                            .change-removed {
                                background-color: #f8d7da !important;
                                border-left: 4px solid #dc3545 !important;
                                opacity: 0.7;
                                text-decoration: line-through;
                            }
                            .change-modified {
                                background-color: #fff3cd !important;
                                border-left: 4px solid #ffc107 !important;
                            }
                            .change-badge-added {
                                background-color: #28a745 !important;
                                color: white;
                                font-size: 0.75rem;
                                padding: 2px 6px;
                                border-radius: 3px;
                                margin-left: 8px;
                            }
                            .change-badge-removed {
                                background-color: #dc3545 !important;
                                color: white;
                                font-size: 0.75rem;
                                padding: 2px 6px;
                                border-radius: 3px;
                                margin-left: 8px;
                            }
                            .change-badge-modified {
                                background-color: #ffc107 !important;
                                color: #212529;
                                font-size: 0.75rem;
                                padding: 2px 6px;
                                border-radius: 3px;
                                margin-left: 8px;
                            }
                            .field-change {
                                font-size: 0.85rem;
                                margin-top: 4px;
                                padding: 4px 8px;
                                background-color: #f8f9fa;
                                border-radius: 4px;
                            }
                            .field-change .old-value {
                                text-decoration: line-through;
                                color: #dc3545;
                            }
                            .field-change .new-value {
                                color: #28a745;
                                font-weight: bold;
                            }
                        </style>

                        <!-- Leyenda de cambios -->
                        @if($snapshotChanges && !$snapshotChanges['is_first_version'])
                            <div class="alert alert-info mb-3">
                                <h6 class="mb-2"><i class="fas fa-info-circle me-2"></i>Comparación con versión anterior (v{{ $selectedSnapshot->version - 1 }})</h6>
                                <div class="d-flex gap-3 flex-wrap">
                                    <span><span class="change-badge-added">✨ NUEVO</span> Elemento agregado</span>
                                    <span><span class="change-badge-modified">🔄 MODIFICADO</span> Valor cambiado</span>
                                    <span><span class="change-badge-removed">❌ ELIMINADO</span> Elemento removido</span>
                                </div>
                            </div>
                        @endif

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
                                        @php
                                            $diagnosesChanges = $snapshotChanges['diagnoses'] ?? null;
                                            $addedIds = $diagnosesChanges ? collect($diagnosesChanges['added'])->pluck('id')->toArray() : [];
                                            $modifiedIds = $diagnosesChanges ? collect($diagnosesChanges['modified'])->pluck('id')->toArray() : [];
                                        @endphp

                                        @foreach($selectedSnapshotData['diagnoses'] as $diagnosis)
                                            @php
                                                $changeClass = '';
                                                $changeBadge = '';
                                                $isAdded = in_array($diagnosis['id'], $addedIds);
                                                $isModified = in_array($diagnosis['id'], $modifiedIds);
                                                $modifiedData = null;

                                                if ($isAdded) {
                                                    $changeClass = 'change-added';
                                                    $changeBadge = '<span class="change-badge-added">✨ NUEVO</span>';
                                                } elseif ($isModified) {
                                                    $changeClass = 'change-modified';
                                                    $changeBadge = '<span class="change-badge-modified">🔄 MODIFICADO</span>';
                                                    $modifiedData = collect($diagnosesChanges['modified'])->firstWhere('id', $diagnosis['id']);
                                                }
                                            @endphp
                                            <li class="list-group-item {{ $changeClass }}">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <strong>{{ $diagnosis['condition']['code'] ?? 'N/A' }}</strong>
                                                        @if(!empty($diagnosis['condition']['description_es']))
                                                            - {{ $diagnosis['condition']['description_es'] }}
                                                        @endif
                                                        {!! $changeBadge !!}
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

                                                        @if($modifiedData && !empty($modifiedData['changed_fields']))
                                                            <div class="mt-2">
                                                                <small class="text-muted"><strong>Campos modificados:</strong></small>
                                                                @foreach($modifiedData['changed_fields'] as $field => $values)
                                                                    <div class="field-change">
                                                                        <strong>{{ ucfirst(str_replace('_', ' ', $field)) }}:</strong>
                                                                        <span class="old-value">{{ is_array($values['old']) ? json_encode($values['old']) : ($values['old'] ?? 'N/A') }}</span>
                                                                        →
                                                                        <span class="new-value">{{ is_array($values['new']) ? json_encode($values['new']) : ($values['new'] ?? 'N/A') }}</span>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                    @if(!empty($diagnosis['condition']['onset_date']))
                                                        <small class="text-muted">{{ \Carbon\Carbon::parse($diagnosis['condition']['onset_date'])->format('d-m-Y H:i') }}</small>
                                                    @endif
                                                </div>
                                            </li>
                                        @endforeach

                                        <!-- Show removed diagnoses -->
                                        @if($diagnosesChanges && !empty($diagnosesChanges['removed']))
                                            @foreach($diagnosesChanges['removed'] as $removed)
                                                <li class="list-group-item change-removed">
                                                    <div>
                                                        <strong>{{ $removed['condition']['code'] ?? 'N/A' }}</strong>
                                                        @if(!empty($removed['condition']['description_es']))
                                                            - {{ $removed['condition']['description_es'] }}
                                                        @endif
                                                        <span class="change-badge-removed">❌ ELIMINADO</span>
                                                    </div>
                                                </li>
                                            @endforeach
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        @elseif($snapshotChanges && !empty($snapshotChanges['diagnoses']['removed']))
                            <!-- Show only removed diagnoses if current list is empty -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0  text-white">
                                        <i class="fas fa-diagnoses me-2"></i>
                                        Diagnósticos (Eliminados)
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group">
                                        @foreach($snapshotChanges['diagnoses']['removed'] as $removed)
                                            <li class="list-group-item change-removed">
                                                <div>
                                                    <strong>{{ $removed['condition']['code'] ?? 'N/A' }}</strong>
                                                    @if(!empty($removed['condition']['description_es']))
                                                        - {{ $removed['condition']['description_es'] }}
                                                    @endif
                                                    <span class="change-badge-removed">❌ ELIMINADO</span>
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
                                    @php
                                        $vitalChanges = $snapshotChanges['vital_signs'] ?? null;
                                        $addedVitalIds = $vitalChanges ? collect($vitalChanges['added'])->pluck('id')->toArray() : [];
                                        $modifiedVitalIds = $vitalChanges ? collect($vitalChanges['modified'])->pluck('id')->toArray() : [];
                                    @endphp
                                    <div class="row">
                                        @foreach($selectedSnapshotData['vital_signs'] as $vital)
                                            @php
                                                $changeClass = '';
                                                $changeBadge = '';
                                                $isAdded = in_array($vital['id'], $addedVitalIds);
                                                $isModified = in_array($vital['id'], $modifiedVitalIds);
                                                $modifiedData = null;

                                                if ($isAdded) {
                                                    $changeClass = 'change-added';
                                                    $changeBadge = '<span class="change-badge-added">✨</span>';
                                                } elseif ($isModified) {
                                                    $changeClass = 'change-modified';
                                                    $changeBadge = '<span class="change-badge-modified">🔄</span>';
                                                    $modifiedData = collect($vitalChanges['modified'])->firstWhere('id', $vital['id']);
                                                }
                                            @endphp
                                            <div class="col-md-4 mb-2">
                                                <div class="border p-2 rounded {{ $changeClass }}">
                                                    <strong>
                                                        {{ $vital['name'] ?? $vital['code'] ?? 'N/A' }}
                                                    </strong>
                                                    {!! $changeBadge !!}
                                                    @if(!empty($vital['name']) && !empty($vital['code']))
                                                        <br><small class="text-muted">{{ $vital['code'] }}</small>
                                                    @endif
                                                    <br>
                                                    <span class="text-primary fw-bold">{{ $vital['value'] ?? 'N/A' }} {{ $vital['unit'] ?? '' }}</span>

                                                    @if($modifiedData && !empty($modifiedData['changed_fields']))
                                                        @if(isset($modifiedData['changed_fields']['value']))
                                                            <div class="mt-1">
                                                                <small class="old-value">{{ $modifiedData['changed_fields']['value']['old'] ?? 'N/A' }}</small>
                                                                <small> → </small>
                                                                <small class="new-value">{{ $modifiedData['changed_fields']['value']['new'] ?? 'N/A' }}</small>
                                                            </div>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach

                                        <!-- Show removed vital signs -->
                                        @if($vitalChanges && !empty($vitalChanges['removed']))
                                            @foreach($vitalChanges['removed'] as $removed)
                                                <div class="col-md-4 mb-2">
                                                    <div class="border p-2 rounded change-removed">
                                                        <strong>{{ $removed['name'] ?? $removed['code'] ?? 'N/A' }}</strong>
                                                        <span class="change-badge-removed">❌</span>
                                                        <br>
                                                        <span class="text-primary fw-bold">{{ $removed['value'] ?? 'N/A' }} {{ $removed['unit'] ?? '' }}</span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
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
                                    @php
                                        $medChanges = $snapshotChanges['medication_requests'] ?? null;
                                        $addedMedIds = $medChanges ? collect($medChanges['added'])->pluck('id')->toArray() : [];
                                        $modifiedMedIds = $medChanges ? collect($medChanges['modified'])->pluck('id')->toArray() : [];
                                    @endphp
                                    <ul class="list-group">
                                        @foreach($selectedSnapshotData['medication_requests'] as $med)
                                            @php
                                                $changeClass = '';
                                                $changeBadge = '';
                                                $isAdded = in_array($med['id'], $addedMedIds);
                                                $isModified = in_array($med['id'], $modifiedMedIds);
                                                $modifiedData = null;

                                                if ($isAdded) {
                                                    $changeClass = 'change-added';
                                                    $changeBadge = '<span class="change-badge-added">✨ NUEVO</span>';
                                                } elseif ($isModified) {
                                                    $changeClass = 'change-modified';
                                                    $changeBadge = '<span class="change-badge-modified">🔄 MODIFICADO</span>';
                                                    $modifiedData = collect($medChanges['modified'])->firstWhere('id', $med['id']);
                                                }
                                            @endphp
                                            <li class="list-group-item {{ $changeClass }}">
                                                <strong>{{ $med['medication'] ?? 'N/A' }}</strong>
                                                {!! $changeBadge !!}
                                                <br>
                                                <small>
                                                    Dosis: {{ $med['dosage_text'] ?? 'N/A' }} |
                                                    Frecuencia: {{ $med['frequency'] ?? 'N/A' }} |
                                                    Cantidad: {{ $med['quantity'] ?? 'N/A' }}
                                                </small>

                                                @if($modifiedData && !empty($modifiedData['changed_fields']))
                                                    <div class="mt-2">
                                                        <small class="text-muted"><strong>Campos modificados:</strong></small>
                                                        @foreach($modifiedData['changed_fields'] as $field => $values)
                                                            <div class="field-change">
                                                                <strong>{{ ucfirst(str_replace('_', ' ', $field)) }}:</strong>
                                                                <span class="old-value">{{ $values['old'] ?? 'N/A' }}</span>
                                                                →
                                                                <span class="new-value">{{ $values['new'] ?? 'N/A' }}</span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </li>
                                        @endforeach

                                        <!-- Show removed medications -->
                                        @if($medChanges && !empty($medChanges['removed']))
                                            @foreach($medChanges['removed'] as $removed)
                                                <li class="list-group-item change-removed">
                                                    <strong>{{ $removed['medication'] ?? 'N/A' }}</strong>
                                                    <span class="change-badge-removed">❌ ELIMINADO</span>
                                                </li>
                                            @endforeach
                                        @endif
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
                                    @php
                                        $examChanges = $snapshotChanges['physical_exams'] ?? null;
                                        $addedExamIds = $examChanges ? collect($examChanges['added'])->pluck('id')->toArray() : [];
                                        $modifiedExamIds = $examChanges ? collect($examChanges['modified'])->pluck('id')->toArray() : [];
                                    @endphp
                                    <ul class="list-group">
                                        @foreach($selectedSnapshotData['physical_exams'] as $exam)
                                            @php
                                                $changeClass = '';
                                                $changeBadge = '';
                                                $isAdded = in_array($exam['id'], $addedExamIds);
                                                $isModified = in_array($exam['id'], $modifiedExamIds);

                                                if ($isAdded) {
                                                    $changeClass = 'change-added';
                                                    $changeBadge = '<span class="change-badge-added">✨ NUEVO</span>';
                                                } elseif ($isModified) {
                                                    $changeClass = 'change-modified';
                                                    $changeBadge = '<span class="change-badge-modified">🔄 MODIFICADO</span>';
                                                }
                                            @endphp
                                            <li class="list-group-item {{ $changeClass }}">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <strong>{{ $exam['code'] ?? 'N/A' }}</strong>
                                                        {!! $changeBadge !!}
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

                                        @if($examChanges && !empty($examChanges['removed']))
                                            @foreach($examChanges['removed'] as $removed)
                                                <li class="list-group-item change-removed">
                                                    <strong>{{ $removed['code'] ?? 'N/A' }}</strong>
                                                    <span class="change-badge-removed">❌ ELIMINADO</span>
                                                </li>
                                            @endforeach
                                        @endif
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
                                    @php
                                        $serviceChanges = $snapshotChanges['service_requests'] ?? null;
                                        $addedServiceIds = $serviceChanges ? collect($serviceChanges['added'])->pluck('id')->toArray() : [];
                                        $modifiedServiceIds = $serviceChanges ? collect($serviceChanges['modified'])->pluck('id')->toArray() : [];
                                    @endphp
                                    <ul class="list-group">
                                        @foreach($selectedSnapshotData['service_requests'] as $service)
                                            @php
                                                $changeClass = '';
                                                $changeBadge = '';
                                                $isAdded = in_array($service['id'], $addedServiceIds);
                                                $isModified = in_array($service['id'], $modifiedServiceIds);
                                                $modifiedData = null;

                                                if ($isAdded) {
                                                    $changeClass = 'change-added';
                                                    $changeBadge = '<span class="change-badge-added">✨ NUEVO</span>';
                                                } elseif ($isModified) {
                                                    $changeClass = 'change-modified';
                                                    $changeBadge = '<span class="change-badge-modified">🔄 MODIFICADO</span>';
                                                    $modifiedData = collect($serviceChanges['modified'])->firstWhere('id', $service['id']);
                                                }
                                            @endphp
                                            <li class="list-group-item {{ $changeClass }}">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div class="flex-grow-1">
                                                        <strong>{{ $service['code_display'] ?? $service['code'] ?? 'Solicitud de Servicio' }}</strong>
                                                        {!! $changeBadge !!}
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

                                                        @if($modifiedData && !empty($modifiedData['changed_fields']))
                                                            <div class="mt-2">
                                                                <small class="text-muted"><strong>Campos modificados:</strong></small>
                                                                @foreach($modifiedData['changed_fields'] as $field => $values)
                                                                    <div class="field-change">
                                                                        <strong>{{ ucfirst(str_replace('_', ' ', $field)) }}:</strong>
                                                                        <span class="old-value">{{ is_array($values['old']) ? json_encode($values['old']) : ($values['old'] ?? 'N/A') }}</span>
                                                                        →
                                                                        <span class="new-value">{{ is_array($values['new']) ? json_encode($values['new']) : ($values['new'] ?? 'N/A') }}</span>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach

                                        <!-- Show removed service requests -->
                                        @if($serviceChanges && !empty($serviceChanges['removed']))
                                            @foreach($serviceChanges['removed'] as $removed)
                                                <li class="list-group-item change-removed">
                                                    <strong>{{ $removed['code_display'] ?? $removed['code'] ?? 'Solicitud de Servicio' }}</strong>
                                                    <span class="change-badge-removed">❌ ELIMINADO</span>
                                                    @if(!empty($removed['service_type']))
                                                        <br><small>Tipo: {{ $removed['service_type'] }}</small>
                                                    @endif
                                                </li>
                                            @endforeach
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        @elseif($snapshotChanges && !empty($snapshotChanges['service_requests']['removed']))
                            <!-- Show only removed service requests if current list is empty -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0 text-white">
                                        <i class="fas fa-file-prescription me-2"></i>
                                        Solicitudes de Servicio (Eliminadas)
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group">
                                        @foreach($snapshotChanges['service_requests']['removed'] as $removed)
                                            <li class="list-group-item change-removed">
                                                <strong>{{ $removed['code_display'] ?? $removed['code'] ?? 'Solicitud de Servicio' }}</strong>
                                                <span class="change-badge-removed">❌ ELIMINADO</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif

                        <!-- Referencias a Especialista -->
                        @if(!empty($selectedSnapshotData['referrals']) && count($selectedSnapshotData['referrals']) > 0)
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0 text-white">
                                        <i class="fas fa-user-md me-2"></i>
                                        Referencias a Especialista ({{ count($selectedSnapshotData['referrals']) }})
                                    </h6>
                                </div>
                                <div class="card-body">
                                    @php
                                        $referralChanges = $snapshotChanges['referrals'] ?? null;
                                        $addedRefIds = $referralChanges ? collect($referralChanges['added'])->pluck('id')->toArray() : [];
                                        $modifiedRefIds = $referralChanges ? collect($referralChanges['modified'])->pluck('id')->toArray() : [];
                                    @endphp
                                    <ul class="list-group">
                                        @foreach($selectedSnapshotData['referrals'] as $referral)
                                            @php
                                                // Determinar si es especialista interno o externo
                                                $isExternal = !empty($referral['external_specialist_name']);

                                                if ($isExternal) {
                                                    $specialistName = $referral['external_specialist_name'];
                                                    $speciality = $referral['external_specialist_specialty'] ?? 'Especialista';
                                                    $license = $referral['external_specialist_license'] ?? null;
                                                    $phone = $referral['external_specialist_phone'] ?? null;
                                                    $clinic = $referral['external_specialist_clinic'] ?? null;
                                                } else {
                                                    $specialistName = $referral['referred_to_practitioner']['name'] ?? null;
                                                    $speciality = $referral['speciality']['name'] ?? 'Especialista';
                                                    $license = $referral['referred_to_practitioner']['licence_code'] ?? null;
                                                    $phone = $referral['referred_to_practitioner']['phone'] ?? null;
                                                    $clinic = null;
                                                }

                                                // Check if this referral has changes
                                                $changeClass = '';
                                                $changeBadge = '';
                                                $isAdded = in_array($referral['id'], $addedRefIds);
                                                $isModified = in_array($referral['id'], $modifiedRefIds);
                                                $modifiedData = null;

                                                if ($isAdded) {
                                                    $changeClass = 'change-added';
                                                    $changeBadge = '<span class="change-badge-added">✨ NUEVO</span>';
                                                } elseif ($isModified) {
                                                    $changeClass = 'change-modified';
                                                    $changeBadge = '<span class="change-badge-modified">🔄 MODIFICADO</span>';
                                                    $modifiedData = collect($referralChanges['modified'])->firstWhere('id', $referral['id']);
                                                }
                                            @endphp
                                            <li class="list-group-item {{ $changeClass }}">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div class="flex-grow-1">
                                                        <strong>{{ $referral['description'] ?? $speciality }}</strong>
                                                        {!! $changeBadge !!}
                                                        @if($isExternal)
                                                            <span class="badge bg-warning ms-2">
                                                                <i class="fas fa-external-link-alt me-1"></i>
                                                                Externo
                                                            </span>
                                                        @else
                                                            <span class="badge bg-success ms-2">
                                                                <i class="fas fa-hospital me-1"></i>
                                                                Interno
                                                            </span>
                                                        @endif
                                                        @if(!empty($referral['status']))
                                                            <span class="badge bg-{{ $referral['status'] === 'completed' ? 'success' : ($referral['status'] === 'active' ? 'info' : 'secondary') }} ms-1">
                                                                {{ ucfirst($referral['status']) }}
                                                            </span>
                                                        @endif
                                                        @if(!empty($referral['priority']))
                                                            <span class="badge bg-{{ $referral['priority'] === 'urgent' ? 'danger' : 'primary' }} ms-1">
                                                                {{ ucfirst($referral['priority']) }}
                                                            </span>
                                                        @endif
                                                        <br>

                                                        @if($specialistName)
                                                            <div class="mt-2">
                                                                <small>
                                                                    <strong>Médico Referido:</strong> {{ $specialistName }}
                                                                    @if($license)
                                                                        <br><strong>Licencia:</strong> {{ $license }}
                                                                    @endif
                                                                </small>
                                                            </div>
                                                        @endif

                                                        @if($clinic)
                                                            <div class="mt-1">
                                                                <small><strong>Clínica/Consultorio:</strong> {{ $clinic }}</small>
                                                            </div>
                                                        @endif

                                                        @if($phone)
                                                            <div class="mt-1">
                                                                <small><strong>Teléfono:</strong> {{ $phone }}</small>
                                                            </div>
                                                        @endif

                                                        @if(!empty($referral['reason']))
                                                            <div class="mt-2">
                                                                <small>
                                                                    <strong>Motivo de Referencia:</strong><br>
                                                                    <span class="text-muted">{{ $referral['reason'] }}</span>
                                                                </small>
                                                            </div>
                                                        @endif

                                                        @if(!empty($referral['occurrence_date']))
                                                            <div class="mt-1">
                                                                <small>
                                                                    <strong>Fecha Sugerida:</strong>
                                                                    {{ \Carbon\Carbon::parse($referral['occurrence_date'])->format('d/m/Y') }}
                                                                </small>
                                                            </div>
                                                        @endif

                                                        @if($modifiedData && !empty($modifiedData['changed_fields']))
                                                            <div class="mt-2">
                                                                <small class="text-muted"><strong>Campos modificados:</strong></small>
                                                                @foreach($modifiedData['changed_fields'] as $field => $values)
                                                                    <div class="field-change">
                                                                        <strong>{{ ucfirst(str_replace('_', ' ', $field)) }}:</strong>
                                                                        <span class="old-value">{{ is_array($values['old']) ? json_encode($values['old']) : ($values['old'] ?? 'N/A') }}</span>
                                                                        →
                                                                        <span class="new-value">{{ is_array($values['new']) ? json_encode($values['new']) : ($values['new'] ?? 'N/A') }}</span>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach

                                        <!-- Show removed referrals -->
                                        @if($referralChanges && !empty($referralChanges['removed']))
                                            @foreach($referralChanges['removed'] as $removed)
                                                @php
                                                    $isExternalRemoved = !empty($removed['external_specialist_name']);
                                                    $removedName = $isExternalRemoved
                                                        ? $removed['external_specialist_name']
                                                        : ($removed['referred_to_practitioner']['name'] ?? 'N/A');
                                                @endphp
                                                <li class="list-group-item change-removed">
                                                    <strong>{{ $removed['description'] ?? 'Referencia' }}</strong>
                                                    <span class="change-badge-removed">❌ ELIMINADO</span>
                                                    <br>
                                                    <small>Médico: {{ $removedName }}</small>
                                                </li>
                                            @endforeach
                                        @endif
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
