@props([
    'show' => false,
    'cancellationReason' => '',
    'customCancellationReason' => '',
])

@if($show)
        <div class="modal-overlay" wire:click="closeCancelModal" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 10000; display: flex; align-items: center; justify-content: center;">
            <div class="modal-content" wire:click.stop>
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle"></i>
                        Cancelar Cita
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeCancelModal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger" role="alert">
                        <strong>⚠️ Atención:</strong> Se enviará una notificación al <br/>paciente informando la cancelación.
                    </div>

                    <!-- Razón de Cancelación -->
                    <div class="mb-3">
                        <label for="cancellationReason" class="form-label">
                            Razón de Cancelación <span class="text-danger">*</span>
                        </label>
                        <select wire:model.live="cancellationReason" id="cancellationReason" class="form-select @error('cancellationReason') is-invalid @enderror">
                            <option value="">Seleccione una razón...</option>
                            @foreach(\App\Enums\AppointmentCancelledReason::toArray() as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('cancellationReason')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Campo de Texto para "Otra razón" -->
                    @if($cancellationReason === 'OTHER')
                        <div class="mb-3">
                            <label for="customCancellationReason" class="form-label">
                                Especifique la razón <span class="text-danger">*</span>
                            </label>
                            <textarea
                                wire:model="customCancellationReason"
                                id="customCancellationReason"
                                class="form-control @error('customCancellationReason') is-invalid @enderror"
                                rows="3"
                                placeholder="Ingrese la razón de cancelación..."
                            ></textarea>
                            @error('customCancellationReason')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" wire:click="confirmCancellation" style="margin-right: 20px;">
                        <i class="fas fa-check"></i> Confirmar Cancelación
                    </button>

                    <button type="button" class="btn btn-secondary" wire:click="closeCancelModal" >
                        <i class="fas fa-times"></i> Cancelar
                    </button>

                </div>
            </div>

    </div>
@endif
