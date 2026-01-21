<div>
    @if($showModal)
        <div class="modal-overlay" wire:click="closeModal" style="z-index: 10000;">
            <div class="modal-content" wire:click.stop>
                <div class="modal-header">
                    <h2 class="modal-title">Registrar Pago de Suscripción</h2>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                @if($invoice)
                    <form wire:submit="savePayment">
                        <div class="modal-body">
                            <!-- Invoice Information -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <div class="row px-3 py-3">
                                                <div class="col-md-6">
                                                    <h6 class="mb-2">
                                                        <strong>Factura:</strong> {{ $invoice->invoice_number }}
                                                    </h6>
                                                    <p class="mb-1">
                                                        <strong>Cliente:</strong> {{ $invoice->client->name }}
                                                    </p>
                                                    <p class="mb-0">
                                                        <strong>Plan:</strong> {{ $invoice->subscription->package->name ?? 'N/A' }}
                                                    </p>
                                                </div>
                                                <div class="col-md-6 text-md-end">
                                                    <h6 class="mb-2">
                                                        <strong>Total a Pagar:</strong> ${{ number_format($invoice->total, 2) }}
                                                    </h6>
                                                    <p class="mb-1">
                                                        <strong>Pagado:</strong> ${{ number_format($invoice->getTotalPaid(), 2) }}
                                                    </p>
                                                    <p class="mb-0 text-danger">
                                                        <strong>Saldo Pendiente:</strong> ${{ number_format($invoice->amount_due, 2) }}
                                                    </p>
                                                </div>
                                            </div>

                                            <!-- Tax Breakdown -->
                                            <div class="row px-3 py-2 mt-3 border-top">
                                                <div class="col-12">
                                                    <h6 class="text-muted mb-2" style="font-size: 0.9rem;">
                                                        <i class="fas fa-info-circle me-1"></i>Desglose de Factura
                                                    </h6>
                                                    <div class="row small">
                                                        <div class="col-6">
                                                            <div class="d-flex justify-content-between mb-1">
                                                                <span>Subtotal:</span>
                                                                <span>${{ number_format($invoice->subtotal, 2) }}</span>
                                                            </div>
                                                            @if($invoice->discount_amount > 0)
                                                                <div class="d-flex justify-content-between mb-1 text-success">
                                                                    <span>Descuento:</span>
                                                                    <span>-${{ number_format($invoice->discount_amount, 2) }}</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="d-flex justify-content-between mb-1">
                                                                <span>
                                                                    <strong>ITBMS ({{ number_format(($invoice->tax_rate ?? 0.07) * 100, 0) }}%):</strong>
                                                                    <i class="fas fa-question-circle text-muted ms-1"
                                                                       data-bs-toggle="tooltip"
                                                                       title="Impuesto de Transferencia de Bienes Muebles y Servicios - Panamá"></i>
                                                                </span>
                                                                <span><strong>${{ number_format($invoice->tax_amount ?? 0, 2) }}</strong></span>
                                                            </div>
                                                            <div class="d-flex justify-content-between">
                                                                <span class="text-muted" style="font-size: 0.75rem;">
                                                                    Este impuesto se paga a la DGI
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
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
                                                    max="{{ $invoice->amount_due }}"
                                                    placeholder="0.00"/>
                                        @error('amount') <span class="text-danger text-sm">{{ $message }}</span> @enderror
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
                                        @error('payment_date') <span class="text-danger text-sm">{{ $message }}</span> @enderror
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
                                        @error('payment_method') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <!-- Reference Number -->
                                <div class="col-md-6">
                                    <div class="input-block local-forms">
                                        <x-input-label for="payment_reference" :value="__('Número de Referencia')" required="true"/>
                                        <x-text-input wire:model="payment_reference"
                                                    class="block mt-1 w-full"
                                                    type="text"
                                                    name="payment_reference"/>
                                        @error('payment_reference') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                            {{--}}
                            <div class="row">
                                <!-- Payment Gateway -->
                                <div class="col-md-6">
                                    <div class="input-block local-forms">
                                        <x-input-label for="payment_gateway" :value="__('Plataforma de Pago')"/>
                                        <x-text-input wire:model="payment_gateway"
                                                    class="block mt-1 w-full"
                                                    type="text"
                                                    name="payment_gateway"
                                                    placeholder="Ej: Yappy, ACH, Banco, etc."/>
                                        @error('payment_gateway') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <!-- Transaction ID -->
                                <div class="col-md-6">
                                    <div class="input-block local-forms">
                                        <x-input-label for="gateway_transaction_id" :value="__('ID de Transacción')"/>
                                        <x-text-input wire:model="gateway_transaction_id"
                                                    class="block mt-1 w-full"
                                                    type="text"
                                                    name="gateway_transaction_id"
                                                    placeholder="Opcional"/>
                                        @error('gateway_transaction_id') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                            {{--}}
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
                                        @error('notes') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Receipt File Upload -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="input-block local-forms">
                                        <x-input-label for="receipt_file" :value="__('Comprobante de Transacción')" required="true" />
                                        <input type="file"
                                               wire:model="receipt_file"
                                               class="form-control"
                                               accept=".pdf,.jpg,.jpeg,.png">
                                        <small class="text-muted">Opcional. Formatos permitidos: PDF, JPG, PNG. Máximo 5MB.</small>
                                        @error('receipt_file') <span class="text-danger text-sm">{{ $message }}</span> @enderror

                                        @if ($receipt_file)
                                            <div class="mt-2">
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check me-1"></i>
                                                    Archivo seleccionado: {{ $receipt_file->getClientOriginalName() }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Previous Payments -->
                            @if($invoice->payments && $invoice->payments->count() > 0)
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <h6>Pagos Anteriores</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Fecha</th>
                                                        <th>Monto</th>
                                                        <th>Método</th>
                                                        <th>Referencia</th>
                                                        <th>Estado</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($invoice->payments->take(5) as $payment)
                                                        <tr>
                                                            <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                                                            <td>${{ number_format($payment->amount, 2) }}</td>
                                                            <td>
                                                                <span class="badge bg-{{ $payment->payment_method->color() }}">
                                                                    {{ $payment->payment_method->label() }}
                                                                </span>
                                                            </td>
                                                            <td>{{ $payment->payment_reference ?? '-' }}</td>
                                                            <td>
                                                                <span class="badge bg-{{ $payment->status->color() }}">
                                                                    {{ $payment->status->label() }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="modal-footer">
                            <div class="flex items-center justify-end mt-4">
                                <div class="doctor-submit text-end">
                                    <button type="submit" class="btn btn-primary submit-form me-2">
                                        <i class="fas fa-save me-1"></i> Registrar Pago
                                    </button>
                                    <a class="btn btn-secondary" wire:click="closeModal">
                                        <i class="fas fa-times me-1"></i> Cancelar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    @endif
</div>
