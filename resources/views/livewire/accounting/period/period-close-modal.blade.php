<div class="modal fade" id="periodCloseModal" tabindex="-1" role="dialog" aria-labelledby="periodCloseModalLabel"
    aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="periodCloseModalLabel">
                    <i class="fas fa-lock-open"></i> Cerrar Período Contable
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            @if ($period)
                <div class="modal-body">
                    <div class="alert alert-warning mb-3">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Advertencia:</strong> Una vez cerrado un período, no se podrán modificar los asientos
                        contables.
                    </div>

                    <p>
                        ¿Deseas cerrar el período <strong>{{ $period->name }}</strong>?
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
                    <button type="button" wire:click="close" class="btn btn-warning">
                        <i class="fas fa-lock-open"></i> Cerrar Período
                    </button>
                </div>
            @else
                <div class="modal-body">
                    <p class="text-muted">Selecciona un período para cerrar</p>
                </div>
            @endif
        </div>
    </div>
</div>
