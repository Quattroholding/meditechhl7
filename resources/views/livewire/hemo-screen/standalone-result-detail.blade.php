<div>
    {{-- HemoScreen Result Detail --}}

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-7">
                <h3 class="page-title">Detalle de Resultado HemoScreen</h3>

            </div>
            <div class="col-sm-5 text-end">
                <a href="{{ route('hemoscreen.dashboard') }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-arrow-left me-1"></i>Volver
                </a>
                <button wire:click="exportToPdf" class="btn btn-primary">
                    <i class="fas fa-file-pdf me-1"></i>Exportar PDF
                </button>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Test Information Card -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0 text-white">
                <i class="fas fa-info-circle me-2"></i>Información del Test
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="text-muted small">Fecha y Hora</label>
                        <div class="fw-bold">{{ $result->test_performed_at->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="text-muted small">Dispositivo</label>
                        <div class="fw-bold">
                            <span class="badge bg-info">
                                <i class="fas fa-microscope me-1"></i>
                                {{ $result->device_serial ?? 'N/A' }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="text-muted small">Panel</label>
                        <div class="fw-bold">{{ $result->panel_name }}</div>
                        <small class="text-muted">CPT {{ $result->panel_code }}</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="text-muted small">ID de Control</label>
                        <div class="fw-bold">{{ $result->message_control_id }}</div>
                    </div>
                </div>
            </div>

            @if($result->patient_identifier)
            <div class="row mt-2">
                <div class="col-md-12">
                    <div class="alert alert-light border mb-0">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <i class="fas fa-user-tag fa-2x text-primary"></i>
                            </div>
                            <div class="col">
                                <label class="text-muted small mb-1">Identificador de Paciente</label>
                                <div class="fw-bold fs-5">{{ $result->patient_identifier }}</div>
                                <small class="text-muted">Identificador enviado por el gateway HemoScreen</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if($result->hasAbnormalValues())
                <div class="alert alert-warning mb-0 mt-3">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Atención:</strong> Este resultado contiene valores fuera del rango de referencia normal.
                    Se recomienda consultar con un profesional de la salud.
                </div>
            @else
                <div class="alert alert-success mb-0 mt-3">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>Resultado Normal:</strong> Todos los valores están dentro del rango de referencia.
                </div>
            @endif
        </div>
    </div>
    <!-- /Test Information Card -->

    <!-- Results by Category -->
    @foreach($groupedObservations as $groupName => $observations)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ $groupName }}</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="35%">Parámetro</th>
                                <th width="15%" class="text-center">Valor</th>
                                <th width="15%" class="text-center">Unidad</th>
                                <th width="20%" class="text-center">Rango de Referencia</th>
                                <th width="15%" class="text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($observations as $obs)
                                <tr class="@if($obs['isAbnormal']) table-warning @endif">
                                    <td>
                                        <strong>{{ $obs['name'] }}</strong>
                                        <br>
                                        <small class="text-muted">LOINC: {{ $obs['code'] }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="fs-5 fw-bold
                                            @if($obs['status'] === 'low') text-primary
                                            @elseif($obs['status'] === 'high') text-danger
                                            @else text-success
                                            @endif">
                                            {{ number_format($obs['value'], 2) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="text-muted">{{ $obs['unit'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($obs['reference'])
                                            <span class="badge bg-light text-dark">
                                                {{ number_format($obs['reference']['min'], 2) }}
                                                -
                                                {{ number_format($obs['reference']['max'], 2) }}
                                                {{ $obs['reference']['unit'] }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($obs['status'] === 'normal')
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle me-1"></i>Normal
                                            </span>
                                        @elseif($obs['status'] === 'low')
                                            <span class="badge bg-primary">
                                                <i class="fas fa-arrow-down me-1"></i>Bajo
                                            </span>
                                        @elseif($obs['status'] === 'high')
                                            <span class="badge bg-danger">
                                                <i class="fas fa-arrow-up me-1"></i>Alto
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endforeach
    <!-- /Results by Category -->

    <!-- Footer Actions -->
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p class="text-muted mb-0">
                        <i class="fas fa-info-circle me-1"></i>
                        Los rangos de referencia pueden variar según el laboratorio y las características del paciente.
                    </p>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('hemoscreen.dashboard') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left me-1"></i>Volver al Dashboard
                    </a>
                    <button wire:click="exportToPdf" class="btn btn-primary">
                        <i class="fas fa-file-pdf me-1"></i>Exportar a PDF
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- /Footer Actions -->

    <!-- Legend Card -->
    <div class="card mt-3">
        <div class="card-body">
            <h6 class="mb-3">Leyenda de Estados:</h6>
            <div class="row">
                <div class="col-md-4">
                    <span class="badge bg-success me-2">
                        <i class="fas fa-check-circle"></i> Normal
                    </span>
                    <span class="text-muted">Dentro del rango de referencia</span>
                </div>
                <div class="col-md-4">
                    <span class="badge bg-primary me-2">
                        <i class="fas fa-arrow-down"></i> Bajo
                    </span>
                    <span class="text-muted">Por debajo del rango mínimo</span>
                </div>
                <div class="col-md-4">
                    <span class="badge bg-danger me-2">
                        <i class="fas fa-arrow-up"></i> Alto
                    </span>
                    <span class="text-muted">Por encima del rango máximo</span>
                </div>
            </div>
        </div>
    </div>
    <!-- /Legend Card -->
</div>
