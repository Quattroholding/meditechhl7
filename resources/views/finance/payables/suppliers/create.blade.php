<x-app-layout>
    <div class="page-wrapper">
        <div class="row content">
            @component('components.page-header')
                @slot('title')
                    Módulo Financiero
                @endslot
                @slot('li_1')
                    Crear Proveedor
                @endslot
            @endcomponent
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="col-12">
                            <div class="form-heading">
                                <h4>Crear Proveedor</h4>
                            </div>
                        </div>
                        @livewire('finance.supplier.supplier-modal', ['isModal' => false])
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
