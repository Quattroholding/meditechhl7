<div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title d-flex align-items-center">
                        <i class="fas fa-tasks me-2"></i>
                        Configuración de Lista de Espera
                    </div>
                </div>
                <div class="card-body">
                    <!-- Info Alert -->
                    <div class="alert alert-info mb-4">
                        <div class="d-flex align-items-start">
                            <div class="me-3">
                                <i class="fas fa-info-circle fa-lg mt-1"></i>
                            </div>
                            <div>
                                <h6 class="alert-heading">Modo de Asignación</h6>
                                <p class="mb-0">
                                    Elige cómo se asignarán automáticamente los espacios liberados en la agenda cuando se cancele o no se asista a una cita.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Settings Options -->
                    <div class="row">
                        <div class="col-md-6">
                            <!-- Manual Assignment Option -->
                            <div class="border rounded p-4 mb-3" style="background-color: {{ !$autoAssignEnabled ? '#f0f8ff' : '#fff' }}; border: 2px solid {{ !$autoAssignEnabled ? '#0066cc' : '#e0e0e0' }};">
                                <div class="d-flex align-items-start mb-3">
                                    <div class="form-check me-3 mt-1">
                                        <input
                                            class="form-check-input"
                                            type="radio"
                                            name="assignment-mode"
                                            id="manual-mode"
                                            wire:click="$set('autoAssignEnabled', false)"
                                            {{ !$autoAssignEnabled ? 'checked' : '' }}
                                        >
                                    </div>
                                    <label for="manual-mode" class="form-check-label cursor-pointer flex-grow-1">
                                        <h6 class="mb-1 fw-bold">Asignación Manual</h6>
                                        <small class="text-muted">
                                            La recepcionista decide a quién asignar el espacio
                                        </small>
                                    </label>
                                </div>
                                <div class="ps-4">
                                    <p class="mb-2 text-muted" style="font-size: 0.9rem;">
                                        <i class="fas fa-check-circle text-success me-2"></i>Sistema sugiere candidatos por score
                                    </p>
                                    <p class="mb-2 text-muted" style="font-size: 0.9rem;">
                                        <i class="fas fa-check-circle text-success me-2"></i>La recepcionista elige quién asignar
                                    </p>
                                    <p class="mb-0 text-muted" style="font-size: 0.9rem;">
                                        <i class="fas fa-check-circle text-success me-2"></i>Control total sobre el proceso
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <!-- Automatic Assignment Option -->
                            <div class="border rounded p-4 mb-3" style="background-color: {{ $autoAssignEnabled ? '#f0f8ff' : '#fff' }}; border: 2px solid {{ $autoAssignEnabled ? '#0066cc' : '#e0e0e0' }};">
                                <div class="d-flex align-items-start mb-3">
                                    <div class="form-check me-3 mt-1">
                                        <input
                                            class="form-check-input"
                                            type="radio"
                                            name="assignment-mode"
                                            id="auto-mode"
                                            wire:click="$set('autoAssignEnabled', true)"
                                            {{ $autoAssignEnabled ? 'checked' : '' }}
                                        >
                                    </div>
                                    <label for="auto-mode" class="form-check-label cursor-pointer flex-grow-1">
                                        <h6 class="mb-1 fw-bold">Asignación Automática</h6>
                                        <small class="text-muted">
                                            Sistema asigna automáticamente al mejor candidato
                                        </small>
                                    </label>
                                </div>
                                <div class="ps-4">
                                    <p class="mb-2 text-muted" style="font-size: 0.9rem;">
                                        <i class="fas fa-check-circle text-success me-2"></i>Asignación automática basada en score
                                    </p>
                                    <p class="mb-2 text-muted" style="font-size: 0.9rem;">
                                        <i class="fas fa-check-circle text-success me-2"></i>Notificación inmediata al paciente
                                    </p>
                                    <p class="mb-0 text-muted" style="font-size: 0.9rem;">
                                        <i class="fas fa-check-circle text-success me-2"></i>Proceso más rápido
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Current Status -->
                    <div class="alert alert-warning mt-4 mb-0">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-lightbulb me-3 mt-1"></i>
                            <div>
                                <strong>Estado actual:</strong>
                                @if($autoAssignEnabled)
                                    <span class="badge bg-success">Asignación Automática Habilitada</span>
                                    <p class="mb-0 mt-2 text-muted small">
                                        Los espacios liberados se asignarán automáticamente al paciente con el mejor score de coincidencia.
                                    </p>
                                @else
                                    <span class="badge bg-info">Asignación Manual Habilitada</span>
                                    <p class="mb-0 mt-2 text-muted small">
                                        Los espacios liberados se mostrarán a la recepcionista con candidatos sugeridos por score.
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
