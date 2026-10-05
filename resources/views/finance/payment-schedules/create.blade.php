<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            @component('components.page-header')
                @slot('title')
                    Crear Programa de Pagos
                @endslot
                @slot('li_1')
                    Módulo Financiero
                @endslot
            @endcomponent

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="col-12">
                                <div class="form-heading">
                                    <h4>Crear Programa de Pagos</h4>
                                </div>
                            </div>
                            @livewire('finance.accounts-payable.payment-schedule-modal', ['isModal' => false, 'invoice' => $invoice])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
