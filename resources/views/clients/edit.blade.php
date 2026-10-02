<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    {{ __('client.title') }}
                @endslot
                @slot('li_1')
                    {{ __('generic.create') }} {{ __('client.titles') }}
                @endslot
            @endcomponent
            <!-- /Page Header -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="col-12">
                                <div class="form-heading">
                                    <h4>  {{ __('generic.edit') }} {{ __('client.title') }}</h4>
                                </div>
                            </div>
                    <form method="POST" action="{{ route('client.update',$data->id) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class=" col-12 col-md-6 col-xl-4">
                            <!-- SHORT NAME -->
                            <div class="input-block  local-forms">
                                <x-input-label for="name" :value="__('Nombre Corto')" required="true"/>
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="$data->name" autofocus/>
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-xl-8">
                            <!-- LONG NAME -->
                            <div class=" input-block  local-forms ">
                                <x-input-label for="long_name" :value="__('Nombre Completo')" required="true"/>
                                <x-text-input id="long_name" class="block mt-1 w-full" type="text" name="long_name" :value="$data->long_name"/>
                                <x-input-error :messages="$errors->get('long_name')" class="mt-2" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class=" col-12 col-md-6 col-xl-2">
                            <!-- RUC/DV -->
                            <div class="input-block  local-forms">
                                <x-input-label for="ruc" :value="__('DV')" required/>
                                <x-text-input id="dv" class="block mt-1 w-full" type="number" name="dv" :value="$data->dv" maxlength="2"  min="1"/>
                                <x-input-error :messages="$errors->get('ruc')" class="mt-2" />
                            </div>
                        </div>
                        <div class=" col-12 col-md-6 col-xl-6">
                            <!-- RUC/DV -->
                            <div class="input-block  local-forms">
                                <x-input-label for="ruc" :value="__('Ruc')" required/>
                                <x-text-input id="ruc" class="block mt-1 w-full" type="text" name="ruc" :value="$data->ruc"/>
                                <x-input-error :messages="$errors->get('ruc')" class="mt-2" />
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-xl-4">
                            <!-- PACKAGE -->
                            <div class="input-block  local-forms ">
                                <x-input-label for="package_id" :value="__('Paquete')" required/>
                                <x-select-input required name="package_id" :options="\App\Models\Package::pluck('name','id')->toArray()" :selected="[$data->package_id]" class="block w-full"/>
                                <x-input-error :messages="$errors->get('package_id')" class="mt-2" />
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-12 col-md-6 col-xl-4">
                            <!-- EMAIL -->
                            <div class="input-block  local-forms ">
                                <x-input-label for="email" :value="__('Email')" required/>
                                <x-text-input id="email2" class="block mt-1 w-full" type="email" name="email" :value="$data->email"/>
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                        </div>
                        <div class=" col-12 col-md-6 col-xl-4">
                            <!-- WHATSAPP -->
                            <div class="input-block  local-forms">
                                <x-input-label for="whatsapp" :value="__('Telefono')" />
                                <x-phone-input
                                    name="whatsapp"
                                    id="whatsapp"
                                    :value="$data->whatsapp"
                                    :error="$errors->get('whatsapp')"
                                    class="block mt-1 w-full"
                                />
                            </div>
                        </div>
                        <!-- IMAGE -->
                        <div class="col-12 col-md-6 col-xl-6">
                            <div class="form-group local-top-form">
                                <label class="local-top">Logo</label>
                                <div class="settings-btn upload-files-avator">
                                    <input type="file" accept="image/*" name="logo" id="file"
                                           onchange="loadFile(event)" class="hide-input">
                                    <label for="file" class="upload">{{__('generic.Choose File')}}</label>
                                </div>
                            </div>
                            <div class="upload-images upload-size">
                                @if(!empty($data->logo))
                                    <img src="{{ url('storage/'.$data->logo) }}" alt="Image" id="preview">
                                @else
                                    <img src="{{ URL::asset('/assets/img/favicon.png')  }}" alt="Image" id="preview">
                                @endif
                                <a href="javascript:void(0);" class="btn-icon logo-hide-btn">
                                    <i class="feather-x-circle"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Características Especiales -->
                    <div class="row">
                        <div class="col-12">
                            <h5 class="mb-3 text-primary">Características Especiales</h5>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-md-6 col-xl-6">
                            <div class="form-check local-forms">
                                <input type="hidden" name="active" value="0">
                                <input class="form-check-input" type="checkbox" id="active" name="active" value="1" {{ $data->active ? 'checked' : '' }}>
                                <label class="form-check-label" for="active">
                                    Cliente Activo
                                </label>
                                <small class="text-muted d-block">Si está marcado, el cliente podrá usar la plataforma</small>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-xl-6">
                            <div class="form-check local-forms">
                                <input type="hidden" name="diagnostic_ai_suggestions" value="0">
                                <input class="form-check-input" type="checkbox" id="diagnostic_ai_suggestions" name="diagnostic_ai_suggestions" value="1" {{ $data->diagnostic_ai_suggestions ? 'checked' : '' }}>
                                <label class="form-check-label" for="diagnostic_ai_suggestions">
                                    Sugerencias de Diagnóstico AI
                                </label>
                                <small class="text-muted d-block">Habilitar sugerencias de diagnóstico con IA</small>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12 col-md-6 col-xl-6">
                            <div class="form-check local-forms">
                                <input type="hidden" name="show_consultation_timer" value="0">
                                <input class="form-check-input" type="checkbox" id="show_consultation_timer" name="show_consultation_timer" value="1" {{ $data->show_consultation_timer ? 'checked' : '' }}>
                                <label class="form-check-label" for="show_consultation_timer">
                                    Mostrar Temporizador de Consulta
                                </label>
                                <small class="text-muted d-block">Mostrar cronómetro durante las consultas</small>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-xl-6">
                            <div class="form-check local-forms">
                                <input type="hidden" name="hemoscreen_only" value="0">
                                <input class="form-check-input" type="checkbox" id="hemoscreen_only" name="hemoscreen_only" value="1" {{ $data->hemoscreen_only ? 'checked' : '' }}>
                                <label class="form-check-label" for="hemoscreen_only">
                                    Solo Hemoscreen
                                </label>
                                <small class="text-muted d-block">Restringir a funcionalidades de Hemoscreen únicamente</small>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12 col-md-6 col-xl-6">
                            <div class="form-check local-forms">
                                <input type="hidden" name="voice_dictation_enabled" value="0">
                                <input class="form-check-input" type="checkbox" id="voice_dictation_enabled" name="voice_dictation_enabled" value="1" {{ $data->voice_dictation_enabled ? 'checked' : '' }}>
                                <label class="form-check-label" for="voice_dictation_enabled">
                                    Dictado por Voz Habilitado
                                </label>
                                <small class="text-muted d-block">Permitir el uso de dictado por voz en las consultas</small>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <div class="doctor-submit text-end">
                            <button type="submit" class="btn btn-primary submit-form me-2">     {{ __('button.edit') }} </button>
                            <a class="btn btn-secondary cancel-form" href="{{ route('patient.index') }}">  {{ __('button.cancel') }}</a>
                        </div>
                    </div>
                    </form>
                </div>
            </div>

            <!-- Referral Code Section -->
            <div class="col-sm-12 mt-4">
                <x-referral-code-display :client="$data" />
            </div>
        </div>
    </div>
        </div>
    </div>
</x-app-layout>
