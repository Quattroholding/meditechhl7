<div>
    @if($showModal && $period)
    <div class="modal-overlay" wire:click="closeModal" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 10000; display: flex; align-items: center; justify-content: center;">
        <div class="modal-content" wire:click.stop style="position: relative; max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto; background: white; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
            <div class="modal-header" style="padding: 1.5rem; border-bottom: 1px solid #e9ecef; display: flex; justify-content: space-between; align-items: center;">
                <h5 class="modal-title" style="margin: 0;">
                    <i class="fas fa-times-circle"></i> Cerrar Período Contable
                </h5>
                <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
            </div>

            <div class="modal-body" style="padding: 1.5rem;">
                <div class="alert alert-warning mb-4">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Advertencia:</strong> Una vez cerrado un período, no se podrán registrar nuevas transacciones en el mismo.
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label" style="font-weight: 600;">Período</label>
                        <p class="text-muted">{{ $period->name }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" style="font-weight: 600;">Año Fiscal</label>
                        <p class="text-muted">{{ $period->fiscal_year }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label" style="font-weight: 600;">Fecha de Inicio</label>
                        <p class="text-muted">{{ $period->start_date->format('d/m/Y') }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" style="font-weight: 600;">Fecha de Fin</label>
                        <p class="text-muted">{{ $period->end_date->format('d/m/Y') }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label" style="font-weight: 600;">Asientos Contables</label>
                        <p class="text-muted">{{ $period->journalEntries()->count() }} asientos registrados</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <label class="form-label" style="font-weight: 600;">Estado Actual</label>
                        @if ($period->status === 'open')
                            <span class="badge bg-success">
                                <i class="fas fa-check-circle"></i> Abierto
                            </span>
                        @else
                            <span class="badge bg-warning">
                                <i class="fas fa-lock-open"></i> Cerrado
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="modal-footer" style="padding: 1.5rem; border-top: 1px solid #e9ecef; display: flex; gap: 0.5rem; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" wire:click="closeModal">
                    Cancelar
                </button>
                <button type="button" class="btn btn-warning" wire:click="close">
                    <i class="fas fa-times-circle"></i> Cerrar Período
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
