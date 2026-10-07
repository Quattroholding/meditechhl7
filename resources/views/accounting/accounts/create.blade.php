<x-app-layout>
    <div class="page-wrapper">
        <div class="row content">
            @component('components.page-header')
                @slot('title')
                    Módulo Financiero
                @endslot
                @slot('li_1')
                    Crear Cuenta Contable
                @endslot
            @endcomponent
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="col-12">
                            <div class="form-heading">
                                <h4>Crear Cuenta Contable</h4>
                            </div>
                        </div>
                        @livewire('accounting.account.accounting-account-modal', ['isModal' => false])
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
