<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Paciente Registrado</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            background-color: #f8f9fa;
        }
        .container {
            background: white;
            margin: 20px;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
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
        .alert {
            background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
            border: 1px solid #b8daff;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .patient-details {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 15px;
        }
        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .detail-label {
            font-weight: 600;
            color: #6c757d;
            font-size: 12px;
            text-transform: uppercase;
        }
        .detail-value {
            color: #343a40;
            font-weight: 500;
        }
        .actions {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(45deg, #007bff, #0056b3);
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 20px;
            font-weight: 600;
            margin: 5px;
            font-size: 14px;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e9ecef;
            font-size: 12px;
            color: #6c757d;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>📋 Nuevo Paciente Registrado</h1>
        <p>{{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="content">
        <div class="alert">
            <h3 style="margin: 0 0 10px; color: #0c5460;">
                Hola {{ $recipient->name }},
            </h3>
            <p style="margin: 0;">
                Se ha registrado un nuevo paciente en el sistema. A continuación encontrará los detalles:
            </p>
        </div>

        <div class="patient-details">
            <h3 style="color: #495057; margin: 0 0 15px;">
                👤 Información del Paciente
            </h3>
            <div class="detail-grid">
                <div class="detail-item">
                    <span class="detail-label">Nombre Completo</span>
                    <span class="detail-value">{{ $patient->first_name }} {{ $patient->last_name }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">ID Paciente</span>
                    <span class="detail-value">{{ $patient->patient_id ?? 'Pendiente de asignar' }}</span>
                </div>
                @if($patient->email)
                    <div class="detail-item">
                        <span class="detail-label">Email</span>
                        <span class="detail-value">{{ $patient->email }}</span>
                    </div>
                @endif
                @if($patient->phone)
                    <div class="detail-item">
                        <span class="detail-label">Teléfono</span>
                        <span class="detail-value">{{ $patient->phone }}</span>
                    </div>
                @endif
                @if($patient->date_of_birth)
                    <div class="detail-item">
                        <span class="detail-label">Fecha de Nacimiento</span>
                        <span class="detail-value">{{ \Carbon\Carbon::parse($patient->date_of_birth)->format('d/m/Y') }} ({{ \Carbon\Carbon::parse($patient->date_of_birth)->age }} años)</span>
                    </div>
                @endif
                @if($patient->gender)
                    <div class="detail-item">
                        <span class="detail-label">Género</span>
                        <span class="detail-value">{{ ucfirst($patient->gender) }}</span>
                    </div>
                @endif
                @if($patient->address)
                    <div class="detail-item" style="grid-column: 1 / -1;">
                        <span class="detail-label">Dirección</span>
                        <span class="detail-value">{{ $patient->address }}</span>
                    </div>
                @endif
                <div class="detail-item">
                    <span class="detail-label">Fecha de Registro</span>
                    <span class="detail-value">{{ $patient->created_at->format('d/m/Y H:i:s') }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Estado</span>
                    <span class="detail-value" style="color: #28a745; font-weight: 600;">✅ Activo</span>
                </div>
            </div>
        </div>

        @if($recipientType === 'staff')
            <div class="actions">
                <h3 style="color: #856404; margin: 0 0 15px;">
                    ⚡ Acciones Recomendadas
                </h3>
                <p style="margin: 0 0 15px; color: #856404;">
                    Como parte del equipo médico, considere realizar las siguientes acciones:
                </p>
                <ul style="color: #856404; margin: 10px 0;">
                    <li>Revisar la información del paciente</li>
                    <li>Preparar historial médico inicial</li>
                    <li>Contactar para programar primera cita</li>
                    <li>Verificar documentación pendiente</li>
                </ul>

                <div style="text-align: center; margin-top: 20px;">
                    <a href="{{ config('app.url') }}/patients/{{ $patient->id }}" class="btn">
                        👁️ Ver Perfil del Paciente
                    </a>
                    <a href="{{ config('app.url') }}/appointments/create?patient={{ $patient->id }}" class="btn">
                        📅 Programar Cita
                    </a>
                </div>
            </div>
        @endif
    </div>

    <div class="footer">
        <p><strong>Sistema de Gestión Médica</strong></p>
        <p>Este es un correo automático generado por el sistema.</p>
        <p>{{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</div>
</body>
</html>
