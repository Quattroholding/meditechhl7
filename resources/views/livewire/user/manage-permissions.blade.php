<div>
    @if($showModal)
        <div class="modal-overlay" wire:click="closeModal" style="z-index: 10000;">
            <div class="modal-content" wire:click.stop style="max-width: 900px; max-height: 90vh; overflow-y: auto;">
                <div class="modal-header">
                    <h5 class="modal-title">Gestionar Permisos - {{ $user->full_name }}</h5>
                    <button wire:click="closeModal" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
                </div>
                <div class="modal-body">
                    <!-- Información del Usuario -->
                    <div class="alert alert-info mb-3">
                        <strong>Usuario:</strong> {{ $user->email }}<br>
                        <strong>Roles:</strong>
                        @foreach($user->roles as $role)
                            <span class="badge bg-primary me-1">{{ $role->name }}</span>
                        @endforeach
                    </div>

                    <div class="alert alert-warning mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        Los permisos marcados con <span class="badge bg-secondary">Desde Rol</span> provienen de los roles asignados y no se pueden quitar aquí.
                        Los permisos marcados con <span class="badge bg-success">Directo</span> son específicos de este usuario.
                    </div>

                    <!-- Buscador -->
                    <div class="mb-3">
                        <input
                            type="text"
                            class="form-control"
                            placeholder="Buscar permiso..."
                            wire:model.live.debounce.300ms="search"
                        >
                    </div>

                    <!-- Resumen de Permisos Seleccionados -->
                    <div class="mb-3">
                        <small class="text-muted">
                            <i class="fas fa-check-circle me-1"></i>
                            <strong>{{ count($selectedPermissions) }}</strong> permisos directos seleccionados
                        </small>
                    </div>

                    <!-- Accordion de Permisos Agrupados por Módulo con Alpine.js -->
                    <div class="permissions-accordion" style="max-height: 500px; overflow-y: auto;">
                        @forelse($groupedPermissions as $module => $permissions)
                            <div class="accordion-item mb-2" x-data="{ open: {{ $loop->first ? 'true' : 'false' }} }">
                                <div class="accordion-header">
                                    <button
                                        type="button"
                                        class="accordion-button-custom"
                                        :class="{ 'active': open }"
                                        @click="open = !open"
                                    >
                                        <div class="d-flex justify-content-between align-items-center w-100">
                                            <div>
                                                <i class="fas" :class="open ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                                                <strong class="ms-2">{{ $moduleDisplayName($module) }}</strong>
                                            </div>
                                            <span class="badge bg-primary">{{ $permissions->count() }}</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="accordion-body-custom" x-show="open" x-collapse>
                                    <div class="p-2">
                                        @foreach($permissions as $permission)
                                            <div class="form-check mb-2 p-2 border rounded permission-item" style="margin: 0 10px">
                                                @if(!$this->isPermissionFromRole($permission->id))
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    value="{{ (int)$permission->id }}"
                                                    id="permission-{{ $permission->id }}"
                                                    wire:model.live="selectedPermissions"
                                                    @if($this->isPermissionFromRole($permission->id) && !$this->isDirectPermission($permission->id))
                                                        checked
                                                        disabled
                                                    @endif
                                                >
                                                @endif
                                                <label class="form-check-label w-100" for="permission-{{ $permission->id }}">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div style="flex: 1;">
                                                            <strong>{{ $permission->name }}</strong>
                                                            @if($permission->description)
                                                                <br><small class="text-muted">{{ $permission->description }}</small>
                                                            @endif
                                                        </div>
                                                        <div class="ms-2" style="white-space: nowrap;">
                                                            @if($this->isPermissionFromRole($permission->id) && !$this->isDirectPermission($permission->id))
                                                                <span class="badge bg-secondary">Desde Rol</span>
                                                            @endif
                                                            @if($this->isDirectPermission($permission->id))
                                                                <span class="badge bg-success">Directo</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="alert alert-info">
                                No se encontraron permisos que coincidan con tu búsqueda.
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeModal">Cancelar</button>
                    <button type="button" class="btn btn-primary" wire:click="save">
                        <i class="fas fa-save me-2"></i>Guardar Permisos
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('showToastrManagePermissions', (event) => {
            toastr[event.type](event.message, '', {
                closeButton: true,
                progressBar: true,
                positionClass: 'toast-top-right',
                timeOut: 5000,
            });
        });
    });

</script>
<style>
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
}

.modal-content {
    background-color: white;
    border-radius: 8px;
    width: 90%;
    max-width: 600px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.modal-header {
    padding: 1rem;
    border-bottom: 1px solid #dee2e6;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-body {
    padding: 1rem;
}

.modal-footer {
    padding: 1rem;
    border-top: 1px solid #dee2e6;
    display: flex;
    justify-content: flex-end;
    gap: 0.5rem;
}

.accordion-item {
    border: 1px solid #dee2e6;
    border-radius: 6px;
    overflow: hidden;
}

.accordion-button-custom {
    width: 100%;
    padding: 0.75rem 1rem;
    background-color: #f8f9fa;
    border: none;
    cursor: pointer;
    transition: background-color 0.2s;
    text-align: left;
}

.accordion-button-custom:hover {
    background-color: #e9ecef;
}

.accordion-button-custom.active {
    background-color: #e7f1ff;
    color: #0d6efd;
}

.accordion-body-custom {
    border-top: 1px solid #dee2e6;
    background-color: #fff;
}

.permission-item {
    transition: background-color 0.2s;
}

.permission-item:hover {
    background-color: #f8f9fa;
}
</style>
