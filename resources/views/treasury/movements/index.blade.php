<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Movimientos de Tesorería
                @endslot
                @slot('li_1')
                    Módulo Financiero
                @endslot
            @endcomponent

            <div class="row">
                <div class="col-sm-12">
                    <div class="card card-table show-entire p-2">
                        <div class="card-header">
                            <div class="d-flex align-items-center justify-content-between">
                                <h5 class="card-title mb-0">Listado de Movimientos</h5>
                                @can('treasury.movements.create')
                                    <a href="{{ route('treasury.movements.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Nuevo Movimiento
                                    </a>
                                @endcan
                            </div>
                        </div>
                        <div class="card-body">
                            <livewire:data-table model="\App\Models\Treasury\TreasuryMovement"
                                                 :columns="['id', 'movement_number', 'movement_date', 'movement_type', 'amount', 'description', 'acciones']"
                                                 :actions="['edit','delete']"
                                                 routename="treasury.movements"
                                                 wire:key="{{\Illuminate\Support\Str::random(5)}}"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
