<div class="page-wrapper">
    <div class="row content">
        @component('components.page-header')
            @slot('title')
                Facturas a Crédito
            @endslot
            @slot('li_1')
                Módulo Financiero
            @endslot
        @endcomponent
        <div class="col-sm-12">
            <div class="card card-table show-entire">
                <div class="card-body">
                    <!-- Table Header -->
                    @component('components.table-header', ['show_create' => false, 'title' => '', 'li_1' => '#'])
                        @slot('filters')
                            <div class="d-flex flex-wrap gap-2">
                                <div class="input-block local-forms mb-0">
                                    <label>{{ __('Búsqueda') }}</label>
                                    <input type="text" wire:model.live="search" placeholder="Factura o paciente..." class="form-control" />
                                </div>
                                <div class="input-block local-forms mb-0">
                                    <label>{{ __('Estatus') }}</label>
                                    <x-select-input wire:model.live="status" id="statusFilter" name="status" :options="['pending' => 'Pendiente', 'partial' => 'Parcial', 'paid' => 'Pagada']" :selected="[]" class="form-select" />
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
                                    <th data-column="patient" data-priority="2">
                                        <x-table-sort-button title="Paciente" columnName="" />
                                    </th>
                                    <th data-column="issue_date" data-priority="3">
                                        <x-table-sort-button title="Fecha Emisión" columnName="" />
                                    </th>
                                    <th data-column="due_date" data-priority="4">
                                        <x-table-sort-button title="Vencimiento" columnName="" />
                                    </th>
                                    <th data-column="total_amount" data-priority="5">
                                        <x-table-sort-button title="Total" columnName="" />
                                    </th>
                                    <th data-column="amount_paid" data-priority="6">
                                        <x-table-sort-button title="Pagado" columnName="" />
                                    </th>
                                    <th data-column="balance" data-priority="7">
                                        <x-table-sort-button title="Saldo" columnName="" />
                                    </th>
                                    <th data-column="status" data-priority="8">
                                        <x-table-sort-button title="Estado" columnName="" />
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoices as $invoice)
                                    <tr class="table-row" data-row-id="{{ $invoice->id }}">
                                        <td data-column="invoice_number" data-priority="1" data-label="Factura #">
                                            <span class="cell-content">{{ $invoice->invoice_number }}</span>
                                        </td>
                                        <td data-column="patient" data-priority="2" data-label="Paciente">
                                            <span class="cell-content">{{ $invoice->patient->full_name ?? 'N/A' }}</span>
                                        </td>
                                        <td data-column="issue_date" data-priority="3" data-label="Fecha Emisión">
                                            <span class="cell-content">{{ $invoice->issue_date->format('d-m-Y') }}</span>
                                        </td>
                                        <td data-column="due_date" data-priority="4" data-label="Vencimiento">
                                            <span class="cell-content">{{ $invoice->due_date?->format('d-m-Y') ?? 'N/A' }}</span>
                                        </td>
                                        <td data-column="total_amount" data-priority="5" data-label="Total">
                                            <span class="cell-content">B/. {{ number_format($invoice->total_amount, 2) }}</span>
                                        </td>
                                        <td data-column="amount_paid" data-priority="6" data-label="Pagado">
                                            <span class="cell-content">B/. {{ number_format($invoice->amount_paid, 2) }}</span>
                                        </td>
                                        <td data-column="balance" data-priority="7" data-label="Saldo">
                                            <span class="cell-content font-weight-bold">B/. {{ number_format($invoice->accountsReceivable->balance ?? 0, 2) }}</span>
                                        </td>
                                        <td data-column="status" data-priority="8" data-label="Estado">
                                            <span class="cell-content badge me-1
                                                @if ($invoice->payment_status->value === 'pending')
                                                    bg-warning
                                                @elseif ($invoice->payment_status->value === 'partial')
                                                    bg-info
                                                @elseif ($invoice->payment_status->value === 'paid')
                                                    bg-success
                                                @else
                                                    bg-secondary
                                                @endif
                                            ">
                                                {{ ucfirst($invoice->payment_status->value) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            No hay facturas a crédito registradas
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
