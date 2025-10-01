<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soluciones MEDITEC - Innovación tecnológica al servicio de la salud</title>
    <link rel="stylesheet" href="{{ asset('landing/style.css?time='.time()) }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <!-- Preload de la imagen de fondo -->
    <link rel="preload" as="image" href="{{ asset('landing/images/LANDING-PORTADA.png') }}">
    <link rel="preload" as="image" href="{{ asset('landing/images/logo-letras.png') }}">
</head>
<body>
     <!-- Loader Screen -->
    <div id="loader" class="loader-screen">
        <div class="loader-content">
            <img src="{{ asset('landing/images/Icono-8.png') }}" alt="Loading" class="loader-logo">
            <div class="loader-spinner"></div>
        </div>
    </div>

    <!-- Header -->
    <header class="header" id="header">
        <nav class="nav">
            <div class="ham-menu">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <div class="nav-brand">
                <img src="{{ asset('landing/images/Icono-8.png') }}" class="logo-header" alt="Logo " height="40px">
            </div>
            <ul class="nav-menu">
                <li><a href="#inicio">INICIO</a></li>
                <li><a href="#what-is-section">QUIENES SOMOS</a></li>
                <li><a href="#features-section">COMO FUNCIONA</a></li>
                <li><a href="#why-choose-section">¿POR QUÉ ELEGIRNOS?</a></li>
                <li><a href="{{route('login')}}">INGRESAR</a></li>
            </ul>
            {{--}}<button class="search-btn">
                <img src="{{ asset('landing/images/search-icon.svg') }}" alt="Buscar">
                BUSCAR
            </button>{{--}}

        </nav>
        <div class="off-screen-menu">
            <ul class="nav-mobile">
                <li><a href="#inicio">INICIO</a></li>
                <li><a href="#what-is-section">QUIENES SOMOS</a></li>
                <li><a href="#features-section">COMO FUNCIONA</a></li>
                <li><a href="#why-choose-section">¿POR QUÉ ELEGIRNOS?</a></li>
                <li><a href="{{route('login')}}">INGRESAR</a></li>
            </ul>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero" id="inicio">
        <div class="hero-background">
            <div class="container">
            <div class="hero-content">
                <div class="hero-logo">
                    <img src="{{ asset('landing/images/logo-letras.png') }}" alt="">
                </div>
                <div class="hero-text">

                    <p class="hero-subtitle">
                        Innovación
                        tecnológica al
                        servicio de la
                        salud.
                    </p>
                </div>
                <!---<div class="hero-graphics">

                    <div class="medical-icons">
                        <div class="medical-cross cross-1">+</div>
                        <div class="medical-cross cross-2">+</div>
                        <div class="medical-cross cross-3">+</div>
                    </div>
                </div>--->
            </div></div>
        </div>
    </section>

    <!-- What is Soluciones Meditec Section -->
    <section class="what-is-section" id="what-is-section">
        <div class="container">
            <div class="section-header">
                <img src="{{ asset('landing/images/Icono-9.png') }}" alt="Clínicas y centros médicos" class="logo-color">
                <h2>   ¿Qué es Soluciones Meditec?</h2>
            </div>
            <p class="section-description">
                Soluciones Meditec es una plataforma integral que digitaliza un software médico.<br><br>
                Es una plataforma integral diseñada para optimizar la forma en que pacientes,
                médicos, clínicas y hospitales gestionan la información y se conectan entre sí.<br><br>
                Centraliza procesos, mejora la eficiencia y eleva la calidad de atención, permitiendo:
            </p>

            <div class="features-grid container">
                <div class="feature-item">
                    <img src="{{ asset('landing/images/Foto-2.png') }}" alt="Historias clínicas" class="feature-image">
                    <div class="feature-number">1</div>
                    <div class="feature-content">
                        <h3>Gestionar historias clínicas electrónicas de forma segura, ágil y actualizada.</h3>
                    </div>
                </div>

                <div class="feature-item custom-item">
                    <div class="feature-number">2</div>
                    <div class="feature-content">
                        <h3>Acceder a la información del paciente en cualquier momento y desde cualquier dispositivo (PC, tablet y/o smartphone).</h3>
                    </div>
                    <img src="{{ asset('landing/images/Foto-3.png') }}" alt="Dispositivos" class="feature-image">
                </div>

                <div class="feature-item">
                    <img src="{{ asset('landing/images/Foto-1.png') }}" alt="Seguridad" class="feature-image">
                    <div class="feature-number">3</div>
                    <div class="feature-content">
                        <h3>Cumplir con regulaciones de privacidad y estándares de seguridad internacional.</h3>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Target Audience Section -->
    <section class="audience-section">
        <div class="container">
            <h2>¿A quién está dirigida?</h2>
            <p>Soluciones Meditec ha sido desarrollada para atender las necesidades de:</p>

            <div class="audience-grid">
                <div class="audience-item">
                    <img src="{{ asset('landing/images/Foto-4.png') }}" alt="Profesionales de la salud" class="audience-image">
                    <h3>Profesionales de la salud independientes</h3>
                </div>

                <div class="audience-item">
                    <img src="{{ asset('landing/images/Foto-5.png') }}" alt="Clínicas y centros médicos" class="audience-image">
                    <h3>Clínicas y centros médicos de todos los niveles de atención</h3>
                </div>

                <div class="audience-item">
                    <img src="{{ asset('landing/images/Foto-6.png') }}" alt="Hospitales y redes de salud" class="audience-image">
                    <h3>Hospitales y redes de salud corporativas</h3>
                </div>
            </div>
        </div>
    </section>

    <!-- Key Features Section -->
    <section class="features-section" id="features-section">
        <div class="container">
            <h2>Funcionalidades clave</h2>

            <div class="key-features-grid">

                <div class="key-feature">
                    <img src="{{ asset('landing/images/Icono-3.png') }}" alt="Cumplimiento y seguridad" class="key-feature-icon">
                    <h3>Gestión de citas y personal</h3>
                    <p>Administra de forma integral las citas médicas y coordina la disponibilidad de tu equipo médico y administrativo. Permite agendar citas en línea, coordinar horarios y gestionar la atención que brinda tu personal, desde una sola plataforma.</p>
                </div>

                <div class="key-feature">
                    <img src="{{ asset('landing/images/Icono-2.png') }}" alt="Programación inteligente" class="key-feature-icon">
                    <h3>Directorio médico inteligente</h3>
                    <p>Centraliza la información de médicos, especialidades y horarios en un sistema dinámico que organiza y actualiza automáticamente la agenda médica con facilidad y precisión.</p>
                </div>

                <div class="key-feature">
                    <img src="{{ asset('landing/images/Icono-1.png') }}" alt="Registros digitales" class="key-feature-icon">
                    <h3>Historia clínica digital centralizada</h3>
                    <p>Consulta, actualiza y gestiona el historial médica de cada paciente con facilidad y precisión.</p>
                </div>

                <div class="key-feature">
                    <img src="{{ asset('landing/images/Icono-4.png') }}" alt="Reportes y métricas" class="key-feature-icon">
                    <h3>Reportes y métricas</h3>
                    <p>Accede a estadísticas relevantes, indicadores de atención y reportes personalizados que facilita la toma de decisiones clínicas y administrativas.</p>
                </div>

                <div class="key-feature">
                    <img src="{{ asset('landing/images/Icono-5.png') }}" alt="Multidispositivo" class="key-feature-icon">
                    <h3>Multidispositivo</h3>
                    <p>Accede desde donde estés <br/> No requiere instalaciones complejas, solo conexión a internet.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Section -->
    <section class="why-choose-section" id="why-choose-section">
        <div class="container">
            <div class="why-choose-header">
                <img src="{{ asset('landing/images/Icono-8.png') }}" alt="Logo Blanco"  class="logo-blanco">
                <h2> ¿Por qué elegir Soluciones Meditec?</h2>
            </div>
            <p>Porque creemos que la tecnología debe estar al servicio de la salud.</p>
            <p>Con Soluciones Meditec, tendrás:</p>

            <div class="benefits-list">
                <div class="benefit-item">
                    <span class="benefit-text">Toda tu clínica en una sola plataforma digital, simple y segura</span>
                </div>
                <div class="benefit-item">
                    <span class="benefit-text">Mejora la organización y gestión de los servicios que ofreces</span>
                </div>
                <div class="benefit-item">
                    <span class="benefit-text">Reduce errores, optimiza procesos y mejora la experiencia de tus pacientes </span>
                </div>
                <div class="benefit-item">
                    <span class="benefit-text">Conéctate desde cualquier lugar y dispositivo</span>
                </div>
            </div>
        </div>
    </section>

    <section class="cta-section">
        <div class="container">
            <div class="register-section">
                <p>¿Listo para dar el siguiente paso en la transformación digital de tu práctica médica?</p>
                <a class="cta-button" href="{{route('patient.register')}}">Regístrate aquí</a>
                <p class="cta-subtitle">Forma parte de la revolución Soluciones Meditec</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-contact">
                    <p>Contáctanos:</p>
                    <span><i class="fa fa-user-injured"></i></span>
                    <div class="contact-info">
                        <p class="contact-email">
                            <img src="{{ asset('landing/images/Icono-6.png') }}" alt="Logo Blanco" class="footer-icon"> info@meditecpty.com
                        </p>
                        <p class="contact-phone">
                            <img src="{{ asset('landing/images/Icono-7.png') }}" alt="Logo Blanco" class="ft-icon"> +507 124-4567
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <script>
        const hamMenu = document.querySelector('.ham-menu');
        const offScreenMenu = document.querySelector('.off-screen-menu');
        hamMenu.addEventListener('click', () => {
            hamMenu.classList.toggle('active');
            offScreenMenu.classList.toggle('active');
        })
        const menuLinks = document.querySelectorAll('.nav-mobile a[href^="#"]');
        menuLinks.forEach(menuLinks => {
            menuLinks.addEventListener("click", function () {
                hamMenu.classList.remove('active');
                offScreenMenu.classList.remove('active');
            })
        })

               // Función para manejar el scroll del navbar
        function handleNavbarScroll() {
            const navbar = document.getElementById('header');
            const heroSection = document.getElementById('inicio');
            const heroHeight = heroSection.offsetHeight;
            const scrollY = window.scrollY;

            // Si el scroll supera la altura de la primera sección
    if (scrollY > heroHeight - 100) { // -100px para que el cambio sea más suave
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
        }

        // Agregar el event listener para el scroll
        window.addEventListener('scroll', handleNavbarScroll);

        // Smooth scrolling para los enlaces del menú
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Llamar la función al cargar la página por si ya hay scroll
        handleNavbarScroll();

         // Loader para esperar a que cargue el background
    window.addEventListener('load', function() {
        const loader = document.getElementById('loader');
        const body = document.body;

        // Esperar un momento adicional para asegurar que todo cargó
        setTimeout(() => {
            loader.classList.add('hidden');
            body.classList.add('loaded');

            // Remover el loader del DOM después de la transición
            setTimeout(() => {
                loader.remove();
            }, 500);
        }, 300);
    });

    // Prevenir FOUC (Flash of Unstyled Content)
    document.addEventListener('DOMContentLoaded', function() {
        const heroBackground = new Image();
        heroBackground.src = "{{ asset('landing/images/LANDING-PORTADA.png') }}";
    });
    </script>
</body>
</html>
