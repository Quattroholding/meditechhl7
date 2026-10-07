<div class="modal fade" id="periodLockModal" tabindex="-1" role="dialog" aria-labelledby="periodLockModalLabel"
    aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="periodLockModalLabel">
                    <i class="fas fa-lock"></i> Bloquear Período Contable
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            @if ($period)
                <div class="modal-body">
                    <div class="alert alert-danger mb-3">
                        <i class="fas fa-lock"></i>
                        <strong>Acción Permanente:</strong> No se podrá desbloquear el período después de esta acción.
                    </div>

                    <p>
                        ¿Deseas bloquear permanentemente el período <strong>{{ $period->name }}</strong>?
                    </p>

                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <p class="mb-1">
                                <strong>Período:</strong> {{ $period->name }}
                            </p>
                            <p class="mb-1">
                                <strong>Rango:</strong>
                                {{ $period->start_date->format('d/m/Y') }} -
                                {{ $period->end_date->format('d/m/Y') }}
                            </p>
                            <p class="mb-1">
                                <strong>Estado Actual:</strong>
                                <span class="badge bg-warning">Cerrado</span>
                            </p>
                            <p class="mb-0">
                                <strong>Asientos:</strong>
                                {{ $period->journalEntries()->count() }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="button" wire:click="lock" class="btn btn-danger">
                        <i class="fas fa-lock"></i> Bloquear Período
                    </button>
                </div>
            @else
                <div class="modal-body">
                    <p class="text-muted">Selecciona un período para bloquear</p>
                </div>
            @endif
        </div>
    </div>
</div>
