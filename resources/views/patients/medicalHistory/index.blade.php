<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    {{ __('patient.title') }}
                @endslot
                @slot('li_1')
                    {{ __('generic.create') }} {{ __('patient.title') }}
                @endslot
            @endcomponent
            <!-- /Page Header -->
            <livewire:patient.medical-history2 :patient_id="$id"/>
        </div>
    </div>
</x-app-layout>
