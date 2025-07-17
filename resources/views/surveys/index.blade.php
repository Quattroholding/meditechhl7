<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    {{ __('Encuestas') }}
                @endslot
                @slot('li_1')
                    {{ __('Encuestas') }}
                @endslot
            @endcomponent
            <!-- /Page Header -->
            <div class="row">
                <div class="col-12">
                    <div class="card">

                        <div class="card-body">
                            @livewire('survey.survey-list')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
