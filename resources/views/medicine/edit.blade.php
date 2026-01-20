<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    {{ __('medication.title') }}
                @endslot
                @slot('li_1')
                    {{ __('generic.edit') }} {{ __('medication.title') }}
                @endslot
            @endcomponent
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="col-12">
                                <div class="form-heading">
                                    <h4>{{ __('generic.edit') }} {{ __('medication.title') }}</h4>
                                </div>
                            </div>
                            <livewire:medicine.edit-form :medication_id="$medicine_id" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
