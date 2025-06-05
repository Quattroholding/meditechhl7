<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a {{ $clinicInfo['name'] }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
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
            background: #2E37A4;
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .header p {
            margin: 10px 0 0;
            font-size: 16px;
            opacity: 0.9;
        }
        .content {
            padding: 40px 30px;
        }
        .welcome-message {
            background:#f8f9ff;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 30px;
            border-left: 5px solid #667eea;
        }
        .patient-info {
            background: #f8fafc;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 15px;
        }
        .info-item {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .info-label {
            font-weight: 600;
            color: #4a5568;
            min-width: 80px;
        }
        .highlight-box {
            background: #d4edda;
            border: 1px solid #b8dabc;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .highlight-box h3 {
            color: #155724;
            margin: 0 0 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .next-steps {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .next-steps h3 {
            color: #856404;
            margin: 0 0 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .next-steps ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        .next-steps li {
            margin-bottom: 8px;
            color: #6c5ce7;
        }
        .contact-info {
            background: #e3f2fd;
            border: 1px solid #bbdefb;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .contact-info h3 {
            color: #1565c0;
            margin: 0 0 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .contact-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 10px;
        }
        .contact-item {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #1976d2;
        }
        .footer {
            background: #f8f9fa;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        .footer p {
            margin: 5px 0;
            color: #6c757d;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            background:#2E37A4;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            margin: 10px 0;
            transition: transform 0.3s ease;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        @media (max-width: 600px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
            .container {
                margin: 10px;
            }
            .header, .content {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Header -->
    <div class="header">
        <h1>¡Bienvenido a {{ env('APP_NAME') }}!</h1>
        <p>Su registro ha sido completado exitosamente</p>
    </div>

    <!-- Contenido -->
    <div class="content">
        <!-- Mensaje de Bienvenida -->
        <div class="welcome-message">
            <h2 style="color: #4a5568; margin: 0 0 15px;">Estimado/a {{ $patient->name }},</h2>
            <p style="font-size: 16px; margin: 0;">
                Nos complace darle la bienvenida a nuestra familia de pacientes. Su registro ha sido procesado
                correctamente y ya forma parte de nuestro sistema de atención médica.
            </p>
        </div>

        <!-- Información del Paciente -->
        <div class="patient-info">
            <h3 style="color: #4a5568; margin: 0 0 15px; display: flex; align-items: center; gap: 8px;">
                👤 Sus Datos Registrados
            </h3>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">{{__('patient.full_name')}}:</span>
                    <span>{{ $patient->name }}</span>
                </div><br/>
                <div class="info-item">
                    <span class="info-label">ID {{__('patient.title')}}:</span>
                    <span><strong>{{ $patient->id ?? 'Se asignará próximamente' }}</strong></span>
                </div><br/>
                @if($patient->identifier)
                    <div class="info-item">
                        <span class="info-label">{{__('patient.full_id_number')}}:</span>
                        <span>{{ $patient->identifier_type }} : {{ $patient->identifier }}</span>
                    </div><br/>
                @endif
                @if($patient->email)
                    <div class="info-item">
                        <span class="info-label">{{__('patient.email')}}:</span>
                        <span>{{ $patient->email }}</span>
                    </div><br/>
                @endif
                @if($patient->phone)
                    <div class="info-item">
                        <span class="info-label">{{__('patient.phone')}}:</span>
                        <span>{{ $patient->phone }}</span>
                    </div><br/>
                @endif
                @if($patient->birth_date)
                    <div class="info-item">
                        <span class="info-label">{{__('patient.birthdate')}}:</span>
                        <span>{{ \Carbon\Carbon::parse($patient->birth_date)->format('d/m/Y') }}</span>
                    </div><br/>
                @endif
                @if($patient->address)
                    <div class="info-item">
                        <span class="info-label">{{__('patient.physical_address')}}:</span>
                        <span>{{ $patient->address }}</span>
                    </div><br/>
                @endif
                <div class="info-item">
                    <span class="info-label">Registrado:</span>
                    <span>{{ \Carbon\Carbon::parse($patient->created_at)->format('d/m/Y') }}</span>
                </div>
            </div>
        </div>

        <!-- Información Importante -->
        <div class="highlight-box">
            <h3>
                ✅ ¡Su Registro está Completo!
            </h3>
            <p style="margin: 0; color: #155724;">
                Ya puede solicitar citas médicas y acceder a todos nuestros servicios.
                Nuestro equipo médico ha sido notificado de su registro.

            </p>
            <p style="margin: 0; color: #155724;">
               Sus datos de acceso son :<br/>
                <p style="font-size: 14px">USUARIO :<b>{{$registrationData['username']}}</b></p>
                <p style="font-size: 14px">CONTRASEÑA:<b>{{$registrationData['password']}}</b></p>
            </p>
        </div>

        <!-- Próximos Pasos -->
        <div class="next-steps">
            <h3>
                📋 Próximos Pasos
            </h3>
            <ul>
                <li>Ingresar a nuestra plataforma con los datos de acceso suministrados</li>
                <li>Acceder a su historia medica.</li>
                <li>Agendar citas </li>
            </ul>
        </div>

        <!-- Información de Contacto -->
        <div class="contact-info">
            <h3>
                📞 Información de Contacto
            </h3>
            <div class="contact-grid">
                @if($clinicInfo['phone'])
                    <div class="contact-item">
                        <span>📱</span>
                        <span><strong>{{__('patient.phone')}}:</strong> {{ $clinicInfo['phone'] }}</span>
                    </div>
                @endif
                <div class="contact-item">
                    <span>📧</span>
                    <span><strong>{{__('patient.email')}}:</strong> {{ $clinicInfo['email'] }}</span>
                </div>
                @if($clinicInfo['address'])
                    <div class="contact-item">
                        <span>📍</span>
                        <span><strong>{{__('patient.physical_address')}}:</strong> {{ $clinicInfo['address'] }}</span>
                    </div>
                @endif
                @if($clinicInfo['website'])
                    <div class="contact-item">
                        <span>🌐</span>
                        <span><strong>Web:</strong> <a href="{{ $clinicInfo['website'] }}" style="color: #1976d2;">{{ $clinicInfo['website'] }}</a></span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Botón de Acción -->
        <div style="text-align: center; margin: 30px 0;">
            @if($clinicInfo['phone'])
                <a href="tel:{{ $clinicInfo['phone'] }}" class="btn">
                    📞 Llamar para Agendar Cita
                </a>
            @endif
            @if($clinicInfo['website'])
                <a href="{{ $clinicInfo['website'] }}/login?username={{$registrationData['username']}}" class="btn" style="margin-left: 10px;">
                    🌐 Visitar Sitio Web
                </a>
            @endif
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p><strong>{{ $clinicInfo['name'] }}</strong></p>
        <p>Este es un correo automático, por favor no responder directamente.</p>
        <p>Si tiene preguntas, contáctenos a {{ $clinicInfo['email'] }}</p>
        <p style="margin-top: 15px; font-size: 12px; color: #999;">
            © {{ date('Y') }} {{ $clinicInfo['name'] }}. Todos los derechos reservados.
        </p>
    </div>
</div>
</body>
</html>
