<div class="">
    <div class="">
        <!-- Table Header -->
        @component('components.table-header',array('show_create'=>false))
            @slot('title')

            @endslot
            @slot('filters')
            <div class="row">
                <div class="col-md-3 ">
                    <div class="input-block local-forms">
                        <label>{{__('Estatus')}}</label>
                        <x-select-input  wire:model.live="statusFilter" name="estatus" :options="\App\Enums\InvoiceStatus::cases()" :selected="[]" class="form-select d-inline-block"/>
                    </div>
                </div>
            </div>
            @endslot
            @slot('li_1')

            @endslot
        @endcomponent
        <!-- /Table Header -->

        <div class="table-responsive">
            <table class="table border-0 custom-table comman-table mb-0 responsive-table">
                <thead>
                <tr>
                    <th data-column="client_invoices.id" data-priority="1">
                        <x-table-sort-button title="ID" columnName="client_invoices.id" :sortField="$sortField" :sortDirection="$sortDirection"/>
                        <span class="expand-control d-none">  <i class="fas fa-plus-circle text-primary"></i></span>
                    </th>
                    <th data-column="invoice_number" data-priority="1">
                        <x-table-sort-button title="N° Factura" columnName="invoice_number" :sortField="$sortField" :sortDirection="$sortDirection"/>
                    </th>
                    @if(auth()->user()->hasRole(['admin', 'contabilidad']))
                        <th data-column="client" data-priority="2">
                            Cliente
                        </th>
                    @endif
                    <th data-column="package" data-priority="3">
                        Plan
                    </th>
                    <th data-column="period" data-priority="4">
                        Período
                    </th>
                    <th data-column="total" data-priority="5">
                        <x-table-sort-button title="Total" columnName="total" :sortField="$sortField" :sortDirection="$sortDirection"/>
                    </th>
                    <th data-column="due_date" data-priority="6">
                        <x-table-sort-button title="Vencimiento" columnName="due_date" :sortField="$sortField" :sortDirection="$sortDirection"/>
                    </th>
                    <th data-column="status" data-priority="7">
                        Estado
                    </th>
                    <th data-column="yappy_pay" data-priority="1" >
                        Pagar con yappy
                    </th>
                    <th data-column="acciones" data-priority="1" class="text-end">
                        Acciones
                    </th>
                </tr>
                </thead>
                <tbody>
                @forelse ($invoices as $invoice)
                    <tr class="table-row" data-row-id="{{ $invoice->id }}">
                        <td data-column="id" data-priority="1" data-label="ID">
                                    <span class="row-expand-btn d-none me-2" onclick="toggleRowDetails(this)">
                                        <i class="fas fa-plus-circle text-primary" style="cursor: pointer;"></i>
                                    </span>
                            <span class="cell-content">{{ $invoice->id}}</span>
                        </td>
                        <td data-column="invoice_number" data-priority="1" data-label="N° Factura">
                            <span class="cell-content">
                                <a href="{{ route('suscriptions.invoices.show', $invoice->id) }}" class="text-primary fw-bold">
                                    {{ $invoice->invoice_number }}
                                </a>
                            </span>
                        </td>
                        @if(auth()->user()->hasRole(['admin', 'contabilidad']))
                            <td data-column="client" data-priority="2" data-label="Cliente">
                                <span class="cell-content">
                                    {{ $invoice->client->name ?? 'N/A' }}
                                </span>
                            </td>
                        @endif
                        <td data-column="package" data-priority="3" data-label="Plan">
                            <span class="cell-content">
                                {{ $invoice->subscription->package->name ?? 'N/A' }}
                            </span>
                        </td>
                        <td data-column="period" data-priority="4" data-label="Período">
                            <span class="cell-content">
                                @if($invoice->period_start && $invoice->period_end)
                                    {{ $invoice->period_start->format('d/m/Y') }} - {{ $invoice->period_end->format('d/m/Y') }}
                                @else
                                    N/A
                                @endif
                            </span>
                        </td>
                        <td data-column="total" data-priority="5" data-label="Total">
                            <span class="cell-content text-primary fw-bold">
                                ${{ number_format($invoice->total, 2) }}
                            </span>
                        </td>
                        <td data-column="due_date" data-priority="6" data-label="Vencimiento">
                            <span class="cell-content">
                                {{ $invoice->due_date ? $invoice->due_date->format('d/m/Y') : 'N/A' }}
                            </span>
                        </td>
                        <td data-column="status" data-priority="7" data-label="Estado">
                            <span class="cell-content">
                                <span class="badge bg-{{ $invoice->status->color() }}">
                                    {{ $invoice->status->label() }}
                                </span>
                            </span>
                        </td>
                        <td data-column="yappy_pay" data-priority="6" data-label="Pagar Con Yappy" class="text-end">
                            @if($invoice->amount_due > 0)
                                <livewire:subscription.pay-invoice-yappy
                                    :invoice="$invoice"
                                    :wire:key="'pay-invoice-'.$invoice->id"
                                />
                            @endif
                        </td>
                        <td data-column="acciones" data-priority="1" data-label="Acciones" >
                            <div class="btn-group btn-group-sm">
                                @can('suscriptions.invoices.show')
                                    <a href="{{ route('suscriptions.invoices.show', $invoice->id) }}"
                                       class="btn btn-info btn-sm"
                                       title="Ver detalles">
                                        <i class="far fa-eye"></i>
                                    </a>
                                @endcan
                                @can('suscriptions.payments.store')
                                    @if($invoice->amount_due > 0)
                                        <button type="button" title="{{__('Registrar Pago')}}"
                                                onclick="Livewire.dispatch('openPaymentModal', { invoiceId: {{ $invoice->id }} })"
                                                class="btn btn-primary btn-sm">
                                            <i class="fas fa-credit-card me-2"></i>
                                        </button>
                                    @endif
                                @endcan
                                @can('suscriptions.invoices.download')
                                    <a href="{{ route('suscriptions.invoices.download', $invoice->id) }}"
                                       target="_blank"
                                       class="btn btn-secondary btn-sm"
                                       title="Descargar PDF">
                                        <i class="fas fa-download"></i>
                                    </a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    <!-- Hidden row for expanded details -->
                    <tr class="row-details d-none" data-parent-row="{{ $invoice->id }}">
                        <td colspan="8" class="p-3 bg-light">
                            <div class="row-details-content">
                                <!-- Details will be populated by JavaScript -->
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ auth()->user()->hasRole(['admin', 'contabilidad']) ? '8' : '7' }}" class="text-center py-4">
                            <div class="d-flex flex-column align-items-center">
                                <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No se encontraron facturas</h5>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @include('partials.pagination',['data'=>$invoices])

    </div>
    <!-- Payment Modal -->
    @livewire('subscription.payment-modal')
</div>
