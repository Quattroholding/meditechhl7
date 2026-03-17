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


            @livewire(\App\Livewire\HemoScreen\StandaloneDashboard::class)
        </div>
    </div>
</x-app-layout>
