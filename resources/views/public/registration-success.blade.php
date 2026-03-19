<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-MP5P532M');</script>
    <!-- End Google Tag Manager -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro Exitoso - Soluciones Meditec</title>
    <link rel="stylesheet" href="{{ url('landing/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    @livewireStyles
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
        }

        .success-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #2D1B69 0%, #1E3A8A 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .success-card {
            background: white;
            border-radius: 20px;
            padding: 60px;
            max-width: 700px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            text-align: center;
        }

        .success-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            animation: scaleIn 0.5s ease;
        }

        .success-icon i {
            font-size: 50px;
            color: white;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        h1 {
            color: #2D1B69;
            margin-bottom: 20px;
            font-size: 32px;
        }

        .success-message {
            color: #666;
            font-size: 18px;
            margin-bottom: 30px;
        }

        .alert-box {
            background: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 10px;
            padding: 25px;
            margin: 30px 0;
            text-align: left;
        }

        .alert-box h3 {
            color: #856404;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-box h3 i {
            font-size: 24px;
        }

        .alert-box p {
            color: #856404;
            line-height: 1.6;
            margin-bottom: 10px;
        }

        .next-steps {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 25px;
            margin: 30px 0;
            text-align: left;
        }

        .next-steps h3 {
            color: #2D1B69;
            margin-bottom: 20px;
        }

        .next-steps ol {
            color: #333;
            line-height: 2;
            padding-left: 20px;
        }

        .next-steps li {
            margin-bottom: 10px;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 30px;
        }

        .btn {
            padding: 15px 40px;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: inline-block;
        }

        .btn-primary {
            background: linear-gradient(135deg, #2D1B69 0%, #1E3A8A 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(45, 27, 105, 0.3);
        }

        .btn-secondary {
            background: white;
            color: #2D1B69;
            border: 2px solid #2D1B69;
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(45, 27, 105, 0.2);
        }

        .divider {
            height: 2px;
            background: linear-gradient(90deg, transparent, #e0e0e0, transparent);
            margin: 30px 0;
        }

        .contact-info {
            color: #666;
            font-size: 14px;
            margin-top: 30px;
        }

        .contact-info a {
            color: #2D1B69;
            text-decoration: none;
        }

        .contact-info a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .success-card {
                padding: 40px 30px;
            }

            h1 {
                font-size: 24px;
            }

            .success-message {
                font-size: 16px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MP5P532M"
                  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
    <div class="success-container">
        <div class="success-card">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>

            <h1>¡Registro Exitoso!</h1>
            <p class="success-message">
                Tu cuenta ha sido creada correctamente en Soluciones Meditec.
            </p>

            @if($invoicePending)
            <div class="alert-box">
                <h3>
                    <i class="fas fa-exclamation-triangle"></i>
                    Suscripción Pendiente de Activación
                </h3>
                <p>
                    <strong>Tu cuenta está activa</strong>, pero tu suscripción aún no.
                    La suscripción se activará automáticamente cuando recibamos la confirmación de pago por parte de Yappy.
                </p>
                <p>
                    <i class="fas fa-calendar-check text-primary"></i>
                    <strong>Importante:</strong> Tener la suscripción activa es lo que te permite realizar el agendamiento de citas en la plataforma.
                </p>
                @if($pendingInvoice)
                <div class="yappy-payment-section" style="margin-top: 20px; padding: 15px; background: #fff; border-radius: 8px; border: 2px solid #2D1B69;">
                    <p style="margin-bottom: 15px; font-weight: 600; color: #2D1B69;">
                        <i class="fas fa-credit-card"></i> Paga ahora con Yappy:
                    </p>
                    <p style="margin-bottom: 10px; color: #666; font-size: 14px;">
                        Total a pagar: <strong style="color: #2D1B69; font-size: 18px;">${{ number_format($pendingInvoice->total, 2) }}</strong>
                    </p>
                    <livewire:subscription.pay-invoice-yappy :invoice="$pendingInvoice" />
                    <p style="margin-top: 10px; font-size: 12px; color: #888;">
                        Al finalizar el pago en la aplicación de Yappy, tu suscripción se activará automáticamente.
                    </p>
                </div>
                @endif
            </div>
            @endif

            <div class="next-steps">
                <h3><i class="fas fa-list-check"></i> Próximos Pasos:</h3>
                <ol>
                    @if($invoicePending)
                    <li><i class="fas fa-mobile-alt"></i> Realiza el pago con Yappy usando el botón de arriba</li>
                    <li><i class="fas fa-bell"></i> Recibirás una notificación en tu app de Yappy</li>
                    <li><i class="fas fa-check-circle"></i> Confirma el pago en la aplicación de Yappy</li>
                    <li><i class="fas fa-rocket"></i> Tu suscripción se activará automáticamente</li>
                    @endif
                    <li><i class="fas fa-sign-in-alt"></i> Inicia sesión y comienza a usar la plataforma</li>
                    <li><i class="fas fa-cogs"></i> Configura tu consultorio y personaliza el sistema</li>
                </ol>
            </div>

            <div class="divider"></div>

            <div class="action-buttons">
                <a href="{{ route('login') }}" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Ir al Login
                </a>
                <a href="{{ route('welcome') }}" class="btn btn-secondary">
                    <i class="fas fa-home"></i> Volver al Inicio
                </a>
            </div>

            <div class="contact-info">
                <p>¿Necesitas ayuda? Contáctanos:</p>
                <p>
                    <i class="fas fa-envelope"></i>
                    <a href="mailto:business@meditecpty.com">business@meditecpty.com</a>
                    |
                    <i class="fas fa-phone"></i>
                    <a href="tel:+50783161 00">+507 8316100</a>
                </p>
            </div>
        </div>
    </div>

    <!-- Scripts necesarios para Yappy -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    @livewireScripts
    {{--}}
    @if($invoicePending && $pendingInvoice)
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('showToastrYappy', (event) => {
                const data = Array.isArray(event) ? event[0] : event;
                toastr[data.type](data.message, '', {
                    closeButton: true,
                    progressBar: true,
                    positionClass: 'toast-top-right',
                    timeOut: 10000,
                });
            });
        });
    </script>
    @endif
    {{--}}
</body>
</html>
