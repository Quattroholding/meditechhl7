<div>
    <div class="row mb-3">
        <div class="col-md-6">
            <h5>Información del Respondedor</h5>
            <div class="list-group list-group-flush">
                <div class="list-group-item">
                    <strong>Paciente:</strong> {{ $response->patient?->name ?? 'N/A' }}
                </div>
                <div class="list-group-item">
                    <strong>Profesional:</strong> {{ $response->practitioner?->name ?? 'N/A' }}
                </div>
                <div class="list-group-item">
                    <strong>Estado:</strong>
                    <span class="badge bg-{{ $response->status == 'completed' ? 'success' : 'warning' }}">
                        {{ ucfirst($response->status) }}
                    </span>
                </div>
                <div class="list-group-item">
                    <strong>Fecha de Respuesta:</strong>
                    {{ $response->submitted_at ? $response->submitted_at->format('d/m/Y H:i') : 'No enviada' }}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <h5>Encuesta</h5>
            <div class="list-group list-group-flush">
                <div class="list-group-item">
                    <strong>Título:</strong> {{ $response->survey->title }}
                </div>
                <div class="list-group-item">
                    <strong>Total de Preguntas:</strong> {{ $response->survey->questions->count() }}
                </div>
                <div class="list-group-item">
                    <strong>Preguntas Respondidas:</strong> {{ count(array_filter($response->responses ?? [])) }}
                </div>
            </div>
        </div>
    </div>

    <hr class="my-3">

    <h5>Respuestas Detalladas</h5>

    @forelse($response->survey->questions as $question)
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0 text-white">{{ $loop->iteration }}. {{ $question->question_text }}</h6>
                {{--}}
                <small class="text-shadow-white">
                    Tipo: <span class="badge bg-secondary">{{ ucfirst($question->question_type) }}</span>
                </small>
                {{--}}
            </div>
            <div class="card-body">
                @php
                    $answer = $response->responses[$question->id] ?? null;
                @endphp

                @if($answer !== null && $answer !== '')
                    @if($question->question_type === 'text' || $question->question_type === 'textarea')
                        <p>{{ $answer }}</p>
                    @elseif($question->question_type === 'select' || $question->question_type === 'radio')
                        <p><strong>Respuesta:</strong> {{ $answer }}</p>
                    @elseif($question->question_type === 'checkbox')
                        <ul>
                            @forelse((array)$answer as $item)
                                <li>{{ $item }}</li>
                            @empty
                                <li>{{ $answer }}</li>
                            @endforelse
                        </ul>
                    @elseif($question->question_type === 'rating')
                        <div>
                            <strong>Calificación:</strong>
                            <div class="text-warning">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= (int)$answer)
                                        <i class="fa fa-star"></i>
                                    @else
                                        <i class="fa fa-star-o"></i>
                                    @endif
                                @endfor
                            </div>
                            <span class="badge bg-info">{{ $answer }}/5</span>
                        </div>
                    @elseif($question->question_type === 'number')
                        <p><strong>Respuesta:</strong> {{ $answer }}</p>
                    @else
                        <p>{{ $answer }}</p>
                    @endif
                @else
                    <p class="text-muted">No respondida</p>
                @endif
            </div>
        </div>
    @empty
        <p class="text-muted">No hay preguntas en esta encuesta.</p>
    @endforelse
</div>
