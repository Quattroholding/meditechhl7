<div class="page-wrapper">
    <div class="row content">
        @component('components.page-header')
            @slot('title')
                Módulo Financiero
            @endslot
            @slot('li_1')
                Estado de CxC
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
                                    <x-select-input wire:model.live="status" id="statusFilter" name="status" :options="['pending' => 'Pendiente', 'partial' => 'Parcial', 'paid' => 'Pagada', 'overdue' => 'Vencida', 'cancelled' => 'Cancelada']" :selected="[]" class="form-select" />
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
                                    <th data-column="invoice_date" data-priority="3">
                                        <x-table-sort-button title="Fecha" columnName="" />
                                    </th>
                                    <th data-column="due_date" data-priority="4">
                                        <x-table-sort-button title="Vencimiento" columnName="" />
                                    </th>
                                    <th data-column="original_amount" data-priority="5">
                                        <x-table-sort-button title="Total" columnName="" />
                                    </th>
                                    <th data-column="paid_amount" data-priority="6">
                                        <x-table-sort-button title="Pagado" columnName="" />
                                    </th>
                                    <th data-column="balance" data-priority="7">
                                        <x-table-sort-button title="Saldo" columnName="" />
                                    </th>
                                    <th data-column="status" data-priority="8">
                                        <x-table-sort-button title="Estado" columnName="" />
                                    </th>
                                    <th data-column="acciones" data-priority="1" class="text-end">
                                        <x-table-sort-button title="{{ __('Acciones') }}" columnName="" />
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($receivables as $receivable)
                                    <tr class="table-row" data-row-id="{{ $receivable->id }}">
                                        <td data-column="invoice_number" data-priority="1" data-label="Factura #">
                                            <span class="cell-content">{{ $receivable->invoice_number }}</span>
                                        </td>
                                        <td data-column="patient" data-priority="2" data-label="Paciente">
                                            <span class="cell-content">{{ $receivable->patient->full_name ?? 'N/A' }}</span>
                                        </td>
                                        <td data-column="invoice_date" data-priority="3" data-label="Fecha">
                                            <span class="cell-content">{{ $receivable->invoice_date->format('d-m-Y') }}</span>
                                        </td>
                                        <td data-column="due_date" data-priority="4" data-label="Vencimiento">
                                            <span class="cell-content">{{ $receivable->due_date->format('d-m-Y') }}</span>
                                        </td>
                                        <td data-column="original_amount" data-priority="5" data-label="Total">
                                            <span class="cell-content">B/. {{ number_format($receivable->original_amount, 2) }}</span>
                                        </td>
                                        <td data-column="paid_amount" data-priority="6" data-label="Pagado">
                                            <span class="cell-content">B/. {{ number_format($receivable->paid_amount, 2) }}</span>
                                        </td>
                                        <td data-column="balance" data-priority="7" data-label="Saldo">
                                            <span class="cell-content font-weight-bold">B/. {{ number_format($receivable->balance, 2) }}</span>
                                        </td>
                                        <td data-column="status" data-priority="8" data-label="Estado">
                                            <span class="cell-content badge me-1
                                                @switch($receivable->status->value)
                                                    @case('pending') bg-warning @break
                                                    @case('partial') bg-info @break
                                                    @case('paid') bg-success @break
                                                    @case('overdue') bg-danger @break
                                                    @case('cancelled') bg-secondary @break
                                                @endswitch
                                            ">
                                                {{ match($receivable->status->value) {
                                                    'pending' => 'Pendiente',
                                                    'partial' => 'Parcial',
                                                    'paid' => 'Pagada',
                                                    'overdue' => 'Vencida',
                                                    'cancelled' => 'Cancelada',
                                                    default => $receivable->status->value
                                                } }}
                                            </span>
                                        </td>
                                        <td data-column="acciones" data-priority="1" data-label="{{ __('Acciones') }}" class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                @if ($receivable->status->value !== 'paid' && $receivable->status->value !== 'cancelled')
                                                    <button wire:click="openPaymentModal({{ $receivable->id }})" class="btn btn-success btn-sm" title="Aplicar Pago">
                                                        <i class="fa-solid fa-money-bill m-r-5"></i>
                                                    </button>
                                                @endif
                                                @if ($receivable->status->value !== 'paid')
                                                    <button wire:click="cancel({{ $receivable->id }})" class="btn btn-danger btn-sm" title="Cancelar">
                                                        <i class="fa-solid fa-ban m-r-5"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            No hay cuentas por cobrar registradas
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @include('partials.pagination', ['data' => $receivables])
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Payment Modal --}}
@if ($showPaymentModal && $selectedReceivable)
    @livewire('finance.accounts-receivable.payment-application-modal', ['receivable' => $selectedReceivable], key('payment-modal-' . $selectedReceivable->id))
@endif
