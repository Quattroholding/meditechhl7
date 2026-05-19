<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Reporte de Stock Bajo
                @endslot
                @slot('li_1')
                    Items con Stock Mínimo
                @endslot
            @endcomponent
            <!-- /Page Header -->

            <div class="row">
                <div class="col-sm-12">
                    <div class="card card-table show-entire p-2">
                        <div class="card-body">
                            <livewire:inventory.low-stock-report/>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @component('components.notification-box')
        @endcomponent
    </div>
</x-app-layout>
