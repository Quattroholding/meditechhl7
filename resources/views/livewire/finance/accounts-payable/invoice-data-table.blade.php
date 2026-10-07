<div class="col-sm-12">
    <div class="card card-table show-entire">
        <div class="card-body">
            <!-- Table Header -->
            @component('components.table-header', ['show_create' => auth()->user()->can('payables.invoices.create'), 'title' => '', 'li_1' => route('finance.payables.invoices.create')])
                @slot('filters')
                    <div class="d-flex flex-wrap gap-2">
                        <div class="input-block local-forms mb-0">
                            <label>{{ __('Estatus') }}</label>
                            <x-select-input wire:model.live="statusFilter" id="statusFilter" name="status" :options="['draft' => 'Borrador', 'registered' => 'Registrada', 'approved' => 'Aprobada', 'partial' => 'Parcial', 'paid' => 'Pagada', 'overdue' => 'Vencida', 'cancelled' => 'Cancelada']" :selected="[]" class="form-select" />
                        </div>
                    </div>
                @endslot
            @endcomponent
            <!-- /Table Header -->

            <div class="table-responsive">
                <table class="table border-0 custom-table comman-table mb-0 responsive-table">
                    <thead>
                        <tr>
                            <th data-column="invoice_number" data-priority="1">
                                <x-table-sort-button title="Número Factura" columnName="invoice_number" :sortField="$sortField" :sortDirection="$sortDirection" />
                            </th>
                            <th data-column="supplier" data-priority="2">
                                <x-table-sort-button title="Proveedor" columnName="" />
                            </th>
                            <th data-column="invoice_date" data-priority="3">
                                <x-table-sort-button title="Fecha Factura" columnName="invoice_date" :sortField="$sortField" :sortDirection="$sortDirection" />
                            </th>
                            <th data-column="due_date" data-priority="4">
                                <x-table-sort-button title="Fecha Vencimiento" columnName="due_date" :sortField="$sortField" :sortDirection="$sortDirection" />
                            </th>
                            <th data-column="total_amount" data-priority="5">
                                <x-table-sort-button title="Total" columnName="total_amount" :sortField="$sortField" :sortDirection="$sortDirection" />
                            </th>
                            <th data-column="paid_amount" data-priority="6">
                                <x-table-sort-button title="Pagado" columnName="paid_amount" :sortField="$sortField" :sortDirection="$sortDirection" />
                            </th>
                            <th data-column="balance" data-priority="7">
                                <x-table-sort-button title="Balance" columnName="balance" :sortField="$sortField" :sortDirection="$sortDirection" />
                            </th>
                            <th data-column="status" data-priority="8">
                                <x-table-sort-button title="Estatus" columnName="status" :sortField="$sortField" :sortDirection="$sortDirection" />
                            </th>
                            @canany(['payables.invoices.create', 'payables.invoices.approve', 'payables.payments.process'])
                                <th data-column="acciones" data-priority="1" class="text-end">
                                    <x-table-sort-button title="{{ __('Acciones') }}" columnName="" />
                                </th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $invoice)
                            <tr class="table-row" data-row-id="{{ $invoice->id }}">
                                <td data-column="invoice_number" data-priority="1" data-label="Número Factura">
                                    <span class="cell-content">{{ $invoice->invoice_number }}</span>
                                </td>
                                <td data-column="supplier" data-priority="2" data-label="Proveedor">
                                    <span class="cell-content">{{ $invoice->supplier?->legal_name }}</span>
                                </td>
                                <td data-column="invoice_date" data-priority="3" data-label="Fecha Factura">
                                    <span class="cell-content">{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d-m-Y') }}</span>
                                </td>
                                <td data-column="due_date" data-priority="4" data-label="Fecha Vencimiento">
                                    <span class="cell-content">{{ \Carbon\Carbon::parse($invoice->due_date)->format('d-m-Y') }}</span>
                                </td>
                                <td data-column="total_amount" data-priority="5" data-label="Total">
                                    <span class="cell-content">B/. {{ number_format($invoice->total_amount, 2) }}</span>
                                </td>
                                <td data-column="paid_amount" data-priority="6" data-label="Pagado">
                                    <span class="cell-content">B/. {{ number_format($invoice->paid_amount, 2) }}</span>
                                </td>
                                <td data-column="balance" data-priority="7" data-label="Balance">
                                    <span class="cell-content font-weight-bold">B/. {{ number_format($invoice->balance, 2) }}</span>
                                </td>
                                <td data-column="status" data-priority="8" data-label="Estatus">
                                    <span class="cell-content badge me-1
                                        {{ $invoice->status === 'draft' ? 'bg-warning' : ($invoice->status === 'approved' ? 'bg-info' : ($invoice->status === 'paid' ? 'bg-success' : ($invoice->status === 'overdue' ? 'bg-danger' : 'bg-secondary'))) }}">
                                        {{ ucfirst(str_replace('_', ' ', $invoice->status)) }}
                                    </span>
                                </td>
                                @canany(['payables.invoices.create', 'payables.invoices.approve', 'payables.payments.process'])
                                    <td data-column="acciones" data-priority="1" data-label="{{ __('Acciones') }}" class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('finance.payables.invoices.show', $invoice->id) }}" class="btn btn-info btn-sm" title="Ver">
                                                <i class="fa-solid fa-eye m-r-5"></i>
                                            </a>
                                            @if($invoice->status === 'draft' && auth()->user()->can('payables.invoices.create'))
                                                <a href="{{ route('finance.payables.invoices.edit', $invoice->id) }}" class="btn btn-success btn-sm" title="Editar">
                                                    <i class="fa-solid fa-pen-to-square m-r-5"></i>
                                                </a>
                                            @endif
                                            @if($invoice->status === 'registered' && auth()->user()->can('payables.invoices.approve'))
                                                <button wire:click="approveInvoice({{ $invoice->id }})" class="btn btn-warning btn-sm" title="Aprobar">
                                                    <i class="fa-solid fa-check m-r-5"></i>
                                                </button>
                                            @endif
                                            @if(($invoice->status === 'approved' || $invoice->status === 'partial') && auth()->user()->can('payables.payments.process'))
                                                <button wire:click="schedulePayment({{ $invoice->id }})" class="btn btn-success btn-sm" title="Programar Pago">
                                                    <i class="fa-solid fa-calendar m-r-5"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                @endcanany
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    No hay facturas de proveedores registradas
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @include('partials.pagination', ['data' => $data])
        </div>
    </div>

    {{-- Payment Scheduling Modal --}}
    @if ($showPaymentSchedulingModal && $selectedInvoice)
        <div class="modal-overlay" wire:click="closePaymentSchedulingModal" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 10000; display: flex; align-items: center; justify-content: center;">
            <div class="modal-content" wire:click.stop style="position: relative; max-width: 500px; width: 90%; max-height: 90vh; overflow-y: auto;">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Pago</h5>
                    <button type="button" class="close" wire:click="closePaymentSchedulingModal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form wire:submit.prevent="savePayment">
                    <div class="modal-body">
                        {{-- Invoice Info --}}
                        <div class="alert alert-info mb-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Factura:</strong> {{ $selectedInvoice->invoice_number }}</p>
                                    <p class="mb-1"><strong>Proveedor:</strong> {{ $selectedInvoice->supplier?->legal_name ?? 'N/A' }}</p>
                                    <p class="mb-0"><strong>Fecha:</strong> {{ $selectedInvoice->invoice_date?->format('d/m/Y') }}</p>
                                </div>
                                <div class="col-md-6 text-end">
                                    <p class="mb-2"><strong>Total:</strong> B/. {{ number_format($selectedInvoice->total_amount, 2) }}</p>
                                    <p class="mb-1"><strong>Pagado:</strong> B/. {{ number_format($selectedInvoice->paid_amount ?? 0, 2) }}</p>
                                    <p class="mb-0 text-danger"><strong>Saldo:</strong> B/. {{ number_format($selectedInvoice->balance, 2) }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Amount and Date --}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Monto a Pagar *</label>
                                    <input wire:model="amount" type="number" step="0.01" class="form-control" max="{{ $selectedInvoice->balance }}" placeholder="0.00" />
                                    @error('amount') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Fecha Pago *</label>
                                    <input wire:model="payment_date" type="date" class="form-control" />
                                    @error('payment_date') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Payment Method and Reference --}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Método de Pago *</label>
                                    <select wire:model="payment_method" class="form-control">
                                        <option value="cash">Efectivo</option>
                                        <option value="credit_card">Tarjeta de Crédito</option>
                                        <option value="debit_card">Tarjeta de Débito</option>
                                        <option value="bank_transfer">Transferencia Bancaria</option>
                                        <option value="check">Cheque</option>
                                        <option value="online">Pago Online</option>
                                        <option value="insurance">Seguro</option>
                                        <option value="other">Otro</option>
                                    </select>
                                    @error('payment_method') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Número de Referencia</label>
                                    <input wire:model="reference_number" type="text" class="form-control" placeholder="Opcional" />
                                    @error('reference_number') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Transaction ID --}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>ID de Transacción</label>
                                    <input wire:model="transaction_id" type="text" class="form-control" placeholder="Opcional" />
                                    @error('transaction_id') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Notes --}}
                        <div class="form-group">
                            <label>Notas</label>
                            <textarea wire:model="notes" class="form-control" rows="2" placeholder="Notas adicionales (opcional)"></textarea>
                            @error('notes') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">
                            Registrar Pago
                        </button>
                        <button type="button" class="btn btn-secondary" wire:click="closePaymentSchedulingModal">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
