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
        <table class="table border-0 custom-table comman-table mb-0">
            <thead>
                <tr>
                    <th><x-table-sort-button title="ID" columnName="id" :sortField="$sortField" :sortDirection="$sortDirection"/></th>
                    <th><x-table-sort-button title="{{__('Nombre')}}" columnName="name" :sortField="$sortField" :sortDirection="$sortDirection"/></th>
                    <th><x-table-sort-button title="{{__('Usuarios')}}" columnName="" :sortField="$sortField" :sortDirection="$sortDirection"/></th>
                    <th><x-table-sort-button title="{{__('Permisos')}}" columnName="" :sortField="$sortField" :sortDirection="$sortDirection"/></th>
                    <th class="text-end"><x-table-sort-button title="{{__('Acciones')}}" columnName="" :sortField="$sortField" :sortDirection="$sortDirection"/></th>
                </tr>
            </thead>
            <tbody>
                @forelse($roles as $role)
                    <tr>
                        <td>{{ $role->id }}</td>
                        <td>
                            <strong>{{ $role->name }}</strong>
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $role->users_count }}</span>
                        </td>
                        <td>
                            <span class="badge bg-success">{{ $role->permissions->count() }}</span>
                        </td>
                        <td  class="text-end">
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


    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div>
            <p class="text-muted mb-0">
                Mostrando del {{ $roles->firstItem() }} al {{ $roles->lastItem() }}
                de {{ $roles->total() }} resultados
            </p>
        </div>
        <div>
            {{ $roles->links('vendor.pagination.custom-pagination') }}
        </div>
    </div>

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
