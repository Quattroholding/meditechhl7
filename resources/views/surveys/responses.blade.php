<x-app-layout>

    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    {{ __('Respuestas de Encuesta') }}
                @endslot
                @slot('li_1')
                    {{ $survey->title }}
                @endslot
            @endcomponent
            <!-- /Page Header -->
            <div class="container-fluid">
                @livewire('survey.survey-responses', ['survey' => $survey])
            </div>
        </div>
    </div>
</x-app-layout>
