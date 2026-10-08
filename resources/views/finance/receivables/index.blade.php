<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Módulo Financiero
                @endslot
                @slot('li_1')
                        Cuentas por Cobrar
                @endslot
            @endcomponent

            <div class="card">
                @livewire('finance.accounts-receivable.data-table')
            </div>
        </div>
    </div>
</x-app-layout>
