<x-app-layout>

    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    {{ __('Encuestas') }}
                @endslot
                @slot('li_1')
                    {{ __('Crear Nueva Encuesta') }}
                @endslot
            @endcomponent
            <!-- /Page Header -->
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title mb-0">Crear Nueva Encuesta</h4>
                            </div>
                            <div class="card-body">
                                @livewire('survey.survey-builder')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
