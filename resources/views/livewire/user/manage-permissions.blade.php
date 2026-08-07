<div>
    @if($showModal)
        <div class="modal-overlay" wire:click="closeModal" style="z-index: 10000;">
            <div class="modal-content" wire:click.stop style="max-width: 800px; max-height: 90vh; overflow-y: auto;">
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
                            wire:model.live="search"
                        >
                    </div>

                    <!-- Lista de Permisos -->
                    <div class="permissions-list" style="max-height: 400px; overflow-y: auto;">
                        @forelse($permissions as $permission)
                            <div class="form-check mb-2 p-2 border rounded">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    value="{{ $permission->id }}"
                                    id="permission-{{ $permission->id }}"
                                    wire:model="selectedPermissions"
                                    @if($this->isPermissionFromRole($permission->id) && !$this->isDirectPermission($permission->id))
                                        checked
                                        disabled
                                    @endif
                                >
                                <label class="form-check-label w-100" for="permission-{{ $permission->id }}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $permission->name }}</strong>
                                            @if($permission->description)
                                                <br><small class="text-muted">{{ $permission->description }}</small>
                                            @endif
                                        </div>
                                        <div>
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
</style>
