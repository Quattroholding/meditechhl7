<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meditech - Plataforma de Gestión Clínica</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            overflow-x: hidden;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header */
        header {
            position: fixed;
            top: 0;
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 123, 191, 0.1);
            z-index: 1000;
            transition: all 0.3s ease;
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
        }

        .logo {
            font-size: 2rem;
            font-weight: bold;
            background: linear-gradient(92.39deg, #4E57CD 1.86%, #2E37A4 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            list-style: none;
        }

        .nav-links a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: #2E37A4;
        }

        .cta-btn {
            background:linear-gradient(92.39deg, #4E57CD 1.86%, #2E37A4 100%);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .cta-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 123, 191, 0.3);
        }

        /* Hero Section */
        .hero {
            min-height: 100vh;
            background: linear-gradient(92.39deg, #4E57CD 1.86%, #2E37A4 100%);
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000" opacity="0.1"><path d="M0,500 Q250,300 500,500 T1000,500 V1000 H0 Z" fill="white"/></svg>');
            background-size: cover;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            color: white;
            max-width: 600px;
        }

        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            animation: fadeInUp 1s ease;
        }

        .hero p {
            font-size: 1.3rem;
            margin-bottom: 2rem;
            opacity: 0.9;
            animation: fadeInUp 1s ease 0.2s both;
        }

        .hero-features {
            display: flex;
            gap: 2rem;
            margin-bottom: 3rem;
            animation: fadeInUp 1s ease 0.4s both;
        }

        .feature-badge {
            background: rgba(255, 255, 255, 0.2);
            padding: 8px 16px;
            border-radius: 25px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .hero-cta {
            animation: fadeInUp 1s ease 0.6s both;
        }

        .hero-cta .cta-btn {
            font-size: 1.1rem;
            padding: 16px 32px;
            margin-right: 1rem;
        }

        .demo-btn {
            background: transparent;
            color: white;
            border: 2px solid white;
            padding: 14px 30px;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .demo-btn:hover {
            background: white;
            color: #667eea;
        }

        .app-download-btn {
            background: linear-gradient(135deg, #34d399 0%, #10b981 100%);
            color: white;
            border: none;
            padding: 14px 30px;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin-left: 1rem;
        }

        .app-download-btn:hover {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
        }

        /* Features Section */
        .features {
            padding: 6rem 0;
            background: #f8fafc;
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 3rem;
            color: #334155;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 3rem;
            margin-top: 4rem;
        }

        .feature-card {
            background: white;
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            border-color: #2E37A4;
            box-shadow: 0 20px 40px rgba(0, 123, 191, 0.1);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(92.39deg, #4E57CD 1.86%, #2E37A4 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            font-size: 2rem;
        }

        .feature-card h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: #334155;
        }

        .feature-card p {
            color: #64748b;
            line-height: 1.7;
        }

        /* Stats Section */
        .stats {
            padding: 6rem 0;
            background:linear-gradient(92.39deg, #4E57CD 1.86%, #2E37A4 100%);
            color: white;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 3rem;
            text-align: center;
        }

        .stat-item h3 {
            font-size: 3rem;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }

        .stat-item p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        /* CTA Section */
        .final-cta {
            padding: 6rem 0;
            background: #1e293b;
            color: white;
            text-align: center;
        }

        .final-cta h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .final-cta p {
            font-size: 1.2rem;
            margin-bottom: 3rem;
            opacity: 0.9;
        }

        /* Footer */
        footer {
            background: #0f172a;
            color: white;
            padding: 3rem 0;
            text-align: center;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .footer-section h4 {
            margin-bottom: 1rem;
            color: #2E37A4;
        }

        .footer-section p, .footer-section a {
            color: #94a3b8;
            text-decoration: none;
            line-height: 1.8;
        }

        .footer-section a:hover {
            color: #2E37A4;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-on-scroll {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease;
        }

        .animate-on-scroll.animate {
            opacity: 1;
            transform: translateY(0);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }

            .hero h1 {
                font-size: 2.5rem;
            }

            .hero-features {
                flex-direction: column;
                gap: 1rem;
            }

            .hero-cta .cta-btn {
                display: block;
                margin-bottom: 1rem;
                margin-right: 0;
            }

            .app-download-btn {
                display: block;
                margin-left: 0;
                margin-bottom: 1rem;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        /* Medical Icons */
        .medical-cross::before {
            content: "⚕️";
        }

        .chart-icon::before {
            content: "📊";
        }

        .users-icon::before {
            content: "👥";
        }

        .security-icon::before {
            content: "🔒";
        }

        .directory-icon::before {
            content: "📱";
        }

        .cloud-icon::before {
            content: "☁️";
        }

        /* Medical Directory Styles */
        .medical-directory {
            padding: 6rem 0;
            background: white;
        }

        .directory-subtitle {
            text-align: center;
            font-size: 1.2rem;
            color: #64748b;
            margin-bottom: 3rem;
        }

        .directory-filters {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 3rem;
        }

        .filter-btn {
            background: white;
            border: 2px solid #e2e8f0;
            color: #64748b;
            padding: 12px 24px;
            border-radius: 50px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .filter-btn:hover,
        .filter-btn.active {
            background: linear-gradient(92.39deg, #4E57CD 1.86%, #2E37A4 100%);
            color: white;
            border-color: transparent;
            transform: translateY(-2px);
        }

        .practitioners-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
            margin-bottom: 4rem;
        }

        .practitioner-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .practitioner-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(78, 87, 205, 0.15);
            border-color: #4E57CD;
        }

        .practitioner-image {
            position: relative;
            text-align: center;
            padding: 2rem 2rem 0;
        }

        .doctor-photo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid white;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .online-status {
            position: absolute;
            top: 2rem;
            right: 2.5rem;
            width: 16px;
            height: 16px;
            background: #10b981;
            border: 3px solid white;
            border-radius: 50%;
            box-shadow: 0 2px 10px rgba(16, 185, 129, 0.3);
        }

        .practitioner-info {
            padding: 1.5rem 2rem 2rem;
            text-align: center;
        }

        .practitioner-name {
            font-size: 1.3rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 1rem;
        }

        .practitioner-specialties {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .specialty-tag {
            background: rgba(78, 87, 205, 0.1);
            color: #4E57CD;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .specialty-tag.more {
            background: rgba(100, 116, 139, 0.1);
            color: #64748b;
        }

        .practitioner-registry {
            font-size: 0.9rem;
            color: #64748b;
            margin-bottom: 1rem;
            font-style: italic;
        }

        .practitioner-contact {
            margin-bottom: 1.5rem;
        }

        .contact-item {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            color: #64748b;
        }

        .contact-icon {
            font-size: 1rem;
        }

        .appointment-btn {
            background: linear-gradient(92.39deg, #4E57CD 1.86%, #2E37A4 100%);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            width: 100%;
            text-align: center;
        }

        .appointment-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(78, 87, 205, 0.3);
        }

        .directory-cta {
            text-align: center;
            padding: 3rem;
            background: rgba(78, 87, 205, 0.05);
            border-radius: 20px;
            border: 2px dashed rgba(78, 87, 205, 0.2);
        }

        .directory-cta p {
            font-size: 1.2rem;
            color: #64748b;
            margin-bottom: 1.5rem;
        }

        .practitioner-card.hidden {
            display: none;
        }

        /* Responsive Medical Directory */
        @media (max-width: 768px) {
            .practitioners-grid {
                grid-template-columns: 1fr;
            }

            .directory-filters {
                flex-direction: column;
                align-items: center;
            }

            .filter-btn {
                width: 100%;
                max-width: 300px;
            }
        }
    </style>
</head>
<body>
<!-- Header -->
<header>
    <nav class="container">
        <div class="logo">Meditech</div>
        <ul class="nav-links">
            <li><a href="#inicio">Inicio</a></li>
            <li><a href="#caracteristicas">Características</a></li>
            <li><a href="#directorio">Directorio Médico</a></li>
            <li><a href="#beneficios">Beneficios</a></li>
            <li><a href="#contacto">Contacto</a></li>
        </ul>
        <a href="{{route('login')}}" class="cta-btn">Ingresar</a>
    </nav>
</header>

<!-- Hero Section -->
<section class="hero" id="inicio">
    <div class="container">
        <div class="hero-content">
            <h1>Revoluciona la Gestión de tu Clínica</h1>
            <p>Plataforma integral para doctores y clínicas que transforma la manera de gestionar historias clínicas y conectar con pacientes. Construida con estándares HL7 FHIR R5.</p>

            <div class="hero-features">
                <span class="feature-badge">✅ Historias Clínicas Digitales</span>
                <span class="feature-badge">✅ Directorio Médico</span>
                <span class="feature-badge">✅ Estándar HL7 FHIR</span>
            </div>

            <div class="hero-cta">
                <a href="{{route('patient.register')}}" class="cta-btn">Registrarse</a>
                <a href="#demo" class="demo-btn">Ver Demo</a>
                <a href="{{ asset('storage/app-release.apk') }}" class="app-download-btn" download="MeditecPacientes.apk">
                    📱 Descargar App Pacientes
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features" id="caracteristicas">
    <div class="container">
        <h2 class="section-title animate-on-scroll">¿Por qué elegir Meditech?</h2>

        <div class="features-grid">
            <div class="feature-card animate-on-scroll">
                <div class="feature-icon medical-cross"></div>
                <h3>Gestión Completa de Historias Clínicas</h3>
                <p>Sistema integral para registrar, actualizar y acceder a historias clínicas de pacientes de forma segura y eficiente. Compatible con estándares internacionales HL7 FHIR R5.</p>
            </div>

            <div class="feature-card animate-on-scroll">
                <div class="feature-icon directory-icon"></div>
                <h3>Directorio Médico Inteligente</h3>
                <p>Conecta pacientes con doctores especialistas a través de nuestro directorio médico avanzado. Facilita la búsqueda y programación de citas médicas.</p>
            </div>

            <div class="feature-card animate-on-scroll">
                <div class="feature-icon users-icon"></div>
                <h3>Gestión Multi-Usuario</h3>
                <p>Administra múltiples doctores, personal médico y pacientes desde una sola plataforma. Roles y permisos personalizables para cada tipo de usuario.</p>
            </div>

            <div class="feature-card animate-on-scroll">
                <div class="feature-icon security-icon"></div>
                <h3>Seguridad y Privacidad</h3>
                <p>Cumplimiento total con regulaciones de privacidad médica. Encriptación de extremo a extremo y backups automáticos para proteger la información sensible.</p>
            </div>

            <div class="feature-card animate-on-scroll">
                <div class="feature-icon chart-icon"></div>
                <h3>Reportes y Analytics</h3>
                <p>Dashboards intuitivos con métricas importantes para tu clínica. Reportes personalizables para optimizar la gestión y mejorar la atención al paciente.</p>
            </div>

            <div class="feature-card animate-on-scroll">
                <div class="feature-icon cloud-icon"></div>
                <h3>Acceso desde Cualquier Lugar</h3>
                <p>Plataforma web responsive que funciona en cualquier dispositivo. Accede a la información de tus pacientes desde computadora, tablet o móvil.</p>
            </div>
        </div>
    </div>
</section>

<!-- Medical Directory Section -->
<section class="medical-directory" id="directorio">
    <div class="container">
        <h2 class="section-title animate-on-scroll">Directorio Médico</h2>
        <p class="directory-subtitle animate-on-scroll">Conoce a nuestro equipo de especialistas médicos certificados</p>

        <div class="directory-filters animate-on-scroll">
            <button class="filter-btn active" data-specialty="all">Todas las Especialidades</button>
            @foreach($specialties as $specialty)
            <button class="filter-btn" data-specialty="{{ $specialty->id }}">{{ $specialty->name }}</button>
            @endforeach
        </div>

        <div class="practitioners-grid" id="practitioners-grid">
            @foreach($practitioners as $practitioner)
            <div class="practitioner-card animate-on-scroll" data-specialty="{{ $practitioner->specialties->pluck('id')->implode(' ') }}">
                <div class="practitioner-image">
                    @if($practitioner->avatar())
                        <img src="{{ asset('storage/' . $practitioner->avatar()->path) }}" alt="{{ $practitioner->name }}" class="doctor-photo">
                    @else
                        <img src="{{ asset('assets/img/profiles/avatar-02.jpg') }}" alt="{{ $practitioner->name }}" class="doctor-photo">
                    @endif
                    <div class="online-status"></div>
                </div>

                <div class="practitioner-info">
                    <h3 class="practitioner-name">{{ $practitioner->name }}</h3>
                    <div class="practitioner-specialties">
                        @foreach($practitioner->specialties->take(2) as $specialty)
                            <span class="specialty-tag">{{ $specialty->name }}</span>
                        @endforeach
                        @if($practitioner->specialties->count() > 2)
                            <span class="specialty-tag more">+{{ $practitioner->specialties->count() - 2 }} más</span>
                        @endif
                    </div>

                    @if($practitioner->registry)
                        <p class="practitioner-registry">Registro: {{ $practitioner->registry }}</p>
                    @endif

                    <div class="practitioner-contact">
                        @if($practitioner->phone)
                            <div class="contact-item">
                                <span class="contact-icon">📞</span>
                                <span>{{ $practitioner->phone }}</span>
                            </div>
                        @endif
                        @if($practitioner->email)
                            <div class="contact-item">
                                <span class="contact-icon">✉️</span>
                                <span>{{ $practitioner->email }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="practitioner-actions">
                        <a href="{{ route('patient.register') }}" class="appointment-btn">
                            📅 Agendar Cita
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        {{--}}
        <div class="directory-cta animate-on-scroll">
            <p>¿Eres médico? Únete a nuestro directorio médico</p>
            <a href="{{ route('login') }}" class="cta-btn">Registrarse como Médico</a>
        </div>
        {{--}}
    </div>
</section>

<!-- Stats Section -->
<section class="stats">
    <div class="container">
        <div class="stats-grid animate-on-scroll">
            <div class="stat-item">
                <h3>{{ $practitioners->count() }}+</h3>
                <p>Doctores Activos</p>
            </div>
            <div class="stat-item">
                <h3>{{ $specialties->count() }}+</h3>
                <p>Especialidades Médicas</p>
            </div>
            <div class="stat-item">
                <h3>10,000+</h3>
                <p>Pacientes Atendidos</p>
            </div>
            <div class="stat-item">
                <h3>99.9%</h3>
                <p>Tiempo de Actividad</p>
            </div>
        </div>
    </div>
</section>

<!-- Final CTA -->
<section class="final-cta" id="contacto">
    <div class="container">
        <h2>¿Listo para transformar tu clínica?</h2>
        <p>Únete a cientos de doctores y clínicas que ya confían en Meditech para gestionar sus operaciones médicas de forma más eficiente.</p>
        <div>
            <a href="mailto:contacto@meditech.com" class="cta-btn" style="margin-right: 1rem;">Contactar Ventas</a>
            <a href="#" class="demo-btn">Agendar Demo</a>
        </div>
    </div>
</section>

<!-- Footer -->
<footer>
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <h4>Meditech</h4>
                <p>Revolucionando la gestión clínica con tecnología de vanguardia y estándares internacionales HL7 FHIR R5.</p>
            </div>
            <div class="footer-section">
                <h4>Producto</h4>
                <p><a href="#">Características</a></p>
                <p><a href="#">Precios</a></p>
                <p><a href="#">Seguridad</a></p>
                <p><a href="#">API Docs</a></p>
                <p><a href="{{ asset('storage/app-release.apk') }}" download="MeditecPacientes.apk">📱 App Móvil</a></p>
            </div>
            <div class="footer-section">
                <h4>Soporte</h4>
                <p><a href="#">Centro de Ayuda</a></p>
                <p><a href="#">Contacto</a></p>
                <p><a href="#">Capacitación</a></p>
                <p><a href="#">Status</a></p>
            </div>
            <div class="footer-section">
                <h4>Contacto</h4>
                <p>contacto@meditech.com</p>
                <p>+1 (555) 123-4567</p>
                <p>Lun - Vie: 9AM - 6PM</p>
            </div>
        </div>
        <div style="border-top: 1px solid #334155; padding-top: 2rem; margin-top: 2rem;">
            <p>&copy; 2025 Meditech. Todos los derechos reservados.</p>
        </div>
    </div>
</footer>

<script>
    // Smooth scrolling
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

    // Animate on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate');
            }
        });
    }, observerOptions);

    document.querySelectorAll('.animate-on-scroll').forEach(el => {
        observer.observe(el);
    });

    // Header background on scroll
    window.addEventListener('scroll', () => {
        const header = document.querySelector('header');
        if (window.scrollY > 100) {
            header.style.background = 'rgba(255, 255, 255, 0.98)';
            header.style.boxShadow = '0 2px 20px rgba(0, 0, 0, 0.1)';
        } else {
            header.style.background = 'rgba(255, 255, 255, 0.95)';
            header.style.boxShadow = 'none';
        }
    });

    // Counter animation
    function animateCounters() {
        const counters = document.querySelectorAll('.stat-item h3');
        counters.forEach(counter => {
            const target = parseInt(counter.textContent.replace(/[^\d]/g, ''));
            let current = 0;
            const increment = target / 50;
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                counter.textContent = Math.floor(current) + (counter.textContent.includes('+') ? '+' : '') + (counter.textContent.includes('%') ? '%' : '');
            }, 30);
        });
    }

    // Trigger counter animation when stats section is visible
    const statsObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounters();
                statsObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    const statsSection = document.querySelector('.stats');
    if (statsSection) {
        statsObserver.observe(statsSection);
    }

    // Medical Directory Filters
    document.addEventListener('DOMContentLoaded', function() {
        const filterBtns = document.querySelectorAll('.filter-btn');
        const practitionerCards = document.querySelectorAll('.practitioner-card');

        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active class from all buttons
                filterBtns.forEach(b => b.classList.remove('active'));
                // Add active class to clicked button
                this.classList.add('active');

                const selectedSpecialty = this.getAttribute('data-specialty');

                practitionerCards.forEach(card => {
                    const cardSpecialties = card.getAttribute('data-specialty');

                    if (selectedSpecialty === 'all' || cardSpecialties.includes(selectedSpecialty)) {
                        card.classList.remove('hidden');
                        // Re-trigger animation
                        setTimeout(() => {
                            card.classList.add('animate');
                        }, 100);
                    } else {
                        card.classList.add('hidden');
                    }
                });
            });
        });
    });
</script>
</body>
</html>
