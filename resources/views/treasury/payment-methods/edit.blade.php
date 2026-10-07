<x-app-layout>
    <div class="page-wrapper">
        <div class="row content">
            @component('components.page-header')
                @slot('title')
                    Módulo Financiero
                @endslot
                @slot('li_1')
                    Editar Método de Pago
                @endslot
            @endcomponent
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="col-12">
                            <div class="form-heading">
                                <h4>Editar Método de Pago</h4>
                            </div>
                        </div>
                        @livewire('treasury.payment-method.payment-method-modal', ['isModal' => false, 'paymentMethod' => $paymentMethod])
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
