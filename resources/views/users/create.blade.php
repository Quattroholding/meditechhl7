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
                                <div class="col-12 col-md-6 col-xl-4">
                                    <!-- EMAIL -->
                                    <div class="input-block  local-forms">
                                        <x-input-label for="email" :value="__('user.email')" required/>
                                        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" autofocus/>
                                        <x-input-error :messages="$errors->get('email')" class="mt-2" /><p>&nbsp;</p>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-xl-4">
                                    <!-- First Name -->
                                    <div class="input-block  local-forms">
                                        <x-input-label for="first_name" :value="__('user.first_name')" required/>
                                        <x-text-input id="first_name" class="block mt-1 w-full" type="text" name="first_name" :value="old('first_name')" required />
                                        <x-input-error :messages="$errors->get('first_name')" class="mt-2" /><p>&nbsp;</p>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-xl-4">
                                    <!-- Last Name -->
                                    <div class="input-block  local-forms">
                                        <x-input-label for="last_name" :value="__('user.last_name')" required/>
                                        <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name" :value="old('last_name')" required/>
                                        <x-input-error :messages="$errors->get('last_name')" class="mt-2" /><p>&nbsp;</p>
                                    </div>
                                </div>
                                    <div class="col-12 col-md-6 col-xl-6">
                                    <!-- CLIENTS -->
                                    <div class="input-block  local-forms">
                                        <x-input-label for="client" :value="__('user.gender')" required/>
                                        <x-select-input name="gender" :options="['male'=> 'male', 'female'=>'female', 'other'=>'other', 'unknow'=>'unknow']" class="block  w-full"/>
                                        <x-input-error class="mt-2" :messages="$errors->get('last_name')" /><p>&nbsp;</p>
                                    </div>
                                </div>     
                                <!-- BIRTHDATE -->
                                <div class=" col-12 col-md-6 col-xl-6">
                                    <div class="input-block local-forms">
                                        <div class="form-group local-forms cal-icon">
                                            <x-input-label for="birthdate" :value="__('user.birthdate')" required="true"/>
                                            <x-text-input id="birthdate" class="block mt-1 w-full datetimepicker" type="text" name="birth_date" :value="old('birthdate')"/>
                                        </div>
                                    </div>
                                </div>  
                                    <div class="col-12 col-md-6 col-xl-6">
                                        <!-- Rol -->
                                        <div class="input-block  local-forms">
                                            <x-input-label for="address" :value="__('user.address')" required/>
                                            <x-text-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address')" required/>
                                            <x-input-error :messages="$errors->get('address')" class="mt-2" /><p>&nbsp;</p>
                                        </div>
                                    </div>

                                    <div class=" col-12 col-md-6 col-xl-6">
                                    <!-- WHATSAPP -->
                                    <div class="input-block  local-forms">
                                        <x-input-label for="whatsapp" :value="__('phone')" />
                                        <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone" :value="old('phone')"/>
                                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                                    </div>
                                </div>
                                
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6 col-xl-4">
                                    <!-- CLIENTS -->
                                    <div class="input-block  local-forms">
                                        <x-input-label for="client" :value="__('user.client')" required/>
                                        <x-select-input name="clients[]" :options="\App\Models\Client::pluck('name','id')->toArray()" :selected="$selected_clients" class="block  w-full"/>
                                        <x-input-error class="mt-2" :messages="$errors->get('last_name')" /><p>&nbsp;</p>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-xl-4">
                                    <!-- Rol -->
                                    <div class="input-block  local-forms">
                                        <x-input-label for="rol" :value="__('user.rol')" required/>
                                        @if(request()->get('role_id')==2)
                                            <x-select-input name="rol" :options="\App\Models\Rol::whereId(2)->pluck('name','id')->toArray()" :selected="[2]" class="block w-full"/>
                                        @elseif(request()->get('role_id')==5)
                                            <x-select-input name="rol" :options="\App\Models\Rol::whereId(3)->pluck('name','id')->toArray()" :selected="[3]" class="block w-full"/>
                                        @else
                                            <x-select-input name="rol" :options="\App\Models\Rol::pluck('name','id')->toArray()" :selected="[null]" class="block w-full"/>
                                        @endif
                                        <x-input-error :messages="$errors->get('rol')" class="mt-2" /><p>&nbsp;</p>
                                    </div>
                                </div>
                                 @if(request()->get('role_id')==2)       
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <!-- Rol -->
                                        <div class="input-block  local-forms">
                                            <x-input-label for="medical_speciality" :value="__('user.speciality')" required/>
                                            <x-select-input name="medical_speciality" :options="\App\Models\MedicalSpeciality::pluck('name','id')->toArray()" class="block w-full"/>
                                            <x-input-error :messages="$errors->get('medical_speciality')" class="mt-2" /><p>&nbsp;</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="row">
                                <!-- PICTURE -->
                                <div class="col-12 col-md-6 col-xl-12">
                                    <div class="form-group local-top-form">
                                        <label class="local-top">Avatar <span class="login-danger">*</span></label>
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
                                <div class="col-12 col-md-6 col-xl-6">
                                    <div class="input-block  local-forms">
                                        <x-input-label for="update_password_password" :value="__('Contraseña')" />
                                        <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                                        <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" /><p>&nbsp;</p>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-xl-6">
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
</x-app-layout>
