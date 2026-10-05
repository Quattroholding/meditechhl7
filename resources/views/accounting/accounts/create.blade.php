<x-app-layout>
    <div class="page-wrapper">
        <div class="row content">
            @component('components.page-header')
                @slot('title')
                    Crear Cuenta Contable
                @endslot
                @slot('li_1')
                    Contabilidad
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
