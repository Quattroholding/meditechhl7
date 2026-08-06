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
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4 class="card-title mb-0">{{ $survey->title }}</h4>
                                <div>
                                    @can('surveys.view')
                                    <a href="{{ route('surveys.responses', $survey->id) }}" class="btn btn-info btn-sm text-white">
                                        <i class="fa fa-chart-bar"></i> Ver Respuestas
                                    </a>
                                    @endcan
                                    @can('surveys.edit')
                                    <a href="{{ route('surveys.edit', $survey->id) }}" class="btn btn-warning btn-sm">
                                        <i class="fa fa-edit"></i> Editar
                                    </a>
                                    @endcan
                                    <a href="{{ route('surveys.index') }}" class="btn btn-secondary btn-sm">
                                        <i class="fa fa-arrow-left"></i> Volver
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <strong>Estado:</strong>
                                        <span class="badge badge-{{ $survey->status == 'active' ? 'success' : ($survey->status == 'draft' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($survey->status) }}
                                        </span>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Activa:</strong>
                                        <span class="badge badge-{{ $survey->is_active ? 'success' : 'danger' }}">
                                            {{ $survey->is_active ? 'Sí' : 'No' }}
                                        </span>
                                    </div>
                                </div>

                                @if($survey->description)
                                <div class="mb-3">
                                    <strong>Descripción:</strong>
                                    <p>{{ $survey->description }}</p>
                                </div>
                                @endif

                                <div class="mb-3">
                                    <strong>Creada por:</strong> {{ $survey->creator->full_name ?? 'Usuario eliminado' }}
                                </div>

                                <div class="mb-3">
                                    <strong>Fecha de creación:</strong> {{ $survey->created_at }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Estadísticas</h5>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <h3 class="text-primary">{{ $survey->questions->count() }}</h3>
                                        <p class="mb-0">Preguntas</p>
                                    </div>
                                    <div class="col-6">
                                        <h3 class="text-success">{{ $survey->responses->where('status', 'completed')->count() }}</h3>
                                        <p class="mb-0">Respuestas</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Constructor de Preguntas</h5>
                            </div>
                            <div class="card-body">
                                @livewire('survey.question-builder', ['surveyId' => $survey->id])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
