<div class="page-wrapper">
    <div class="row content">
        @component('components.page-header')
            @slot('title')
                Bancos
            @endslot
            @slot('li_1')
                Módulo de Tesorería
            @endslot
        @endcomponent
        <div class="col-sm-12">
            <div class="card card-table show-entire">
                <div class="card-body">
                    <!-- Table Header -->
                    @component('components.table-header', ['show_create' => auth()->user()->can('banks.create'), 'title' => '', 'li_1' => route('treasury.banks.create')])
                        @slot('filters')
                            <div class="d-flex flex-wrap gap-2">
                                <div class="input-block local-forms mb-0">
                                    <label>{{ __('Estatus') }}</label>
                                    <x-select-input wire:model.live="statusFilter" id="statusFilter" name="status" :options="['active' => 'Activos', 'inactive' => 'Inactivos']" :selected="[]" class="form-select" />
                                </div>
                            </div>
                        @endslot
                    @endcomponent
                    <!-- /Table Header -->

                    <div class="table-responsive">
                        <table class="table border-0 custom-table comman-table mb-0 responsive-table">
                            <thead>
                                <tr>
                                    <th data-column="bank_name" data-priority="1">
                                        <x-table-sort-button title="Banco" columnName="bank_name" :sortField="$sortField" :sortDirection="$sortDirection" />
                                    </th>
                                    <th data-column="account_number" data-priority="2">
                                        <x-table-sort-button title="Número de Cuenta" columnName="account_number" :sortField="$sortField" :sortDirection="$sortDirection" />
                                    </th>
                                    <th data-column="account_type" data-priority="3">
                                        <x-table-sort-button title="Tipo de Cuenta" columnName="account_type" :sortField="$sortField" :sortDirection="$sortDirection" />
                                    </th>
                                    <th data-column="currency" data-priority="4">
                                        <x-table-sort-button title="Moneda" columnName="currency" :sortField="$sortField" :sortDirection="$sortDirection" />
                                    </th>
                                    <th data-column="balance" data-priority="5">
                                        <x-table-sort-button title="Saldo" columnName="balance" :sortField="$sortField" :sortDirection="$sortDirection" />
                                    </th>
                                    <th data-column="status" data-priority="6">
                                        <x-table-sort-button title="Estatus" columnName="status" :sortField="$sortField" :sortDirection="$sortDirection" />
                                    </th>
                                    @canany(['banks.edit', 'banks.delete', 'bank-movements.view'])
                                        <th data-column="acciones" data-priority="7" class="text-end">
                                            <x-table-sort-button title="{{ __('Acciones') }}" columnName="" />
                                        </th>
                                    @endcanany
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $bank)
                                    <tr class="table-row" data-row-id="{{ $bank->id }}">
                                        <td data-column="bank_name" data-priority="1" data-label="Banco">
                                            <span class="cell-content">{{ $bank->bank_name }}</span>
                                        </td>
                                        <td data-column="account_number" data-priority="2" data-label="Número de Cuenta">
                                            <span class="cell-content">{{ $bank->account_number }}</span>
                                        </td>
                                        <td data-column="account_type" data-priority="3" data-label="Tipo de Cuenta">
                                            <span class="cell-content badge me-1 bg-info">
                                                {{ $bank->account_type === 'checking' ? 'Corriente' : 'Ahorros' }}
                                            </span>
                                        </td>
                                        <td data-column="currency" data-priority="4" data-label="Moneda">
                                            <span class="cell-content">{{ $bank->currency }}</span>
                                        </td>
                                        <td data-column="balance" data-priority="5" data-label="Saldo">
                                            <span class="cell-content font-weight-bold">{{ number_format($bank->balance, 2) }}</span>
                                        </td>
                                        <td data-column="status" data-priority="6" data-label="Estatus">
                                            <span class="cell-content badge me-1 {{ $bank->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                                                {{ $bank->status === 'active' ? 'Activo' : 'Inactivo' }}
                                            </span>
                                        </td>
                                        @canany(['banks.edit', 'banks.delete', 'bank-movements.view'])
                                            <td data-column="acciones" data-priority="7" data-label="{{ __('Acciones') }}" class="text-end">
                                                <div class="btn-group btn-group-sm">
                                                    @can('banks.edit')
                                                        <a href="{{ route('treasury.banks.edit', $bank->id) }}" class="btn btn-primary btn-sm" title="{{ __('generic.edit') }}">
                                                            <i class="fa-solid fa-pen-to-square m-r-5"></i>
                                                        </a>
                                                    @endcan
                                                    @can('bank-movements.view')
                                                        <a href="{{ route('treasury.bank-movements.index', $bank->id) }}" class="btn btn-info btn-sm" title="Ver Movimientos">
                                                            <i class="fa-solid fa-arrow-right-arrow-left m-r-5"></i>
                                                        </a>
                                                    @endcan
                                                    @can('banks.view')
                                                        <a href="{{ route('treasury.banks.show', $bank->id) }}" class="btn btn-success btn-sm" title="{{ __('generic.show') }}">
                                                            <i class="fa-solid fa-eye m-r-5"></i>
                                                        </a>
                                                    @endcan
                                                </div>
                                            </td>
                                        @endcanany
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            No hay bancos registrados
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
