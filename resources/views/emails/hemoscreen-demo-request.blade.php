<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud de Demostración - HemoScreen</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
        }
        .details {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .detail-row {
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
        }
        .detail-label {
            font-weight: 600;
            color: #667eea;
        }
        .detail-value {
            color: #333;
        }
        .message-box {
            background-color: #f8f9fa;
            border: 1px solid #e0e0e0;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
            line-height: 1.8;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #e0e0e0;
        }
        .cta-button {
            display: inline-block;
            background-color: #667eea;
            color: white;
            padding: 12px 24px;
            border-radius: 4px;
            text-decoration: none;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🩺 Nueva Solicitud de Demostración</h1>
            <p>HemoScreen Gateway</p>
        </div>

        <div class="content">
            <div class="greeting">
                <p>Hola,</p>
                <p>Se ha recibido una nueva solicitud de demostración para HemoScreen. A continuación se muestran los detalles del contacto:</p>
            </div>

            <div class="details">
                <div class="detail-row">
                    <span class="detail-label">Nombre:</span>
                    <span class="detail-value">{{ $name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Email:</span>
                    <span class="detail-value">{{ $email }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Teléfono:</span>
                    <span class="detail-value">{{ $phone }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Especialidad:</span>
                    <span class="detail-value">{{ $specialty }}</span>
                </div>
            </div>

            <div>
                <p><strong>Mensaje del Cliente:</strong></p>
                <div class="message-box">{{ $demoMessage }}</div>
            </div>

            <p>
                <strong>Acción Recomendada:</strong> Contacta al cliente lo antes posible para programar una demostración y responder cualquier pregunta que pueda tener.
            </p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} SAMI - Sistema de Administración Médica Integral</p>
            <p>Este es un correo automático generado desde el formulario de solicitud de demostración en la página de HemoScreen.</p>
        </div>
    </div>
</body>
</html>
