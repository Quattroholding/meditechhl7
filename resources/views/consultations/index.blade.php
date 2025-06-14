<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    {{ __('encounter.titles') }}
                @endslot
                @slot('li_1')
                    {{ __('generic.list') }} {{ __('encounter.titles') }}
                @endslot
            @endcomponent
            <!-- /Page Header -->

            <div class="row">
                <div class="col-sm-12">
                    <div class="card card-table show-entire p-0 table-dash">
                        <div class="card-body">
                            <livewire:consultation.data-table/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @component('components.notification-box')
        @endcomponent
    </div>
</x-app-layout>
