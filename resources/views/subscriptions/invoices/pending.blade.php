<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Facturas Pendientes
                @endslot
                @slot('li_1')
                    Facturas por Pagar
                @endslot
            @endcomponent
            <!-- /Page Header -->

            <div class="row">
                <div class="col-sm-12">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Las siguientes facturas están pendientes de pago. Por favor, realice el pago para evitar la suspensión del servicio.
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    @livewire('subscription.invoice-data-table', ['statusFilter' => 'pending'])
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
