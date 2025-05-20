<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    {{ __('Configuraciones') }}
                @endslot
                @slot('li_1')
                    {{ __('Procedimientos') }}
                @endslot
            @endcomponent
            <!-- /Page Header -->
            <div class="row">
                <div class="col-sm-12">
                    <livewire:settings.user-procedure-create/>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
