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
                        <div class=" col-12 col-md-6 col-xl-4">
                            <!-- RUC/DV -->
                            <div class="input-block  local-forms">
                                <x-input-label for="ruc" :value="__('Ruc')" />
                                <x-text-input id="ruc" class="block mt-1 w-full" type="number" name="ruc" :value="$data->ruc"/>
                                <x-input-error :messages="$errors->get('ruc')" class="mt-2" />
                            </div>
                        </div>
                        <div class=" col-12 col-md-6 col-xl-4">
                            <!-- RUC/DV -->
                            <div class="input-block  local-forms">
                                <x-input-label for="ruc" :value="__('DV')" />
                                <x-text-input id="dv" class="block mt-1 w-full" type="number" name="dv" :value="$data->dv" maxlength="2"  min="1"/>
                                <x-input-error :messages="$errors->get('ruc')" class="mt-2" />
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-xl-4">
                            <!-- EMAIL -->
                            <div class="input-block  local-forms ">
                                <x-input-label for="email" :value="__('Email')" />
                                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="$data->email"/>
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class=" col-12 col-md-6 col-xl-4">
                            <!-- WHATSAPP -->
                            <div class="input-block  local-forms">
                                <x-input-label for="whatsapp" :value="__('Telefono')" />
                                <x-text-input id="whatsapp" class="block mt-1 w-full" type="text" name="whatsapp" :value="$data->whatsapp"/>
                                <x-input-error :messages="$errors->get('whatsapp')" class="mt-2" />
                            </div>
                        </div>
                        <!-- IMAGE -->
                        <div class="col-12 col-md-6 col-xl-6">
                            <div class="form-group local-top-form">
                                <label class="local-top">Logo <span class="login-danger">*</span></label>
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
                    <div class="flex items-center justify-end mt-4">
                        <div class="doctor-submit text-end">
                            <button type="submit" class="btn btn-primary submit-form me-2">     {{ __('button.edit') }} </button>
                            <a class="btn btn-primary cancel-form" href="{{ route('patient.index') }}">  {{ __('button.cancel') }}</a>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
