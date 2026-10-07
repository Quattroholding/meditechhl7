<div class="page-wrapper">
    <div class="row content">
        @component('components.page-header')
            @slot('title')
                Módulo Financiero
            @endslot
            @slot('li_1')
                Facturas Proveedor (Detallado)
            @endslot
        @endcomponent
        <div class="col-sm-12">
            <div class="card card-table show-entire">
                <div class="card-body">
                    <!-- Table Header -->
                    @component('components.table-header', ['show_create' => auth()->user()->can('payables.invoices.create'), 'title' => '', 'li_1' => route('finance.payables.invoices.create')])
                        @slot('filters')
                            <div class="d-flex flex-wrap gap-2">
                                <div class="input-block local-forms mb-0">
                                    <label>{{ __('Búsqueda') }}</label>
                                    <input type="text" wire:model.live="search" placeholder="Factura o proveedor..." class="form-control" />
                                </div>
                                <div class="input-block local-forms mb-0">
                                    <label>{{ __('Estatus') }}</label>
                                    <x-select-input wire:model.live="status" id="statusFilter" name="status" :options="['draft' => 'Borrador', 'approved' => 'Aprobada', 'partial' => 'Parcial', 'paid' => 'Pagada', 'overdue' => 'Vencida']" :selected="[]" class="form-select" />
                                </div>
                                <div class="input-block local-forms mb-0">
                                    <label>{{ __('Proveedor') }}</label>
                                    <x-select-input wire:model.live="supplier" id="supplierFilter" name="supplier" :options="\App\Models\Supplier::where('client_id', auth()->user()->getCurrentClient()->id)->pluck('legal_name', 'id')->prepend('Todos', '')" :selected="[]" class="form-select" />
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
                                        <x-table-sort-button title="Factura #" columnName="" />
                                    </th>
                                    <th data-column="supplier" data-priority="2">
                                        <x-table-sort-button title="Proveedor" columnName="" />
                                    </th>
                                    <th data-column="invoice_date" data-priority="3">
                                        <x-table-sort-button title="Fecha" columnName="" />
                                    </th>
                                    <th data-column="due_date" data-priority="4">
                                        <x-table-sort-button title="Vencimiento" columnName="" />
                                    </th>
                                    <th data-column="total_amount" data-priority="5">
                                        <x-table-sort-button title="Total" columnName="" />
                                    </th>
                                    <th data-column="paid_amount" data-priority="6">
                                        <x-table-sort-button title="Pagado" columnName="" />
                                    </th>
                                    <th data-column="status" data-priority="7">
                                        <x-table-sort-button title="Estado" columnName="" />
                                    </th>
                                    <th data-column="acciones" data-priority="1" class="text-end">
                                        <x-table-sort-button title="{{ __('Acciones') }}" columnName="" />
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoices as $invoice)
                                    <tr class="table-row" data-row-id="{{ $invoice->id }}">
                                        <td data-column="invoice_number" data-priority="1" data-label="Factura #">
                                            <span class="cell-content">{{ $invoice->invoice_number }}</span>
                                        </td>
                                        <td data-column="supplier" data-priority="2" data-label="Proveedor">
                                            <span class="cell-content">{{ $invoice->supplier->legal_name }}</span>
                                        </td>
                                        <td data-column="invoice_date" data-priority="3" data-label="Fecha">
                                            <span class="cell-content">{{ $invoice->invoice_date->format('d-m-Y') }}</span>
                                        </td>
                                        <td data-column="due_date" data-priority="4" data-label="Vencimiento">
                                            <span class="cell-content">{{ $invoice->due_date->format('d-m-Y') }}</span>
                                        </td>
                                        <td data-column="total_amount" data-priority="5" data-label="Total">
                                            <span class="cell-content">B/. {{ number_format($invoice->total_amount, 2) }}</span>
                                        </td>
                                        <td data-column="paid_amount" data-priority="6" data-label="Pagado">
                                            <span class="cell-content">B/. {{ number_format($invoice->paid_amount, 2) }}</span>
                                        </td>
                                        <td data-column="status" data-priority="7" data-label="Estado">
                                            <span class="cell-content badge me-1
                                                @switch($invoice->status)
                                                    @case('draft') bg-secondary @break
                                                    @case('approved') bg-info @break
                                                    @case('partial') bg-warning @break
                                                    @case('paid') bg-success @break
                                                    @case('overdue') bg-danger @break
                                                    @default bg-secondary
                                                @endswitch
                                            ">
                                                {{ ucfirst($invoice->status) }}
                                            </span>
                                        </td>
                                        <td data-column="acciones" data-priority="1" data-label="{{ __('Acciones') }}" class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                @if ($invoice->status === 'draft')
                                                    <button wire:click="openModal({{ $invoice->id }})" class="btn btn-success btn-sm" title="Editar">
                                                        <i class="fa-solid fa-pen-to-square m-r-5"></i>
                                                    </button>
                                                    <button wire:click="approve({{ $invoice->id }})" class="btn btn-info btn-sm" title="Aprobar">
                                                        <i class="fa-solid fa-check m-r-5"></i>
                                                    </button>
                                                @elseif($invoice->status !== 'paid' && $invoice->status !== 'cancelled')
                                                    <button wire:click="$dispatch('openPaymentSchedule', { invoiceId: {{ $invoice->id }} })" class="btn btn-success btn-sm" title="Pagar">
                                                        <i class="fa-solid fa-money-bill m-r-5"></i>
                                                    </button>
                                                @endif
                                                @if ($invoice->status === 'draft')
                                                    <button wire:click="cancel({{ $invoice->id }})" class="btn btn-danger btn-sm" title="Cancelar">
                                                        <i class="fa-solid fa-ban m-r-5"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            No hay facturas registradas
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @include('partials.pagination', ['data' => $invoices])
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal --}}
@if ($showModal)
    @livewire('finance.accounts-payable.supplier-invoice-modal', ['invoice' => $editingInvoice], key('invoice-modal-' . ($editingInvoice?->id ?? 'new')))
@endif
