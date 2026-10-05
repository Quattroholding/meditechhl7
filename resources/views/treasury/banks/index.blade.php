<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Bancos
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
                                <h5 class="card-title mb-0">Listado de Bancos</h5>
                                @can('treasury.banks.manage')
                                    <a href="{{ route('treasury.banks.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Nuevo Banco
                                    </a>
                                @endcan
                            </div>
                        </div>
                        <div class="card-body">
                            <livewire:data-table model="\App\Models\Treasury\Bank"
                                                 :columns="['id', 'bank_name', 'account_number', 'account_type', 'currency', 'balance', 'status', 'acciones']"
                                                 :actions="['edit','delete']"
                                                 routename="treasury.banks"
                                                 wire:key="{{\Illuminate\Support\Str::random(5)}}"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
