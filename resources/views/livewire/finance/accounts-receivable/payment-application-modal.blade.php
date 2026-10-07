<!-- Modal Bootstrap con Alpine.js -->
<div x-data="{ show: @entangle('showPaymentModal') }"
     x-show="show"
     class="modal fade"
     id="paymentApplicationModal"
     tabindex="-1"
     role="dialog"
     aria-labelledby="paymentApplicationModalLabel"
     aria-hidden="true"
     style="display: none; background-color: rgba(0,0,0,0.5);">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentApplicationModalLabel">Aplicar Pago</h5>
                <button type="button" class="close" wire:click="closePaymentModal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form wire:submit.prevent="apply">
                <div class="modal-body">
                    {{-- Invoice Info --}}
                    <div class="alert alert-info">
                        <p><strong>Factura:</strong> {{ $receivable->invoice_number }}</p>
                        <p><strong>Paciente:</strong> {{ $receivable->patient->full_name ?? 'N/A' }}</p>
                        <p><strong>Total:</strong> B/. {{ number_format($receivable->original_amount, 2) }}</p>
                        <p><strong>Saldo Pendiente:</strong> B/. {{ number_format($receivable->balance, 2) }}</p>
                    </div>

                    {{-- Amount Input --}}
                    <div class="form-group">
                        <label for="amount_to_apply">Monto a Aplicar *</label>
                        <input
                            type="number"
                            step="0.01"
                            id="amount_to_apply"
                            wire:model="amount_to_apply"
                            class="form-control"
                            max="{{ $receivable->balance }}"
                        />
                        @error('amount_to_apply')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Notes --}}
                    <div class="form-group">
                        <label for="notes">Notas</label>
                        <textarea
                            id="notes"
                            wire:model="notes"
                            class="form-control"
                            rows="2"
                        ></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closePaymentModal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Aplicar Pago
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
