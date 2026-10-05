<x-app-layout>
    <div class="page-wrapper">
        <div class="row content">
            @component('components.page-header')
                @slot('title')
                    Editar Centro de Costo
                @endslot
                @slot('li_1')
                    Módulo Financiero
                @endslot
            @endcomponent
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="col-12">
                            <div class="form-heading">
                                <h4>Editar Centro de Costo</h4>
                            </div>
                        </div>
                        @livewire('finance.cost-center.cost-center-modal', ['isModal' => false, 'costCenter' => $costCenter])
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
