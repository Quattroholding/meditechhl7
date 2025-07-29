<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Paquetes
                @endslot
                @slot('li_1')
                    Editar Paquete
                @endslot
            @endcomponent
            <!-- /Page Header -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="col-12">
                                <div class="form-heading">
                                    <h4>Editar Paquete</h4>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('package.update', $data->id) }}">
                            @csrf
                            <div class="row">
                                <div class="col-12 col-md-6 col-xl-6">
                                    <!-- NAME -->
                                    <div class="input-block local-forms">
                                        <x-input-label for="name" :value="__('Nombre')" required/>
                                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="$data->name" autofocus/>
                                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-xl-6">
                                    <!-- MAX USERS -->
                                    <div class="input-block local-forms">
                                        <x-input-label for="max_users" :value="__('Máximo de Usuarios')" required/>
                                        <x-text-input id="max_users" class="block mt-1 w-full" type="number" name="max_users" :value="$data->max_users" min="1"/>
                                        <x-input-error :messages="$errors->get('max_users')" class="mt-2" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <!-- DESCRIPTION -->
                                    <div class="input-block local-forms">
                                        <x-input-label for="description" :value="__('Descripción')" required/>
                                        <textarea id="description" class="form-control" name="description" rows="4">{{ $data->description }}</textarea>
                                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <!-- IS ACTIVE -->
                                    <div class="input-block local-forms">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ $data->is_active ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">
                                                Activo
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center justify-end mt-4">
                                <div class="doctor-submit text-end">
                                    <button type="submit" class="btn btn-primary submit-form me-2">Actualizar</button>
                                    <a class="btn btn-primary cancel-form" href="{{ route('package.index') }}">Cancelar</a>
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