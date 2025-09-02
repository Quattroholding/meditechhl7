<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soluciones MEDITEC - Innovación tecnológica al servicio de la salud</title>
    <link rel="stylesheet" href="{{ asset('landing/style.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <nav class="nav">
            <div class="nav-brand">
                <img src="{{ asset('landing/images/logo.svg') }}" alt="Meditec" class="logo">
            </div>
            <ul class="nav-menu">
                <li><a href="#inicio">INICIO</a></li>
                <li><a href="#quienes-somos">QUIENES SOMOS</a></li>
                <li><a href="#como-funciona">COMO FUNCIONA</a></li>
                <li><a href="#planes">PLANES Y PRECIOS</a></li>
                <li><a href="#contacto">CONTACTO</a></li>
            </ul>
            <button class="search-btn">
                <img src="{{ asset('landing/images/search-icon.svg') }}" alt="Buscar">
                BUSCAR
            </button>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero" id="inicio">
        <div class="hero-background">
            <div class="hero-content">
                <div class="hero-text">
                    <h1 class="hero-title">
                        Soluciones<br>
                        <span class="hero-title-green">MEDITEC</span>
                    </h1>
                    <p class="hero-subtitle">
                        Innovación<br>
                        tecnológica al<br>
                        servicio de la<br>
                        salud.
                    </p>
                </div>
                <div class="hero-graphics">
                    <img src="{{ asset('landing/images/dna-hand.png') }}" alt="DNA in hand" class="hero-image">
                    <div class="medical-icons">
                        <div class="medical-cross cross-1">+</div>
                        <div class="medical-cross cross-2">+</div>
                        <div class="medical-cross cross-3">+</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- What is Soluciones Meditec Section -->
    <section class="what-is-section">
        <div class="container">
            <div class="section-header">
                <h2>¿Qué es Soluciones Meditec?</h2>
            </div>
            <p class="section-description">
                Soluciones Meditec es una plataforma integral que digitaliza un software médico.<br><br>
                Es una plataforma integral diseñada para optimizar la forma en que pacientes,
                médicos, clínicas y hospitales gestionan la información y se conectan entre sí.<br><br>
                Centraliza procesos, mejora la eficiencia y eleva la calidad de atención, permitiendo:
            </p>

            <div class="features-grid">
                <div class="feature-item">
                    <div class="feature-number">1</div>
                    <div class="feature-content">
                        <h3>Gestionar historias clínicas electrónicas de forma segura, ágil y actualizada.</h3>
                        <img src="{{ asset('landing/images/medical-records.png') }}" alt="Historias clínicas" class="feature-image">
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-number">2</div>
                    <div class="feature-content">
                        <h3>Acceder a la información del paciente en cualquier momento y desde cualquier dispositivo (PC, tablet e smartphone).</h3>
                        <img src="{{ asset('landing/images/devices.png') }}" alt="Dispositivos" class="feature-image">
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-number">3</div>
                    <div class="feature-content">
                        <h3>Cumplir con regulaciones de privacidad y estándares de seguridad internacional.</h3>
                        <img src="{{ asset('landing/images/security.png') }}" alt="Seguridad" class="feature-image">
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
                    <img src="{{ asset('landing/images/healthcare-professionals.png') }}" alt="Profesionales de la salud" class="audience-image">
                    <h3>Profesionales de la salud independientes</h3>
                </div>

                <div class="audience-item">
                    <img src="{{ asset('landing/images/clinics-centers.png') }}" alt="Clínicas y centros médicos" class="audience-image">
                    <h3>Clínicas y centros médicos de todos los tamaños</h3>
                </div>

                <div class="audience-item">
                    <img src="{{ asset('landing/images/hospitals-networks.png') }}" alt="Hospitales y redes de salud" class="audience-image">
                    <h3>Hospitales y redes de salud corporativas</h3>
                </div>
            </div>
        </div>
    </section>

    <!-- Key Features Section -->
    <section class="features-section">
        <div class="container">
            <h2>Funcionalidades clave</h2>

            <div class="key-features-grid">
                <div class="key-feature">
                    <img src="{{ asset('landing/images/digital-records.png') }}" alt="Registros digitales" class="key-feature-icon">
                    <h3>Registro médico digital centralizado</h3>
                    <p>Almacena, consulta y gestiona el historial médico del paciente con facilidad y precisión.</p>
                </div>

                <div class="key-feature">
                    <img src="{{ asset('landing/images/smart-scheduling.png') }}" alt="Programación inteligente" class="key-feature-icon">
                    <h3>Programación inteligente</h3>
                    <p>Optimiza consulta y organiza la agenda médica automáticamente con facilidad y precisión.</p>
                </div>

                <div class="key-feature">
                    <img src="{{ asset('landing/images/compliance-security.png') }}" alt="Cumplimiento y seguridad" class="key-feature-icon">
                    <h3>Cumplimiento y seguridad</h3>
                    <p>Protege la privacidad del paciente cumpliendo con estándares médicos y administrativos desde un solo lugar.</p>
                </div>

                <div class="key-feature">
                    <img src="{{ asset('landing/images/reports-metrics.png') }}" alt="Reportes y métricas" class="key-feature-icon">
                    <h3>Reportes y métricas</h3>
                    <p>Genera informes personalizados que facilitan el seguimiento de pacientes, indicadores de atención y reportes estadísticos para facilitar la toma de decisiones estratégicas.</p>
                </div>

                <div class="key-feature">
                    <img src="{{ asset('landing/images/telemedicine.png') }}" alt="Telemedicina" class="key-feature-icon">
                    <h3>Telemedicina</h3>
                    <p>Permite consultas remotas ampliando la cobertura médica para mejorar la accesibilidad a servicios médicos desde la comodidad del hogar.</p>
                </div>

                <div class="key-feature">
                    <img src="{{ asset('landing/images/integration.png') }}" alt="Integración" class="key-feature-icon">
                    <h3>Integración</h3>
                    <p>Se integra fácilmente a sistemas existentes, permitiendo un flujo de trabajo sin interrupciones complicadas, datos compartidos e históricos.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Section -->
    <section class="why-choose-section">
        <div class="container">
            <h2>¿Por qué elegir Soluciones Meditec?</h2>
            <p>Porque creemos que la tecnología debe estar al servicio de la salud.</p>
            <p>Con Soluciones Meditec, tendrás:</p>

            <div class="benefits-list">
                <div class="benefit-item">
                    <span class="benefit-text">Mayor control y organizativa</span>
                </div>
                <div class="benefit-item">
                    <span class="benefit-text">Datos seguros y siempre disponibles</span>
                </div>
                <div class="benefit-item">
                    <span class="benefit-text">Menos papeleo = más tiempo para tus pacientes</span>
                </div>
                <div class="benefit-item">
                    <span class="benefit-text">Una mejor experiencia para pacientes y tu equipo</span>
                </div>
            </div>

            <div class="cta-section">
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
                    <p>📧 respaldo@meditecpty.com</p>
                    <p>📞 +507 124-4567</p>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
