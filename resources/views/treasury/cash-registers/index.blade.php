<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Cajas
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
                                <h5 class="card-title mb-0">Listado de Cajas</h5>
                                @can('treasury.cash-registers.manage')
                                    <a href="{{ route('treasury.cash-registers.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Nueva Caja
                                    </a>
                                @endcan
                            </div>
                        </div>
                        <div class="card-body">
                            <livewire:data-table model="\App\Models\Treasury\CashRegister"
                                                 :columns="['id', 'name', 'branch_id', 'responsible_user_id', 'balance', 'status', 'acciones']"
                                                 :actions="['edit','delete']"
                                                 routename="treasury.cash-registers"
                                                 wire:key="{{\Illuminate\Support\Str::random(5)}}"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
