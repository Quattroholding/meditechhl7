<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Módulo Financiero
                @endslot
                @slot('li_1')
                    Cajas
                @endslot
            @endcomponent

            <div class="row">
                <div class="col-sm-12">
                    <div class="card card-table show-entire p-2">

                        <div class="card-body">
                            @livewire('treasury.cash-register.cash-register-data-table')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
