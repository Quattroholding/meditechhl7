<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    {{ __('service_request.titles') }}
                @endslot
                @slot('li_1')
                    {{ __('generic.list') }}
                @endslot
            @endcomponent
            <!-- /Page Header -->
            <livewire:service-request.data-table />


        </div>
    </div>
</x-app-layout>
