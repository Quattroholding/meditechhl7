<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            @component('components.page-header')
                @slot('title')
                    Crear Caja
                @endslot
                @slot('li_1')
                    Tesorería
                @endslot
            @endcomponent

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            @livewire('treasury.cash-register.cash-register-modal', ['isModal' => false])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
