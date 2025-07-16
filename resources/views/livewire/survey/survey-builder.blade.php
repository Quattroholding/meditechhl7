<div>
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    <form wire:submit.prevent="saveSurvey">
        <div class="row">
            <div class="col-md-8">
                <div class="form-group">
                    <label for="title">Título de la Encuesta *</label>
                    <input type="text" wire:model="title" class="form-control @error('title') is-invalid @enderror" 
                           id="title" placeholder="Ingrese el título de la encuesta">
                    @error('title') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="description">Descripción</label>
                    <textarea wire:model="description" class="form-control @error('description') is-invalid @enderror" 
                              id="description" rows="3" placeholder="Descripción opcional de la encuesta"></textarea>
                    @error('description') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="status">Estado</label>
                    <select wire:model="status" class="form-control @error('status') is-invalid @enderror" id="status">
                        <option value="draft">Borrador</option>
                        <option value="active">Activo</option>
                        <option value="inactive">Inactivo</option>
                    </select>
                    @error('status') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" wire:model="is_active" class="custom-control-input" id="is_active">
                        <label class="custom-control-label" for="is_active">Encuesta Activa</label>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fa fa-save"></i> Guardar Encuesta
                    </button>
                </div>
            </div>
        </div>
    </form>

    @if($survey && $survey->questions->count() > 0)
        <hr>
        <h5>Vista Previa de la Encuesta</h5>
        <div class="card">
            <div class="card-body">
                <h4>{{ $survey->title }}</h4>
                @if($survey->description)
                    <p class="text-muted">{{ $survey->description }}</p>
                @endif
                
                @foreach($survey->questions as $question)
                    <div class="form-group">
                        <label>
                            {{ $question->question_text }}
                            @if($question->is_required)
                                <span class="text-danger">*</span>
                            @endif
                        </label>
                        
                        @switch($question->question_type)
                            @case('text')
                                <input type="text" class="form-control" placeholder="Respuesta de texto corto" disabled>
                                @break
                            
                            @case('textarea')
                                <textarea class="form-control" rows="3" placeholder="Respuesta de texto largo" disabled></textarea>
                                @break
                            
                            @case('select')
                                <select class="form-control" disabled>
                                    <option>Seleccione una opción</option>
                                    @foreach($question->options_list as $option)
                                        <option>{{ $option }}</option>
                                    @endforeach
                                </select>
                                @break
                            
                            @case('radio')
                                @foreach($question->options_list as $option)
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" disabled>
                                        <label class="form-check-label">{{ $option }}</label>
                                    </div>
                                @endforeach
                                @break
                            
                            @case('checkbox')
                                @foreach($question->options_list as $option)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" disabled>
                                        <label class="form-check-label">{{ $option }}</label>
                                    </div>
                                @endforeach
                                @break
                            
                            @case('rating')
                                <div class="d-flex">
                                    @for($i = 1; $i <= 5; $i++)
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" disabled>
                                            <label class="form-check-label">{{ $i }}</label>
                                        </div>
                                    @endfor
                                </div>
                                @break
                            
                            @case('number')
                                <input type="number" class="form-control" placeholder="Ingrese un número" disabled>
                                @break
                        @endswitch
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
