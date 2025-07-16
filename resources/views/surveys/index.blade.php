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
                <div class="col-sm-12">
                    <div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Encuestas de Satisfacción</h4>
                    <a href="{{ route('surveys.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus"></i> Nueva Encuesta
                    </a>
                </div>
                <div class="card-body">
                    @livewire('survey.survey-list')
                </div>
            </div>
        </div>
    </div>
</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
