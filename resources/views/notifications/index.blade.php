<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Notificaciones
                @endslot
                @slot('li_1')
                    Historial de Notificaciones
                @endslot
            @endcomponent
            <!-- /Page Header -->

            <livewire:notification.data-table />
        </div>
    </div>
</x-app-layout>
