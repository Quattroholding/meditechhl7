<div>
    <div class="row">
        <div class="col-sm-12">
            <div class="card card-table show-entire">
                <div class="card-body">
                    <!-- Table Header -->
                    @component('components.table-header')
                        @slot('title')

                        @endslot
                        @slot('li_1')
                            {{ route('medicine.create') }}
                        @endslot
                    @endcomponent
                    <!-- /Table Header -->

                    <livewire:medicine.modal-save wire:model="showModal"/>

                    <div class="table-responsive">
                        <table class="table border-0 custom-table comman-table mb-0 responsive-table">
                            <thead>
                            <tr>
                                <th data-column="id" data-priority="1">
                                    <x-table-sort-button title="ID" columnName="id" :sortField="$sortField" :sortDirection="$sortDirection"/>
                                </th>
                                <th data-column="ndc_code" data-priority="2">
                                    <x-table-sort-button title="NDC Code" columnName="ndc_code" :sortField="$sortField" :sortDirection="$sortDirection"/>
                                </th>
                                <th data-column="source" data-priority="3">
                                    <x-table-sort-button title="Fuente" columnName="source" :sortField="$sortField" :sortDirection="$sortDirection"/>
                                </th>
                                <th data-column="generic_name" data-priority="4">
                                    <x-table-sort-button title="Nombre" columnName="generic_name" :sortField="$sortField" :sortDirection="$sortDirection"/>
                                </th>
                                <th data-column="narcotic" data-priority="5">
                                    <x-table-sort-button title="Narcotico" columnName="narcotic" :sortField="$sortField" :sortDirection="$sortDirection"/>
                                </th>
                                <th data-column="status" data-priority="6">
                                    <x-table-sort-button title="Estado" columnName="status" :sortField="$sortField" :sortDirection="$sortDirection"/>
                                </th>
                                <th data-column="acciones" data-priority="1" class="text-end">
                                    <x-table-sort-button title="Acciones" columnName="" :sortField="$sortField" :sortDirection="$sortDirection"/>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($data as $medicine)
                                <tr class="table-row" data-row-id="{{ $medicine->id }}">
                                    <td data-column="id" data-priority="1" data-label="ID">
                                        <span class="row-expand-btn d-none me-2" onclick="toggleRowDetails(this)">
                                            <i class="fas fa-plus-circle text-primary" style="cursor: pointer;"></i>
                                        </span>
                                        <span class="cell-content">{{$medicine->id}}</span>
                                    </td>
                                    <td data-column="ndc_code" data-priority="2" data-label="NDC Code">
                                        <span class="cell-content">{{$medicine->ndc_code}}</span>
                                    </td>
                                    <td data-column="source" data-priority="3" data-label="Fuente">
                                        <span class="cell-content">{{$medicine->source}}</span>
                                    </td>
                                    <td data-column="generic_name" data-priority="4" data-label="Nombre">
                                        <span class="cell-content">{!! $medicine->full_name !!}</span>
                                    </td>
                                    <td data-column="narcotic" data-priority="5" data-label="Narcotico">
                                        <span class="cell-content">
                                            <span class="badge bg-{{ $medicine->narcotic  ? 'success' : 'danger' }}">
                                                {{ $medicine->narcotic ? 'SI' : 'NO' }}
                                            </span>
                                        </span>
                                    </td>
                                    <td data-column="status" data-priority="6" data-label="Estado">
                                        <span class="cell-content">
                                            <span class="badge bg-{{ $medicine->active  ? 'success' : 'danger' }}">
                                                {{ $medicine->active ? 'Activo' : 'Inactivo' }}
                                            </span>
                                        </span>
                                    </td>
                                    <td data-column="acciones" data-priority="1" data-label="Acciones" class="text-end">
                                        @if($medicine->source=='CUSTOM')
                                            <div class="btn-group btn-group-sm">
                                                <a wire:click="openModal({{ $medicine->id }})" class="btn btn-success btn-sm" title="Editar">
                                                    <i class="fa-solid fa-pen-to-square m-r-5"></i>
                                                </a>
                                                <a href="javascript:;" onclick="confirm('¿Estás seguro de eliminar este medicamento?') || event.stopImmediatePropagation()" wire:click="deleteMedicine({{ $medicine->id }})" class="btn btn-danger btn-sm" title="Eliminar">
                                                    <i class="fa fa-trash-alt m-r-5"></i>
                                                </a>
                                            </div>
                                        @endif
                                        {{--}}<div class="dropdown dropdown-action">
                                            <a href="javascript:;" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fa fa-ellipsis-v"></i>
                                            </a>
                                                                                        <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item" wire:click="openModal({{ $medicine->id }})">
                                                    <i class="fa-solid fa-pen-to-square m-r-5"></i>
                                                    Editar
                                                </a>
                                                <a class="dropdown-item" href="javascript:;" onclick="confirm('¿Estás seguro de eliminar este medicamento?') || event.stopImmediatePropagation()" wire:click="deleteMedicine({{ $medicine->id }})">
                                                    <i class="fa fa-trash-alt m-r-5"></i>
                                                    Eliminar
                                                </a>
                                            </div>
                                        </div>{{--}}
                                    </td>
                                </tr>
                                <!-- Hidden row for expanded details -->
                                <tr class="row-details d-none" data-parent-row="{{ $medicine->id }}">
                                    <td colspan="7" class="p-3 bg-light">
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
</div>
