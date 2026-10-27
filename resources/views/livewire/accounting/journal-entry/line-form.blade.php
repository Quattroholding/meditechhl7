<div>
    <div class="modal fade show" id="lineFormModal" tabindex="-1" role="dialog" style="display: block; background: rgba(0,0,0,0.5);">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Línea del Asiento</h5>
                    <button
                        type="button"
                        class="close"
                        wire:click="$parent.closeLineForm()"
                    >
                        <span>&times;</span>
                    </button>
                </div>

                <form wire:submit="save">
                    <div class="modal-body">
                        <!-- Cuenta Contable -->
                        <div class="form-group mb-3">
                            <label for="accountingAccount" class="form-label">Cuenta Contable *</label>
                            <select
                                id="accountingAccount"
                                wire:model.live="accountingAccount"
                                class="form-select @error('accountingAccount') is-invalid @enderror"
                            >
                                <option value="">Seleccionar cuenta</option>
                                @foreach($this->accountingAccounts as $account)
                                    <option value="{{ $account->id }}" @if($accountingAccount == $account->id) selected @endif>
                                        {{ $account->code }} - {{ $account->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('accountingAccount')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Centro de Costo -->
                        <div class="form-group mb-3">
                            <label for="costCenter" class="form-label">Centro de Costo</label>
                            <select
                                id="costCenter"
                                wire:model.live="costCenter"
                                class="form-select @error('costCenter') is-invalid @enderror"
                            >
                                <option value="">Sin asignar</option>
                                @foreach($this->costCenters as $center)
                                    <option value="{{ $center->id }}" @if($costCenter == $center->id) selected @endif>
                                        {{ $center->code }} - {{ $center->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('costCenter')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Monto -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="debit" class="form-label">Débito</label>
                                    <input
                                        type="number"
                                        id="debit"
                                        wire:model.live="debit"
                                        step="0.01"
                                        min="0"
                                        class="form-control @error('debit') is-invalid @enderror"
                                        placeholder="0.00"
                                    >
                                    @error('debit')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="credit" class="form-label">Crédito</label>
                                    <input
                                        type="number"
                                        id="credit"
                                        wire:model.live="credit"
                                        step="0.01"
                                        min="0"
                                        class="form-control @error('credit') is-invalid @enderror"
                                        placeholder="0.00"
                                    >
                                    @error('credit')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Descripción -->
                        <div class="form-group mb-3">
                            <label for="description" class="form-label">Descripción</label>
                            <textarea
                                id="description"
                                wire:model.live="description"
                                class="form-control @error('description') is-invalid @enderror"
                                rows="3"
                                placeholder="Descripción de la línea"
                            ></textarea>
                            @error('description')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-secondary"
                            wire:click="$parent.closeLineForm()"
                        >
                            Cancelar
                        </button>
                        <button
                            type="button"
                            class="btn btn-danger"
                            @if($journalEntry->isDraft())
                                wire:click="delete"
                            @else
                                disabled
                            @endif
                        >
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <i class="fas fa-save"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('livewire:initialized', () => {

            Livewire.on('showToastrJournalEntryEdit', (event) => {
                toastr[event.type](event.message, '', {
                    closeButton: true,
                    progressBar: true,
                    positionClass: 'toast-top-right',
                    timeOut: 5000,
                });
            });
        });

    </script>
</div>
