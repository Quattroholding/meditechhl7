<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Facturas de Suscripción
                @endslot
                @slot('li_1')
                    Lista de Facturas
                @endslot
            @endcomponent
            <!-- /Page Header -->
            @include('partials.message')
            <!-- /Page Header -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            @livewire('subscription.invoice-data-table')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
