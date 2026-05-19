<div>
    <div class="row">
        <div class="col-sm-12">
            <div class="card card-table show-entire">
                <div class="card-body">
                    <!-- Table Header -->
                    @component('components.table-header',['show_create'=>auth()->user()->can('medicines.create')])
                        @slot('title')

                        @endslot
                        @slot('li_1')
                            {{ route('medicine.create') }}
                        @endslot
                    @endcomponent
                    <!-- /Table Header -->

                    <livewire:medicine.modal-save />

                    <div class="table-responsive">
                        <table class="table border-0 custom-table comman-table mb-0 responsive-table">
                            <thead>
                            <tr>
                                <th data-column="id" data-priority="1">
                                    <x-table-sort-button title="ID" columnName="id" :sortField="$sortField" :sortDirection="$sortDirection"/>
                                </th>
                                <th data-column="code" data-priority="2">
                                    <x-table-sort-button title="Código" columnName="code" :sortField="$sortField" :sortDirection="$sortDirection"/>
                                </th>
                                <th data-column="code_system" data-priority="3">
                                    <x-table-sort-button title="Fuente" columnName="code_system" :sortField="$sortField" :sortDirection="$sortDirection"/>
                                </th>
                                <th data-column="generic_name" data-priority="4">
                                    <x-table-sort-button title="Nombre" columnName="generic_name" :sortField="$sortField" :sortDirection="$sortDirection"/>
                                </th>
                                <th data-column="form" data-priority="5">
                                    <x-table-sort-button title="Forma" columnName="form" :sortField="$sortField" :sortDirection="$sortDirection"/>
                                </th>
                                <th data-column="manufacturer" data-priority="6">
                                    <x-table-sort-button title="Fabricante" columnName="manufacturer" :sortField="$sortField" :sortDirection="$sortDirection"/>
                                </th>
                                <th data-column="status" data-priority="7">
                                    <x-table-sort-button title="Estado" columnName="status" :sortField="$sortField" :sortDirection="$sortDirection"/>
                                </th>
                                <th data-column="acciones" data-priority="1" class="text-end">
                                    <x-table-sort-button title="Acciones" columnName="" :sortField="$sortField" :sortDirection="$sortDirection"/>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($data as $medication)
                                <tr class="table-row" data-row-id="{{ $medication->id }}">
                                    <td data-column="id" data-priority="1" data-label="ID">
                                        <span class="row-expand-btn d-none me-2" onclick="toggleRowDetails(this)">
                                            <i class="fas fa-plus-circle text-primary" style="cursor: pointer;"></i>
                                        </span>
                                        <span class="cell-content">{{ $medication->id }}</span>
                                    </td>
                                    <td data-column="code" data-priority="2" data-label="Código">
                                        <span class="cell-content">{{ $medication->code }}</span>
                                    </td>
                                    <td data-column="code_system" data-priority="3" data-label="Fuente">
                                        <span class="cell-content">
                                            <span class="badge bg-{{ $medication->code_system === 'CUSTOM' ? 'info' : 'secondary' }}">
                                                {{ $medication->code_system }}
                                            </span>
                                        </span>
                                    </td>
                                    <td data-column="generic_name" data-priority="4" data-label="Nombre">
                                        <span class="cell-content">
                                            <strong>{{ $medication->display }}</strong>
                                            @if($medication->ingredients->count() > 0)
                                                <br>
                                                <small class="text-muted">
                                                    @foreach($medication->ingredients as $ingredient)
                                                        {{ $ingredient->substance_display }}
                                                        @if($ingredient->strength_value)
                                                            ({{ $ingredient->strength_value }} {{ $ingredient->strength_unit }})
                                                        @endif
                                                        @if(!$loop->last), @endif
                                                    @endforeach
                                                </small>
                                            @endif
                                        </span>
                                    </td>
                                    <td data-column="form" data-priority="5" data-label="Forma">
                                        <span class="cell-content">{{ $medication->form }}</span>
                                    </td>
                                    <td data-column="manufacturer" data-priority="6" data-label="Fabricante">
                                        <span class="cell-content">{{ $medication->manufacturer }}</span>
                                    </td>
                                    <td data-column="status" data-priority="7" data-label="Estado">
                                        <span class="cell-content">
                                            <span class="badge bg-{{ $medication->status === 'active' ? 'success' : 'danger' }}">
                                                {{ $medication->status === 'active' ? 'Activo' : 'Inactivo' }}
                                            </span>
                                        </span>
                                    </td>
                                    <td data-column="acciones" data-priority="1" data-label="Acciones" class="text-end">
                                        @can('update', $medication)
                                            <a href="{{ route('medicine.edit', $medication->id) }}" class="btn btn-success btn-sm" title="Editar">
                                                <i class="fa-solid fa-pen-to-square m-r-5"></i>
                                            </a>
                                        @endcan
                                        @can('delete', $medication)
                                            <a href="javascript:;"
                                               onclick="confirm('¿Estás seguro de eliminar este medicamento?\n\nEsta acción no se puede deshacer.') || event.stopImmediatePropagation()"
                                               wire:click="$dispatch('deleteMedication', { id: {{ $medication->id }} })"
                                               class="btn btn-danger btn-sm"
                                               title="Eliminar">
                                                <i class="fa fa-trash-alt m-r-5"></i>
                                            </a>
                                        @endcan
                                    </td>
                                </tr>
                                <!-- Hidden row for expanded details -->
                                <tr class="row-details d-none" data-parent-row="{{ $medication->id }}">
                                    <td colspan="8" class="p-3 bg-light">
                                        <div class="row-details-content">
                                            <!-- Details will be populated by JavaScript -->
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    @include('partials.pagination',['data'=>$data])
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('showToastrDeleteMEdication', (event) => {
                toastr[event.type](event.message, '', {
                    closeButton: true,
                    progressBar: true,
                    positionClass: 'toast-top-right',
                    timeOut: 5000,
                });
            });
        });
    </script>
</div>
