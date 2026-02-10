<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo mensaje de contacto</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #2B5E86 0%, #4E7F2B 100%);
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }
        .content {
            padding: 30px;
        }
        .field {
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e9ecef;
        }
        .field:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .field-label {
            font-weight: 700;
            color: #2B5E86;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        .field-value {
            font-size: 15px;
            color: #333;
        }
        .message-box {
            background: #f8f9fa;
            border-left: 4px solid #4E7F2B;
            padding: 15px;
            border-radius: 4px;
            font-style: italic;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📧 Nuevo Mensaje de Contacto</h1>
        </div>

        <div class="content">
            <div class="field">
                <div class="field-label">Nombre</div>
                <div class="field-value">{{ $formData['name'] }}</div>
            </div>

            <div class="field">
                <div class="field-label">Correo Electrónico</div>
                <div class="field-value">
                    <a href="mailto:{{ $formData['email'] }}" style="color: #2B5E86; text-decoration: none;">
                        {{ $formData['email'] }}
                    </a>
                </div>
            </div>

            @if(!empty($formData['phone']))
            <div class="field">
                <div class="field-label">Teléfono</div>
                <div class="field-value">{{ $formData['phone'] }}</div>
            </div>
            @endif

            @if(!empty($formData['company']))
            <div class="field">
                <div class="field-label">Empresa</div>
                <div class="field-value">{{ $formData['company'] }}</div>
            </div>
            @endif

            <div class="field">
                <div class="field-label">Mensaje</div>
                <div class="message-box">
                    {{ $formData['message'] }}
                </div>
            </div>
        </div>

        <div class="footer">
            <p>Este mensaje fue enviado desde el formulario de contacto de Soluciones Meditec</p>
            <p>Enviado el {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>
</body>
</html>
