<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    {{ __('user.title') }}
                @endslot
                @slot('li_1')
                    {{ __('generic.create') }} {{ __('user.title') }}
                @endslot
            @endcomponent
            <!-- /Page Header -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="col-12">
                                <div class="form-heading">
                                    <h4>  {{ __('generic.create') }}
                                        @if(request()->get('role_id')==2)
                                            {{ __('user.doctor') }}
                                        @elseif(request()->get('role_id')==5)
                                            {{ __('user.asistent') }}
                                        @else
                                            {{ __('user.title') }}
                                        @endif
                                    </h4>
                                </div>
                            </div>
                        <form method="POST" action="{{ route('user.store') }}" enctype="multipart/form-data" id="form">
                            @csrf
                            <div class="row">
                                <div class="col-12 col-md-6 col-xl-4" id="role">
                                    <!-- ROL -->
                                    <div class="input-block  local-forms">
                                        <x-input-label for="rol" :value="__('user.rol')"/>
                                            <x-select-input id="rol" name="rol" :options="\App\Models\Rol::pluck('name','id')->toArray()" class="block w-full" wire:model="rol"/>
                                        <x-input-error :messages="$errors->get('rol')" class="mt-2" /><p>&nbsp;</p>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-xl-4" style="display: none" id="email">
                                    <!-- EMAIL -->
                                    <div class="input-block  local-forms">
                                        <x-input-label for="email" :value="__('user.email')"/>
                                        <x-text-input class="block mt-1 w-full" type="email" name="email" :value="old('email')" autofocus/>
                                        <x-input-error :messages="$errors->get('email')" class="mt-2" /><p>&nbsp;</p>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-xl-4" style="display: none" id="first_name">
                                    <!-- First Name -->
                                    <div class="input-block  local-forms">
                                        <x-input-label for="first_name" :value="__('user.first_name')"/>
                                        <x-text-input class="block mt-1 w-full" type="text" name="first_name" :value="old('first_name')"  />
                                        <x-input-error :messages="$errors->get('first_name')" class="mt-2" /><p>&nbsp;</p>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-xl-4" style="display: none" id="last_name">
                                    <!-- Last Name -->
                                    <div class="input-block  local-forms">
                                        <x-input-label for="last_name" :value="__('user.last_name')" />
                                        <x-text-input class="block mt-1 w-full" type="text" name="last_name" :value="old('last_name')" />
                                        <x-input-error :messages="$errors->get('last_name')" class="mt-2" /><p>&nbsp;</p>
                                    </div>
                                </div>
                          
                                <!-- ID NUMBER -->
                                <div class="col-4 col-md-4 col-xl-4" style="display: none" id="id_type">
                                    <div class="input-block  local-forms">
                                        <x-input-label for="id_type" :value="__('doctor.id_type')"/>
                                        <x-select-input name="id_type" :options="\App\Models\Lista::documentType()" :selected="['CC']" class="block mt-1 w-full"/>
                                        <x-input-error :messages="$errors->get('id_type')" class="mt-2" />
                                    </div>
                                </div>
                                <div class="col-4 col-md-4 col-xl-4" style="display: none" id="id_number">
                                    <div class=" input-block  local-forms ">
                                        <x-input-label for="id_number" :value="__('doctor.full_id_number')" />
                                        <x-text-input class="block mt-1 w-full" type="number" name="id_number" value="" autofocus/>
                                        <x-input-error :messages="$errors->get('id_number')" class="mt-2" />
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-xl-4" style="display: none" id="medical_speciality">
                                    <!-- SPECIALTY -->
                                    <div class="input-block  local-forms">
                                        <x-input-label for="medical_speciality" :value="__('practitioner.speciality')" />
                                        <x-select-input name="medical_speciality[]" :options="\App\Models\MedicalSpeciality::pluck('name','id')->toArray()" class="block  w-full"/>
                                        <x-input-error class="mt-2" :messages="$errors->get('medical_speciality')" /><p>&nbsp;</p>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-xl-4" style="display: none" id="maritalstatus">
                                    <!-- MARITAL STATUS -->
                                    <div class="input-block  local-forms">
                                        <x-input-label for="marital_status" :value="__('patient.marital_status')" />
                                        <x-select-input name="marital_status" :options="\App\Models\Lista::maritalStatus()" class="block  w-full"/>
                                        <x-input-error class="mt-2" :messages="$errors->get('marital_status')" /><p>&nbsp;</p>
                                    </div>
                                </div>
                                    <div class="col-12 col-md-6 col-xl-6" style="display: none" id="gender">
                                    <!-- CLIENTS -->
                                    <div class="input-block  local-forms">
                                        <x-input-label for="gender" :value="__('user.gender')" />
                                        <x-select-input name="gender" :options="['male'=> 'male', 'female'=>'female', 'other'=>'other', 'unknow'=>'unknow']" class="block  w-full"/>
                                        <x-input-error class="mt-2" :messages="$errors->get('gender')" /><p>&nbsp;</p>
                                    </div>
                                </div>     
                                <!-- BIRTHDATE -->
                                <div class=" col-12 col-md-6 col-xl-6" style="display: none" id="birthdate_user">
                                    <div class="input-block local-forms">
                                        <div class="form-group local-forms cal-icon">
                                            <x-input-label for="birthdate" :value="__('user.birthdate')" /> 
                                            <x-text-input id="birthdate" class="block mt-1 w-full datetimepicker" type="text" name="birth_date" :value="old('birthdate')"/>
                                            <x-input-error class="mt-2" :messages="$errors->get('birth_date')" />
                                        </div>
                                    </div>
                                </div>  
                                    <div class="col-12 col-md-6 col-xl-6" style="display: none" id="physical_address">
                                        <!-- ADDRESS -->
                                        <div class="input-block  local-forms">
                                            <x-input-label for="address" :value="__('user.address')" />
                                            <x-text-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address')" />
                                            <x-input-error :messages="$errors->get('address')" class="mt-2" /><p>&nbsp;</p>
                                        </div>
                                    </div>

                                    <div class=" col-12 col-md-6 col-xl-6" style="display: none" id="whatsapp">
                                    <!-- WHATSAPP -->
                                    <div class="input-block  local-forms">
                                        <x-input-label for="whatsapp" :value="__('phone')" />
                                        <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone" :value="old('phone')"/>
                                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-xl-4" style="display: none" id="client_id">
                                    <!-- CLIENTS -->
                                    <div class="input-block  local-forms">
                                        <x-input-label for="client" :value="__('user.client')" />
                                        <x-select-input name="clients[]" :options="\App\Models\Client::pluck('name','id')->toArray()" class="block  w-full"/>
                                        <x-input-error class="mt-2" :messages="$errors->get('clients')" /><p>&nbsp;</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="display: none" id="image">
                                <!-- PICTURE -->
                                <div class="col-12 col-md-6 col-xl-12">
                                    <div class="form-group local-top-form">
                                        <label class="local-top" for="avatar">Avatar <span class="login-danger">*</span></label>
                                        <div class="settings-btn upload-files-avator">
                                            <input type="file" accept="image/*" name="avatar" id="file"    onchange="loadFile(event)" class="hide-input">
                                            <label for="file" class="upload">Buscar Archivo</label>
                                        </div>
                                    </div>
                                    <div class="upload-images upload-size">
                                        <img src="{{ URL::asset('/assets/img/favicon.png')  }}" alt="Image" id="preview">
                                        <a href="javascript:void(0);" class="btn-icon logo-hide-btn">
                                            <i class="feather-x-circle"></i>
                                        </a>
                                    </div>
                                </div>
                            </div><p>&nbsp;</p><p>&nbsp;</p>
                            <div class="row">
                                <div class="col-12 col-md-6 col-xl-6" style="display: none" id="password">
                                    <div class="input-block  local-forms">
                                        <x-input-label for="update_password_password" :value="__('Contraseña')" />
                                        <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                                        <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" /><p>&nbsp;</p>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-xl-6" style="display: none" id="confirm_password">
                                    <div class="input-block  local-forms">
                                        <x-input-label for="update_password_password_confirmation" :value="__('user.confirm_password')" />
                                        <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                                        <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" /><p>&nbsp;</p>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center justify-end mt-4">
                                <div class="doctor-submit text-end">
                                    <button type="submit" class="btn btn-primary submit-form me-2">     {{ __('button.register') }} </button>
                                    <a class="btn btn-primary cancel-form" href="{{ route('user.index') }}">  {{ __('button.cancel') }}</a>
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
    @push('scripts')
    <script>
        $( document ).ready(function() {
            $("#rol").change(function() {
                var type = this.value;
                console.log(type);
                changeByType(type);
        
    });
function changeByType(type) {

        $("#email").hide();
        $("#first_name").hide();
        $('#last_name').hide();
        $('#id_type').hide();
        $('#id_number').hide();
        $('#medical_speciality').hide();
        $('#gender').hide();
        $('#birthdate_user').hide();;
        $('#physical_address').hide();
        $('#client_id').hide();
        $('#image').hide();
        $('#password').hide();
        $('#confirm_password').hide();
        $('#maritalstatus').hide();
        $('#whatsapp').hide();
        
       

        switch(type) {
            /*-----FORMULARIO PARA ROLE ADMIN-CLIENT-----*/
            case '5':
            /*-----FORMULARIO PARA ROLE ADMIN-----*/
            case '1':
                //$("#client").show();
                $('#first_name').show();
                $("#last_name").show();
                $("#email").show();
                $("#image").show();
                $("#password").show();
                $("#client_id").show();
                $("#confirm_password").show();
                break;
            /*-----FORMULARIO PARA ROLE DOCTOR-----*/
            case '2':
                $("#client_id").show();
                $("#id_type").show();
                $("#id_number").show();
                $("#medical_speciality").show();
                $("#gender").show();
                $("#birthdate_user").show();
                $("#physical_address").show();
                $("#whatsapp").show();
                $('#first_name').show();
                $("#last_name").show();
                $("#email").show();
                $("#image").show();
                $("#password").show();
                $("#confirm_password").show();
                break;
            /*-----FORMULARIO PARA ROLE ASISTENTE-----*/
            case '3':
                $("#client_id").show();
                $("#id_type").show();
                $("#id_number").show();
                //$("#medical_speciality").show();
                $("#gender").show();
                $("#birthdate_user").show();
                $("#physical_address").show();
                $("#whatsapp").show();
                $('#first_name').show();
                $("#last_name").show();
                $("#email").show();
                $("#image").show();
                $("#password").show();
                $("#confirm_password").show();
                break;
                /*-----FORMULARIO PARA ROLE PACIENTE-----*/
            case '4':
                //$("#client").show();
                $("#id_type").show();
                $("#id_number").show();
                $('#maritalstatus').show();
                //$("#medical_speciality").show();
                $("#gender").show();
                $("#birthdate_user").show();
                $("#physical_address").show();
                $("#whatsapp").show();
                $('#first_name').show();
                $("#last_name").show();
                $("#email").show();
                $("#image").show();
                $("#password").show();
                $("#confirm_password").show();
                break;
            default:
            // code block
        }
    }
        });
    </script>
    @endpush
</x-app-layout>
