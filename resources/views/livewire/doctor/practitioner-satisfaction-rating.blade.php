<div>
    @if($rating['count'] > 0)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h5 class="card-title text-white mb-0">
                    <i class="fa fa-star text-warning"></i> Calificación de Satisfacción del Paciente
                </h5>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <!-- Resumen de Calificación -->
                    <div class="col-md-6">
                        <div class="text-center">
                            <!-- Estrellas grandes -->
                            <div class="mb-3">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $rating['average'])
                                        <i class="fa fa-star text-warning" style="font-size: 28px;"></i>
                                    @elseif($i - 0.5 <= $rating['average'])
                                        <i class="fa fa-star-half-o text-warning" style="font-size: 28px;"></i>
                                    @else
                                        <i class="fa fa-star-o text-muted" style="font-size: 28px;"></i>
                                    @endif
                                @endfor
                            </div>

                            <!-- Número de calificación -->
                            <h2 class="text-primary mb-1">{{ $rating['average'] }}<span class="text-muted">/5</span></h2>

                            <!-- Porcentaje -->
                            <div class="progress mb-2" style="height: 8px;">
                                <div class="progress-bar bg-primary" role="progressbar"
                                     style="width: {{ $rating['percentage'] }}%;"
                                     aria-valuenow="{{ $rating['percentage'] }}"
                                     aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <small class="text-muted">{{ $rating['percentage'] }}% del máximo</small>
                        </div>
                    </div>

                    <!-- Estadísticas -->
                    <div class="col-md-6">
                        <div class="list-group list-group-flush">
                            <div class="list-group-item px-0 py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong>Encuestas Completadas:</strong>
                                    <span class="badge bg-primary">{{ $rating['count'] }}</span>
                                </div>
                            </div>
                            <div class="list-group-item px-0 py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong>Total de Calificaciones:</strong>
                                    <span class="badge bg-info">{{ $rating['total'] }}</span>
                                </div>
                            </div>
                            <div class="list-group-item px-0 py-2">
                                @php
                                    $ratingStatus = match(true) {
                                        $rating['average'] >= 4.5 => ['text' => 'Excelente', 'class' => 'bg-success'],
                                        $rating['average'] >= 4.0 => ['text' => 'Muy Bueno', 'class' => 'bg-info'],
                                        $rating['average'] >= 3.5 => ['text' => 'Bueno', 'class' => 'bg-warning'],
                                        $rating['average'] >= 3.0 => ['text' => 'Aceptable', 'class' => 'bg-orange'],
                                        default => ['text' => 'Necesita Mejora', 'class' => 'bg-danger'],
                                    };
                                @endphp
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong>Desempeño:</strong>
                                    <span class="badge {{ $ratingStatus['class'] }}">{{ $ratingStatus['text'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Nota informativa -->
                <div class="alert alert-info mt-3 mb-0">
                    <small>
                        <i class="fa fa-info-circle"></i>
                        Esta calificación se basa en las respuestas completadas de la encuesta de satisfacción del paciente.
                        Incluye el promedio de todas las preguntas de tipo calificación.
                    </small>
                </div>
            </div>
        </div>
    @else
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-4">
                <i class="fa fa-star-o text-muted" style="font-size: 48px; opacity: 0.5;"></i>
                <p class="text-muted mt-3 mb-0">
                    No hay encuestas de satisfacción completadas para este profesional.
                </p>
            </div>
        </div>
    @endif
</div>
