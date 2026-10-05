<div class="page-wrapper">
    <div class="row content">
        @component('components.page-header')
            @slot('title')
                Movimientos de Tesorería
            @endslot
            @slot('li_1')
                Módulo de Tesorería
            @endslot
        @endcomponent
        <div class="col-sm-12">
            <div class="card card-table show-entire">
                <div class="card-body">
                    <!-- Table Header -->
                    @component('components.table-header', ['show_create' => auth()->user()->can('treasury-movements.create'), 'title' => '', 'li_1' => route('treasury.movements.create')])
                        @slot('filters')
                            <div class="d-flex flex-wrap gap-2">
                                <div class="input-block local-forms mb-0">
                                    <label>Tipo de Movimiento</label>
                                    <x-select-input wire:model.live="typeFilter" id="typeFilter" name="type" :options="['deposit' => 'Depósito', 'withdrawal' => 'Retiro', 'transfer' => 'Transferencia', 'adjustment' => 'Ajuste']" :selected="[]" class="form-select" />
                                </div>
                                <div class="input-block local-forms mb-0">
                                    <label>Cuenta</label>
                                    <x-select-input wire:model.live="accountFilter" id="accountFilter" name="account" :options="['bank' => 'Banco', 'cash' => 'Caja']" :selected="[]" class="form-select" />
                                </div>
                            </div>
                        @endslot
                    @endcomponent
                    <!-- /Table Header -->

                    <div class="table-responsive">
                        <table class="table border-0 custom-table comman-table mb-0 responsive-table">
                            <thead>
                                <tr>
                                    <th data-column="movement_number" data-priority="1">
                                        <x-table-sort-button title="Número" columnName="movement_number" :sortField="$sortField" :sortDirection="$sortDirection" />
                                    </th>
                                    <th data-column="movement_date" data-priority="2">
                                        <x-table-sort-button title="Fecha" columnName="movement_date" :sortField="$sortField" :sortDirection="$sortDirection" />
                                    </th>
                                    <th data-column="movement_type" data-priority="3">
                                        <x-table-sort-button title="Tipo" columnName="movement_type" :sortField="$sortField" :sortDirection="$sortDirection" />
                                    </th>
                                    <th data-column="account" data-priority="4">
                                        <x-table-sort-button title="Cuenta" columnName="" />
                                    </th>
                                    <th data-column="amount" data-priority="5">
                                        <x-table-sort-button title="Monto" columnName="amount" :sortField="$sortField" :sortDirection="$sortDirection" />
                                    </th>
                                    <th data-column="description" data-priority="6">
                                        <x-table-sort-button title="Descripción" columnName="description" :sortField="$sortField" :sortDirection="$sortDirection" />
                                    </th>
                                    @canany(['treasury-movements.edit', 'treasury-movements.view'])
                                        <th data-column="acciones" data-priority="7" class="text-end">
                                            <x-table-sort-button title="{{ __('Acciones') }}" columnName="" />
                                        </th>
                                    @endcanany
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $movement)
                                    <tr class="table-row" data-row-id="{{ $movement->id }}">
                                        <td data-column="movement_number" data-priority="1" data-label="Número">
                                            <span class="cell-content">{{ $movement->movement_number }}</span>
                                        </td>
                                        <td data-column="movement_date" data-priority="2" data-label="Fecha">
                                            <span class="cell-content">{{ \Carbon\Carbon::parse($movement->movement_date)->format('d-m-Y') }}</span>
                                        </td>
                                        <td data-column="movement_type" data-priority="3" data-label="Tipo">
                                            <span class="cell-content badge me-1
                                                @if($movement->movement_type === 'deposit') bg-success
                                                @elseif($movement->movement_type === 'withdrawal') bg-danger
                                                @elseif($movement->movement_type === 'transfer') bg-info
                                                @else bg-warning
                                                @endif
                                            ">
                                                {{ match($movement->movement_type) {
                                                    'deposit' => 'Depósito',
                                                    'withdrawal' => 'Retiro',
                                                    'transfer' => 'Transferencia',
                                                    'adjustment' => 'Ajuste',
                                                    default => $movement->movement_type
                                                } }}
                                            </span>
                                        </td>
                                        <td data-column="account" data-priority="4" data-label="Cuenta">
                                            <span class="cell-content">
                                                @if($movement->bank_id)
                                                    {{ $movement->bank?->bank_name ?? 'N/A' }} (Banco)
                                                @elseif($movement->cash_register_id)
                                                    {{ $movement->cashRegister?->name ?? 'N/A' }} (Caja)
                                                @else
                                                    N/A
                                                @endif
                                            </span>
                                        </td>
                                        <td data-column="amount" data-priority="5" data-label="Monto">
                                            <span class="cell-content font-weight-bold">{{ number_format($movement->amount, 2) }}</span>
                                        </td>
                                        <td data-column="description" data-priority="6" data-label="Descripción">
                                            <span class="cell-content">{{ \Illuminate\Support\Str::limit($movement->description, 50) }}</span>
                                        </td>
                                        @canany(['treasury-movements.edit', 'treasury-movements.view'])
                                            <td data-column="acciones" data-priority="7" data-label="{{ __('Acciones') }}" class="text-end">
                                                <div class="btn-group btn-group-sm">
                                                    @can('treasury-movements.view')
                                                        <a href="{{ route('treasury.movements.show', $movement->id) }}" class="btn btn-success btn-sm" title="{{ __('generic.show') }}">
                                                            <i class="fa-solid fa-eye m-r-5"></i>
                                                        </a>
                                                    @endcan
                                                    @can('treasury-movements.edit')
                                                        @if(!$movement->journalEntry || !$movement->journalEntry->is_posted)
                                                            <a href="{{ route('treasury.movements.edit', $movement->id) }}" class="btn btn-primary btn-sm" title="{{ __('generic.edit') }}">
                                                                <i class="fa-solid fa-pen-to-square m-r-5"></i>
                                                            </a>
                                                        @endif
                                                    @endcan
                                                </div>
                                            </td>
                                        @endcanany
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            No hay movimientos de tesorería registrados
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
