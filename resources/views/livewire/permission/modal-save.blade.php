<div>
    @if($showModal)
        <div class="modal-overlay" wire:click="closeModal" style="z-index: 10000;">
            <div class="modal-content" wire:click.stop>
                <div class="modal-header">
                    <h5 class="modal-title">{{ $isEdit ? 'Editar Permiso' : 'Nuevo Permiso' }}</h5>
                    <button wire:click="closeModal" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
                </div>
                <form wire:submit.prevent="save">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre del Permiso</label>
                            <input
                                type="text"
                                class="form-control @error('name') is-invalid @enderror"
                                id="name"
                                wire:model="name"
                                placeholder="Ej: users.create, patients.view"
                            >
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción (Opcional)</label>
                            <textarea
                                class="form-control @error('description') is-invalid @enderror"
                                id="description"
                                wire:model="description"
                                rows="3"
                                placeholder="Descripción del permiso"
                            ></textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            {{ $isEdit ? 'Actualizar' : 'Crear' }} Permiso
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

