<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Módulo Financiero
                @endslot
                @slot('li_1')
                    Asientos Contables
                @endslot
            @endcomponent

            <div class="card">
                @livewire('accounting.journal-entry-data-table')
            </div>
        </div>
    </div>
</x-app-layout>
