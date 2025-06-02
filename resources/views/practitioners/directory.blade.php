<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    {{ __('doctor.titles') }}
                @endslot
                @slot('li_1')
                    {{ __('generic.list') }} {{ __('doctor.titles') }}
                @endslot
            @endcomponent
            <!-- /Page Header -->

            <div class="row">
                <livewire:practitioner.medical-directory/>
            </div>
        </div>
        @component('components.notification-box')
        @endcomponent
    </div>
</x-app-layout>
