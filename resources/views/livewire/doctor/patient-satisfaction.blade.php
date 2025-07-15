<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">
            <i class="fas fa-heart me-2" style="color: var(--primary-color, #3498db);"></i>
            Satisfacción de Pacientes
        </h4>
        <div class="dropdown">
            <select wire:model.live="timeFrame" class="form-select form-select-sm">
                <option value="7">Últimos 7 días</option>
                <option value="30">Últimos 30 días</option>
                <option value="90">Últimos 3 meses</option>
                <option value="365">Último año</option>
                <option value="0">Todos los registros</option>
            </select>
        </div>
    </div>
    
    <div class="card-body">
        @if($isLoading)
            <div class="loading-skeleton">
                <!-- Skeleton para rating general -->
                <div class="text-center mb-4">
                    <div class="skeleton-rating-circle mx-auto mb-3"></div>
                    <div class="skeleton-stars mb-2"></div>
                    <div class="skeleton-text-small mx-auto"></div>
                </div>
                
                <!-- Skeleton para distribución -->
                <div class="skeleton-section mb-4">
                    <div class="skeleton-title mb-3"></div>
                    @for($i = 0; $i < 5; $i++)
                        <div class="skeleton-rating-bar mb-2"></div>
                    @endfor
                </div>
                
                <!-- Skeleton para comentarios -->
                <div class="skeleton-section">
                    <div class="skeleton-title mb-3"></div>
                    @for($i = 0; $i < 3; $i++)
                        <div class="skeleton-comment-item mb-3"></div>
                    @endfor
                </div>
            </div>
        @elseif($totalReviews > 0)
            <!-- Rating general -->
            <div class="overall-rating text-center mb-4">
                <div class="rating-circle" style="border-color: {{ $this->getOverallRatingColor() }};">
                    <span class="rating-value" style="color: {{ $this->getOverallRatingColor() }};">
                        {{ $averageRating }}
                    </span>
                </div>
                <div class="rating-stars mt-2">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= floor($averageRating))
                            <i class="fas fa-star text-warning"></i>
                        @elseif($i - 0.5 <= $averageRating)
                            <i class="fas fa-star-half-alt text-warning"></i>
                        @else
                            <i class="far fa-star text-muted"></i>
                        @endif
                    @endfor
                </div>
                <p class="text-muted mt-2">
                    Basado en {{ $totalReviews }} {{ $totalReviews == 1 ? 'consulta' : 'consultas' }}
                </p>
            </div>

            <!-- Distribución de ratings -->
            <div class="rating-distribution mb-4">
                <h6 class="mb-3">
                    <i class="fas fa-chart-bar me-1"></i>
                    Distribución de Calificaciones
                </h6>
                @for($rating = 5; $rating >= 1; $rating--)
                    <div class="rating-bar mb-2">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="rating-label">
                                <span class="rating-number">{{ $rating }}</span>
                                <i class="fas fa-star text-warning ms-1"></i>
                            </div>
                            <div class="rating-progress flex-grow-1 mx-3">
                                <div class="progress">
                                    <div class="progress-bar" 
                                         style="width: {{ $ratingDistribution[$rating]['percentage'] }}%; background-color: {{ $this->getRatingColor($rating) }};"
                                         role="progressbar">
                                    </div>
                                </div>
                            </div>
                            <div class="rating-count">
                                <span class="fw-bold">{{ $ratingDistribution[$rating]['count'] }}</span>
                                <small class="text-muted">({{ $ratingDistribution[$rating]['percentage'] }}%)</small>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>

            <!-- Comentarios recientes -->
            @if(count($recentComments) > 0)
                <div class="recent-comments">
                    <h6 class="mb-3">
                        <i class="fas fa-comments me-1"></i>
                        Comentarios Recientes
                    </h6>
                    @foreach($recentComments as $comment)
                        <div class="comment-item mb-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="comment-content">
                                    <div class="comment-stars mb-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $comment['rating'])
                                                <i class="fas fa-star" style="color: {{ $this->getRatingColor($comment['rating']) }}; font-size: 0.8em;"></i>
                                            @else
                                                <i class="far fa-star text-muted" style="font-size: 0.8em;"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <p class="comment-text mb-1">{{ $comment['comment'] }}</p>
                                    <small class="text-muted">
                                        {{ $comment['patient_name'] }} • {{ $comment['date'] }}
                                    </small>
                                </div>
                                <div class="comment-rating">
                                    <span class="badge" style="background-color: {{ $this->getRatingColor($comment['rating']) }};">
                                        {{ $comment['rating'] }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Insights -->
            <div class="satisfaction-insights mt-4">
                <div class="row">
                    <div class="col-md-6">
                        <div class="insight-item">
                            <i class="fas fa-thumbs-up text-success me-2"></i>
                            <span class="fw-bold">
                                {{ $ratingDistribution[5]['count'] + $ratingDistribution[4]['count'] }}
                            </span>
                            <small class="text-muted">evaluaciones positivas</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="insight-item">
                            <i class="fas fa-percentage text-primary me-2"></i>
                            <span class="fw-bold">
                                {{ round((($ratingDistribution[5]['count'] + $ratingDistribution[4]['count']) / $totalReviews) * 100, 1) }}%
                            </span>
                            <small class="text-muted">tasa de satisfacción</small>
                        </div>
                    </div>
                </div>
            </div>

        @else
            <div class="empty-state text-center py-4">
                <i class="fas fa-heart fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No hay evaluaciones disponibles</h5>
                <p class="text-muted mb-0">
                    @if($timeFrame == '0')
                        No se encontraron consultas finalizadas para evaluar.
                    @else
                        No se encontraron consultas en los últimos {{ $timeFrame }} días.
                    @endif
                </p>
            </div>
        @endif
    </div>
    
    <div class="card-footer text-muted text-center">
        <small>
            <i class="fas fa-info-circle me-1"></i>
            Evaluaciones simuladas basadas en encounters finalizados
        </small>
    </div>
</div>

@push('css')
<style>
.rating-circle {
    width: 80px;
    height: 80px;
    border: 4px solid;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    background-color: #f8f9fa;
}

.rating-value {
    font-size: 1.8em;
    font-weight: bold;
}

.rating-stars {
    font-size: 1.2em;
}

.rating-distribution {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border: 1px solid #dee2e6;
}

.rating-bar {
    display: flex;
    align-items: center;
}

.rating-label {
    min-width: 40px;
    display: flex;
    align-items: center;
}

.rating-number {
    font-weight: 600;
    color: #495057;
}

.rating-progress {
    height: 20px;
}

.rating-progress .progress {
    height: 100%;
    background-color: #e9ecef;
    border-radius: 10px;
}

.rating-count {
    min-width: 80px;
    text-align: right;
}

.comment-item {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border: 1px solid #dee2e6;
    transition: all 0.3s ease;
}

.comment-item:hover {
    background-color: #e9ecef;
    transform: translateY(-1px);
}

.comment-text {
    color: #495057;
    line-height: 1.4;
    margin-bottom: 5px;
}

.comment-stars {
    margin-bottom: 5px;
}

.satisfaction-insights {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border: 1px solid #dee2e6;
}

.insight-item {
    display: flex;
    align-items: center;
    gap: 5px;
    margin-bottom: 10px;
}

.insight-item:last-child {
    margin-bottom: 0;
}

.empty-state {
    min-height: 300px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

@media (max-width: 768px) {
    .rating-circle {
        width: 60px;
        height: 60px;
    }
    
    .rating-value {
        font-size: 1.4em;
    }
    
    .rating-stars {
        font-size: 1em;
    }
    
    .rating-bar {
        flex-direction: column;
        align-items: stretch;
        gap: 5px;
    }
    
    .rating-label,
    .rating-count {
        min-width: auto;
        text-align: left;
    }
    
    .rating-progress {
        margin: 0 !important;
    }
    
    .comment-item {
        padding: 10px;
    }
    
    .insight-item {
        flex-direction: column;
        align-items: flex-start;
        text-align: left;
    }
}

/* Animaciones */
.rating-circle {
    animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.comment-item {
    animation: fadeInUp 0.5s ease-out forwards;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Loading skeleton styles */
.loading-skeleton {
    animation: pulse 1.5s ease-in-out infinite;
}

.skeleton-rating-circle {
    width: 80px;
    height: 80px;
    background: #e9ecef;
    border-radius: 50%;
}

.skeleton-stars {
    height: 20px;
    background: #e9ecef;
    border-radius: 4px;
    width: 120px;
    margin: 0 auto;
}

.skeleton-text-small {
    height: 14px;
    background: #e9ecef;
    border-radius: 4px;
    width: 150px;
}

.skeleton-section {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border: 1px solid #dee2e6;
}

.skeleton-title {
    height: 18px;
    background: #e9ecef;
    border-radius: 4px;
    width: 60%;
}

.skeleton-rating-bar {
    height: 20px;
    background: #e9ecef;
    border-radius: 4px;
    width: 100%;
}

.skeleton-comment-item {
    background: #e9ecef;
    border-radius: 8px;
    height: 80px;
    width: 100%;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}
</style>
@endpush