<div>
    <!-- Table Header -->
    <div class="page-table-header mb-2">
        <div class="row align-items-center">
            <div class="col">
                <div class="doctor-table-blk">
                    <div class="doctor-search-blk">
                        <div class="top-nav-search table-search-blk">
                            <form action="javascript:;">
                                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar..." class="form-control" id="search">
                                <a class="btn"></a>
                            </form>
                        </div>
                        <div class="">
                            <button
                                id="newRoleBtn"
                                class="btn btn-primary submit-form me-2 add-pluss ms-2 py-2"
                                data-bs-toggle="modal"
                                data-bs-target="#roleModal"
                                wire:click="$dispatch('open-role-modal')"
                                title="Nuevo Rol"
                            >
                                <i class="fa fa-plus"></i> Nuevo
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Table Header -->
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
                    <th data-column="users_count" data-priority="3">
                        <x-table-sort-button title="{{__('Usuarios')}}" columnName="" :sortField="$sortField" :sortDirection="$sortDirection"/>
                    </th>
                    <th data-column="permissions_count" data-priority="4">
                        <x-table-sort-button title="{{__('Permisos')}}" columnName="" :sortField="$sortField" :sortDirection="$sortDirection"/>
                    </th>
                    <th data-column="acciones" data-priority="1" class="text-end">
                        <x-table-sort-button title="{{__('Acciones')}}" columnName="" :sortField="$sortField" :sortDirection="$sortDirection"/>
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($roles as $role)
                    <tr class="table-row" data-row-id="{{ $role->id }}">
                        <td data-column="id" data-priority="1" data-label="ID">
                            <span class="row-expand-btn d-none me-2" onclick="toggleRowDetails(this)">
                                <i class="fas fa-plus-circle text-primary" style="cursor: pointer;"></i>
                            </span>
                            <span class="cell-content">{{ $role->id }}</span>
                        </td>
                        <td data-column="name" data-priority="2" data-label="{{__('Nombre')}}">
                            <span class="cell-content">
                                <strong>{{ $role->name }}</strong>
                            </span>
                        </td>
                        <td data-column="users_count" data-priority="3" data-label="{{__('Usuarios')}}">
                            <span class="cell-content">
                                <span class="badge bg-info">{{ $role->users_count }}</span>
                            </span>
                        </td>
                        <td data-column="permissions_count" data-priority="4" data-label="{{__('Permisos')}}">
                            <span class="cell-content">
                                <span class="badge bg-success">{{ $role->permissions->count() }}</span>
                            </span>
                        </td>
                        <td data-column="acciones" data-priority="1" data-label="{{__('Acciones')}}" class="text-end">
                            <button
                                class="btn btn-sm btn-warning"
                                data-bs-toggle="modal"
                                data-bs-target="#roleModal"
                                wire:click="$dispatch('open-role-modal', {roleId: {{ $role->id }}})"
                            >
                                <i class="fas fa-edit"></i>
                            </button>
                            <button
                                class="btn btn-sm btn-danger"
                                wire:click="deleteRole({{ $role->id }})"
                                wire:confirm="¿Estás seguro de eliminar este rol?"
                            >
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <!-- Hidden row for expanded details -->
                    <tr class="row-details d-none" data-parent-row="{{ $role->id }}">
                        <td colspan="5" class="p-3 bg-light">
                            <div class="row-details-content">
                                <!-- Details will be populated by JavaScript -->
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            No se encontraron roles
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>


    @include('partials.pagination',['data'=>$roles])

    <!-- Modal -->
    <livewire:role.modal-save />
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('role-saved', () => {
            $('#roleModal').modal('hide');
            location.reload();
        });

        Livewire.on('open-role-modal', (data) => {
            $('#roleModal').modal('show');
        });

        // Handle modal close event
        $('#roleModal').on('hidden.bs.modal', function () {
            Livewire.dispatch('close-modal');
        });
    });
</script>
@endpush
