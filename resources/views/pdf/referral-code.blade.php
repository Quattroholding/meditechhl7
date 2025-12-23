<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Código de Referido - {{ $client->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            padding: 40px;
            background: #ffffff;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 3px solid #2D1B69;
        }

        .logo {
            max-width: 150px;
            margin-bottom: 15px;
        }

        .header h1 {
            color: #2D1B69;
            font-size: 28px;
            margin-bottom: 10px;
        }

        .header p {
            color: #6c757d;
            font-size: 14px;
        }

        .main-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
        }

        .code-label {
            font-size: 16px;
            color: #6c757d;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .referral-code {
            font-size: 42px;
            font-weight: 700;
            color: #2D1B69;
            letter-spacing: 3px;
            margin: 20px 0;
            padding: 20px;
            background: white;
            border-radius: 10px;
            border: 3px dashed #2D1B69;
        }

        .qr-section {
            text-align: center;
            margin: 30px 0;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .qr-section h3 {
            color: #2D1B69;
            font-size: 18px;
            margin-bottom: 20px;
        }

        .qr-code-container {
            display: inline-block;
            padding: 15px;
            background: white;
            border: 2px solid #dee2e6;
            border-radius: 8px;
        }

        .qr-code {
            width: 200px;
            height: 200px;
        }

        .url-section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .url-section p {
            font-size: 11px;
            color: #495057;
            word-break: break-all;
            margin: 0;
        }

        .instructions {
            margin-top: 30px;
            padding: 25px;
            background: #fff3cd;
            border-left: 5px solid #FFC107;
            border-radius: 5px;
        }

        .instructions h3 {
            color: #856404;
            font-size: 18px;
            margin-bottom: 15px;
        }

        .instructions ol {
            padding-left: 20px;
            color: #856404;
        }

        .instructions li {
            margin-bottom: 10px;
            line-height: 1.6;
        }

        .benefits {
            margin-top: 30px;
            padding: 25px;
            background: #d4edda;
            border-left: 5px solid #28a745;
            border-radius: 5px;
        }

        .benefits h3 {
            color: #155724;
            font-size: 18px;
            margin-bottom: 15px;
        }

        .benefits ul {
            list-style: none;
            padding-left: 0;
        }

        .benefits li {
            color: #155724;
            margin-bottom: 8px;
            padding-left: 25px;
            position: relative;
        }

        .benefits li:before {
            content: "✓";
            position: absolute;
            left: 0;
            font-weight: bold;
            color: #28a745;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            padding-top: 20px;
            border-top: 2px solid #dee2e6;
            color: #6c757d;
            font-size: 12px;
        }

        .highlight-box {
            background: #2D1B69;
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin: 20px 0;
        }

        .highlight-box h2 {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .highlight-box p {
            font-size: 14px;
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="header">
        @if($client->logo)
            <img src="{{ public_path('storage/'.$client->logo) }}" alt="{{ $client->name }}" class="logo">
        @endif
        <h1>{{ $client->name }}</h1>
        <p>Código de Referido Personal</p>
    </div>
     {{--}}
    <div class="highlight-box">
        <h2>¡Gana $5 por cada referido!</h2>
        <p>Comparte este código con tus colegas y obtén crédito en tu próxima factura</p>
    </div>
    {{--}}
    <div class="main-section">
        <div class="code-label">TU CÓDIGO DE REFERIDO</div>
        <div class="referral-code">{{ $referralCode->code }}</div>
        <p style="color: #6c757d; font-size: 14px; margin-top: 10px;">
            Comparte este código con nuevos clientes
        </p>
    </div>

    <div class="qr-section">
        <h3>Escanea este código QR para registrarte</h3>
        <div class="qr-code-container">
            <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code" class="qr-code">
        </div>
        <div class="url-section">
            <p><strong>Enlace de registro:</strong><br>{{ $shareUrl }}</p>
        </div>
    </div>
    <p>&nbsp;</p>  <p>&nbsp;</p>  <p>&nbsp;</p> <p>&nbsp;</p>
    <div class="instructions">
        <h3>📋 Cómo usar tu código de referido</h3>
        <ol>
            <li><strong>Comparte el código o QR:</strong> Envía este PDF a tus colegas o permite que escaneen el código QR</li>
            <li><strong>El nuevo cliente se registra:</strong> Debe ingresar tu código durante el proceso de registro</li>
            <li><strong>Recibes tu recompensa:</strong> Automáticamente obtienes $5 de crédito en tu próxima factura</li>
            <li><strong>Sin límites:</strong> Refiere a tantos clientes como quieras y acumula créditos</li>
        </ol>
    </div>

    <div class="benefits">
        <h3>✨ Beneficios del Programa de Referidos</h3>
        <ul>
            <li>$5.00 de crédito por cada cliente que se registre con tu código</li>
            <li>Los créditos se acumulan automáticamente</li>
            <li>Se aplican como descuento en tu próxima factura</li>
            <li>Sin límite de referidos - ¡refiere todo lo que quieras!</li>
            <li>Ayudas a crecer nuestra comunidad médica</li>
        </ul>
    </div>

    <div class="footer">
        <p><strong>Soluciones Meditec</strong></p>
        <p>Sistema de gestión médica integral</p>
        <p style="margin-top: 10px;">Generado el {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
