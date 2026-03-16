<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Dashboard
                @endslot
                @slot('li_1')
                    Hemoscreen Dashboard
                @endslot
            @endcomponent
            <!-- /Page Header -->


            @livewire('hemo-screen.standalone-dashboard')
        </div>
    </div>
</x-app-layout>
