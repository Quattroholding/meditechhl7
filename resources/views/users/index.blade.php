<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    {{ __('user.titles') }}
                @endslot
                @slot('li_1')
                        {{ __('generic.list') }} {{ __('user.titles') }}
                @endslot
            @endcomponent
            <!-- /Page Header -->

            <livewire:user.data-table />
        </div>
    </div>
</x-app-layout>
