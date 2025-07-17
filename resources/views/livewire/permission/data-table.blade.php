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
        <table class="table border-0 custom-table comman-table mb-0">
            <thead>
                <tr>
                    <th><x-table-sort-button title="ID" columnName="id" :sortField="$sortField" :sortDirection="$sortDirection"/></th>
                    <th><x-table-sort-button title="{{__('Nombre')}}" columnName="name" :sortField="$sortField" :sortDirection="$sortDirection"/></th>
                    <th><x-table-sort-button title="{{__('Descripción')}}" columnName="description" :sortField="$sortField" :sortDirection="$sortDirection"/></th>
                    <th><x-table-sort-button title="{{__('Modulo')}}" columnName="module" :sortField="$sortField" :sortDirection="$sortDirection"/></th>
                    <th><x-table-sort-button title="{{__('Roles')}}" columnName="" :sortField="$sortField" :sortDirection="$sortDirection"/></th>
                    <th class="text-end"><x-table-sort-button title="{{__('Acciones')}}" columnName="" :sortField="$sortField" :sortDirection="$sortDirection"/></th>
                </tr>
            </thead>
            <tbody>
                @forelse($permissions as $permission)
                    <tr>
                        <td>{{ $permission->id }}</td>
                        <td>
                            <strong>{{ $permission->name }}</strong>
                        </td>
                        <td>
                            {{ $permission->description ?? 'Sin descripción' }}
                        </td>
                        <td>
                            {{ $permission->module ?? 'Sin modulo' }}
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $permission->roles_count }}</span>
                        </td>
                        <td class="text-end">
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
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            No se encontraron permisos
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
                Mostrando del {{ $permissions->firstItem() }} al {{ $permissions->lastItem() }}
                de {{ $permissions->total() }} resultados
            </p>
        </div>
        <div>
            {{ $permissions->links('vendor.pagination.custom-pagination') }}
        </div>
    </div>

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
