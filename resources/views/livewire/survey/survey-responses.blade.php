<div>
    <!-- Header con estadísticas -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Respuestas - {{ $survey->title }}</h2>
                <a href="{{ route('surveys.show', $survey->id) }}" class="btn btn-secondary btn-sm">
                    <i class="fa fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <!-- Estadísticas Básicas -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="card-title text-primary">{{ $totalResponses }}</h3>
                    <p class="card-text text-muted">Total de Respuestas</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="card-title text-success">{{ $completedResponses }}</h3>
                    <p class="card-text text-muted">Completadas</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="card-title text-warning">{{ $pendingResponses }}</h3>
                    <p class="card-text text-muted">Pendientes</p>
                </div>
            </div>
        </div>
    </div>

    @include('partials.message')

    <!-- Evaluación Global de Rating -->
    @if($surveyStats['totalRatings'] > 0)
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Evaluación Global de la Encuesta</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Promedio -->
                            <div class="col-md-4 text-center border-end">
                                <div class="mb-3">
                                    <h1 class="text-warning mb-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $surveyStats['averageRating'])
                                                <i class="fa fa-star"></i>
                                            @elseif($i - 0.5 <= $surveyStats['averageRating'])
                                                <i class="fa fa-star-half-o"></i>
                                            @else
                                                <i class="fa fa-star-o"></i>
                                            @endif
                                        @endfor
                                    </h1>
                                    <h2>{{ $surveyStats['averageRating'] }}/5</h2>
                                    <p class="text-muted mb-0">Calificación Promedio</p>
                                </div>
                                <div class="alert alert-info mb-0">
                                    <strong>{{ $surveyStats['totalRatings'] }}</strong> calificaciones recibidas
                                </div>
                            </div>

                            <!-- Tabla de Distribución -->
                            <div class="col-md-8">
                                <h6 class="mb-3">¿Cuántas personas dieron cada calificación?</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm mb-0" border="1">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 120px;">Estrellas</th>
                                                <th>Personas</th>
                                                <th style="width: 50px;">%</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @for($i = 5; $i >= 1; $i--)
                                                @php
                                                    $count = $surveyStats['ratingDistribution'][$i];
                                                    $percentage = $surveyStats['ratingPercentage'][$i];
                                                    $barColor = match($i) {
                                                        5 => 'bg-success',
                                                        4 => 'bg-info',
                                                        3 => 'bg-warning',
                                                        2 => 'bg-orange',
                                                        1 => 'bg-danger',
                                                    };
                                                @endphp
                                                <tr @if($count == 0) class="text-muted" @endif>
                                                    <td>
                                                        @for($j = 1; $j <= $i; $j++)
                                                            <i class="fa fa-star text-warning"></i>
                                                        @endfor
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="progress flex-grow-1" style="height: 24px; min-width: 150px;">
                                                                <div class="progress-bar {{ $barColor }}" role="progressbar"
                                                                     style="width: {{ $percentage }}%;"
                                                                     aria-valuenow="{{ $percentage }}"
                                                                     aria-valuemin="0" aria-valuemax="100">
                                                                    @if($percentage > 20)
                                                                        <span class="text-white fw-bold">{{ $count }}</span>
                                                                    @endif
                                                                        @if($percentage <= 20 && $count > 0)
                                                                            <span class="ms-2 fw-bold">{{ $count }}</span>
                                                                        @endif
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </td>
                                                    <td class="text-end fw-bold">{{ $percentage }}%</td>
                                                </tr>
                                            @endfor
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Filtros de búsqueda -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <input
                        type="text"
                        class="form-control"
                        placeholder="Buscar por paciente o profesional..."
                        wire:model.live="search"
                    />
                </div>
                <div class="col-md-6">
                    <select class="form-select" wire:model.live="statusFilter">
                        <option value="">Todos los estados</option>
                        <option value="completed">Completadas</option>
                        <option value="pending">Pendientes</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de respuestas -->
    <div class="table-responsive">
        <table class="table border-0 custom-table comman-table mb-0">
            <thead>
            <tr>
                <th>Paciente</th>
                <th>Profesional</th>
                <th>Estado</th>
                <th>Calificación</th>
                <th>Fecha de Respuesta</th>
                <th class="text-end">Acciones</th>
            </tr>
            </thead>
            <tbody>
            @forelse($responses as $response)
                <tr>
                    <td>
                        <strong>{{ $response->patient?->name ?? 'N/A' }}</strong>
                    </td>
                    <td>
                        {{ $response->practitioner?->name ?? 'N/A' }}
                    </td>
                    <td>
                            <span class="badge bg-{{ $response->status == 'completed' ? 'success' : 'warning' }}">
                                {{ ucfirst($response->status) }}
                            </span>
                    </td>
                    <td>
                        @php
                            $ratings = [];
                            foreach ($survey->questions as $q) {
                                if ($q->question_type === 'rating') {
                                    $rating = $response->responses[$q->id] ?? null;
                                    if ($rating !== null && $rating !== '') {
                                        $ratings[] = (int)$rating;
                                    }
                                }
                            }
                            $avgRating = count($ratings) > 0 ? array_sum($ratings) / count($ratings) : 0;
                        @endphp
                        @if($avgRating > 0)
                            <div>
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $avgRating)
                                        <i class="fa fa-star text-warning"></i>
                                    @elseif($i - 0.5 <= $avgRating)
                                        <i class="fa fa-star-half-o text-warning"></i>
                                    @else
                                        <i class="fa fa-star-o text-muted"></i>
                                    @endif
                                @endfor
                            </div>
                            <small class="text-muted">{{ round($avgRating, 2) }}/5</small>
                        @else
                            <span class="badge bg-secondary">Sin calificar</span>
                        @endif
                    </td>
                    <td>
                        {{ $response->submitted_at ? $response->submitted_at->format('d/m/Y H:i') : 'No enviada' }}
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm justify-content-end">
                            <a href="#" data-bs-toggle="modal" data-bs-target="#responseModal{{ $response->id }}" class="btn btn-info btn-sm text-white">
                                <i class="fa fa-eye"></i> Ver
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No se encontraron respuestas</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div class="d-flex justify-content-center mt-4">
        {{ $responses->links() }}
    </div>

    <!-- Calificación Individual por Pregunta -->
    @if(!empty($surveyStats['questionStats']) && count($surveyStats['questionStats']) > 0)
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Calificación Individual por Pregunta</h5>
                    </div>
                    <div class="card-body">
                        @foreach($surveyStats['questionStats'] as $qStats)
                            <div class="mb-4 pb-3 border-bottom">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="mb-2">{{ $qStats['question'] }}</h6>
                                        <div class="d-flex align-items-center gap-2">
                                            <div>
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $qStats['averageRating'])
                                                        <i class="fa fa-star text-warning"></i>
                                                    @elseif($i - 0.5 <= $qStats['averageRating'])
                                                        <i class="fa fa-star-half-o text-warning"></i>
                                                    @else
                                                        <i class="fa fa-star-o text-muted"></i>
                                                    @endif
                                                @endfor
                                            </div>
                                            <strong class="text-primary">{{ $qStats['averageRating'] }}/5</strong>
                                            <small class="text-muted">({{ $qStats['totalRatings'] }} respuestas)</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex gap-1 justify-content-end">
                                            @for($i = 5; $i >= 1; $i--)
                                                <div title="{{ $qStats['distribution'][$i] }} personas - {{ $qStats['percentage'][$i] }}%">
                                                    @php
                                                        $barColor = match($i) {
                                                            5 => 'bg-success',
                                                            4 => 'bg-info',
                                                            3 => 'bg-warning',
                                                            2 => 'bg-orange',
                                                            1 => 'bg-danger',
                                                        };
                                                    @endphp
                                                    <div class="progress" style="width: 40px; height: 30px;">
                                                        <div class="progress-bar {{ $barColor }}" role="progressbar"
                                                             style="width: 100%; {{ $qStats['distribution'][$i] == 0 ? 'opacity: 0.3;' : '' }}"
                                                             aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                                                            <small class="text-white fw-bold">{{ $qStats['distribution'][$i] }}</small>
                                                        </div>
                                                    </div>
                                                    <small class="text-center d-block">{{ $i }}★</small>
                                                </div>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif


    <!-- Modales para ver detalles -->
    @forelse($responses as $response)
        <div class="modal fade" id="responseModal{{ $response->id }}" tabindex="-1" >
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content" style="margin: 0 20%;">
                    <div class="modal-header">
                        <h5 class="modal-title">Detalles de la Respuesta</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        @livewire('survey.response-detail', ['response' => $response])
                    </div>
                </div>
            </div>
        </div>
    @empty
    @endforelse
</div>
