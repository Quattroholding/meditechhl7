<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            @component('components.page-header')
                @slot('title')
                    Crear Banco
                @endslot
                @slot('li_1')
                    Tesorería
                @endslot
            @endcomponent

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="col-12">
                                <div class="form-heading">
                                    <h4>Crear Banco</h4>
                                </div>
                            </div>
                            @livewire('treasury.bank.bank-modal', ['isModal' => false])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
