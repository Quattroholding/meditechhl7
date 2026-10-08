<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Módulo Financiero
                @endslot
                @slot('li_1')
                    Bancos
                @endslot
            @endcomponent

            <div class="row">
                <div class="col-sm-12">
                    <div class="card card-table show-entire p-2">
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
