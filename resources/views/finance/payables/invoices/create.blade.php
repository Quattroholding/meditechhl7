<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            @component('components.page-header')
                @slot('title')
                    Módulo Financiero
                @endslot
                @slot('li_1')
                    Crear Factura de Proveedor
                @endslot
            @endcomponent

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="col-12">
                                <div class="form-heading">
                                    <h4>Crear Factura de Proveedor</h4>
                                </div>
                            </div>
                            @livewire('finance.accounts-payable.supplier-invoice-modal', ['isModal' => false])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
