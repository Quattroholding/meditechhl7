<div>
    <!-- Success/Error Messages -->
    @if($successMessage)
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ $successMessage }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errorMessage)
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ $errorMessage }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Add Card Button -->
    @if(!$showAddForm)
        <div class="mb-3">
            <button type="button" wire:click="showAdd" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Agregar Tarjeta
            </button>
        </div>
    @endif

    <!-- Add Card Form -->
    @if($showAddForm)
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-credit-card me-2"></i>Agregar Nueva Tarjeta</h5>
            </div>
            <div class="card-body">
                <form wire:submit.prevent="saveCard">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="cardNumber" class="form-label">Número de Tarjeta <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                class="form-control @error('cardNumber') is-invalid @enderror"
                                id="cardNumber"
                                wire:model="cardNumber"
                                placeholder="1234 5678 9012 3456"
                                maxlength="19"
                            >
                            @error('cardNumber')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="cardHolder" class="form-label">Nombre del Titular <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                class="form-control @error('cardHolder') is-invalid @enderror"
                                id="cardHolder"
                                wire:model="cardHolder"
                                placeholder="NOMBRE APELLIDO"
                                style="text-transform: uppercase"
                            >
                            @error('cardHolder')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Como aparece en la tarjeta</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="expMonth" class="form-label">Mes de Expiración <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                class="form-control @error('expMonth') is-invalid @enderror"
                                id="expMonth"
                                wire:model="expMonth"
                                placeholder="MM"
                                maxlength="2"
                            >
                            @error('expMonth')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="expYear" class="form-label">Año de Expiración <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                class="form-control @error('expYear') is-invalid @enderror"
                                id="expYear"
                                wire:model="expYear"
                                placeholder="YY"
                                maxlength="2"
                            >
                            @error('expYear')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <small>Su información es procesada de forma segura a través de NeoPayments. No almacenamos números de tarjeta completos.</small>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="saveCard">
                                <i class="fas fa-save me-2"></i>Guardar Tarjeta
                            </span>
                            <span wire:loading wire:target="saveCard">
                                <i class="fas fa-spinner fa-spin me-2"></i>Guardando...
                            </span>
                        </button>
                        <button type="button" wire:click="cancelAdd" class="btn btn-secondary">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Credit Cards List -->
    @if($creditCards->count() > 0)
        <div class="row">
            @foreach($creditCards as $card)
                <div class="col-md-6 mb-3">
                    <div class="card {{ $card->is_default ? 'border-primary' : '' }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h5 class="card-title mb-1">
                                        <i class="fas fa-credit-card me-2"></i>{{ $card->card_brand ?? 'CARD' }}
                                        @if($card->is_default)
                                            <span class="badge bg-primary">Predeterminada</span>
                                        @endif
                                    </h5>
                                    <p class="card-text text-muted mb-0">{{ $card->getMaskedCardNumber() }}</p>
                                </div>
                                <div class="text-end">
                                    @if($card->isExpired())
                                        <span class="badge bg-danger">Expirada</span>
                                    @else
                                        <small class="text-muted">Exp: {{ $card->getExpirationDisplay() }}</small>
                                    @endif
                                </div>
                            </div>

                            <p class="card-text mb-3">
                                <small class="text-muted">{{ $card->card_holder }}</small>
                            </p>

                            <div class="d-flex gap-2">
                                @if(!$card->is_default)
                                    <button
                                        type="button"
                                        wire:click="setDefault({{ $card->id }})"
                                        class="btn btn-sm btn-outline-primary"
                                        wire:loading.attr="disabled"
                                    >
                                        <i class="fas fa-star me-1"></i>Predeterminada
                                    </button>
                                @endif

                                <button
                                    type="button"
                                    wire:click="confirmDelete({{ $card->id }})"
                                    class="btn btn-sm btn-outline-danger"
                                    wire:loading.attr="disabled"
                                >
                                    <i class="fas fa-trash me-1"></i>Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        @if(!$showAddForm)
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                No tienes tarjetas registradas. Agrega una tarjeta para facilitar los pagos automáticos.
            </div>
        @endif
    @endif

    <!-- Delete Confirmation Modal -->
    @if($confirmDeleteId)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirmar Eliminación</h5>
                        <button type="button" class="btn-close" wire:click="cancelDelete"></button>
                    </div>
                    <div class="modal-body">
                        <p>¿Está seguro que desea eliminar esta tarjeta?</p>
                        <p class="text-muted mb-0"><small>Esta acción no se puede deshacer.</small></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="cancelDelete">
                            Cancelar
                        </button>
                        <button type="button" class="btn btn-danger" wire:click="deleteCard" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="deleteCard">
                                <i class="fas fa-trash me-2"></i>Eliminar
                            </span>
                            <span wire:loading wire:target="deleteCard">
                                <i class="fas fa-spinner fa-spin me-2"></i>Eliminando...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
