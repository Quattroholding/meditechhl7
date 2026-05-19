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
                    @cannot('create',auth()->user())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                           {!! __('user.plan_error_message',['link'=>"<a class='btn btn-primary' href='".route('suscriptions.show')."'>clic aqui</a>"]) !!}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endcannot
                    <div class="card">
                        <div class="card-body">
                            <div class="col-12">
                                <div class="form-heading">
                                    <h4>  {{ __('generic.create') }}
                                        @if(request()->get('role_id')==2)
                                            {{ __('user.doctor') }}
                                        @elseif(request()->get('role_id')==3)
                                            {{ __('user.recepcionist') }}
                                        @elseif(request()->get('role_id')==6)
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
                                        <x-input-label for="rol" :value="__('user.rol')" required/>
                                        @if(!request()->has('role_id'))
                                            <x-select-input id="rol" name="rol" :options="\App\Models\Rol::pluck('name','id')->toArray()" class="block w-full" :selected="[old('rol')]"/>
                                        @else
                                            <x-select-input id="rol" name="rol" :options="\App\Models\Rol::pluck('name','id')->toArray()" class="block w-full" :selected="[(int)request()->get('role_id')]"/>
                                        @endif
                                        <x-input-error :messages="$errors->get('rol')" class="mt-2" />
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-xl-4" style="display: none" id="email">
                                    <!-- EMAIL -->
                                    <div class="input-block  local-forms">
                                        <x-input-label for="email" :value="__('user.email')" required/>
                                        <x-text-input class="block mt-1 w-full" type="email" name="email" :value="old('email')" autofocus/>
                                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-xl-4" style="display: none" id="first_name">
                                    <!-- First Name -->
                                    <div class="input-block  local-forms">
                                        <x-input-label for="first_name" :value="__('user.first_name')" required/>
                                        <x-text-input class="block mt-1 w-full" type="text" name="first_name" :value="old('first_name')"  />
                                        <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-xl-4" style="display: none" id="last_name">
                                    <!-- Last Name -->
                                    <div class="input-block  local-forms">
                                        <x-input-label for="last_name" :value="__('user.last_name')" required/>
                                        <x-text-input class="block mt-1 w-full" type="text" name="last_name" :value="old('last_name')" />
                                        <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                                    </div>
                                </div>

                                <!-- ID NUMBER -->
                                <div class="col-6 col-md-6 col-xl-6" style="display: none" id="id_type">
                                    <div class="input-block  local-forms">
                                        <x-input-label for="id_type" :value="__('doctor.id_type')" />
                                        <x-select-input name="id_type" :options="\App\Models\Lista::documentType()" :selected="[old('id_type')]" class="block mt-1 w-full" id_type/>
                                        <x-input-error :messages="$errors->get('id_type')" class="mt-2" />
                                    </div>
                                </div>
                                <div class="col-6 col-md-6 col-xl-6" style="display: none" id="id_number">
                                    <div class=" input-block  local-forms ">
                                        <x-input-label for="id_number" :value="__('doctor.full_id_number')" />
                                        <x-text-input class="block mt-1 w-full" type="text" name="id_number" value="{{old('id_number')}}" autofocus/>
                                        <x-input-error :messages="$errors->get('id_number')" class="mt-2" />
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-xl-4" style="display: none" id="medical_speciality">
                                    <!-- SPECIALTY -->
                                    <div class="input-block  local-forms">
                                        <x-input-label for="medical_speciality" :value="__('doctor.qualification')" />
                                        <x-select-input name="medical_speciality[]" :selected="old('medical_speciality', [])" :options="\App\Models\MedicalSpeciality::pluck('name','id')->toArray()" class="block  w-full"/>
                                        <x-input-error class="mt-2" :messages="$errors->get('medical_speciality')" />
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-xl-4" style="display: none" id="maritalstatus">
                                    <!-- MARITAL STATUS -->
                                    <div class="input-block  local-forms">
                                        <x-input-label for="marital_status" :value="__('patient.marital_status')" />
                                        <x-select-input name="marital_status" :options="App\Enums\MaritalStatus::options()" class="block  w-full" :selected="[old('marital_status')]"/>
                                        <x-input-error class="mt-2" :messages="$errors->get('marital_status')" />
                                    </div>
                                </div>
                                    <div class="col-12 col-md-6 col-xl-6" style="display: none" id="gender">
                                    <!-- GENDER -->
                                    <div class="input-block  local-forms">
                                        <x-input-label for="gender" :value="__('user.gender')" />
                                        <x-select-input name="gender" :options="App\Enums\Gender::options()" class="block  w-full" :selected="old('gender') ? [old('gender')] : []"/>
                                        <x-input-error class="mt-2" :messages="$errors->get('gender')" />
                                    </div>
                                </div>
                                <!-- BIRTHDATE -->
                                <div class=" col-12 col-md-6 col-xl-6" style="display: none" id="birthdate_user">
                                    <div class="input-block local-forms">
                                        <div class="form-group local-forms cal-icon">
                                            <x-input-label for="birthdate" :value="__('user.birthdate')" />
                                            <x-text-input id="birthdate" class="block mt-1 w-full datetimepicker" type="text" name="birth_date" :value="old('birth_date')"/>
                                            <x-input-error class="mt-2" :messages="$errors->get('birth_date')" />
                                        </div>
                                    </div>
                                </div>
                                    <div class="col-12 col-md-6 col-xl-6" style="display: none" id="physical_address">
                                        <!-- ADDRESS -->
                                        <div class="input-block  local-forms">
                                            <x-input-label for="address" :value="__('user.address')" />
                                            <x-text-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address')" />
                                            <x-input-error :messages="$errors->get('address')" class="mt-2" />
                                        </div>
                                    </div>

                                    <div class=" col-12 col-md-6 col-xl-6" style="display: none" id="whatsapp">
                                    <!-- WHATSAPP -->
                                    <div class="input-block  local-forms">
                                        <x-input-label for="whatsapp" :value="__('user.phone')" />
                                        <x-phone-input
                                            name="whatsapp_phone"
                                            id="phone"
                                            :value="old('whatsapp_phone')"
                                            :error="$errors->get('whatsapp_phone')"
                                            class="block mt-1 w-full"
                                        />
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-xl-4" style="display: none" id="client_id">
                                    <!-- CLIENTS -->
                                    <div class="input-block  local-forms">
                                        <x-input-label for="client" :value="__('user.client')" />
                                        @if(auth()->user()->clients()->count()==1 and !auth()->user()->hasRole('admin'))
                                            <input type="text" name="cliente_default" value="{{auth()->user()->getCurrentClient()->name}}" readonly class="form-control">
                                            <input type="hidden" name="clients[]" value="{{auth()->user()->getCurrentClient()->id}}" readonly class="form-control">
                                        @else
                                            @if(auth()->user()->hasRole('admin'))
                                                <x-select-input name="clients[]" :options="\App\Models\Client::pluck('name','id')->toArray()" class="block  w-full"/>
                                            @elseif(auth()->user()->hasRole('doctor') or auth()->user()->hasRole('admin client'))
                                                <x-select-input name="clients[{{auth()->user()->getCurrentClient()->id}}]" :options="auth()->user()->clients()->pluck('clients.name','clients.id')->toArray()" multiple class="block  w-full"/>
                                            @else
                                                <x-select-input name="clients[]" :options="\App\Models\Client::whereIn('id',[3,5])->pluck('name','id')->toArray()" class="block  w-full"/>
                                            @endif
                                            <x-input-error class="mt-2" :messages="$errors->get('clients')" />
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                    <div class="col-6 col-md-6 col-xl-6" style="display: none" id="registry_field">
                                        <!-- REGISTRY -->
                                        <div class="input-block local-forms">
                                            <x-input-label for="registry" value="{{__('doctor.registry')}}" required="true"/>
                                            <x-text-input id="registry" class="block mt-1 w-full" type="registry" name="registry" :value="old('registry')" maxlength="60"/>
                                            <x-input-error :messages="$errors->get('registry')" class="mt-2" />
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-6 col-xl-6" style="display: none" id="licensecode_field">
                                        <!-- LICENSE CODE -->
                                        <div class="input-block local-forms">
                                            <x-input-label for="licence_code" value="{{__('doctor.licence_code')}}" required="true"/>
                                            <x-text-input id="licence_code" class="block mt-1 w-full" type="licence_code" name="licence_code" :value="old('licence_code')" maxlength="60"/>
                                            <x-input-error :messages="$errors->get('licence_code')" class="mt-2" />
                                        </div>
                                    </div>
                                </div>
                            <div class="row" style="display: none" id="image">
                                <!-- PICTURE -->
                                <div class="col-12 col-md-12 col-xl-12">
                                    <div class="form-group local-top-form">
                                        <label class="local-top" for="avatar">Avatar</label>
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
                                        <x-input-label for="password" :value="__('Contraseña')" />
                                        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                        <p>&nbsp;</p>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-xl-6" style="display: none" id="confirm_password">
                                    <div class="input-block  local-forms">
                                        <x-input-label for="password_confirmation" :value="__('user.confirm_password')" />
                                        <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                                        <p>&nbsp;</p>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center justify-end mt-4">
                                <div class="doctor-submit text-end">
                                    @can('create',auth()->user())
                                    <button type="submit" class="btn btn-primary submit-form me-2">     {{ __('button.register') }} </button>
                                    @endcan
                                    <a class="btn btn-secondary cancel-form" href="{{ route('user.index') }}">  {{ __('button.cancel') }}</a>
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
                changeByType(type);
            });

            function changeByType(type) {

                console.log('Tipo :'+type);

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
                $("#registry_field").hide();
                $("#licensecode_field").hide();

                switch(parseInt(type)) {
                    /*-----FORMULARIO PARA ROLE ASISTENTE MEDICO-----*/
                    case 2:
                        $("#medical_speciality").show();
                        $("#registry_field").show();
                        $("#licensecode_field").show();
                    case 6:
                        $("#client_id").show();
                        $("#id_type").show();
                        $("#id_number").show();
                       /* $("#medical_speciality").show();*/
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
                        //$("#client").show();
                        $('#first_name').show();
                        $("#last_name").show();
                        $("#email").show();
                        $("#image").show();
                        $("#password").show();
                        $("#confirm_password").show();
                        $("#client_id").show();
                        break;
                // code block
                }
        }

            // Ejecutar changeByType automáticamente si hay un rol seleccionado (por ejemplo, después de un error de validación)
            @if(old('rol'))
                changeByType({{ old('rol') }});
            @elseif(request()->get('role_id'))
                changeByType({{ request()->get('role_id') }});
            @endif

            // Forzar submit del formulario evitando interferencia de Livewire
            $('.submit-form').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                console.log('Forzando submit del formulario...');

                // Usar el método nativo de submit en lugar del de jQuery
                document.getElementById('form').submit();
            });
        });
    </script>
    @endpush
</x-app-layout>
