<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Módulo Financiero
                @endslot
                @slot('li_1')
                    Plan de Cuentas
                @endslot
            @endcomponent

            <div class="card">
                @livewire('accounting.account-data-table')
            </div>
        </div>
    </div>
</x-app-layout>
