<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    {{ __('Perfil de usuario') }}
                @endslot
                @slot('li_1')
                 {{ __(' Configuración de Autenticación de Dos Factores') }}
                @endslot
            @endcomponent
            <!-- /Page Header -->
            <div class="row">
                <div class="col-12">
                    <div class="card">

                        <div class="card-body">
                            @livewire('user.two-factor-authentication')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
