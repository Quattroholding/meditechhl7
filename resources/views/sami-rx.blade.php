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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- SEO Meta Tags -->
    <title>SAMI Recetas - App Gratuita de Recetas Médicas Digitales para Panamá</title>
    <meta name="description" content="SAMI Recetas es una app móvil 100% gratuita para crear y compartir recetas médicas digitales en Panamá. Disponible en iOS y Android. Rápido, seguro y sin límites.">
    <meta name="keywords" content="recetas médicas digitales, app recetas, SAMI Recetas, recetas Panamá, app médica gratis, prescripciones digitales, iOS, Android">
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
    <meta name="author" content="Soluciones Meditec">
    <meta name="language" content="es-ES">
    <link rel="canonical" href="https://samirx.meditecpty.com">

    <!-- Open Graph Tags (Redes Sociales) -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://samirx.meditecpty.com">
    <meta property="og:title" content="SAMI Recetas - App Gratuita de Recetas Médicas Digitales">
    <meta property="og:description" content="Crea recetas médicas digitales desde tu celular, 100% gratis. Disponible para iOS y Android en Panamá.">
    <meta property="og:image" content="{{ asset('landing/images/sami_recetas_movil_nueva_receta.jpeg') }}">
    <meta property="og:site_name" content="SAMI Recetas">
    <meta property="og:locale" content="es_ES">

    <!-- Twitter Card Tags -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://samirx.meditecpty.com">
    <meta property="twitter:title" content="SAMI Recetas - App Gratuita de Recetas Médicas">
    <meta property="twitter:description" content="Crea recetas médicas digitales desde tu celular, 100% gratis para Panamá.">
    <meta property="twitter:image" content="{{ asset('landing/images/sami_recetas_movil_nueva_receta.jpeg') }}">

    <link rel="icon" href="{{url('images/iconoSAMI.ico')}}" type="image/x-icon">
    <link rel="stylesheet" href="{{url('styles/sami_rx.css')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- JSON-LD Schema Markup for Mobile Application -->
    @php
    $mobileAppSchema = [
      "@context" => "https://schema.org",
      "@type" => "MobileApplication",
      "name" => "SAMI Recetas",
      "description" => "Aplicación móvil para crear y compartir recetas médicas digitales de forma segura en Panamá",
      "url" => "https://samirx.meditecpty.com",
      "image" => asset('landing/images/logoSAMIRecetas.jpg'),
      "applicationCategory" => "MedicalApplication",
      "operatingSystem" => "iOS, Android",
      "offers" => [
        "@type" => "Offer",
        "price" => "0",
        "priceCurrency" => "USD"
      ],
      "author" => [
        "@type" => "Organization",
        "name" => "Soluciones Meditec",
        "url" => "https://meditecpty.com",
        "contactPoint" => [
          "@type" => "ContactPoint",
          "contactType" => "Customer Service",
          "email" => "business@meditecpty.com",
          "telephone" => "+507-831-6100"
        ]
      ],
      "aggregateRating" => [
        "@type" => "AggregateRating",
        "ratingValue" => "4.7",
        "ratingCount" => "1500",
        "bestRating" => "5",
        "worstRating" => "1"
      ],
      "downloadUrl" => [
        "https://play.google.com/store/apps/details?id=com.meditec.recepy.meditec_recepy_app",
        "https://apps.apple.com/app/sami-recetas/id6757765164"
      ],
      "featureList" => [
        "Recetas médicas digitales",
        "100% Gratis",
        "Recetas ilimitadas",
        "Disponible en iOS y Android",
        "Cumple regulaciones de Panamá",
        "Rápido y fácil de usar"
      ]
    ];
    @endphp
    <script type="application/ld+json">@json($mobileAppSchema)</script>

    <!-- Organization Schema -->
    @php
    $orgSchema = [
      "@context" => "https://schema.org",
      "@type" => "Organization",
      "name" => "Soluciones Meditec",
      "url" => "https://meditecpty.com",
      "logo" => asset('images/logoFull.png'),
      "description" => "Empresa de tecnología en salud que desarrolla aplicaciones médicas innovadoras",
      "email" => "business@meditecpty.com",
      "telephone" => "+507-831-6100",
      "contactPoint" => [
        "@type" => "ContactPoint",
        "contactType" => "Customer Service",
        "email" => "business@meditecpty.com",
        "telephone" => "+507-831-6100"
      ],
      "sameAs" => [
        "https://sami.meditecpty.com"
      ]
    ];
    @endphp
    <script type="application/ld+json">@json($orgSchema)</script>
</head>
<body>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MP5P532M"
                  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<!-- TOP -->
<section class="top">
    <div class="container top__inner">

        <div class="top__logo">
            <img src="{{url('landing/images/logoSAMIRecetas.jpg')}}" alt="SAMI Recetas - Recetas médicas digitales" title="SAMI Recetas">
        </div>

        <h1 class="top__headline">
            Crea y comparte recetas médicas de forma rápida, segura y 100% digital.
        </h1>

        <p class="top__sub">
            Una solución moderna que optimiza tu tiempo y mejora la experiencia de <br>
            tus pacientes, desde cualquier dispositivo.
        </p>
    </div>
</section>

<!-- BLUE SECTION -->
<section class="blue">
    <div class="container">

        <!-- Benefits row -->
        <div class="benefits">
            <div class="benefit">
                <div class="benefit__num">1</div>
                <div class="benefit__text">
                    Crea tu cuenta
                </div>
            </div>

            <div class="benefit benefit--center">
                <div class="benefit__num">2</div>
                <div class="benefit__text">
                    Crea tu perfil
                </div>
            </div>

            <div class="benefit">
                <div class="benefit__num">3</div>
                <div class="benefit__text">
                    Empieza a crear tus <br>
                    recetas.
                </div>
            </div>
        </div>

        <!-- Phones -->
        <div class="phones">
            <div class="phone">
                <img src="{{url('landing/images/sami_recetas_movil_registro.jpeg')}}" alt="SAMI Recetas - Paso 1: Registro de usuario en aplicación móvil">
            </div>

            <div class="phone phone--mid">
                <img src="{{url('landing/images/sami_recetas_movil_dashboard.jpeg')}}" alt="SAMI Recetas - Paso 2: Dashboard principal con acceso a crear recetas">
            </div>

            <div class="phone">
                <img src="{{url('landing/images/sami_recetas_movil_nueva_receta.jpeg')}}" alt="SAMI Recetas - Paso 3: Crear y compartir recetas médicas digitales">
            </div>
        </div>

    </div>
</section>

<!-- DOWNLOAD APPS SECTION -->
<section class="download">
    <div class="container download__inner">

        <h2 class="download__title">
            Descarga Nuestra App Móvil
        </h2>

        <p class="download__subtitle">
            Crea recetas médicas desde tu celular. <br>
            <strong>¡Completamente GRATIS y sin límites!</strong>
        </p>

        <div class="download__features">
            <div class="feature">
                <svg class="feature__icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>100% Gratis</span>
            </div>
            <div class="feature">
                <svg class="feature__icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4 12H20M4 12L8 8M4 12L8 16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M20 12L16 8M20 12L16 16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>Recetas Ilimitadas</span>
            </div>
            <div class="feature">
                <svg class="feature__icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 8V12L15 15M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>Rápido y Fácil</span>
            </div>
        </div>

        <div class="download__buttons">
            <a href="https://play.google.com/store/apps/details?id=com.meditec.recepy.meditec_recepy_app"
               target="_blank"
               rel="noopener noreferrer"
               class="download__button"
               title="Descargar SAMI Recetas en Google Play">
                <img src="{{url('images/google_play_logo_trim.png')}}" alt="Descargar SAMI Recetas gratis en Google Play para Android">
            </a>

            <a href="https://apps.apple.com/app/sami-recetas/id6757765164"
               target="_blank"
               rel="noopener noreferrer"
               class="download__button"
               title="Descargar SAMI Recetas en App Store">
                <img src="{{url('images/apple_store_logo_trim.png')}}" alt="Descargar SAMI Recetas gratis en App Store para iOS">
            </a>
        </div>

        <p class="download__note">
            Disponible para iOS y Android
        </p>

    </div>
</section>

<!-- FOOTER -->
<footer class="footer" style="background: linear-gradient(135deg, #1a365d 0%, #2c5282 100%); color: white; padding: 40px 20px; text-align: center;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <div style="margin-bottom: 20px;">
            <p style="margin: 10px 0; font-size: 14px;">
                SAMI Recetas es un producto de <a href="https://meditecpty.com" style="color: #63b3ed; text-decoration: none; font-weight: bold;">Soluciones Meditec</a>
            </p>
            <p style="margin: 10px 0; font-size: 14px;">
                <span style="color: #cbd5e0;">Contáctanos:</span>
                <a href="mailto:business@meditecpty.com" style="color: #63b3ed; text-decoration: none;">business@meditecpty.com</a>
                <span style="color: #cbd5e0;"> | </span>
                <a href="tel:+5078316100" style="color: #63b3ed; text-decoration: none;">+507 831-6100</a>
            </p>
        </div>
        <div style="margin-bottom: 20px; display: flex; justify-content: center; gap: 20px;">
            <a href="https://www.instagram.com/samipanama/" target="_blank" rel="noopener noreferrer" title="SAMI en Instagram" style="color: #63b3ed; font-size: 24px;">
                <i class="fab fa-instagram"></i>
            </a>
            <a href="https://www.facebook.com/profile.php?id=61589894518714" target="_blank" rel="noopener noreferrer" title="SAMI en Facebook" style="color: #63b3ed; font-size: 24px;">
                <i class="fab fa-facebook"></i>
            </a>
        </div>
        <p style="margin: 10px 0; font-size: 12px; color: #cbd5e0;">
            © 2024 Soluciones Meditec. Todos los derechos reservados. | Recetas digitales para Panamá
        </p>
    </div>
</footer>

</body>
</html>

