<div class="page-wrapper">
    <div class="row content">
        @component('components.page-header')
            @slot('title')
                Centros de Costo
            @endslot
            @slot('li_1')
                Módulo Financiero
            @endslot
        @endcomponent
        <div class="col-sm-12">
            <div class="card card-table show-entire">
                <div class="card-body">
                    <!-- Table Header -->
                    @component('components.table-header', ['show_create' => auth()->user()->can('cost-centers.create'), 'title' => '', 'li_1' => route('finance.cost-centers.create')])
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
                                    <th data-column="code" data-priority="1">
                                        <x-table-sort-button title="Código" columnName="code" :sortField="$sortField" :sortDirection="$sortDirection" />
                                    </th>
                                    <th data-column="name" data-priority="2">
                                        <x-table-sort-button title="Nombre" columnName="name" :sortField="$sortField" :sortDirection="$sortDirection" />
                                    </th>
                                    <th data-column="description" data-priority="3">
                                        <x-table-sort-button title="Descripción" columnName="description" :sortField="$sortField" :sortDirection="$sortDirection" />
                                    </th>
                                    <th data-column="status" data-priority="4">
                                        <x-table-sort-button title="Estatus" columnName="status" :sortField="$sortField" :sortDirection="$sortDirection" />
                                    </th>
                                    <th data-column="created_at" data-priority="5">
                                        <x-table-sort-button title="Fecha Creación" columnName="created_at" :sortField="$sortField" :sortDirection="$sortDirection" />
                                    </th>
                                    @canany(['cost-centers.edit', 'cost-centers.delete'])
                                        <th data-column="acciones" data-priority="1" class="text-end">
                                            <x-table-sort-button title="{{ __('Acciones') }}" columnName="" />
                                        </th>
                                    @endcanany
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $costCenter)
                                    <tr class="table-row" data-row-id="{{ $costCenter->id }}">
                                        <td data-column="code" data-priority="1" data-label="Código">
                                            <span class="cell-content">{{ $costCenter->code }}</span>
                                        </td>
                                        <td data-column="name" data-priority="2" data-label="Nombre">
                                            <span class="cell-content">{{ $costCenter->name }}</span>
                                        </td>
                                        <td data-column="description" data-priority="3" data-label="Descripción">
                                            <span class="cell-content">{{ \Illuminate\Support\Str::limit($costCenter->description, 50) }}</span>
                                        </td>
                                        <td data-column="status" data-priority="4" data-label="Estatus">
                                            <span class="cell-content badge me-1 {{ $costCenter->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                                                {{ $costCenter->status === 'active' ? 'Activo' : 'Inactivo' }}
                                            </span>
                                        </td>
                                        <td data-column="created_at" data-priority="5" data-label="Fecha Creación">
                                            <span class="cell-content">{{ \Carbon\Carbon::parse($costCenter->created_at)->format('d-m-Y') }}</span>
                                        </td>
                                        @canany(['cost-centers.edit', 'cost-centers.delete'])
                                            <td data-column="acciones" data-priority="1" data-label="{{ __('Acciones') }}" class="text-end">
                                                <div class="btn-group btn-group-sm">
                                                    @can('cost-centers.edit')
                                                        <a href="{{ route('finance.cost-centers.edit', $costCenter->id) }}" class="btn btn-success btn-sm" title="{{ __('generic.edit') }}">
                                                            <i class="fa-solid fa-pen-to-square m-r-5"></i>
                                                        </a>
                                                    @endcan
                                                </div>
                                            </td>
                                        @endcanany
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            No hay centros de costo registrados
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
