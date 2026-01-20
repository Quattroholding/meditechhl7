<div>
    <form wire:submit="updateMedicine">
        <div class="row">
            <div class="col-12 col-sm-4">
                <div class="input-block local-forms">
                    <x-input-label for="generic_name" value="Nombre" required/>
                    <input wire:model="generic_name" type="text" class="form-control" id="generic_name" placeholder="Ingrese el nombre genérico">
                    <x-input-error :messages="$errors->get('generic_name')"/>
                </div>
            </div>
            <div class="col-12 col-sm-4">
                <div class="input-block local-forms">
                    <x-input-label for="home_name" value="Nombre Comercial"/>
                    <input wire:model="home_name" type="text" class="form-control" id="home_name" placeholder="Ingrese el nombre comercial">
                    <x-input-error :messages="$errors->get('home_name')"/>
                </div>
            </div>
            <div class="col-12 col-sm-4">
                <div class="input-block local-forms">
                    <x-input-label for="code" value="Código"/>
                    <input wire:model="code" type="text" class="form-control" id="code" placeholder="Código del medicamento">
                    <x-input-error :messages="$errors->get('code')"/>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-sm-4">
                <div class="input-block local-forms">
                    <x-input-label for="form" value="Forma Farmacéutica" required/>
                    <x-select-input wire:model="form" name="form" :options="\App\Models\Lista::medicineTypes()" :selected="[]" class="block w-full"/>
                    <x-input-error :messages="$errors->get('form')"/>
                </div>
            </div>
            <div class="col-12 col-sm-4">
                <div class="input-block local-forms">
                    <x-input-label for="manufacturer" value="Fabricante"/>
                    <input wire:model="manufacturer" type="text" class="form-control" id="manufacturer" placeholder="Nombre del fabricante">
                    <x-input-error :messages="$errors->get('manufacturer')"/>
                </div>
            </div>
            <div class="col-12 col-sm-4">
                <div class="input-block local-forms">
                    <x-input-label for="status" value="Estado" required/>
                    <select wire:model="status" class="form-control" id="status">
                        <option value="active">Activo</option>
                        <option value="inactive">Inactivo</option>
                    </select>
                    <x-input-error :messages="$errors->get('status')"/>
                </div>
            </div>
        </div>

        <!-- Ingredients Section -->
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-white">Ingredientes Activos</h5>
                <button type="button" wire:click="addIngredient" class="btn btn-sm btn-primary">
                    <i class="fa fa-plus"></i> Agregar Ingrediente
                </button>
            </div>
            <div class="card-body">
                @foreach($ingredients as $index => $ingredient)
                    <div class="row align-items-end mb-3 ingredient-row" wire:key="ingredient-{{ $index }}">
                        <div class="col-12 col-sm-4">
                            <div class="input-block local-forms mb-0">
                                <x-input-label for="ingredients.{{ $index }}.substance_display" value="Nombre del Ingrediente" required/>
                                <input wire:model="ingredients.{{ $index }}.substance_display"
                                       type="text"
                                       class="form-control"
                                       placeholder="Ej: Paracetamol">
                                <x-input-error :messages="$errors->get('ingredients.'.$index.'.substance_display')"/>
                            </div>
                        </div>
                        <div class="col-12 col-sm-3">
                            <div class="input-block local-forms mb-0">
                                <x-input-label for="ingredients.{{ $index }}.strength_value" value="Dosis" required/>
                                <input wire:model="ingredients.{{ $index }}.strength_value"
                                       type="text"
                                       class="form-control"
                                       placeholder="Ej: 500">
                                <x-input-error :messages="$errors->get('ingredients.'.$index.'.strength_value')"/>
                            </div>
                        </div>
                        <div class="col-12 col-sm-3">
                            <div class="input-block local-forms mb-0">
                                <x-input-label for="ingredients.{{ $index }}.strength_unit" value="Unidad" required/>
                                <x-select-input wire:model="ingredients.{{ $index }}.strength_unit"
                                                name="ingredients.{{ $index }}.strength_unit"
                                                :options="\App\Models\Lista::medicineMgsTypes()"
                                                :selected="[]"
                                                class="block w-full"/>
                                <x-input-error :messages="$errors->get('ingredients.'.$index.'.strength_unit')"/>
                            </div>
                        </div>
                        <div class="col-12 col-sm-2">
                            @if(count($ingredients) > 1)
                                <button type="button"
                                        wire:click="removeIngredient({{ $index }})"
                                        class="btn btn-danger btn-sm w-100">
                                    <i class="fa fa-trash"></i> Eliminar
                                </button>
                            @endif
                        </div>
                    </div>
                    @if(!$loop->last)
                        <hr class="my-2">
                    @endif
                @endforeach

                @if($errors->has('ingredients'))
                    <div class="alert alert-danger mt-2">
                        {{ $errors->first('ingredients') }}
                    </div>
                @endif
            </div>
        </div>

        <div class="flex items-center justify-end mt-4">
            <div class="doctor-submit text-end">
                <button type="submit" class="btn btn-primary submit-form me-2">{{ __('button.update') }}</button>
                <a class="btn btn-secondary cancel-form" href="{{ route('medicine.index') }}">{{ __('button.cancel') }}</a>
            </div>
        </div>
    </form>
</div>
