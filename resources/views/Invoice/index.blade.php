<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    {{__('invoice.invoices')}}
                @endslot
                @slot('li_1')
                    {{__('generic.list')}}   {{__('invoice.invoices')}}
                @endslot
            @endcomponent
            <!-- /Page Header -->
            {{--}}
            @component('components.invoices-active-tab')
            @endcomponent
            <!-- Report Filter -->
            @component('components.invoices-report-filter')
            @endcomponent
            @component('components.invoices-tab')
            @endcomponent
            @component('components.invoices-card')
            @endcomponent
            {{--}}

            <div class="row">
                <div class="col-sm-12">
                    <div class="card card-table show-entire p-2">
                        <div class="card-body">
                            <livewire:invoice.data-table/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @component('components.notification-box')
        @endcomponent
    </div>
</x-app-layout>>
