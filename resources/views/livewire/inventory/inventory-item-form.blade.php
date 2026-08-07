<div>
    <form wire:submit.prevent="save">
        <div class="col-12">
            <div class="form-heading">
                <h4>{{ $item ? 'Editar' : 'Nuevo' }} Articulo de Inventario</h4>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-md-6">
                <div class="input-block local-forms">
                    <x-input-label for="name" value="Nombre" required/>
                    <x-text-input wire:model="name" class="block mt-1 w-full" type="text" name="name" id="name"/>
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
            </div>

            <div class="col-12 col-md-3">
                <div class="input-block local-forms">
                    <x-input-label for="sku" value="SKU" required/>
                    <x-text-input wire:model="sku" class="block mt-1 w-full" type="text" name="sku" id="sku"/>
                    <x-input-error :messages="$errors->get('sku')" class="mt-2" />
                </div>
            </div>

            <div class="col-12 col-md-3">
                <div class="input-block local-forms">
                    <x-input-label for="barcode" value="Código de Barras"/>
                    <x-text-input wire:model="barcode" class="block mt-1 w-full" type="text" name="barcode" id="barcode"/>
                    <x-input-error :messages="$errors->get('barcode')" class="mt-2" />
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="input-block local-forms">
                    <x-input-label for="description" value="Descripción"/>
                    <textarea wire:model="description" class="form-control" id="description" rows="2"></textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-md-3">
                <div class="input-block local-forms">
                    <x-input-label for="item_type" value="Tipo" required/>
                    <x-select-input wire:model="item_type" name="item_type" :options="App\Enums\InventoryItemType::options()" class="block w-full" :selected="[$item_type ?? 'supply']"/>
                    <x-input-error :messages="$errors->get('item_type')" class="mt-2" />
                </div>
            </div>

            <div class="col-12 col-md-3">
                <div class="input-block local-forms">
                    <x-input-label for="unit_of_measure" value="Unidad" required/>
                    <x-select-input wire:model="unit_of_measure" name="unit_of_measure" :options="App\Enums\UnitOfMeasure::options()" class="block w-full" :selected="[$unit_of_measure ?? 'unit']"/>
                    <x-input-error :messages="$errors->get('unit_of_measure')" class="mt-2" />
                </div>
            </div>

            <div class="col-12 col-md-2">
                <div class="input-block local-forms">
                    <x-input-label for="base_cost" value="Costo"/>
                    <x-text-input wire:model="base_cost" class="block mt-1 w-full" type="number" step="0.01" name="base_cost" id="base_cost"/>
                    <x-input-error :messages="$errors->get('base_cost')" class="mt-2" />
                </div>
            </div>

            <div class="col-12 col-md-2">
                <div class="input-block local-forms">
                    <x-input-label for="base_price" value="Precio"/>
                    <x-text-input wire:model="base_price" class="block mt-1 w-full" type="number" step="0.01" name="base_price" id="base_price"/>
                    <x-input-error :messages="$errors->get('base_price')" class="mt-2" />
                </div>
            </div>

            <div class="col-12 col-md-2">
                <div class="input-block local-forms">
                    <x-input-label for="status" value="Estado"/>
                    <select wire:model="status" class="form-select" id="status">
                        <option value="active">Activo</option>
                        <option value="inactive">Inactivo</option>
                    </select>
                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-md-3">
                <div class="input-block local-forms">
                    <x-input-label for="reorder_point" value="Punto de Reorden"/>
                    <x-text-input wire:model="reorder_point" class="block mt-1 w-full" type="number" name="reorder_point" id="reorder_point"/>
                    <x-input-error :messages="$errors->get('reorder_point')" class="mt-2" />
                </div>
            </div>

            <div class="col-12 col-md-3">
                <div class="input-block local-forms">
                    <x-input-label for="reorder_quantity" value="Cantidad de Reorden"/>
                    <x-text-input wire:model="reorder_quantity" class="block mt-1 w-full" type="number" name="reorder_quantity" id="reorder_quantity"/>
                    <x-input-error :messages="$errors->get('reorder_quantity')" class="mt-2" />
                </div>
            </div>

            <div class="col-12">
                <div class="input-block">
                    <label class="form-label d-block mb-3"><strong>Opciones de Rastreo y Control</strong></label>
                    <div class="row">
                        <div class="col-12 col-md-6 col-lg-3 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" wire:model="track_by_lot" id="track_by_lot">
                                <label class="form-check-label" for="track_by_lot">
                                    <i class="fas fa-barcode text-primary me-1"></i>
                                    <strong>Rastrear por Lote</strong>
                                    <small class="d-block text-muted">Control de lotes de fabricación</small>
                                </label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-3 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" wire:model="track_by_serial" id="track_by_serial">
                                <label class="form-check-label" for="track_by_serial">
                                    <i class="fas fa-hashtag text-info me-1"></i>
                                    <strong>Rastrear por Serie</strong>
                                    <small class="d-block text-muted">Control por número de serie</small>
                                </label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-3 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" wire:model="expiration_tracking" id="expiration_tracking">
                                <label class="form-check-label" for="expiration_tracking">
                                    <i class="fas fa-calendar-times text-warning me-1"></i>
                                    <strong>Control de Vencimiento</strong>
                                    <small class="d-block text-muted">Seguimiento de fecha de caducidad</small>
                                </label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-3 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" wire:model="requires_prescription" id="requires_prescription">
                                <label class="form-check-label" for="requires_prescription">
                                    <i class="fas fa-prescription text-danger me-1"></i>
                                    <strong>Requiere Receta</strong>
                                    <small class="d-block text-muted">Medicamento bajo prescripción</small>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end mt-4">
            <div class="doctor-submit text-end">
                <button type="submit" class="btn btn-primary me-2">{{ $item ? 'Actualizar' : 'Crear' }} Item</button>
                <a class="btn btn-secondary me-2" href="{{ route('inventory.items.index') }}">{{ __('button.cancel') }}</a>
            </div>
        </div>
    </form>
</div>
