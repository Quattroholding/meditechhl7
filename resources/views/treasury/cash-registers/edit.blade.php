<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            @component('components.page-header')
                @slot('title')
                    Módulo Financiero
                @endslot
                @slot('li_1')
                    Editar Caja
                @endslot
            @endcomponent

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="col-12">
                                <div class="form-heading">
                                    <h4>Editar Caja</h4>
                                </div>
                            </div>
                            @livewire('treasury.cash-register.cash-register-modal', ['isModal' => false, 'cashRegister' => $cashRegister])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
