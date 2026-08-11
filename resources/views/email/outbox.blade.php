<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Correos de Salida - Exchange Office 365
                @endslot
                @slot('li_1')
                    Seguimiento de Correos Enviados
                @endslot
            @endcomponent
            <!-- /Page Header -->

            <livewire:email.outbox-data-table />
        </div>
    </div>
</x-app-layout>
