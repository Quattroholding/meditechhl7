<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    {{ __('appointment.titles') }}
                @endslot
                @slot('li_1')
                    Agendar {{ __('appointment.titles') }}
                @endslot
            @endcomponent
            <!-- /Page Header -->
            <div class="row">
               <livewire:appointment.calendar/>
            </div>
        </div>
    </div>
</x-app-layout>
