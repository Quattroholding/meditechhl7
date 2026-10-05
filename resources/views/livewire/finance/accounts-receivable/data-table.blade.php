<div class="page-wrapper">
    <div class="row content">
        @component('components.page-header')
            @slot('title')
                Cuentas por Cobrar
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
                                                {{ $receivable->patient?->full_name ?? 'N/A' }}
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
        </div>
    </div>
</div>
