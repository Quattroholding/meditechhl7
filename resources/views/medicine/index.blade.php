<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    {{ __('Medicamentos') }}
                @endslot
                @slot('li_1')
                    {{__('Lista')}}  {{ __('Medicamentos') }}
                @endslot
            @endcomponent
            <!-- /Page Header -->
            <livewire:medicine.data-table />
        </div>
    </div>
</x-app-layout>>
