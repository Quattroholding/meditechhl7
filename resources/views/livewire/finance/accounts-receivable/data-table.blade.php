<div class="col-sm-12">
            <div class="card card-table show-entire">
                <div class="card-body">
                    <!-- Table Header -->
                    @component('components.table-header', ['show_create' => false, 'title' => '', 'li_1' => '#'])
                        @slot('filters')
                            <div class="d-flex flex-wrap gap-2">
                                <div class="input-block local-forms mb-0">
                                    <label>{{ __('Estatus') }}</label>
                                    <x-select-input wire:model.live="statusFilter" id="statusFilter" name="status" :options="['pending' => 'Pendiente', 'partial' => 'Parcial', 'paid' => 'Pagada', 'overdue' => 'Vencida', 'cancelled' => 'Cancelada']" :selected="[]" class="form-select" />
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
                                        <x-table-sort-button title="Factura" columnName="invoice_number" :sortField="$sortField" :sortDirection="$sortDirection" />
                                    </th>
                                    <th data-column="patient" data-priority="2">
                                        <x-table-sort-button title="Paciente" columnName="" />
                                    </th>
                                    <th data-column="original_amount" data-priority="3">
                                        <x-table-sort-button title="Monto Original" columnName="original_amount" :sortField="$sortField" :sortDirection="$sortDirection" />
                                    </th>
                                    <th data-column="paid_amount" data-priority="4">
                                        <x-table-sort-button title="Pagado" columnName="paid_amount" :sortField="$sortField" :sortDirection="$sortDirection" />
                                    </th>
                                    <th data-column="balance" data-priority="5">
                                        <x-table-sort-button title="Balance" columnName="balance" :sortField="$sortField" :sortDirection="$sortDirection" />
                                    </th>
                                    <th data-column="due_date" data-priority="6">
                                        <x-table-sort-button title="Fecha Vencimiento" columnName="due_date" :sortField="$sortField" :sortDirection="$sortDirection" />
                                    </th>
                                    <th data-column="status" data-priority="7">
                                        <x-table-sort-button title="Estatus" columnName="status" :sortField="$sortField" :sortDirection="$sortDirection" />
                                    </th>
                                    @canany(['receivables.manage', 'receivables.apply-payment'])
                                        <th data-column="acciones" data-priority="1" class="text-end">
                                            <x-table-sort-button title="{{ __('Acciones') }}" columnName="" />
                                        </th>
                                    @endcanany
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $receivable)
                                    <tr class="table-row" data-row-id="{{ $receivable->id }}">
                                        <td data-column="invoice_number" data-priority="1" data-label="Factura">
                                            <span class="cell-content">{{ $receivable->invoice_number }}</span>
                                        </td>
                                        <td data-column="patient" data-priority="2" data-label="Paciente">
                                            <span class="cell-content">
                                                {{ $receivable->patient?->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td data-column="original_amount" data-priority="3" data-label="Monto Original">
                                            <span class="cell-content">B/. {{ number_format($receivable->original_amount, 2) }}</span>
                                        </td>
                                        <td data-column="paid_amount" data-priority="4" data-label="Pagado">
                                            <span class="cell-content">B/. {{ number_format($receivable->paid_amount, 2) }}</span>
                                        </td>
                                        <td data-column="balance" data-priority="5" data-label="Balance">
                                            <span class="cell-content font-weight-bold">B/. {{ number_format($receivable->balance, 2) }}</span>
                                        </td>
                                        <td data-column="due_date" data-priority="6" data-label="Fecha Vencimiento">
                                            <span class="cell-content">{{ \Carbon\Carbon::parse($receivable->due_date)->format('d-m-Y') }}</span>
                                        </td>
                                        <td data-column="status" data-priority="7" data-label="Estatus">
                                            <span class="cell-content badge me-1
                                                {{ $receivable->status === 'pending' ? 'bg-warning' : ($receivable->status === 'paid' ? 'bg-success' : ($receivable->status === 'overdue' ? 'bg-danger' : 'bg-info')) }}">
                                                {{ ucfirst($receivable->status) }}
                                            </span>
                                        </td>
                                        @canany(['receivables.manage', 'receivables.apply-payment'])
                                            <td data-column="acciones" data-priority="1" data-label="{{ __('Acciones') }}" class="text-end">
                                                <div class="btn-group btn-group-sm">
                                                    @can('receivables.manage')
                                                        <a href="{{ route('finance.receivables.show', $receivable->id) }}" class="btn btn-info btn-sm" title="Ver">
                                                            <i class="fa-solid fa-eye m-r-5"></i>
                                                        </a>
                                                    @endcan
                                                    @can('receivables.apply-payment')
                                                        @if($receivable->balance > 0)
                                                            <button wire:click="applyPayment({{ $receivable->id }})" class="btn btn-success btn-sm" title="Aplicar Pago">
                                                                <i class="fa-solid fa-money-bill m-r-5"></i>
                                                            </button>
                                                        @endif
                                                    @endcan
                                                </div>
                                            </td>
                                        @endcanany
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            No hay cuentas por cobrar registradas
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @include('partials.pagination', ['data' => $data])
                </div>
            </div>

            {{-- Payment Modal --}}
            @if ($showPaymentModal && $selectedReceivable)
                <div class="modal fade show d-block" tabindex="-1" role="dialog" aria-modal="true" style="display: block; background-color: rgba(0, 0, 0, 0.5); z-index: 1050;">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Aplicar Pago</h5>
                                <button type="button" class="close" wire:click="closePaymentModal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <form wire:submit.prevent="">
                                <div class="modal-body">
                                    {{-- Invoice Info --}}
                                    <div class="alert alert-info mb-3">
                                        <p class="mb-2"><strong>Factura:</strong> {{ $selectedReceivable->invoice_number }}</p>
                                        <p class="mb-2"><strong>Paciente:</strong> {{ $selectedReceivable->patient->full_name ?? 'N/A' }}</p>
                                        <p class="mb-2"><strong>Total:</strong> B/. {{ number_format($selectedReceivable->original_amount, 2) }}</p>
                                        <p class="mb-0"><strong>Saldo Pendiente:</strong> B/. {{ number_format($selectedReceivable->balance, 2) }}</p>
                                    </div>

                                    <div class="form-group">
                                        <label>Monto a Aplicar *</label>
                                        <input type="number" step="0.01" class="form-control" max="{{ $selectedReceivable->balance }}" value="{{ $selectedReceivable->balance }}" />
                                    </div>

                                    <div class="form-group">
                                        <label>Notas</label>
                                        <textarea class="form-control" rows="2"></textarea>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" wire:click="closePaymentModal">
                                        Cancelar
                                    </button>
                                    <button type="button" class="btn btn-primary">
                                        Aplicar Pago
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
