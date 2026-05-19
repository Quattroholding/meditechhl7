<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    {{ __('user.title') }}
                @endslot
                @slot('li_1')
                    {{ __('generic.edit') }} {{ __('user.titles') }}
                @endslot
            @endcomponent
            <!-- /Page Header -->

        <div class="row">
            <div class="col-lg-12">
                @livewire('user.profile')
            </div>
        </div>
    </div>
    </div>
</x-app-layout>
