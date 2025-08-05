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
                        <table class="table border-0 custom-table comman-table mb-0">
                            <thead>
                            <tr>
                                <th><x-table-sort-button title="ID" columnName="id" :sortField="$sortField" :sortDirection="$sortDirection"/></th>
                                <th><x-table-sort-button title="NDC Code" columnName="ndc_code" :sortField="$sortField" :sortDirection="$sortDirection"/></th>
                                <th><x-table-sort-button title="Fuente" columnName="source" :sortField="$sortField" :sortDirection="$sortDirection"/></th>
                                <th><x-table-sort-button title="Nombre" columnName="generic_name" :sortField="$sortField" :sortDirection="$sortDirection"/></th>
                                <th><x-table-sort-button title="Narcotico" columnName="narcotic" :sortField="$sortField" :sortDirection="$sortDirection"/></th>
                                <th><x-table-sort-button title="Estado" columnName="status" :sortField="$sortField" :sortDirection="$sortDirection"/></th>
                                <th class="text-end"><x-table-sort-button title="Acciones" columnName="" :sortField="$sortField" :sortDirection="$sortDirection"/></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($data as $medicine)
                                <tr>
                                    <td>{{$medicine->id}}</td>
                                    <td>{{$medicine->ndc_code}}</td>
                                    <td>{{$medicine->source}}</td>
                                    <td>{!! $medicine->full_name !!}</td>
                                    <td>
                                         <span class="badge bg-{{ $medicine->narcotic  ? 'success' : 'danger' }}">
                                            {{ $medicine->narcotic ? 'SI' : 'NO' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $medicine->active  ? 'success' : 'danger' }}">
                                            {{ $medicine->active ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td class="text-end">
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
