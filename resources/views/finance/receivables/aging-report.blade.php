<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Módulo Financiero
                @endslot
                @slot('li_1')
                    Reporte de Antigüedad - CxC
                @endslot
            @endcomponent
            <div class="row">
                @livewire('finance.accounts-receivable.receivables-aging-report')
            </div>
        </div>
    </div>
</x-app-layout>
