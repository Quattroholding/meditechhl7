<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Módulo Financiero
                @endslot
                @slot('li_1')
                    Centros de Costo
                @endslot
            @endcomponent


            <div class="card">
                @livewire('finance.cost-center.data-table')
            </div>
        </div>
    </div>
</x-app-layout>
