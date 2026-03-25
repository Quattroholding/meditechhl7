<div>
    {{-- Success / Error messages --}}
    @if($successMessage)
        <div class="alert alert-success alert-dismissible fade show mb-4">
            <i class="fas fa-check-circle me-2"></i>{{ $successMessage }}
            <button type="button" class="btn-close" wire:click="$set('successMessage', '')"></button>
        </div>
    @endif

    @if($errorMessage)
        <div class="alert alert-danger alert-dismissible fade show mb-4">
            <i class="fas fa-exclamation-circle me-2"></i>{{ $errorMessage }}
            <button type="button" class="btn-close" wire:click="$set('errorMessage', '')"></button>
        </div>
    @endif

    {{-- Existing Cards --}}
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Tarjetas Registradas</h5>
            @if(!$showAddForm)
                <button type="button" class="btn btn-primary btn-sm" wire:click="showAdd" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="showAdd">
                        <i class="fas fa-plus me-1"></i>Agregar Tarjeta
                    </span>
                    <span wire:loading wire:target="showAdd">
                        <i class="fas fa-spinner fa-spin me-1"></i>Cargando...
                    </span>
                </button>
            @endif
        </div>

        @if($tokens->isEmpty())
            <div class="text-center py-4 text-muted">
                <i class="fas fa-credit-card fa-2x mb-2 d-block"></i>
                No tiene tarjetas registradas.
            </div>
        @else
            <div class="row g-3">
                @foreach($tokens as $token)
                    <div class="col-md-6" wire:key="token-{{ $token->id }}">
                        <div class="card border {{ $token->is_default ? 'border-primary' : '' }} h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <i class="fas fa-credit-card text-secondary"></i>
                                            <strong>
                                                @if($token->card_brand)
                                                    {{ ucfirst($token->card_brand) }}
                                                @else
                                                    Tarjeta
                                                @endif
                                                @if($token->card_last4)
                                                    •••• {{ $token->card_last4 }}
                                                @endif
                                            </strong>
                                            @if($token->is_default)
                                                <span class="badge bg-primary">Predeterminada</span>
                                            @endif
                                        </div>
                                        @if($token->card_exp_month && $token->card_exp_year)
                                            <small class="text-muted">
                                                Vence: {{ str_pad($token->card_exp_month, 2, '0', STR_PAD_LEFT) }}/{{ $token->card_exp_year }}
                                            </small>
                                        @endif
                                        @if($token->billing_name)
                                            <div><small class="text-muted">{{ $token->billing_name }}</small></div>
                                        @endif
                                    </div>
                                    <div class="d-flex flex-column gap-1">
                                        @if(!$token->is_default)
                                            <button type="button"
                                                class="btn btn-outline-primary btn-sm"
                                                wire:click="setDefault({{ $token->id }})"
                                                title="Establecer como predeterminada">
                                                <i class="fas fa-star"></i>
                                            </button>
                                        @endif
                                        <button type="button"
                                            class="btn btn-outline-danger btn-sm"
                                            wire:click="deleteToken({{ $token->id }})"
                                            wire:confirm="¿Está seguro de que desea eliminar esta tarjeta?"
                                            title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Add Card Form --}}
    @if($showAddForm)
        <div class="card border-primary mt-3">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <span><i class="fas fa-plus me-2"></i>Agregar Nueva Tarjeta</span>
                <button type="button" class="btn-close btn-close-white" wire:click="cancelAdd"></button>
            </div>
            <div class="card-body">
                @if($captureContext)
                    <div class="mb-3">
                        <label class="form-label">Número de Tarjeta <span class="text-danger">*</span></label>
                        <div id="add-card-number-container"
                            style="height: 45px; border: 1px solid #ced4da; border-radius: 0.375rem; padding: 0 12px;">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label">Mes de Vencimiento <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('newCardExpMonth') is-invalid @enderror"
                                wire:model="newCardExpMonth"
                                placeholder="MM" min="1" max="12">
                            @error('newCardExpMonth') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-6">
                            <label class="form-label">Año de Vencimiento <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('newCardExpYear') is-invalid @enderror"
                                wire:model="newCardExpYear"
                                placeholder="AAAA" min="{{ date('Y') }}" max="{{ date('Y') + 20 }}">
                            @error('newCardExpYear') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nombre en la Tarjeta <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('newCardBillingName') is-invalid @enderror"
                            wire:model="newCardBillingName"
                            placeholder="Como aparece en la tarjeta">
                        @error('newCardBillingName') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <input type="hidden" id="add-card-transient-token" wire:model="transientToken">

                    <div id="add-card-error" class="alert alert-danger d-none mb-3"></div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" wire:click="cancelAdd">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="add-card-submit-btn">
                            <i class="fas fa-save me-1"></i>Guardar Tarjeta
                        </button>
                    </div>
                @endif
            </div>
        </div>

        @if($captureContext)
            <script>
                (function () {
                    const captureContext = @json($captureContext);

                    function initAddCardMicroform() {
                        if (typeof FLEX === 'undefined') {
                            setTimeout(initAddCardMicroform, 300);
                            return;
                        }

                        const flex = new FLEX(captureContext);
                        const microform = flex.microform('card', {
                            styles: {
                                input: {
                                    'font-size': '14px',
                                    'font-family': 'sans-serif',
                                    'color': '#333',
                                },
                                ':focus': { color: '#333' },
                                ':disabled': { cursor: 'not-allowed' },
                                valid: { color: '#28a745' },
                                invalid: { color: '#dc3545' },
                            },
                        });

                        microform.createField('number', { placeholder: '•••• •••• •••• ••••' })
                            .load('#add-card-number-container');

                        document.getElementById('add-card-submit-btn').addEventListener('click', function () {
                            const errorDiv = document.getElementById('add-card-error');
                            errorDiv.classList.add('d-none');
                            errorDiv.textContent = '';

                            const expMonth = document.querySelector('[wire\\:model="newCardExpMonth"]').value;
                            const expYear = document.querySelector('[wire\\:model="newCardExpYear"]').value;

                            microform.createToken({ expirationMonth: expMonth, expirationYear: expYear }, function (err, token) {
                                if (err) {
                                    errorDiv.textContent = err.message || 'Error al tokenizar la tarjeta.';
                                    errorDiv.classList.remove('d-none');
                                    return;
                                }

                                @this.set('transientToken', token);
                                setTimeout(() => @this.call('saveCard'), 100);
                            });
                        });
                    }

                    document.addEventListener('DOMContentLoaded', initAddCardMicroform);
                    if (document.readyState !== 'loading') {
                        initAddCardMicroform();
                    }
                })();
            </script>
        @endif
    @endif
</div>
