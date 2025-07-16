<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encuesta Completada - Gracias</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .completion-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .completion-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            overflow: hidden;
            text-align: center;
        }
        .completion-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 40px 30px;
        }
        .completion-icon {
            font-size: 80px;
            margin-bottom: 20px;
            animation: checkmark 0.6s ease-in-out;
        }
        @keyframes checkmark {
            0% {
                transform: scale(0);
                opacity: 0;
            }
            50% {
                transform: scale(1.2);
                opacity: 0.8;
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }
        .completion-title {
            font-size: 32px;
            font-weight: 300;
            margin: 0 0 10px 0;
        }
        .completion-subtitle {
            font-size: 18px;
            opacity: 0.9;
            margin: 0;
        }
        .completion-body {
            padding: 40px 30px;
        }
        .thank-you-message {
            font-size: 18px;
            color: #2c3e50;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .survey-details {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 25px;
            margin: 25px 0;
            border-left: 4px solid #28a745;
        }
        .survey-details h4 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 18px;
        }
        .survey-details p {
            margin: 8px 0;
            color: #666;
        }
        .completion-footer {
            background: #f8f9fa;
            padding: 25px 30px;
            border-top: 1px solid #e9ecef;
        }
        .completion-footer p {
            margin: 0;
            color: #6c757d;
            font-size: 14px;
        }
        .rating-display {
            display: inline-flex;
            gap: 5px;
            align-items: center;
            margin: 10px 0;
        }
        .star {
            color: #ffc107;
            font-size: 20px;
        }
        .feedback-box {
            background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .feedback-box h5 {
            color: #1976d2;
            margin-bottom: 10px;
        }
        @media (max-width: 768px) {
            .completion-container {
                padding: 10px;
            }
            .completion-body {
                padding: 30px 20px;
            }
            .completion-icon {
                font-size: 60px;
            }
            .completion-title {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="completion-container">
        <div class="completion-card">
            <!-- Header -->
            <div class="completion-header">
                <div class="completion-icon">✅</div>
                <h1 class="completion-title">¡Encuesta Completada!</h1>
                <p class="completion-subtitle">Gracias por su valiosa opinión</p>
            </div>

            <!-- Body -->
            <div class="completion-body">
                <div class="thank-you-message">
                    Su respuesta ha sido registrada exitosamente. Agradecemos el tiempo que dedicó a completar nuestra encuesta de satisfacción.
                </div>

                <div class="survey-details">
                    <h4><i class="fas fa-info-circle"></i> Detalles de su respuesta</h4>
                    <p><strong>Encuesta:</strong> {{ $surveyResponse->survey->title }}</p>
                    <p><strong>Completada el:</strong> {{ $surveyResponse->submitted_at->format('d/m/Y \a \l\a\s H:i') }}</p>
                    <p><strong>ID de respuesta:</strong> #{{ $surveyResponse->id }}</p>
                </div>

                <div class="feedback-box">
                    <h5><i class="fas fa-heart"></i> Su opinión es importante</h5>
                    <p>Sus comentarios nos ayudan a mejorar continuamente la calidad de nuestros servicios médicos y la experiencia de nuestros pacientes.</p>
                </div>

                @if(session('success'))
                    <div class="alert alert-success" role="alert">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                    </div>
                @endif

                <div class="rating-display justify-content-center">
                    @for($i = 1; $i <= 5; $i++)
                        <span class="star">⭐</span>
                    @endfor
                </div>
                <p class="text-muted">¡Su satisfacción es nuestra prioridad!</p>
            </div>

            <!-- Footer -->
            <div class="completion-footer">
                <p><strong>{{ config('app.name') }}</strong></p>
                <p>Comprometidos con su salud y bienestar</p>
                <p style="margin-top: 15px; font-size: 12px;">
                    Si tiene alguna pregunta adicional, no dude en contactarnos.
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add some celebration animation
        setTimeout(() => {
            // Create confetti effect
            for (let i = 0; i < 50; i++) {
                createConfetti();
            }
        }, 500);

        function createConfetti() {
            const confetti = document.createElement('div');
            confetti.style.position = 'fixed';
            confetti.style.width = '10px';
            confetti.style.height = '10px';
            confetti.style.backgroundColor = ['#667eea', '#764ba2', '#28a745', '#ffc107'][Math.floor(Math.random() * 4)];
            confetti.style.left = Math.random() * 100 + 'vw';
            confetti.style.top = '-10px';
            confetti.style.zIndex = '1000';
            confetti.style.borderRadius = '50%';
            confetti.style.pointerEvents = 'none';
            document.body.appendChild(confetti);

            const fall = confetti.animate([
                { transform: 'translateY(-10px) rotate(0deg)', opacity: 1 },
                { transform: 'translateY(100vh) rotate(360deg)', opacity: 0 }
            ], {
                duration: Math.random() * 2000 + 1000,
                easing: 'ease-out'
            });

            fall.addEventListener('finish', () => {
                confetti.remove();
            });
        }
    </script>
</body>
</html>