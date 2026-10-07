<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Módulo Financiero
                @endslot
                @slot('li_1')
                    Proveedores
                @endslot
            @endcomponent

            <div class="card">
                @livewire('finance.supplier.data-table')
            </div>
        </div>
    </div>
</x-app-layout>
