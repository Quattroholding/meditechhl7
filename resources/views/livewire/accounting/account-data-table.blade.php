<div class="page-wrapper">
    <div class="row content">
        @component('components.page-header')
            @slot('title')
                Plan de Cuentas
            @endslot
            @slot('li_1')
                Contabilidad
            @endslot
        @endcomponent
        <div class="col-sm-12">
            <div class="card card-table show-entire">
                <div class="card-body">
                    <!-- Table Header -->
                    @component('components.table-header', ['show_create' => auth()->user()->can('accounting.accounts.manage'), 'title' => '', 'li_1' => '#'])
                        @slot('filters')
                            <div class="d-flex flex-wrap gap-2">
                                <div class="input-block local-forms mb-0">
                                    <label>{{ __('Tipo') }}</label>
                                    <x-select-input wire:model.live="typeFilter" id="typeFilter" name="type" :options="['asset' => 'Activo', 'liability' => 'Pasivo', 'equity' => 'Patrimonio', 'income' => 'Ingreso', 'expense' => 'Gasto', 'cost' => 'Costo']" :selected="[]" class="form-select" />
                                </div>
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
                                    <th data-column="code" data-priority="1">
                                        <x-table-sort-button title="Código" columnName="code" :sortField="$sortField" :sortDirection="$sortDirection" />
                                    </th>
                                    <th data-column="name" data-priority="2">
                                        <x-table-sort-button title="Nombre" columnName="name" :sortField="$sortField" :sortDirection="$sortDirection" />
                                    </th>
                                    <th data-column="account_type" data-priority="3">
                                        <x-table-sort-button title="Tipo" columnName="account_type" :sortField="$sortField" :sortDirection="$sortDirection" />
                                    </th>
                                    <th data-column="level" data-priority="4">
                                        <x-table-sort-button title="Nivel" columnName="level" :sortField="$sortField" :sortDirection="$sortDirection" />
                                    </th>
                                    <th data-column="allows_transaction" data-priority="5">
                                        <x-table-sort-button title="Permite Movimientos" columnName="allows_transaction" :sortField="$sortField" :sortDirection="$sortDirection" />
                                    </th>
                                    <th data-column="status" data-priority="6">
                                        <x-table-sort-button title="Estatus" columnName="status" :sortField="$sortField" :sortDirection="$sortDirection" />
                                    </th>
                                    @canany(['accounting.accounts.manage'])
                                        <th data-column="acciones" data-priority="1" class="text-end">
                                            <x-table-sort-button title="{{ __('Acciones') }}" columnName="" />
                                        </th>
                                    @endcanany
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $account)
                                    <tr class="table-row" data-row-id="{{ $account->id }}">
                                        <td data-column="code" data-priority="1" data-label="Código">
                                            <span class="cell-content">{{ $account->code }}</span>
                                        </td>
                                        <td data-column="name" data-priority="2" data-label="Nombre">
                                            <span class="cell-content" style="padding-left: {{ ($account->level - 1) * 20 }}px;">
                                                <i class="fas fa-chart-line me-2"></i>{{ $account->name }}
                                            </span>
                                        </td>
                                        <td data-column="account_type" data-priority="3" data-label="Tipo">
                                            <span class="cell-content">
                                                @php
                                                    $typeLabels = [
                                                        'asset' => 'Activo',
                                                        'liability' => 'Pasivo',
                                                        'equity' => 'Patrimonio',
                                                        'income' => 'Ingreso',
                                                        'expense' => 'Gasto',
                                                        'cost' => 'Costo',
                                                    ];
                                                @endphp
                                                {{ $typeLabels[$account->account_type] ?? $account->account_type }}
                                            </span>
                                        </td>
                                        <td data-column="level" data-priority="4" data-label="Nivel">
                                            <span class="cell-content">{{ $account->level }}</span>
                                        </td>
                                        <td data-column="allows_transaction" data-priority="5" data-label="Permite Movimientos">
                                            <span class="cell-content badge me-1 {{ $account->allows_transaction ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $account->allows_transaction ? 'Sí' : 'No' }}
                                            </span>
                                        </td>
                                        <td data-column="status" data-priority="6" data-label="Estatus">
                                            <span class="cell-content badge me-1 {{ $account->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                                                {{ $account->status === 'active' ? 'Activa' : 'Inactiva' }}
                                            </span>
                                        </td>
                                        @canany(['accounting.accounts.manage'])
                                            <td data-column="acciones" data-priority="1" data-label="{{ __('Acciones') }}" class="text-end">
                                                <div class="btn-group btn-group-sm">
                                                    <a href="javascript:;" class="btn btn-success btn-sm" title="Editar">
                                                        <i class="fa-solid fa-pen-to-square m-r-5"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        @endcanany
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            No hay cuentas registradas
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
