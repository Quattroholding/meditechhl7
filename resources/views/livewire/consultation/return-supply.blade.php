<div>
    @if($showModal)
        <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title">
                            <i class="fas fa-undo me-2"></i>
                            Devolver Suministro
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        @if($delivery)
                            <!-- Error Message -->
                            @error('general')
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    {{ $message }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @enderror

                            <!-- Item Information -->
                            <div class="alert alert-light border mb-3">
                                <h6 class="mb-2">
                                    <i class="fas fa-box me-2 text-primary"></i>
                                    {{ $delivery->inventoryItem->name }}
                                </h6>
                                <div class="row g-2 small">
                                    <div class="col-6">
                                        <strong>SKU:</strong> {{ $delivery->inventoryItem->sku }}
                                    </div>
                                    <div class="col-6">
                                        <strong>Dispensado:</strong> {{ $quantityDispensed }} {{ $delivery->unit_of_measure }}
                                    </div>
                                    @if($quantityAlreadyReturned > 0)
                                        <div class="col-6">
                                            <strong>Ya devuelto:</strong>
                                            <span class="text-danger">{{ $quantityAlreadyReturned }} {{ $delivery->unit_of_measure }}</span>
                                        </div>
                                    @endif
                                    <div class="col-6">
                                        <strong>Disponible para devolver:</strong>
                                        <span class="text-success fw-bold">{{ $quantityAvailableToReturn }} {{ $delivery->unit_of_measure }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Return Form -->
                            <form wire:submit.prevent="processReturn">
                                @if($quantityAvailableToReturn > 0)
                                    <!-- Quantity -->
                                    <div class="mb-3">
                                        <label for="quantityToReturn" class="form-label">
                                            <i class="fas fa-hashtag me-1"></i>
                                            Cantidad a Devolver <span class="text-danger">*</span>
                                        </label>
                                        <input
                                            type="number"
                                            class="form-control @error('quantityToReturn') is-invalid @enderror"
                                            id="quantityToReturn"
                                            wire:model="quantityToReturn"
                                            step="0.01"
                                            min="0.01"
                                            max="{{ $quantityAvailableToReturn }}"
                                            required>
                                        @error('quantityToReturn')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">
                                            Máximo: {{ $quantityAvailableToReturn }} {{ $delivery->unit_of_measure }}
                                        </div>
                                    </div>

                                    <!-- Reason -->
                                    <div class="mb-3">
                                        <label for="reason" class="form-label">
                                            <i class="fas fa-question-circle me-1"></i>
                                            Razón de la Devolución <span class="text-danger">*</span>
                                        </label>
                                        <select
                                            class="form-select @error('reason') is-invalid @enderror"
                                            id="reason"
                                            wire:model="reason"
                                            required>
                                            <option value="">Seleccione una razón...</option>
                                            @foreach($returnReasons as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('reason')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Notes -->
                                    <div class="mb-3">
                                        <label for="notes" class="form-label">
                                            <i class="fas fa-sticky-note me-1"></i>
                                            Notas Adicionales (Opcional)
                                        </label>
                                        <textarea
                                            class="form-control @error('notes') is-invalid @enderror"
                                            id="notes"
                                            wire:model="notes"
                                            rows="3"
                                            maxlength="1000"
                                            placeholder="Detalles adicionales sobre la devolución..."></textarea>
                                        @error('notes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Warning Box -->
                                    <div class="alert alert-warning d-flex align-items-start">
                                        <i class="fas fa-exclamation-triangle me-2 mt-1"></i>
                                        <div class="small">
                                            <strong>Importante:</strong> Esta acción:
                                            <ul class="mb-0 mt-1">
                                                <li>Restaurará el stock al inventario</li>
                                                <li>Ajustará o anulará el cargo de facturación</li>
                                                <li>Quedará registrada en el historial permanente</li>
                                            </ul>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Este suministro ya fue devuelto completamente.
                                    </div>
                                @endif

                                <div style="margin-top: 30px; display: flex; gap: 15px;">
                                    @if($quantityAvailableToReturn > 0)
                                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                            <span wire:loading.remove wire:target="processReturn">
                                                <i class="fas fa-undo me-1"></i> Procesar Devolución
                                            </span>
                                            <span wire:loading wire:target="processReturn">
                                                <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                                Procesando...
                                            </span>
                                        </button>
                                    @endif
                                    <button type="button" class="btn btn-secondary" wire:click="closeModal">
                                        <i class="fas fa-times me-1"></i> Cancelar
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
