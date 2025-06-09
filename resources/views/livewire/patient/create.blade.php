<div>
    @section('scripts')
        <script src="{{ URL::asset('/assets/js/bootstrap-datetimepicker.min.js') }}"></script>
    @stop
    <form wire:submit="savePatient">
        @csrf
        <div class="row">
            <!-- ID NUMBER -->
            <div class="col-12 col-md-6 col-xl-4">
                <div class="input-block  local-forms">
                    <x-input-label for="id_type" :value="__('patient.id_type')" required="true"/>
                    <x-select-input wire:model="id_type" name="id_type" :options="\App\Models\Lista::documentType()" :selected="['CC']" class="block mt-1 w-full"/>
                    <x-input-error :messages="$errors->get('id_type')" class="mt-2" />
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-8">
                <div class=" input-block  local-forms ">
                    <x-input-label for="id_number" :value="__('patient.full_id_number')" required="true"/>
                    <x-text-input wire:model.live="id_number" id="id_number" class="block mt-1 w-full" type="number"  value="" autofocus/>
                    <x-input-error :messages="$errors->get('id_number')" class="mt-2" />
                </div>
            </div>
        </div>
        @if($patientExists)
        <div class="row">
            <p>Este usuario ya se encuentra registrado en el sistema, ¿Desea asociarlo a su empresa?</p>
            <button class="btn btn-primary cancel-form" id="associate-yes" wire:click="asociar()">  Asociar </button>
        </div>
        @endif
        @if($patientDontExists)
        <div id="form-patient" >
            <div class="row ">
                <div class="col-12 col-md-6 col-xl-4">
                    <!-- First Name -->
                    <div class="input-block local-forms">
                        <x-input-label for="first_name" :value="__('patient.first_name')" required="true"/>
                        <x-text-input wire:model="first_name" class="block mt-1 w-full" type="text" name="first_name" :value="old('first_name')" />
                        <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                    </div>
                </div>
                <div class="col-12 col-md-6 col-xl-4">
                    <!-- Last Name -->
                    <div class="input-block local-forms">
                        <x-input-label for="last_name" :value="__('patient.last_name')" required="true"/>
                        <x-text-input wire:model="last_name" class="block mt-1 w-full" type="text" name="last_name" :value="old('last_name')"/>
                        <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                    </div>
                </div>
                <div class="col-12 col-md-6 col-xl-4">
                    <!-- EMAIL -->
                    <div class="input-block local-forms">
                        <x-input-label for="email" value="{{__('patient.email').'/usuario'}}" required="true"/>
                        <x-text-input wire:model="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"/>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- GENDER -->
                <div class="col-12 col-md-6 col-xl-6">
                    <div class="input-block local-forms ">
                        <x-input-label for="gender" :value="__('patient.gender')" required="true"/>
                        <x-select-input wire:model="gender" name="gender" :options="\App\Models\Lista::gender()" :selected="[null]" class="block w-full"/>
                        <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                    </div>
                </div>
                <!-- BIRTHDATE -->
                <div class="col-12 col-md-6 col-xl-6">
                    <div class="form-group local-forms {{--}}cal-icon{{--}}">
                        <x-input-label for="birthdate" :value="__('patient.birthdate')" required="true"/>
                        <x-text-input id="birthdate" type="text" name="birthdate"  type="date" class="block mt-1 w-full" />
                        <x-input-error :messages="$errors->get('birthdate')" class="mt-2" />
                    </div>
                </div>

            </div>
            <div class="row">
                <!-- PHYSICAL ADDRESS -->
                <div class=" col-12 col-md-6 col-xl-6">
                    <div class="input-block local-forms">
                        <x-input-label for="physical_address" :value="__('patient.physical_address')" required/>
                        <x-textarea-input wire:model="physical_address" class="block mt-1 w-full" type="email" name="physical_address"/>
                        <x-input-error :messages="$errors->get('physical_address')" class="mt-2" />
                    </div>
                </div>
                <!-- BILLING ADDRESS -->
                <div class=" col-12 col-md-6 col-xl-6">
                    <div class="input-block local-forms">
                        <x-input-label for="marital_status" :value="__('patient.billing_address')" />
                        <x-textarea-input wire:model="billing_address" class="block mt-1 w-full" type="email" name="billing_address"/>
                        <x-input-error :messages="$errors->get('billing_address')" class="mt-2" />
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- PHONE -->
                <div class=" col-12 col-md-6 col-xl-6">
                    <div class="input-block local-forms">
                        <x-input-label for="phone" :value="__('patient.phone')" required/>
                        <x-text-input wire:model="phone" class="block mt-1 w-full" type="tel" name="phone" :value="old('phone')"/>
                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                    </div>
                </div>
                <div class=" col-12 col-md-6 col-xl-6">
                    <div class="input-block local-forms">
                        <x-input-label for="marital_status" :value="__('patient.marital_status')" required/>
                        <x-select-input wire:model="marital_status" name="marital_status" :options="\App\Models\Lista::maritalStatus()" :selected="[null]" class="block w-full"/>
                        <x-input-error :messages="$errors->get('marital_status')" class="mt-2" />
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- BLOOD TYPE -->
                <div class=" col-12 col-md-6 col-xl-6">
                    <div class="input-block local-forms">
                        <x-input-label for="blood_type" :value="__('patient.blood_type')" class="local-top"/>
                        <x-select-input wire:model="blood_type" name="blood_type" :options="\App\Models\Lista::bloodTypes()" :selected="[null]" class="block w-full"/>
                        <x-input-error :messages="$errors->get('blood_type')" class="mt-2" />
                    </div>
                </div>
                {{--}}
                <div class="col-12 col-md-6 col-xl-6">
                    <div class="form-group local-top-form">
                        <label class="local-top">Avatar <span class="login-danger">*</span></label>
                        <div class="settings-btn upload-files-avator">
                            <input type="file" accept="image/*" name="image" wire:model="avatar" onchange="loadFile(event)" class="hide-input">

                            <label for="file" class="upload">{{__('Seleccionar Imagen')}}</label>
                            @error('avatar') <span class="error">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                {{--}}
            </div>
            <div class="flex items-center justify-end mt-4">
                <div class="text-end">
                    <button type="submit" class="btn btn-primary submit-form me-2">     {{ __('button.register') }} </button>
                    <a class="btn btn-primary cancel-form" href="{{ route('patient.index') }}">  {{ __('button.cancel') }}</a>
                </div>
            </div>
        </div>
        @endif
    </form>
    <script>

        jQuery(function () {
            $('#birthdate').datetimepicker({
                viewMode: 'years'
            });
        });
    </script>
</div>
