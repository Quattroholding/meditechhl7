<div>
    @if($showModal)
        <div class="modal-overlay" wire:click="closePaymentSchedulingModal" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 10000; display: flex; align-items: center; justify-content: center;">
            <div class="modal-content" wire:click.stop style="position: relative; max-width: 700px; width: 90%; max-height: 90vh; overflow-y: auto;">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Pago</h5>
                    <button type="button" class="close" wire:click="closeModal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="p-6">
                    <!-- Invoice Info -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="alert alert-light" role="alert">
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <small class="text-muted d-block mb-1">Factura:</small>
                                        <strong>{{ $invoice->invoice_number }}</strong>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <small class="text-muted d-block mb-1">Proveedor:</small>
                                        <strong>{{ $invoice->supplier->legal_name }}</strong>
                                    </div>
                                    <div class="col-12 col-md-6 mt-2">
                                        <small class="text-muted d-block mb-1">Total:</small>
                                        <strong>B/. {{ number_format($invoice->total_amount, 2) }}</strong>
                                    </div>
                                    <div class="col-12 col-md-6 mt-2">
                                        <small class="text-muted d-block mb-1">Saldo Pendiente:</small>
                                        <strong class="text-danger">B/. {{ number_format($invoice->balance, 2) }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Form -->
                    <form wire:submit.prevent="save">
                        <!-- Amount -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Monto a Pagar *</label>
                                    <input wire:model="amount" type="number" step="0.01" class="form-control" max="{{ $invoice->balance }}" placeholder="0.00" />
                                    @error('amount') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Fecha de Pago *</label>
                                    <input wire:model="payment_date" type="date" class="form-control" />
                                    @error('payment_date') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Banco</label>
                                    <select wire:model="bank_id" class="form-select">
                                        <option value="">Selecciona un banco</option>
                                        @foreach($banks as $bank)
                                            <option value="{{ $bank->id }}">{{ $bank->balance_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('bank_id') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Caja</label>
                                    <select wire:model="cash_register_id" class="form-select">
                                        <option value="">Selecciona una caja</option>
                                        @foreach($cashRegisters as $register)
                                            <option value="{{ $register->id }}">{{ $register->balance_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('cash_register_id') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="form-group mb-3">
                            <label class="form-label">Descripción *</label>
                            <textarea wire:model="description" class="form-control" rows="2" placeholder="Descripción del pago"></textarea>
                            @error('description') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <!-- Reference Number -->
                        <div class="form-group mb-3">
                            <label class="form-label">Número de Referencia</label>
                            <input wire:model="reference_number" type="text" class="form-control" placeholder="Ej. Número de cheque, transferencia, etc." />
                            @error('reference_number') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <!-- Actions -->
                        <div class="doctor-submit text-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-check me-2"></i>Registrar Pago
                            </button>
                            <button type="button" wire:click="closeModal" class="btn btn-secondary">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('showToastrSupplierPaymentModal', (event) => {
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
