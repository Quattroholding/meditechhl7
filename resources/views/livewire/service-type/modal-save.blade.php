<div>
    @if($showModal)
        <div class="modal-overlay" wire:click="closeModal" style="z-index: 10000;">
            <div class="modal-content" wire:click.stop style="max-width: 500px;">
                <div class="modal-header ">
                    <h5 class="modal-title">{{ $title }}</h5>
                    <button type="button" class="close" wire:click="closeModal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="save">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Nombre <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           wire:model="name" placeholder="Ej: Licencia Básica">
                                    @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Estado <span class="text-danger">*</span></label>
                                    <select class="form-control @error('status') is-invalid @enderror"
                                            wire:model="status">
                                        <option value="active">Activo</option>
                                        <option value="inactive">Inactivo</option>
                                    </select>
                                    @error('status') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Descripción</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      wire:model="description" rows="3"
                                      placeholder="Descripción del servicio..."></textarea>
                            @error('description') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Costo Base <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0"
                                           class="form-control @error('base_cost') is-invalid @enderror"
                                           wire:model="base_cost" placeholder="0.00">
                                    @error('base_cost') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Moneda <span class="text-danger">*</span></label>
                                    <select class="form-control @error('currency') is-invalid @enderror"
                                            wire:model="currency">
                                        <option value="USD">USD</option>
                                        <option value="PAB">PAB</option>
                                    </select>
                                    @error('currency') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeModal">Cancelar</button>&nbsp;&nbsp;
                    <button type="button" class="btn btn-primary" wire:click="save">
                        <i class="fa fa-save"></i> {{ $buttonSaveTitle }}
                    </button>
                </div>
            </div>
        </div>

    @endif
</div>
