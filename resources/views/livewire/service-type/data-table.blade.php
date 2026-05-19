<div>
    <!-- Filtros y búsqueda -->
    <div class="row mb-3">
        <div class="col-md-4">
            <input type="text" class="form-control" wire:model.live="search"
                   placeholder="Buscar por nombre o descripción...">
        </div>
        <div class="col-md-3">
            <select class="form-control" wire:model.live="statusFilter">
                <option value="all">Todos los estados</option>
                <option value="active">Activo</option>
                <option value="inactive">Inactivo</option>
            </select>
        </div>
        <div class="col-md-2">
            <select class="form-control" wire:model.live="pagination">
                <option value="10">10 por página</option>
                <option value="25">25 por página</option>
                <option value="50">50 por página</option>
                <option value="100">100 por página</option>
            </select>
        </div>
        <div class="col-md-3 text-right">
            @can('service_types.create')
            <button type="button" class="btn btn-primary"
                    wire:click="openCreateModal">
                <i class="fa fa-plus"></i> Nuevo Tipo de Servicio
            </button>
            @endcan
        </div>
    </div>

    <!-- Tabla -->
    <div class="table-responsive">
        <table class="table table-hover table-striped">
            <thead>
                <tr>
                    <th wire:click="sortBy('name')" style="cursor: pointer;">
                        Nombre
                        @if($sortField === 'name')
                            <i class="fa fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th>Descripción</th>
                    <th wire:click="sortBy('base_cost')" style="cursor: pointer;" class="text-right">
                        Costo Base
                        @if($sortField === 'base_cost')
                            <i class="fa fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th>Moneda</th>
                    <th wire:click="sortBy('status')" style="cursor: pointer;">
                        Estado
                        @if($sortField === 'status')
                            <i class="fa fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $serviceType)
                <tr wire:key="service-type-{{ $serviceType->id }}">
                    <td><strong>{{ $serviceType->name }}</strong></td>
                    <td>{{ Str::limit($serviceType->description, 50) }}</td>
                    <td>${{ $serviceType->formatted_cost }}</td>
                    <td>{{ $serviceType->currency }}</td>
                    <td>
                        @if($serviceType->status === 'active')
                            <span class="badge bg-success">Activo</span>
                        @else
                            <span class="badge bg-secondary">Inactivo</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @can('service_types.edit')
                        <button class="btn btn-sm btn-warning"
                                wire:click="openEditModal({{ $serviceType->id }})"
                                title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        @endcan
                        @can('service_types.delete')
                        <button class="btn btn-sm btn-danger"
                                onclick="confirmDelete({{ $serviceType->id }})"
                                title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">No hay tipos de servicio para mostrar</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div class="mt-3">
        {{ $data->links() }}
    </div>

    @push('scripts')
    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción no se puede revertir",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('deleteServiceType', id);
                }
            });
        }
    </script>
    @endpush
</div>
