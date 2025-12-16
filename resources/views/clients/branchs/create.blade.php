<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    {{ __('client.branch') }}
                @endslot
                @slot('li_1')
                    {{ __('generic.create') }} {{ __('client.branch') }}
                @endslot
            @endcomponent
            <!-- /Page Header -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="col-12">
                                <div class="form-heading">
                                    <h4>  {{ __('generic.create') }} {{ __('client.branch') }}</h4>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('client.branch.store') }}" enctype="multipart/form-data" id="form">
                            @csrf
                            @if(auth()->user()->clients()->count()==1)
                                    <input name="client_id" value="{{auth()->user()->default_client_id}}" type="hidden">
                            @else
                            <!-- Client -->
                            <div class="input-block  local-forms">
                                <x-input-label for="client_id" :value="__('client.title')" required/>
                                @if(!auth()->user() && auth()->user()->hasRole('doctor'))
                                    <x-select-input name="client_id" :options="\App\Models\Client::pluck('name','id')->toArray()" :selected="[auth()->user()->default_client_id]" class="block w-full" autofocus/>
                                @else
                                    <x-select-input name="client_id" :options="auth()->user()->clients()->pluck('name','clients.id')->toArray()" :selected="[auth()->user()->default_client_id]" class="block w-full" autofocus/>
                                @endif
                                <x-input-error :messages="$errors->get('client_id')" class="mt-2" />
                            </div>
                            @endif
                            <!-- TYPE -->
                            <div class="input-block  local-forms">
                                <x-input-label for="type" :value="__('Tipo')" required/>
                                <x-select-input name="type" :options="\App\Models\Lista::branchType()" :selected="[old('type')]" class="block w-full"/>
                                <x-input-error :messages="$errors->get('type')" class="mt-2" />
                            </div>
                            <!-- Name -->
                            <div class="input-block  local-forms">
                                <x-input-label for="name" :value="__('Nombre')" required/>
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                            <!-- PHONE -->
                            <div class="input-block  local-forms">
                                <x-input-label for="phone" :value="__('Teléfono')" required/>
                                <input  id="phone" class="block mt-1 w-full input-phone" type="tel" name="phone" value="{{old('phone')}}">
                                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                            </div>
                            <!-- ADDRESS -->
                            <div class="input-block  local-forms">
                                <x-input-label for="address" :value="__('Dirección')" />
                                <x-textarea-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address')"/>
                                <x-input-error :messages="$errors->get('address')" class="mt-2" />
                            </div>
                            <!-- COUNTRY -->
                            <div class="input-block  local-forms">
                                <x-input-label for="country_id" value="País"/>
                                <select name="country_id" id="country_id" class="form-control">
                                    <option value="">Seleccione un país...</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('country_id')" class="mt-2" />
                            </div>
                            <!-- STATE -->
                            <div class="input-block  local-forms">
                                <x-input-label for="state_id" value="Provincia/Estado"/>
                                <select name="state_id" id="state_id" class="form-control" disabled>
                                    <option value="">Seleccione una provincia...</option>
                                </select>
                                <x-input-error :messages="$errors->get('state_id')" class="mt-2" />
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <div class="doctor-submit text-end">
                                    <button type="submit" class="btn btn-primary submit-form me-2">     {{ __('button.register') }} </button>
                                    <a class="btn btn-secondary cancel-form" href="{{ route('client.index') }}">  {{ __('button.cancel') }}</a>
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
        document.addEventListener('DOMContentLoaded', function() {
            const countrySelect = document.getElementById('country_id');
            const stateSelect = document.getElementById('state_id');

            countrySelect.addEventListener('change', function() {
                const countryId = this.value;

                // Limpiar y deshabilitar el select de estados
                stateSelect.innerHTML = '<option value="">Seleccione una provincia...</option>';
                stateSelect.disabled = true;

                if (countryId) {
                    // Realizar petición AJAX para obtener los estados
                    fetch(`/api/states/${countryId}`)
                        .then(response => response.json())
                        .then(states => {
                            if (states.length > 0) {
                                states.forEach(state => {
                                    const option = document.createElement('option');
                                    option.value = state.id;
                                    option.textContent = state.name;
                                    stateSelect.appendChild(option);
                                });
                                stateSelect.disabled = false;
                            }
                        })
                        .catch(error => {
                            console.error('Error al cargar los estados:', error);
                        });
                }
            });

            // Si hay un valor old para country_id, cargar los estados
            @if(old('country_id'))
                const oldCountryId = '{{ old('country_id') }}';
                const oldStateId = '{{ old('state_id') }}';

                if (oldCountryId) {
                    fetch(`/api/states/${oldCountryId}`)
                        .then(response => response.json())
                        .then(states => {
                            if (states.length > 0) {
                                states.forEach(state => {
                                    const option = document.createElement('option');
                                    option.value = state.id;
                                    option.textContent = state.name;
                                    option.selected = state.id == oldStateId;
                                    stateSelect.appendChild(option);
                                });
                                stateSelect.disabled = false;
                            }
                        });
                }
            @endif
        });
    </script>
    @endpush
</x-app-layout>
