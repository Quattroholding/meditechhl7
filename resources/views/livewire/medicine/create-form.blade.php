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
                    <x-input-label for="home_name" value="Home Name"/>
                    <input wire:model="home_name" type="text" class="form-control" id="home_name" placeholder="Ingrese el nombre comercial">
                    <x-input-error :messages="$errors->get('home_name')"/>
                </div>
            </div>
            <div class="col-12 col-sm-4">
                <div class="input-block local-forms">
                    <x-input-label for="ndc_code" value="NDC Code"/>
                    <input wire:model="ndc_code" type="text" class="form-control" id="ndc_code" placeholder="Formato: #####-###-##, ####-####-## o #####-####-#" maxlength="12">
                    <x-input-error :messages="$errors->get('ndc_code')"/>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-sm-4">
                <div class="input-block local-forms">
                    <x-input-label for="type" value="Tipo" required/>
                    <x-select-input wire:model="type" name="type" :options="\App\Models\Lista::medicineTypes()" :selected="[]" class="block w-full"/>
                    <x-input-error :messages="$errors->get('type')"/>
                </div>
            </div>
            <div class="col-12 col-sm-4">
                <div class="input-block local-forms">
                    <x-input-label for="mgs" value="Dosis" required/>
                    <input wire:model="mgs" type="text" class="form-control" id="mgs" placeholder="ej: 500">
                    <x-input-error :messages="$errors->get('mgs')"/>
                </div>
            </div>
            <div class="col-12 col-sm-4">
                <div class="input-block local-forms">
                    <x-input-label for="mgs_type" value="Unidad" required/>
                    <x-select-input wire:model="mgs_type" name="mgs_type" :options="\App\Models\Lista::medicineMgsTypes()" :selected="[]" class="block w-full"/>
                    <x-input-error :messages="$errors->get('mgs_type')"/>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-sm-6">
                <div class="input-block local-forms">
                    <x-input-label for="active" value="Estado" required/>
                    <select wire:model="active" class="form-control" id="active">
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                    <x-input-error :messages="$errors->get('active')"/>
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <div class="input-block local-forms">
                    <x-input-label for="narcotic" value="Narcótico" required/>
                    <select wire:model="narcotic" class="form-control" id="narcotic">
                        <option value="1">Sí</option>
                        <option value="0">No</option>
                    </select>
                    <x-input-error :messages="$errors->get('narcotic')"/>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end mt-4">
            <div class="doctor-submit text-end">
                <button type="submit" class="btn btn-primary submit-form me-2">     {{ __('button.register') }} </button>
                <a class="btn btn-primary cancel-form" href="{{ route('medicine.index') }}">  {{ __('button.cancel') }}</a>
            </div>
        </div>
    </form>
</div>
