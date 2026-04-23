<div>
   @include('partials.message')

    <div class="row">
        @can('surveys.edit')
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        {{ $editingQuestion ? 'Editar Pregunta' : 'Agregar Nueva Pregunta' }}
                    </h5>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="saveQuestion">
                        <div class="form-group">
                            <label for="question_text">Texto de la Pregunta *</label>
                            <input type="text" wire:model="question_text"
                                   class="form-control @error('question_text') is-invalid @enderror"
                                   id="question_text" placeholder="Escriba la pregunta aquí">
                            @error('question_text') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label for="question_type">Tipo de Pregunta *</label>
                            <select wire:model.live="question_type"
                                    class="form-control @error('question_type') is-invalid @enderror"
                                    id="question_type">
                                @foreach($questionTypes as $type => $label)
                                    <option value="{{ $type }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('question_type') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        @if(in_array($question_type, ['select', 'radio', 'checkbox']))
                            <div class="form-group">
                                <label>Opciones de Respuesta</label>
                                <div class="input-group mb-2">
                                    <input type="text" wire:model="newOption" class="form-control"
                                           placeholder="Agregar nueva opción">
                                    <div class="input-group-append">
                                        <button type="button" wire:click="addOption" class="btn btn-secondary">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                </div>

                                @if(count($options) > 0)
                                    <div class="list-group">
                                        @foreach($options as $index => $option)
                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                {{ $option }}
                                                <button type="button" wire:click="removeOption({{ $index }})"
                                                        class="btn btn-danger btn-sm">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" wire:model="is_required"
                                       class="custom-control-input" id="is_required">
                                <label class="custom-control-label" for="is_required">
                                    Pregunta obligatoria
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-success">
                                <i class="fa fa-save"></i>
                                {{ $editingQuestion ? 'Actualizar' : 'Agregar' }} Pregunta
                            </button>
                            @if($editingQuestion)
                                <button type="button" wire:click="cancelEdit" class="btn btn-secondary ml-2">
                                    <i class="fa fa-times"></i> Cancelar
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endcan

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Preguntas de la Encuesta ({{ $survey->questions->count() }})</h5>
                </div>
                <div class="card-body">
                    @if($survey->questions->count() > 0)
                        @foreach($survey->questions as $question)
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">
                                                {{ $question->question_text }}
                                                @if($question->is_required)
                                                    <span class="text-danger">*</span>
                                                @endif
                                            </h6>
                                            <small class="text-muted">
                                                Tipo: {{ $questionTypes[$question->question_type] ?? $question->question_type }}
                                            </small>

                                            @if($question->options && count($question->options) > 0)
                                                <div class="mt-2">
                                                    <small class="text-muted">Opciones:</small>
                                                    <ul class="list-unstyled mb-0">
                                                        @foreach($question->options as $option)
                                                            <li><small>• {{ $option }}</small></li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                        </div>
                                        @can('surveys.edit')
                                        <div class="btn-group btn-group-sm ml-2">
                                            <button wire:click="editQuestion({{ $question->id }})"
                                                    class="btn btn-warning btn-sm">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <button wire:click="deleteQuestion({{ $question->id }})"
                                                    onclick="return confirm('¿Está seguro de eliminar esta pregunta?')"
                                                    class="btn btn-danger btn-sm">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted">
                            <i class="fa fa-question-circle fa-3x mb-3"></i>
                            <p>No hay preguntas en esta encuesta.<br>Agrega la primera pregunta usando el formulario.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
