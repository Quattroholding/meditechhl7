<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Módulo Financiero
                @endslot
                @slot('li_1')
                    Facturas de Proveedor
                @endslot
            @endcomponent

            <div class="card">
                @livewire('finance.accounts-payable.invoice-data-table')
            </div>
        </div>
    </div>
</x-app-layout>
