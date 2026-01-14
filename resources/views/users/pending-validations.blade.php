<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    {{ __('user.titles') }}
                @endslot
                @slot('li_1')
                        Pendientes de Validación
                @endslot
            @endcomponent
            <!-- /Page Header -->

            <livewire:user.pending-validations />
        </div>
    </div>
</x-app-layout>
