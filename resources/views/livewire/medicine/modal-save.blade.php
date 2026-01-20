<div>

    @if($showModal)
        <div class="modal-overlay" wire:click="closeModal" style="z-index: 10000;">
            <div class="modal-content" wire:click.stop>
                <div class="modal-header">
                    <h2 class="modal-title">{{ $title }}</h2>
                    <button wire:click="closeModal" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
                </div>

                <form wire:submit="saveMedicine">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-block local-forms">
                                <x-input-label for="generic_name" value="Nombre Genérico" required/>
                                <input wire:model="generic_name" type="text" class="form-control" id="generic_name" placeholder="Ingrese el nombre genérico">
                                <x-input-error :messages="$errors->get('generic_name')"/>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-block local-forms">
                                <x-input-label for="home_name" value="Nombre Comercial"/>
                                <input wire:model="home_name" type="text" class="form-control" id="home_name" placeholder="Ingrese el nombre comercial">
                                <x-input-error :messages="$errors->get('home_name')"/>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="input-block local-forms">
                                <x-input-label for="code" value="Código"/>
                                <input wire:model="code" type="text" class="form-control" id="code" placeholder="Código del medicamento">
                                <x-input-error :messages="$errors->get('code')"/>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-block local-forms">
                                <x-input-label for="form" value="Forma Farmacéutica" required/>
                                <x-select-input wire:model="form" name="form" :options="\App\Models\Lista::medicineTypes()" :selected="[]" class="block w-full"/>
                                <x-input-error :messages="$errors->get('form')"/>
                            </div>
                        </div>
                        <div class="col-md-4">
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

                    <div class="input-block local-forms">
                        <x-input-label for="manufacturer" value="Fabricante"/>
                        <input wire:model="manufacturer" type="text" class="form-control" id="manufacturer" placeholder="Nombre del fabricante">
                        <x-input-error :messages="$errors->get('manufacturer')"/>
                    </div>

                    <!-- Ingredients Section -->
                    <div class="card mt-3">
                        <div class="card-header d-flex justify-content-between align-items-center py-2">
                            <h6 class="mb-0">Ingredientes Activos</h6>
                            <button type="button" wire:click="addIngredient" class="btn btn-sm btn-primary">
                                <i class="fa fa-plus"></i> Agregar
                            </button>
                        </div>
                        <div class="card-body py-2">
                            @foreach($ingredients as $index => $ingredient)
                                <div class="row align-items-end mb-2" wire:key="ingredient-{{ $index }}">
                                    <div class="col-5">
                                        <div class="input-block local-forms mb-0">
                                            @if($loop->first)
                                                <x-input-label value="Ingrediente" required/>
                                            @endif
                                            <input wire:model="ingredients.{{ $index }}.substance_display"
                                                   type="text"
                                                   class="form-control form-control-sm"
                                                   placeholder="Ej: Paracetamol">
                                            <x-input-error :messages="$errors->get('ingredients.'.$index.'.substance_display')"/>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="input-block local-forms mb-0">
                                            @if($loop->first)
                                                <x-input-label value="Dosis" required/>
                                            @endif
                                            <input wire:model="ingredients.{{ $index }}.strength_value"
                                                   type="text"
                                                   class="form-control form-control-sm"
                                                   placeholder="500">
                                            <x-input-error :messages="$errors->get('ingredients.'.$index.'.strength_value')"/>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="input-block local-forms mb-0">
                                            @if($loop->first)
                                                <x-input-label value="Unidad" required/>
                                            @endif
                                            <x-select-input wire:model="ingredients.{{ $index }}.strength_unit"
                                                            name="ingredients.{{ $index }}.strength_unit"
                                                            :options="\App\Models\Lista::medicineMgsTypes()"
                                                            :selected="[]"
                                                            class="block w-full form-control-sm"/>
                                            <x-input-error :messages="$errors->get('ingredients.'.$index.'.strength_unit')"/>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        @if(count($ingredients) > 1)
                                            <button type="button"
                                                    wire:click="removeIngredient({{ $index }})"
                                                    class="btn btn-danger btn-sm w-100">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach

                            @if($errors->has('ingredients'))
                                <div class="alert alert-danger mt-2 py-1 small">
                                    {{ $errors->first('ingredients') }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">{{ $buttonSaveTitle }}</button>
                        <button type="button" wire:click="closeModal" class="btn btn-secondary">{{ __('button.cancel') }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('showToastr', (event) => {
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
            background: white;
            border-radius: 8px;
            padding: 20px;
            max-width: 900px;
            width: 95%;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-footer {
            display: flex;
            justify-content: end;
            gap: 10px;
            margin-top: 20px;
        }
    </style>
</div>
