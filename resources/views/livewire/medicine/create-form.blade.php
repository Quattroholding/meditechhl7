<div>
    <form wire:submit="saveMedicine">
        <div class="row">
            <div class="col-12 col-sm-4">
                <div class="input-block local-forms">
                    <x-input-label for="generic_name" value="Nombre Genérico" required/>
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
                    <x-input-label for="strength_value" value="Dosis" required/>
                    <input wire:model="strength_value" type="text" class="form-control" id="strength_value" placeholder="ej: 500">
                    <x-input-error :messages="$errors->get('strength_value')"/>
                </div>
            </div>
            <div class="col-12 col-sm-4">
                <div class="input-block local-forms">
                    <x-input-label for="strength_unit" value="Unidad" required/>
                    <x-select-input wire:model="strength_unit" name="strength_unit" :options="\App\Models\Lista::medicineMgsTypes()" :selected="[]" class="block w-full"/>
                    <x-input-error :messages="$errors->get('strength_unit')"/>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-sm-6">
                <div class="input-block local-forms">
                    <x-input-label for="manufacturer" value="Fabricante"/>
                    <input wire:model="manufacturer" type="text" class="form-control" id="manufacturer" placeholder="Nombre del fabricante">
                    <x-input-error :messages="$errors->get('manufacturer')"/>
                </div>
            </div>
            <div class="col-12 col-sm-6">
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

        <div class="flex items-center justify-end mt-4">
            <div class="doctor-submit text-end">
                <button type="submit" class="btn btn-primary submit-form me-2">{{ __('button.register') }}</button>
                <a class="btn btn-secondary cancel-form" href="{{ route('medicine.index') }}">{{ __('button.cancel') }}</a>
            </div>
        </div>
    </form>
</div>