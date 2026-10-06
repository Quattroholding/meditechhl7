<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Centros de Costo
                @endslot
                @slot('li_1')
                    Módulo Financiero
                @endslot
            @endcomponent

            @include('partials.message')
            <div class="card">
                @livewire('finance.cost-center.data-table')
            </div>
        </div>
    </div>
</x-app-layout>
