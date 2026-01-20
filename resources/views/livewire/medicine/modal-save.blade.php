<div>

    @if($showModal)
        <div class="modal-overlay" wire:click="closeModal" style="z-index: 10000;">
            <div class="modal-content" wire:click.stop>
                <div class="modal-header">
                    <h2 class="modal-title">{{ $title }}</h2>
                    <button wire:click="closeModal" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
                </div>

                <form wire:submit="saveMedicine">
                    <div class="input-block local-forms">
                        <x-input-label for="generic_name" value="Nombre Genérico" required/>
                        <input wire:model="generic_name" type="text" class="form-control" id="generic_name" placeholder="Ingrese el nombre genérico">
                        <x-input-error :messages="$errors->get('generic_name')"/>
                    </div>

                    <div class="input-block local-forms">
                        <x-input-label for="home_name" value="Nombre Comercial"/>
                        <input wire:model="home_name" type="text" class="form-control" id="home_name" placeholder="Ingrese el nombre comercial">
                        <x-input-error :messages="$errors->get('home_name')"/>
                    </div>

                    <div class="input-block local-forms">
                        <x-input-label for="code" value="Código"/>
                        <input wire:model="code" type="text" class="form-control" id="code" placeholder="Código del medicamento">
                        <x-input-error :messages="$errors->get('code')"/>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                        <div class="input-block local-forms">
                            <x-input-label for="form" value="Forma Farmacéutica" required/>
                            <x-select-input wire:model="form" name="form" :options="\App\Models\Lista::medicineTypes()" :selected="[]" class="block w-full"/>
                            <x-input-error :messages="$errors->get('form')"/>
                        </div>

                        <div class="input-block local-forms">
                            <x-input-label for="strength_value" value="Dosis" required/>
                            <input wire:model="strength_value" type="text" class="form-control" id="strength_value" placeholder="ej: 500">
                            <x-input-error :messages="$errors->get('strength_value')"/>
                        </div>

                        <div class="input-block local-forms">
                            <x-input-label for="strength_unit" value="Unidad" required/>
                            <x-select-input wire:model="strength_unit" name="strength_unit" :options="\App\Models\Lista::medicineMgsTypes()" :selected="[]" class="block w-full"/>
                            <x-input-error :messages="$errors->get('strength_unit')"/>
                        </div>
                    </div>

                    <div class="input-block local-forms">
                        <x-input-label for="manufacturer" value="Fabricante"/>
                        <input wire:model="manufacturer" type="text" class="form-control" id="manufacturer" placeholder="Nombre del fabricante">
                        <x-input-error :messages="$errors->get('manufacturer')"/>
                    </div>

                    <div class="input-block local-forms">
                        <x-input-label for="status" value="Estado" required/>
                        <select wire:model="status" class="form-control" id="status">
                            <option value="active">Activo</option>
                            <option value="inactive">Inactivo</option>
                        </select>
                        <x-input-error :messages="$errors->get('status')"/>
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
            max-width: 800px;
            width: 90%;
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