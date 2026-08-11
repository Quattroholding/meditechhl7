<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Seguimiento de Mensajes
                @endslot
                @slot('li_1')
                    Message Trace - Verificación de Entrega
                @endslot
            @endcomponent
            <!-- /Page Header -->

            <livewire:email.message-trace-table />
        </div>
    </div>
</x-app-layout>
