<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $success ? 'Acción Exitosa' : 'Error' }} - {{ config('app.name') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 100%;
            padding: 40px 30px;
            text-align: center;
        }

        .icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
        }

        .icon.success {
            background: #d4edda;
            color: #28a745;
        }

        .icon.error {
            background: #f8d7da;
            color: #dc3545;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 15px;
            color: #333;
        }

        .message {
            font-size: 16px;
            color: #666;
            line-height: 1.6;
            margin-bottom: 25px;
        }

        .appointment-details {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            text-align: left;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 600;
            color: #495057;
        }

        .detail-value {
            color: #6c757d;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }

        .status-badge.confirmed {
            background: #d4edda;
            color: #155724;
        }

        .status-badge.cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        .btn {
            display: inline-block;
            padding: 12px 30px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 20px;
            transition: transform 0.2s;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        @media (max-width: 480px) {
            .container {
                padding: 30px 20px;
            }

            h1 {
                font-size: 20px;
            }

            .message {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon {{ $success ? 'success' : 'error' }}">
            @if($success)
                ✓
            @else
                ✗
            @endif
        </div>

        <h1>{{ $success ? '¡Acción Exitosa!' : 'Error en la Acción' }}</h1>

        <p class="message">{{ $message }}</p>

        @if($appointment)
            <div class="appointment-details">
                <div class="detail-row">
                    <span class="detail-label">Paciente:</span>
                    <span class="detail-value">{{ $appointment->patient->name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Doctor:</span>
                    <span class="detail-value">{{ $appointment->practitioner->name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Fecha:</span>
                    <span class="detail-value">{{ $appointment->start->format('d/m/Y') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Hora:</span>
                    <span class="detail-value">{{ $appointment->start->format('H:i a') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Estado:</span>
                    <span class="detail-value">
                        @if(isset($action))
                            <span class="status-badge {{ $action }}">
                                {{ $action === 'confirmed' ? 'Confirmada' : 'Cancelada' }}
                            </span>
                        @else
                            {{ $appointment->status->label() }}
                        @endif
                    </span>
                </div>
            </div>
        @endif

        @if($success && isset($action))
            <p style="font-size: 14px; color: #666; margin-top: 20px;">
                @if($action === 'confirmed')
                    Por favor llegue 15 minutos antes de su cita. Recibirá un correo de confirmación.
                @else
                    Si desea reagendar, por favor contacte a la clínica.
                @endif
            </p>
        @endif

        <a href="{{ url('/') }}" class="btn btn-primary">Volver al Inicio</a>
    </div>
</body>
</html>
