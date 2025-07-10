<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Personalización de Tema
                @endslot
                @slot('li_1')
                    Configuración de Branding para {{ $client->name }}
                @endslot
            @endcomponent
            <!-- /Page Header -->

            <div class="row">
                <div class="col-sm-12">
                    <livewire:admin.theme-manager :client_id="$client_id" />
                </div>
            </div>
        </div>
        
        @component('components.notification-box')
        @endcomponent
    </div>
</x-app-layout>