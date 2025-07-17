<div>
    @if($showModal)
    <div class="modal-overlay" wire:click="closeModal" style="z-index: 10000;">
        <div class="modal-content" wire:click.stop>
            <div class="modal-header">
                <h5 class="modal-title">{{ $isEdit ? 'Editar Rol' : 'Nuevo Rol' }}</h5>
                <button wire:click="closeModal" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
            </div>
            <form wire:submit.prevent="save">
                <div class="mb-3">
                    <label for="name" class="form-label">Nombre del Rol</label>
                    <input
                        type="text"
                        class="form-control @error('name') is-invalid @enderror"
                        id="name"
                        wire:model="name"
                        placeholder="Ingrese el nombre del rol"
                    >
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Permisos por Módulo</label>
                    <div class="permissions-container">
                        @foreach($permissionsByModule as $module => $permissions)
                            <div class="card mb-3">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div  style="color: #fff;">
                                            <i class="fas fa-layer-group me-2"></i>
                                            <strong>{{ ucfirst($module) }}</strong>
                                            <span class="badge bg-primary ms-2">{{ $permissions->count() }}</span>
                                        </div>
                                        <div class="form-check">
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                id="selectAll{{ $loop->index }}"
                                                onchange="toggleModulePermissions('{{ $module }}', this.checked)"
                                            >
                                            <label class="form-check-label fw-bold" for="selectAll{{ $loop->index }}" style="color: #fff;">
                                                Seleccionar todos
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @foreach($permissions as $permission)
                                            <div class="col-md-6 mb-2">
                                                <div class="form-check">
                                                    <input
                                                        class="form-check-input module-{{ $module }}"
                                                        type="checkbox"
                                                        value="{{ $permission->id }}"
                                                        id="permission_{{ $permission->id }}"
                                                        wire:model="selectedPermissions"
                                                    >
                                                    <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                        {{--}}
                                                        <strong>{{ $permission->name }}</strong>
                                                        {{--}}
                                                        @if($permission->description)
                                                            <small class="text-muted d-block">{{ $permission->description }}</small>
                                                        @endif
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @error('selectedPermissions')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div style="margin-top: 30px; display: flex; gap: 15px;">
                    <button type="button" class="btn btn-secondary" wire:click="closeModal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        {{ $isEdit ? 'Actualizar' : 'Crear' }} Rol
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
    <script>
        function toggleModulePermissions(module, checked) {
            const checkboxes = document.querySelectorAll('.module-' + module);
            checkboxes.forEach(checkbox => {
                if (checkbox.checked !== checked) {
                    checkbox.click(); // Use click to properly trigger Livewire
                }
            });
        }

        // Simple function to update select all state
        function updateSelectAllStates() {
            @foreach($permissionsByModule as $module => $permissions)
            const moduleCheckboxes = document.querySelectorAll('.module-{{ $module }}');
            const selectAllCheckbox = document.getElementById('selectAll{{ $loop->index }}');

            if (selectAllCheckbox && moduleCheckboxes.length > 0) {
                const checkedCount = Array.from(moduleCheckboxes).filter(cb => cb.checked).length;
                selectAllCheckbox.checked = checkedCount === moduleCheckboxes.length;
                selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < moduleCheckboxes.length;
            }
            @endforeach
        }

        // Update when document is ready and when checkboxes change
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(updateSelectAllStates, 500);

            // Listen for changes on permission checkboxes
            document.addEventListener('change', function(e) {
                if (e.target.classList.contains('form-check-input') && !e.target.id.startsWith('selectAll')) {
                    setTimeout(updateSelectAllStates, 100);
                }
            });
        });

        // Update when Livewire updates
        document.addEventListener('livewire:init', () => {
            Livewire.hook('morph.updated', () => {
                setTimeout(updateSelectAllStates, 100);
            });
        });
    </script>
</div>
