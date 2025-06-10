<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    {{ __('client.branch') }}
                @endslot
                @slot('li_1')
                    {{ __('generic.edit') }} {{ __('client.branch') }}
                @endslot
            @endcomponent
            <!-- /Page Header -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="col-12">
                                <div class="form-heading">
                                    <h4>  {{ __('generic.edit') }} {{ __('client.branch') }}</h4>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('client.branch.update',$data->id) }}">
                                @csrf
                                <!-- Client -->
                                <div class="input-block  local-forms">
                                    <x-input-label for="client_id" :value="__('client.title')" required/>
                                    <x-select-input name="client_id" :options="\App\Models\Client::pluck('name','id')->toArray()" :selected="[$data->client_id]" class="block w-full" autofocus/>
                                    <x-input-error :messages="$errors->get('client_id')" class="mt-2" />
                                </div>
                                <!-- TYPE -->
                                <div class="input-block  local-forms">
                                    <x-input-label for="type" :value="__('client.branch.type')" required/>
                                    <x-select-input name="type" :options="\App\Models\Lista::branchType()" :selected="[$data->type]" class="block w-full"/>
                                    <x-input-error :messages="$errors->get('type')" class="mt-2" />
                                </div>
                                <!-- Name -->
                                <div class="input-block  local-forms">
                                    <x-input-label for="name" :value="__('client.branch.name')" required/>
                                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="$data->name" />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>
                                <!-- PHONE -->
                                <div class="input-block  local-forms">
                                    <x-input-label for="phone" :value="__('client.branch.phone')" required/>
                                    <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone" :value="$data->phone"/>
                                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                                </div>
                                <!-- ADDRESS -->
                                <div class="input-block  local-forms">
                                    <x-input-label for="address" :value="__('client.branch.address')" />
                                    <x-textarea-input id="address" class="block mt-1 w-full" type="text" name="address" :value="$data->address">{{$data->address}}</x-textarea-input>
                                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                                </div>

                                <div class="flex items-center justify-end mt-4">
                                    <div class="doctor-submit text-end">
                                        <button type="submit" class="btn btn-primary submit-form me-2">     {{ __('button.update') }} </button>
                                        <a class="btn btn-primary cancel-form" href="{{ route('client.branch.index') }}">  {{ __('button.cancel') }}</a>
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
