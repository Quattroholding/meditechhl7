<x-app-layout>

    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    {{ __('Encuestas') }}
                @endslot
                @slot('li_1')
                    {{ __('Editar Encuesta') }}
                @endslot
            @endcomponent
            <!-- /Page Header -->
            <div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Editar Encuesta</h4>
                    <a href="{{ route('surveys.show', $survey->id) }}" class="btn btn-secondary">
                        <i class="fa fa-arrow-left"></i> Volver
                    </a>
                </div>
                <div class="card-body">
                    @livewire('survey.survey-builder', ['surveyId' => $survey->id])
                </div>
            </div>
        </div>
    </div>
</div>
        </div>
    </div>
</x-app-layout>
