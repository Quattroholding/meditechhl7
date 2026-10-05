<div class="page-wrapper">
    <div class="row content">
        @component('components.page-header')
            @slot('title')
                Gestión de Proveedores
            @endslot
            @slot('li_1')
                Módulo Financiero
            @endslot
        @endcomponent
        <div class="col-sm-12">
            <div class="card card-table show-entire">
                <div class="card-body">
                    <!-- Table Header -->
                    @component('components.table-header', ['show_create' => auth()->user()->can('payables.suppliers.manage'), 'title' => '', 'li_1' => '#'])
                        @slot('filters')
                            <div class="d-flex flex-wrap gap-2">
                                <div class="input-block local-forms mb-0">
                                    <label>{{ __('Búsqueda') }}</label>
                                    <input type="text" wire:model.live="search" placeholder="RUC o nombre..." class="form-control" />
                                </div>
                                <div class="input-block local-forms mb-0">
                                    <label>{{ __('Estatus') }}</label>
                                    <x-select-input wire:model.live="status" id="statusFilter" name="status" :options="['active' => 'Activos', 'inactive' => 'Inactivos']" :selected="[]" class="form-select" />
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
                                        <x-table-sort-button title="RUC" columnName="" />
                                    </th>
                                    <th data-column="legal_name" data-priority="2">
                                        <x-table-sort-button title="Razón Social" columnName="" />
                                    </th>
                                    <th data-column="contact_person" data-priority="3">
                                        <x-table-sort-button title="Contacto" columnName="" />
                                    </th>
                                    <th data-column="phone" data-priority="4">
                                        <x-table-sort-button title="Teléfono" columnName="" />
                                    </th>
                                    <th data-column="status" data-priority="5">
                                        <x-table-sort-button title="Estado" columnName="" />
                                    </th>
                                    <th data-column="acciones" data-priority="1" class="text-end">
                                        <x-table-sort-button title="{{ __('Acciones') }}" columnName="" />
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($suppliers as $supplier)
                                    <tr class="table-row" data-row-id="{{ $supplier->id }}">
                                        <td data-column="ruc" data-priority="1" data-label="RUC">
                                            <span class="cell-content">{{ $supplier->ruc }}</span>
                                        </td>
                                        <td data-column="legal_name" data-priority="2" data-label="Razón Social">
                                            <span class="cell-content">{{ $supplier->legal_name }}</span>
                                        </td>
                                        <td data-column="contact_person" data-priority="3" data-label="Contacto">
                                            <span class="cell-content">{{ $supplier->contact_person ?? 'N/A' }}</span>
                                        </td>
                                        <td data-column="phone" data-priority="4" data-label="Teléfono">
                                            <span class="cell-content">{{ $supplier->phone ?? 'N/A' }}</span>
                                        </td>
                                        <td data-column="status" data-priority="5" data-label="Estado">
                                            <span class="cell-content badge me-1 {{ $supplier->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                                                {{ $supplier->status === 'active' ? 'Activo' : 'Inactivo' }}
                                            </span>
                                        </td>
                                        <td data-column="acciones" data-priority="1" data-label="{{ __('Acciones') }}" class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                @can('payables.suppliers.manage')
                                                    <button wire:click="openModal({{ $supplier->id }})" class="btn btn-success btn-sm" title="Editar">
                                                        <i class="fa-solid fa-pen-to-square m-r-5"></i>
                                                    </button>
                                                    <button wire:click="toggleStatus({{ $supplier->id }})" class="btn btn-warning btn-sm" title="{{ $supplier->status === 'active' ? 'Desactivar' : 'Activar' }}">
                                                        <i class="fa-solid fa-{{ $supplier->status === 'active' ? 'ban' : 'check' }} m-r-5"></i>
                                                    </button>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            No hay proveedores registrados
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @include('partials.pagination', ['data' => $suppliers])
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal --}}
@if ($showModal)
    @livewire('finance.supplier.supplier-modal', ['supplier' => $editingSupplier], key('supplier-modal-' . ($editingSupplier?->id ?? 'new')))
@endif
