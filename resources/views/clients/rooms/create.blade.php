<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    {{ __('client.title') }}
                @endslot
                @slot('li_1')
                    {{ __('generic.create') }} {{ __('client.room') }}
                @endslot
            @endcomponent
            <!-- /Page Header -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="col-12">
                                <div class="form-heading">
                                    <h4>  {{ __('generic.create') }} {{ __('client.room') }}</h4>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('client.room.store') }}" enctype="multipart/form-data" id="form">
                            @csrf
                            <!-- Branch -->
                            <div class="input-block  local-forms">
                                <x-input-label for="branch_id" :value="__('client.branch')" required/>
                                <x-select-input name="branch_id" :options="\App\Models\Branch::pluck('name','id')->toArray()" :selected="[null]" class="block w-full" autofocus/>
                                <x-input-error :messages="$errors->get('branch_id')"/>
                            </div>
                            <!-- Name -->
                            <div class="input-block  local-forms">
                                <x-input-label for="name" :value="__('Nombre')" required/>
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')"  />
                                <x-input-error :messages="$errors->get('name')"/>
                            </div>
                            <!-- NUMBER -->
                            <div class="input-block  local-forms">
                                <x-input-label for="ruc" :value="__('Número')" />
                                <x-text-input id="number" class="block mt-1 w-full" type="text" name="number" :value="old('number')"/>
                                <x-input-error :messages="$errors->get('number')"/>
                            </div>
                            <!-- FLOOR -->
                            <div class="input-block  local-forms">
                                <x-input-label for="floor" :value="__('Piso')" />
                                <x-text-input id="floor" class="block mt-1 w-full" type="text" name="floor" :value="old('floor')"/>
                                <x-input-error :messages="$errors->get('floor')"/>
                            </div>
                            <div class="flex items-center justify-end mt-4">
                                <div class="doctor-submit text-end">
                                    <button type="submit" class="btn btn-primary submit-form me-2">     {{ __('button.register') }} </button>
                                    <a class="btn btn-primary cancel-form" href="{{ route('client.index') }}">  {{ __('button.cancel') }}</a>
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
