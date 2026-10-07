<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Contabilidad
                @endslot
                @slot('li_1')
                    Períodos Contables
                @endslot
            @endcomponent

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <livewire:accounting.period.accounting-period-data-table />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
