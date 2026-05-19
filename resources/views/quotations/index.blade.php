<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    {{ __('Cotizaciones') }}
                @endslot
                @slot('li_1')
                    {{ __('generic.list') }} {{ __('Cotizaciones') }}
                @endslot
            @endcomponent
            <!-- /Page Header -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="card card-table show-entire p-2">
                        <div class="card-body">
                            @livewire('quotation.data-table')
                        </div>
                    </div>
                </div>
            </div>
            @can('quotations.create')
                @livewire('quotation.modal-save')
            @endcan
        </div>
        @component('components.notification-box')
        @endcomponent
    </div>
</x-app-layout>
