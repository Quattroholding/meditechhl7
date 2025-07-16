<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $survey->title }} - Encuesta de Satisfacción</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .survey-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }
        .survey-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .survey-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .survey-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 300;
        }
        .survey-header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
        }
        .survey-body {
            padding: 40px;
        }
        .question-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 25px;
            border-left: 4px solid #667eea;
        }
        .question-title {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 15px;
        }
        .required {
            color: #e74c3c;
        }
        .form-control, .form-select {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s;
        }
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .form-check-input {
            border: 2px solid #e9ecef;
            border-radius: 4px;
        }
        .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }
        .rating-container {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .rating-option {
            text-align: center;
            padding: 15px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
            min-width: 80px;
        }
        .rating-option:hover {
            border-color: #667eea;
            background-color: #f8f9ff;
        }
        .rating-option input[type="radio"] {
            display: none;
        }
        .rating-option input[type="radio"]:checked + .rating-label {
            background-color: #667eea;
            color: white;
            border-radius: 8px;
        }
        .rating-label {
            display: block;
            padding: 10px;
            border-radius: 5px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .submit-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 15px 40px;
            border-radius: 10px;
            font-size: 18px;
            font-weight: 600;
            color: white;
            transition: all 0.3s;
            display: block;
            margin: 30px auto 0;
        }
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .progress-bar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        @media (max-width: 768px) {
            .survey-container {
                margin: 20px auto;
                padding: 10px;
            }
            .survey-body {
                padding: 20px;
            }
            .question-card {
                padding: 20px;
            }
            .rating-container {
                gap: 10px;
            }
            .rating-option {
                min-width: 60px;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="survey-container">
        <div class="survey-card">
            <!-- Header -->
            <div class="survey-header">
                <div style="font-size: 48px; margin-bottom: 15px;">📋</div>
                <h1>{{ $survey->title }}</h1>
                @if($survey->description)
                    <p>{{ $survey->description }}</p>
                @endif
            </div>

            <!-- Progress Bar -->
            <div class="progress" style="height: 5px; border-radius: 0;">
                <div class="progress-bar" role="progressbar" style="width: 0%" id="progressBar"></div>
            </div>

            <!-- Body -->
            <div class="survey-body">
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('survey.submit', $surveyResponse->token) }}" method="POST" id="surveyForm">
                    @csrf
                    
                    @foreach($survey->questions as $index => $question)
                        <div class="question-card" data-question="{{ $index + 1 }}">
                            <div class="question-title">
                                {{ $index + 1 }}. {{ $question->question_text }}
                                @if($question->is_required)
                                    <span class="required">*</span>
                                @endif
                            </div>

                            @switch($question->question_type)
                                @case('text')
                                    <input type="text" 
                                           name="responses[{{ $question->id }}]" 
                                           class="form-control" 
                                           placeholder="Escriba su respuesta aquí..."
                                           {{ $question->is_required ? 'required' : '' }}>
                                    @break

                                @case('textarea')
                                    <textarea name="responses[{{ $question->id }}]" 
                                              class="form-control" 
                                              rows="4" 
                                              placeholder="Escriba su respuesta aquí..."
                                              {{ $question->is_required ? 'required' : '' }}></textarea>
                                    @break

                                @case('select')
                                    <select name="responses[{{ $question->id }}]" 
                                            class="form-select" 
                                            {{ $question->is_required ? 'required' : '' }}>
                                        <option value="">Seleccione una opción</option>
                                        @foreach($question->options_list as $option)
                                            <option value="{{ $option }}">{{ $option }}</option>
                                        @endforeach
                                    </select>
                                    @break

                                @case('radio')
                                    @foreach($question->options_list as $optionIndex => $option)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" 
                                                   type="radio" 
                                                   name="responses[{{ $question->id }}]" 
                                                   value="{{ $option }}" 
                                                   id="radio_{{ $question->id }}_{{ $optionIndex }}"
                                                   {{ $question->is_required ? 'required' : '' }}>
                                            <label class="form-check-label" for="radio_{{ $question->id }}_{{ $optionIndex }}">
                                                {{ $option }}
                                            </label>
                                        </div>
                                    @endforeach
                                    @break

                                @case('checkbox')
                                    @foreach($question->options_list as $optionIndex => $option)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   name="responses[{{ $question->id }}][]" 
                                                   value="{{ $option }}" 
                                                   id="checkbox_{{ $question->id }}_{{ $optionIndex }}">
                                            <label class="form-check-label" for="checkbox_{{ $question->id }}_{{ $optionIndex }}">
                                                {{ $option }}
                                            </label>
                                        </div>
                                    @endforeach
                                    @break

                                @case('rating')
                                    <div class="rating-container">
                                        @for($i = 1; $i <= 5; $i++)
                                            <div class="rating-option">
                                                <input type="radio" 
                                                       name="responses[{{ $question->id }}]" 
                                                       value="{{ $i }}" 
                                                       id="rating_{{ $question->id }}_{{ $i }}"
                                                       {{ $question->is_required ? 'required' : '' }}>
                                                <label class="rating-label" for="rating_{{ $question->id }}_{{ $i }}">
                                                    {{ $i }}
                                                </label>
                                            </div>
                                        @endfor
                                    </div>
                                    <div class="text-center mt-2">
                                        <small class="text-muted">1 = Muy insatisfecho | 5 = Muy satisfecho</small>
                                    </div>
                                    @break

                                @case('number')
                                    <input type="number" 
                                           name="responses[{{ $question->id }}]" 
                                           class="form-control" 
                                           placeholder="Ingrese un número"
                                           {{ $question->is_required ? 'required' : '' }}>
                                    @break
                            @endswitch
                        </div>
                    @endforeach

                    <button type="submit" class="submit-btn">
                        <i class="fas fa-paper-plane"></i> Enviar Encuesta
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Progress bar functionality
        const form = document.getElementById('surveyForm');
        const progressBar = document.getElementById('progressBar');
        const questions = document.querySelectorAll('.question-card');
        
        function updateProgress() {
            const totalQuestions = questions.length;
            let answeredQuestions = 0;
            
            questions.forEach(question => {
                const inputs = question.querySelectorAll('input, select, textarea');
                let isAnswered = false;
                
                inputs.forEach(input => {
                    if (input.type === 'radio' || input.type === 'checkbox') {
                        if (input.checked) isAnswered = true;
                    } else {
                        if (input.value.trim() !== '') isAnswered = true;
                    }
                });
                
                if (isAnswered) answeredQuestions++;
            });
            
            const progress = (answeredQuestions / totalQuestions) * 100;
            progressBar.style.width = progress + '%';
        }
        
        // Add event listeners to all form inputs
        form.addEventListener('input', updateProgress);
        form.addEventListener('change', updateProgress);
        
        // Form submission
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('.submit-btn');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
            submitBtn.disabled = true;
        });
    </script>
</body>
</html>