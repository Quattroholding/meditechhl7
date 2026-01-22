<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Sistema de Tickets de Soporte
                @endslot
                @slot('li_1')
                    Gestión de Tickets
                @endslot
            @endcomponent
            <!-- /Page Header -->

            <livewire:ticket-system.data-table />
        </div>
        @component('components.notification-box')
        @endcomponent
    </div>
</x-app-layout>
