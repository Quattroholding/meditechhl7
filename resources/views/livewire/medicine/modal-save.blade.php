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
                        <x-input-label for="home_name" value="Home name"/>
                        <input wire:model="home_name" type="text" class="form-control" id="home_name" placeholder="Ingrese el nombre comercial">
                        <x-input-error :messages="$errors->get('home_name')"/>
                    </div>

                    <div class="input-block local-forms">
                        <x-input-label for="ndc_code" value="NDC Code"/>
                        <input wire:model="ndc_code" type="text" class="form-control" id="ndc_code" placeholder="ej: 12345-123-12, 1234-1234-12 o 12345-1234-1">
                        <x-input-error :messages="$errors->get('ndc_code')"/>
                        <small class="text-muted">Formato: #####-###-##, ####-####-## o #####-####-#</small>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                        <div class="input-block local-forms">
                            <x-input-label for="type" value="Tipo" required/>
                            <x-select-input wire:model="type" name="type" :options="\App\Models\Lista::medicineTypes()" :selected="[]" class="block w-full"/>
                            <x-input-error :messages="$errors->get('type')"/>
                        </div>

                        <div class="input-block local-forms">
                            <x-input-label for="mgs" value="Dosis" required/>
                            <input wire:model="mgs" type="text" class="form-control" id="mgs" placeholder="ej: 500">
                            <x-input-error :messages="$errors->get('mgs')"/>
                        </div>

                        <div class="input-block local-forms">
                            <x-input-label for="mgs_type" value="Unidad" required/>
                            <x-select-input wire:model="mgs_type" name="type" :options="\App\Models\Lista::medicineMgsTypes()" :selected="[]" class="block w-full"/>
                            <x-input-error :messages="$errors->get('mgs_type')"/>
                        </div>
                    </div>
                    {{--}}
                    <div class="input-block local-forms">
                        <x-input-label for="instructions" value="Instrucciones de Uso"/>
                        <textarea wire:model="instructions" class="form-control" id="instructions" rows="3" placeholder="Ingrese las instrucciones de uso"></textarea>
                        <x-input-error :messages="$errors->get('instructions')"/>
                    </div>

                    <div class="input-block local-forms">
                        <x-input-label for="side_effects" value="Efectos Secundarios"/>
                        <textarea wire:model="side_effects" class="form-control" id="side_effects" rows="3" placeholder="Ingrese los efectos secundarios"></textarea>
                        <x-input-error :messages="$errors->get('side_effects')"/>
                    </div>

                    <div class="input-block local-forms">
                        <x-input-label for="contraindications" value="Contraindicaciones"/>
                        <textarea wire:model="contraindications" class="form-control" id="contraindications" rows="3" placeholder="Ingrese las contraindicaciones"></textarea>
                        <x-input-error :messages="$errors->get('contraindications')"/>
                    </div>

                    <div class="input-block local-forms">
                        <x-input-label for="interactions" value="Interacciones"/>
                        <textarea wire:model="interactions" class="form-control" id="interactions" rows="3" placeholder="Ingrese las interacciones medicamentosas"></textarea>
                        <x-input-error :messages="$errors->get('interactions')"/>
                    </div>
                    {{--}}
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="input-block local-forms">
                            <x-input-label for="active" value="Estado" required/>
                            <select wire:model="active" class="form-control" id="status">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')"/>
                        </div>
                        <div class="input-block local-forms">
                            <x-input-label for="narcotic" value="Narcotico" required/>
                            <select wire:model="narcotic" class="form-control" id="narcotic">
                                <option value="1">SI</option>
                                <option value="0">NO</option>
                            </select>
                            <x-input-error :messages="$errors->get('narcotic')"/>
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
