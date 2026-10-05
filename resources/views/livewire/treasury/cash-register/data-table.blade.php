<div class="page-wrapper">
    <div class="row content">
        @component('components.page-header')
            @slot('title')
                Cajas
            @endslot
            @slot('li_1')
                Módulo de Tesorería
            @endslot
        @endcomponent
        <div class="col-sm-12">
            <div class="card card-table show-entire">
                <div class="card-body">
                    <!-- Table Header -->
                    @component('components.table-header', ['show_create' => auth()->user()->can('cash-registers.create'), 'title' => '', 'li_1' => route('treasury.cash-registers.create')])
                        @slot('filters')
                            <div class="d-flex flex-wrap gap-2">
                                <div class="input-block local-forms mb-0">
                                    <label>{{ __('Estatus') }}</label>
                                    <x-select-input wire:model.live="statusFilter" id="statusFilter" name="status" :options="['active' => 'Activas', 'inactive' => 'Inactivas']" :selected="[]" class="form-select" />
                                </div>
                            </div>
                        @endslot
                    @endcomponent
                    <!-- /Table Header -->

                    <div class="table-responsive">
                        <table class="table border-0 custom-table comman-table mb-0 responsive-table">
                            <thead>
                                <tr>
                                    <th data-column="name" data-priority="1">
                                        <x-table-sort-button title="Nombre" columnName="name" :sortField="$sortField" :sortDirection="$sortDirection" />
                                    </th>
                                    <th data-column="branch_id" data-priority="2">
                                        <x-table-sort-button title="Sucursal" columnName="branch_id" :sortField="$sortField" :sortDirection="$sortDirection" />
                                    </th>
                                    <th data-column="responsible_user_id" data-priority="3">
                                        <x-table-sort-button title="Responsable" columnName="responsible_user_id" :sortField="$sortField" :sortDirection="$sortDirection" />
                                    </th>
                                    <th data-column="balance" data-priority="4">
                                        <x-table-sort-button title="Saldo" columnName="balance" :sortField="$sortField" :sortDirection="$sortDirection" />
                                    </th>
                                    <th data-column="status" data-priority="5">
                                        <x-table-sort-button title="Estatus" columnName="status" :sortField="$sortField" :sortDirection="$sortDirection" />
                                    </th>
                                    @canany(['cash-registers.edit', 'cash-registers.delete', 'cash-register-movements.view'])
                                        <th data-column="acciones" data-priority="6" class="text-end">
                                            <x-table-sort-button title="{{ __('Acciones') }}" columnName="" />
                                        </th>
                                    @endcanany
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $cashRegister)
                                    <tr class="table-row" data-row-id="{{ $cashRegister->id }}">
                                        <td data-column="name" data-priority="1" data-label="Nombre">
                                            <span class="cell-content">{{ $cashRegister->name }}</span>
                                        </td>
                                        <td data-column="branch_id" data-priority="2" data-label="Sucursal">
                                            <span class="cell-content">{{ $cashRegister->branch?->name ?? 'N/A' }}</span>
                                        </td>
                                        <td data-column="responsible_user_id" data-priority="3" data-label="Responsable">
                                            <span class="cell-content">{{ $cashRegister->responsibleUser?->name ?? 'N/A' }}</span>
                                        </td>
                                        <td data-column="balance" data-priority="4" data-label="Saldo">
                                            <span class="cell-content font-weight-bold">{{ number_format($cashRegister->balance, 2) }}</span>
                                        </td>
                                        <td data-column="status" data-priority="5" data-label="Estatus">
                                            <span class="cell-content badge me-1 {{ $cashRegister->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                                                {{ $cashRegister->status === 'active' ? 'Activa' : 'Inactiva' }}
                                            </span>
                                        </td>
                                        @canany(['cash-registers.edit', 'cash-registers.delete', 'cash-register-movements.view'])
                                            <td data-column="acciones" data-priority="6" data-label="{{ __('Acciones') }}" class="text-end">
                                                <div class="btn-group btn-group-sm">
                                                    @can('cash-registers.edit')
                                                        <a href="{{ route('treasury.cash-registers.edit', $cashRegister->id) }}" class="btn btn-primary btn-sm" title="{{ __('generic.edit') }}">
                                                            <i class="fa-solid fa-pen-to-square m-r-5"></i>
                                                        </a>
                                                    @endcan
                                                    @can('cash-register-movements.view')
                                                        <a href="{{ route('treasury.cash-register-movements.index', $cashRegister->id) }}" class="btn btn-info btn-sm" title="Ver Movimientos">
                                                            <i class="fa-solid fa-arrow-right-arrow-left m-r-5"></i>
                                                        </a>
                                                    @endcan
                                                    @can('cash-registers.view')
                                                        <a href="{{ route('treasury.cash-registers.show', $cashRegister->id) }}" class="btn btn-success btn-sm" title="{{ __('generic.show') }}">
                                                            <i class="fa-solid fa-eye m-r-5"></i>
                                                        </a>
                                                    @endcan
                                                </div>
                                            </td>
                                        @endcanany
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            No hay cajas registradas
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
