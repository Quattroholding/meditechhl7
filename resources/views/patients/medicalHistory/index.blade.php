<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    {{ __('patient.title') }}
                @endslot
                @slot('li_1')
                    {{ __('patient.medical_history') }}
                @endslot
            @endcomponent
            <!-- /Page Header -->
            <livewire:patient.medical-history2 :patient_id="$patient->id"/>
        </div>
    </div>
</x-app-layout>
