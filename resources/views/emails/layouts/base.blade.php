<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? config('app.name') }}</title>
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
            background: {{ $headerColor ?? '#0c9547' }};
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
            background: #f8f9ff;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 30px;
            border-left: 5px solid #667eea;
        }
        .highlight-box {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .highlight-box h3 {
            color: #856404;
            margin: 0 0 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .success-box {
            background: #d4edda;
            border: 1px solid #b8dabc;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .success-box h3 {
            color: #155724;
            margin: 0 0 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .info-box {
            background: #e3f2fd;
            border: 1px solid #bbdefb;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .info-box h3 {
            color: #1565c0;
            margin: 0 0 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .warning-box {
            background: #fef2f2;
            border: 1px solid #fed7d7;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .warning-box h3 {
            color: #c53030;
            margin: 0 0 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .btn {
            display: inline-block;
            background: {{ $buttonColor ?? '#0c9547' }};
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            margin: 20px 0;
            transition: transform 0.3s ease;
            font-size: 16px;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .btn-secondary {
            background: #6c757d;
        }
        .btn-success {
            background: #28a745;
        }
        .btn-danger {
            background: #dc3545;
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
        <h1>{{ $headerIcon ?? '' }} {{ $headerTitle ?? config('app.name') }}</h1>
        @isset($headerSubtitle)
            <p>{{ $headerSubtitle }}</p>
        @endisset
    </div>

    <!-- Content -->
    <div class="content">
        @yield('content')
    </div>

    <!-- Footer -->
    <div class="footer">
        <p><strong>{{ config('app.name') }}</strong></p>
        <p>{{ $footerMessage ?? 'Este es un correo automático, por favor no responder directamente.' }}</p>
        @isset($contactEmail)
            <p>Si tienes preguntas, contáctanos en {{ $contactEmail }}</p>
        @else
            <p>Si tienes preguntas, contáctanos en {{ config('mail.from.address') }}</p>
        @endisset
        <p style="margin-top: 15px; font-size: 12px; color: #999;">
            © {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.
        </p>
    </div>
</div>
</body>
</html>
