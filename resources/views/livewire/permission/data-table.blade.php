<div>
    <div class="page-table-header mb-2">
        <div class="row align-items-center">
            <div class="col">
                <div class="doctor-table-blk">
                    <div class="doctor-search-blk">
                        <div class="top-nav-search table-search-blk">
                            <form action="javascript:;">
                                <input type="text"  wire:model.live.debounce.300ms="search" placeholder="Buscar..." class="form-control" id="search">
                                <a class="btn">{{--}}<img src="{{ URL::asset('/assets/img/icons/search-normal.svg') }}"  alt="">{{--}}</a>
                            </form>
                        </div>
                        <div class="">
                            <a class="btn btn-primary submit-form me-2 add-pluss ms-2 py-2"
                                data-bs-toggle="modal"
                                data-bs-target="#permissionModal"
                                wire:click="$dispatch('open-permission-modal')" >
                                <i class="fa fa-plus" alt="{{__('generic.new')}}"></i> {{__('generic.new')}}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table border-0 custom-table comman-table mb-0 responsive-table">
            <thead>
                <tr>
                    <th data-column="id" data-priority="1">
                        <x-table-sort-button title="ID" columnName="id" :sortField="$sortField" :sortDirection="$sortDirection"/>
                    </th>
                    <th data-column="name" data-priority="2">
                        <x-table-sort-button title="{{__('Nombre')}}" columnName="name" :sortField="$sortField" :sortDirection="$sortDirection"/>
                    </th>
                    <th data-column="description" data-priority="3">
                        <x-table-sort-button title="{{__('Descripción')}}" columnName="description" :sortField="$sortField" :sortDirection="$sortDirection"/>
                    </th>
                    <th data-column="module" data-priority="4">
                        <x-table-sort-button title="{{__('Modulo')}}" columnName="module" :sortField="$sortField" :sortDirection="$sortDirection"/>
                    </th>
                    <th data-column="roles" data-priority="5">
                        <x-table-sort-button title="{{__('Roles')}}" columnName="" :sortField="$sortField" :sortDirection="$sortDirection"/>
                    </th>
                    <th data-column="acciones" data-priority="1" class="text-end">
                        <x-table-sort-button title="{{__('Acciones')}}" columnName="" :sortField="$sortField" :sortDirection="$sortDirection"/>
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($permissions as $permission)
                    <tr class="table-row" data-row-id="{{ $permission->id }}">
                        <td data-column="id" data-priority="1" data-label="ID">
                            <span class="row-expand-btn d-none me-2" onclick="toggleRowDetails(this)">
                                <i class="fas fa-plus-circle text-primary" style="cursor: pointer;"></i>
                            </span>
                            <span class="cell-content">{{ $permission->id }}</span>
                        </td>
                        <td data-column="name" data-priority="2" data-label="{{__('Nombre')}}">
                            <span class="cell-content">
                                <strong>{{ $permission->name }}</strong>
                            </span>
                        </td>
                        <td data-column="description" data-priority="3" data-label="{{__('Descripción')}}">
                            <span class="cell-content">
                                {{ $permission->description ?? 'Sin descripción' }}
                            </span>
                        </td>
                        <td data-column="module" data-priority="4" data-label="{{__('Modulo')}}">
                            <span class="cell-content">
                                {{ $permission->module ?? 'Sin modulo' }}
                            </span>
                        </td>
                        <td data-column="roles" data-priority="5" data-label="{{__('Roles')}}">
                            <span class="cell-content">
                                <span class="badge bg-info">{{ $permission->roles_count }}</span>
                            </span>
                        </td>
                        <td data-column="acciones" data-priority="1" data-label="{{__('Acciones')}}" class="text-end">
                            <button
                                class="btn btn-sm btn-warning"
                                data-bs-toggle="modal"
                                data-bs-target="#permissionModal"
                                wire:click="$dispatch('open-permission-modal', {permissionId: {{ $permission->id }}})"
                            >
                                <i class="fas fa-edit"></i>
                            </button>
                            <button
                                class="btn btn-sm btn-danger"
                                wire:click="deletePermission({{ $permission->id }})"
                                wire:confirm="¿Estás seguro de eliminar este permiso?"
                            >
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <!-- Hidden row for expanded details -->
                    <tr class="row-details d-none" data-parent-row="{{ $permission->id }}">
                        <td colspan="6" class="p-3 bg-light">
                            <div class="row-details-content">
                                <!-- Details will be populated by JavaScript -->
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">
                            No se encontraron permisos
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @include('partials.pagination',['data'=>$permissions])
    <!-- Modal -->
    <livewire:permission.modal-save/>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('permission-saved', () => {
            $('#permissionModal').modal('hide');
            location.reload();
        });

        Livewire.on('open-permission-modal', (data) => {
            $('#permissionModal').modal('show');
        });

        // Handle modal close event
        $('#permissionModal').on('hidden.bs.modal', function () {
            Livewire.dispatch('close-modal');
        });
    });
</script>
@endpush
