<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encuesta de Satisfacción</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 300;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #2c3e50;
        }
        .message {
            font-size: 16px;
            line-height: 1.8;
            margin-bottom: 30px;
            color: #555;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white !important;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
            transition: transform 0.2s;
            margin: 20px 0;
        }
        .cta-button:hover {
            transform: translateY(-2px);
            text-decoration: none;
            color: white !important;
        }
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 20px 0;
            border-radius: 0 5px 5px 0;
        }
        .info-box h3 {
            margin-top: 0;
            color: #2c3e50;
            font-size: 16px;
        }
        .info-box p {
            margin-bottom: 0;
            color: #666;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        .footer p {
            margin: 0;
            color: #6c757d;
            font-size: 14px;
        }
        .survey-icon {
            width: 60px;
            height: 60px;
            background-color: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            font-size: 24px;
        }
        @media (max-width: 600px) {
            .container {
                margin: 0;
                width: 100%;
            }
            .content {
                padding: 30px 20px;
            }
            .header {
                padding: 25px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="survey-icon">📋</div>
            <h1>{{ $surveyTitle }}</h1>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Estimado/a {{ $patientName }},
            </div>

            <div class="message">
                Esperamos que se encuentre bien. Su opinión es muy importante para nosotros y nos ayuda a mejorar continuamente la calidad de nuestros servicios.
            </div>

            <div class="info-box">
                <h3>Detalles de su consulta:</h3>
                <p><strong>Fecha:</strong> {{ $encounterDate }}</p>
                <p><strong>Médico:</strong> {{ $practitionerName }}</p>
                <p><strong>Centro médico:</strong> {{ $clinicName }}</p>
            </div>

            <div class="message">
                Le invitamos a completar una breve encuesta de satisfacción sobre la atención recibida. 
                Su respuesta nos permitirá:
            </div>

            <ul style="color: #555; line-height: 1.8;">
                <li>Mejorar la calidad de nuestros servicios</li>
                <li>Optimizar los tiempos de atención</li>
                <li>Brindar una mejor experiencia a nuestros pacientes</li>
            </ul>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $surveyUrl }}" class="cta-button">
                    Completar Encuesta
                </a>
            </div>

            <div class="message" style="font-size: 14px; color: #666;">
                <strong>Nota:</strong> Esta encuesta tomará aproximadamente 2-3 minutos en completarse. 
                Si no puede ver el botón, puede copiar y pegar el siguiente enlace en su navegador:<br>
                <a href="{{ $surveyUrl }}" style="color: #667eea;">{{ $surveyUrl }}</a>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Gracias por confiar en nosotros para su cuidado médico.</p>
            <p><strong>{{ $clinicName }}</strong></p>
            <p style="margin-top: 10px; font-size: 12px;">
                Este es un mensaje automático, por favor no responda a este correo.
            </p>
        </div>
    </div>
</body>
</html>