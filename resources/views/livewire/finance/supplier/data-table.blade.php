<div class="col-sm-12">
    <div class="card card-table show-entire">
        <div class="card-body">
            <!-- Table Header -->
            @component('components.table-header', ['show_create' => auth()->user()->can('payables.suppliers.manage'), 'title' => '', 'li_1' => route('finance.payables.suppliers.create')])
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
                            <th data-column="ruc" data-priority="1">
                                <x-table-sort-button title="RUC" columnName="ruc" :sortField="$sortField" :sortDirection="$sortDirection" />
                            </th>
                            <th data-column="legal_name" data-priority="2">
                                <x-table-sort-button title="Razón Social" columnName="legal_name" :sortField="$sortField" :sortDirection="$sortDirection" />
                            </th>
                            <th data-column="commercial_name" data-priority="3">
                                <x-table-sort-button title="Nombre Comercial" columnName="commercial_name" :sortField="$sortField" :sortDirection="$sortDirection" />
                            </th>
                            <th data-column="email" data-priority="4">
                                <x-table-sort-button title="Email" columnName="email" :sortField="$sortField" :sortDirection="$sortDirection" />
                            </th>
                            <th data-column="phone" data-priority="5">
                                <x-table-sort-button title="Teléfono" columnName="phone" :sortField="$sortField" :sortDirection="$sortDirection" />
                            </th>
                            <th data-column="credit_days" data-priority="6">
                                <x-table-sort-button title="Días de Crédito" columnName="credit_days" :sortField="$sortField" :sortDirection="$sortDirection" />
                            </th>
                            <th data-column="status" data-priority="7">
                                <x-table-sort-button title="Estatus" columnName="status" :sortField="$sortField" :sortDirection="$sortDirection" />
                            </th>
                            @canany(['payables.suppliers.manage'])
                                <th data-column="acciones" data-priority="1" class="text-end">
                                    <x-table-sort-button title="{{ __('Acciones') }}" columnName="" />
                                </th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $supplier)
                            <tr class="table-row" data-row-id="{{ $supplier->id }}">
                                <td data-column="ruc" data-priority="1" data-label="RUC">
                                    <span class="cell-content">{{ $supplier->ruc }}-{{ $supplier->dv }}</span>
                                </td>
                                <td data-column="legal_name" data-priority="2" data-label="Razón Social">
                                    <span class="cell-content">{{ $supplier->legal_name }}</span>
                                </td>
                                <td data-column="commercial_name" data-priority="3" data-label="Nombre Comercial">
                                    <span class="cell-content">{{ $supplier->commercial_name ?? 'N/A' }}</span>
                                </td>
                                <td data-column="email" data-priority="4" data-label="Email">
                                    <span class="cell-content">{{ $supplier->email ?? 'N/A' }}</span>
                                </td>
                                <td data-column="phone" data-priority="5" data-label="Teléfono">
                                    <span class="cell-content">{{ $supplier->phone ?? 'N/A' }}</span>
                                </td>
                                <td data-column="credit_days" data-priority="6" data-label="Días de Crédito">
                                    <span class="cell-content">{{ $supplier->credit_days }}</span>
                                </td>
                                <td data-column="status" data-priority="7" data-label="Estatus">
                                    <span class="cell-content badge me-1 {{ $supplier->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                                        {{ $supplier->status === 'active' ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                @canany(['payables.suppliers.manage'])
                                    <td data-column="acciones" data-priority="1" data-label="{{ __('Acciones') }}" class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('finance.payables.suppliers.show', $supplier->id) }}" class="btn btn-info btn-sm" title="Ver">
                                                <i class="fa-solid fa-eye m-r-5"></i>
                                            </a>
                                            <a href="{{ route('finance.payables.suppliers.edit', $supplier->id) }}" class="btn btn-success btn-sm" title="Editar">
                                                <i class="fa-solid fa-pen-to-square m-r-5"></i>
                                            </a>
                                        </div>
                                    </td>
                                @endcanany
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    No hay proveedores registrados
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
