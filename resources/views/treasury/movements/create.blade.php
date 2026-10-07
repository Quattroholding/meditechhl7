<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Módulo Financiero
                @endslot
                @slot('li_1')
                    {{ isset($movement) ? 'Editar Movimiento' : 'Nuevo Movimiento' }}
                @endslot
            @endcomponent

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="col-12">
                                <div class="form-heading">
                                    <h4>{{ isset($movement) ? 'Editar Movimiento' : 'Registrar Nuevo Movimiento' }}</h4>
                                </div>
                            </div>

                            <livewire:treasury.movement.treasury-movement-modal :isModal="false" :movement="$movement ?? null" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
