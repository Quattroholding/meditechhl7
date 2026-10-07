<div>
    @if($showModal)
        <div class="modal-overlay" wire:click="closeModal" style="z-index: 10000;">
            <div class="modal-content" wire:click.stop>
                <div class="modal-header">
                    <h2 class="modal-title">{{ __('Registrar Pago') }}</h2>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                @if($receivable)
                        <form wire:submit="savePayment">
                            <div class="modal-body">
                                <!-- Invoice Information -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <div class="row  px-3 py-3">
                                                    <div class="col-md-6">
                                                        <h6 class="mb-2">
                                                            <strong>Factura:</strong> {{ $receivable->invoice_number }}
                                                        </h6>
                                                        <p class="mb-1">
                                                            <strong>Paciente:</strong> {{ $receivable->patient->name }}
                                                        </p>
                                                        <p class="mb-0">
                                                            <strong>Fecha:</strong> {{ $receivable->invoice_date?->format('d/m/Y') }}
                                                        </p>
                                                    </div>
                                                    <div class="col-md-6 text-md-end">
                                                        <h6 class="mb-2">
                                                            <strong>Total:</strong> B/. {{ number_format($receivable->original_amount, 2) }}
                                                        </h6>
                                                        <p class="mb-1">
                                                            <strong>Pagado:</strong> B/. {{ number_format($receivable->paid_amount ?? 0, 2) }}
                                                        </p>
                                                        <p class="mb-0 text-danger">
                                                            <strong>Saldo:</strong> B/. {{ number_format($receivable->balance, 2) }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Form -->
                                <div class="row">
                                    <!-- Amount -->
                                    <div class="col-md-6">
                                        <div class="input-block local-forms">
                                            <x-input-label for="amount" :value="__('Monto a Pagar')" required="true"/>
                                            <x-text-input wire:model="amount"
                                                        class="block mt-1 w-full"
                                                        type="number"
                                                        step="0.01"
                                                        name="amount"
                                                        max="{{ $receivable->balance }}"
                                                        placeholder="0.00"/>
                                            @error('amount') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <!-- Payment Date -->
                                    <div class="col-md-6">
                                        <div class="input-block local-forms">
                                            <x-input-label for="payment_date" :value="__('Fecha de Pago')" required="true"/>
                                            <x-text-input wire:model="payment_date"
                                                        class="block mt-1 w-full"
                                                        type="date"
                                                        name="payment_date"/>
                                            @error('payment_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Payment Method -->
                                    <div class="col-md-6">
                                        <div class="input-block local-forms">
                                            <x-input-label for="payment_method" :value="__('Método de Pago')" required="true"/>
                                            <select wire:model="payment_method" class="form-control">
                                                @foreach($this->paymentMethods as $key => $value)
                                                    <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                            @error('payment_method') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <!-- Reference Number -->
                                    <div class="col-md-6">
                                        <div class="input-block local-forms">
                                            <x-input-label for="reference_number" :value="__('Número de Referencia')"/>
                                            <x-text-input wire:model="reference_number"
                                                        class="block mt-1 w-full"
                                                        type="text"
                                                        name="reference_number"
                                                        placeholder="Opcional"/>
                                            @error('reference_number') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Transaction ID -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-block local-forms">
                                            <x-input-label for="transaction_id" :value="__('ID de Transacción')"/>
                                            <x-text-input wire:model="transaction_id"
                                                        class="block mt-1 w-full"
                                                        type="text"
                                                        name="transaction_id"
                                                        placeholder="Opcional"/>
                                            @error('transaction_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Notes -->
                                <div class="row">
                                    <div class="col-12">
                                        <div class="input-block local-forms">
                                            <x-input-label for="notes" :value="__('Notas')"/>
                                            <textarea wire:model="notes"
                                                    class="form-control"
                                                    rows="3"
                                                    name="notes"
                                                    placeholder="Notas adicionales sobre el pago (opcional)"></textarea>
                                            @error('notes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <div class="flex items-center justify-end mt-4">
                                    <div class="doctor-submit text-end">
                                        <button type="submit" class="btn btn-primary submit-form me-2">{{ __('button.register') }}</button>
                                        <a class="btn btn-secondary"  wire:click="closeModal">{{ __('button.cancel') }}</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    @endif
            </div>
        </div>
    @endif
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('showToastr', (event) => {
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
