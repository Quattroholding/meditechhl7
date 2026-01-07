<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="facebook-domain-verification" content="v6w57zlpwwn256rbqabs1ws6702zpk" />
    <title>Soluciones MEDITEC - Innovación tecnológica al servicio de la salud</title>
    <link rel="icon" href="{{url('images/favicon.ico')}}" type="image/x-icon">
    <link rel="stylesheet" href="{{ url('landing/style.css?time='.time()) }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
                <li><a href="#pricing-section">PRECIOS</a></li>
                <li><a href="#prescription-app-section">HERRAMIENTAS</a></li>
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
                <li><a href="#pricing-section">PRECIOS</a></li>
                <li><a href="#prescription-app-section">HERRAMIENTAS</a></li>
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

     <!-- Pricing Section -->
     <section class="pricing-section" id="pricing-section">
         <div class="container">
             <div class="section-header text-center">
                 <img src="{{ asset('landing/images/Icono-9.png') }}" alt="Paquetes" class="logo-color">
                 <h2>Nuestros Planes de Suscripción</h2><br/>

             </div>
             <p class="section-description">
                 Elige el plan perfecto para tus necesidades.
                 Todos incluyen soporte técnico y actualizaciones constantes.
             </p>

             <div class="sami-info-box">
                 <div class="sami-icon">
                     <i class="fas fa-robot"></i>
                 </div>
                 <div class="sami-text">
                     <h4><i class="fas fa-star"></i> ¿Por qué recomendamos el Plan Estándar?</h4>
                     <p>
                         <strong>Incluye acceso a SAMI</strong>, nuestro agente de WhatsApp impulsado por IA que no solo facilita
                         la creación de citas, sino que además <strong>te conecta con nuevos pacientes</strong> al incluir tu
                         consultorio en nuestro <strong>Directorio Médico Inteligente</strong>.
                     </p>
                     <p class="sami-benefits">
                         <span><i class="fas fa-check-circle"></i> Agenda citas 24/7 automáticamente</span>
                         <span><i class="fas fa-check-circle"></i> Aparece en búsquedas de pacientes</span>
                         <span><i class="fas fa-check-circle"></i> Consigue nuevos pacientes constantemente</span>
                     </p>
                 </div>
             </div>

             <div class="pricing-grid">
                 @foreach(\App\Models\Package::where('is_active', true)->orderBy('base_price')->get() as $package)
                     <div class="pricing-card {{ $loop->last ? 'pricing-card-enterprise' : '' }} {{ $package->id == 2 ? 'pricing-card-recommended' : '' }}">
                         {{--}}
                         @if($package->id == 2)
                             <div class="recommended-badge">
                                 <i class="fas fa-star"></i> RECOMENDADO
                             </div>
                         @endif
                         {{--}}
                         @if($package->agent_available)
                             <div class="sami-badge">
                                 <i class="fas fa-robot"></i> INCLUYE SAMI
                             </div>
                         @endif
                         <div class="pricing-header">
                             <h3>{{ $package->name }}</h3>
                             @if($package->base_price > 0 and $package->id <>4)
                                 <div class="pricing-price">
                                     <span class="currency">$</span>
                                     <span class="amount">@isset($package->base_price){{ number_format($package->base_price, 0) }} @else XXX @endif</span>
                                     <span class="period">/mes</span>
                                 </div>
                             @else
                                 <div class="pricing-price">
                                     <span class="amount">Contactar</span>
                                 </div>
                             @endif
                             @if($package->id <>4)
                                 <p class="pricing-subtitle">
                                     {{ $package->max_users }} {{ $package->max_users == 1 ? 'Usuario' : 'Usuarios' }}
                                 </p>
                             @endif
                         </div>

                         <div class="pricing-features">
                             <ul>
                                 @foreach($package->features ?? [] as $feature)
                                     <li><i class="fas fa-check"></i> {{ $feature }}</li>
                                 @endforeach
                             </ul>
                         </div>

                         <div class="pricing-action">
                             @if($loop->last)
                                 <button class="pricing-button pricing-button-enterprise" onclick="openEnterpriseModal()">
                                     Contactar Ventas
                                 </button>
                             @else
                                 <a href="{{ route('public.register', ['package' => $package->id]) }}" class="pricing-button">
                                     Suscribirse Ahora
                                 </a>
                             @endif
                         </div>
                     </div>
                 @endforeach
             </div>
         </div>
     </section>

    <!-- Medical Prescription App Section -->
    <section class="prescription-app-section" id="prescription-app-section">
        <div class="container">
            <div class="section-header">
                <img src="{{ asset('landing/images/Icono-9.png') }}" alt="App de Recetas" class="logo-color">
                <h2>Herramientas adicionales para médicos</h2>
            </div>

            <div class="app-content">
                <div class="app-description">
                    <h3>App de Recetas Médicas - ¡Completamente Gratuita!</h3>
                    <p class="app-intro">
                        Como parte de nuestro compromiso con los profesionales de la salud,
                        ofrecemos una aplicación móvil gratuita para la creación y gestión de recetas médicas.
                    </p>

                    <div class="app-features">
                        <div class="app-feature-item">
                            <span class="feature-icon">📱</span>
                            <div>
                                <h4>Múltiples Perfiles</h4>
                                <p>Crea y gestiona diferentes perfiles médicos para distintas especialidades o consultorios</p>
                            </div>
                        </div>

                        <div class="app-feature-item">
                            <span class="feature-icon">📋</span>
                            <div>
                                <h4>Creación Rápida de Recetas</h4>
                                <p>Genera recetas médicas profesionales de forma rápida y sencilla desde tu smartphone</p>
                            </div>
                        </div>

                        <div class="app-feature-item">
                            <span class="feature-icon">💬</span>
                            <div>
                                <h4>Compartir por WhatsApp</h4>
                                <p>Envía las recetas directamente a tus pacientes por WhatsApp de manera segura</p>
                            </div>
                        </div>

                        <div class="app-feature-item">
                            <span class="feature-icon">🆓</span>
                            <div>
                                <h4>100% Gratuita</h4>
                                <p>Sin costo alguno, sin publicidad, sin limitaciones. Una herramienta profesional a tu disposición</p>
                            </div>
                        </div>
                    </div>


                </div>

                <div class="app-visual">
                    <div class="app-mockup">
                        <img src="{{ asset('landing/images/Icono-5.png') }}" alt="App de Recetas" class="mockup-image">
                    </div>
                    <div class="app-download">
                        <a href="{{ asset('storage/app-recepy.apk') }}" class="download-button" download>
                            <img src="{{ asset('landing/images/Icono-8.png') }}" alt="Descargar" class="download-icon">
                            Descargar APK para Android
                        </a>
                        <p class="download-note">Compatible con Android 5.0 o superior</p>
                    </div>
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

    <!-- Modal de Contacto Empresarial -->
    <div id="enterpriseModal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <button class="modal-close" onclick="closeEnterpriseModal()">&times;</button>
            <h3>Contactar Ventas - Plan Empresarial</h3>
            <p>Completa el formulario y nos pondremos en contacto para una propuesta personalizada.</p>

            <form id="enterpriseForm" action="{{ route('enterprise.lead.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Nombre Completo *</label>
                    <input type="text" name="full_name" required>
                </div>
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Teléfono *</label>
                    <input type="tel" name="phone" required>
                </div>
                <div class="form-group">
                    <label>Nombre de la Empresa/Clínica *</label>
                    <input type="text" name="company_name" required>
                </div>
                <div class="form-group">
                    <label>Cuéntanos sobre tus necesidades</label>
                    <textarea name="message" rows="4"></textarea>
                </div>
                <button type="submit" class="btn-submit">Enviar Solicitud</button>
            </form>
        </div>
    </div>
    {{--}}
    <section class="cta-section">
        <div class="container">
            <div class="register-section">
                <p>¿Listo para dar el siguiente paso en la transformación digital de tu práctica médica?</p>
                <a class="cta-button" href="{{route('patient.register')}}">Regístrate aquí</a>
                <p class="cta-subtitle">Forma parte de la revolución Soluciones Meditec</p>
            </div>
        </div>
    </section>
    {{--}}

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-contact">
                    <p>Contáctanos:</p>
                    <span><i class="fa fa-user-injured"></i></span>
                    <div class="contact-info">
                        <p class="contact-email">
                            <img src="{{ asset('landing/images/Icono-6.png') }}" alt="Logo Blanco" class="footer-icon"> business@meditecpty.com
                        </p>
                        <p class="contact-phone">
                            <img src="{{ asset('landing/images/Icono-7.png') }}" alt="Logo Blanco" class="ft-icon"> +507 8316100
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

    // Funciones para el modal empresarial
    function openEnterpriseModal() {
        document.getElementById('enterpriseModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeEnterpriseModal() {
        document.getElementById('enterpriseModal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    // Cerrar modal al hacer click fuera
    document.getElementById('enterpriseModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeEnterpriseModal();
    });

    // Manejar submit del formulario
    document.getElementById('enterpriseForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        try {
            const response = await fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (response.ok) {
                alert('¡Gracias! Nos pondremos en contacto contigo pronto.');
                closeEnterpriseModal();
                this.reset();
            } else {
                alert('Hubo un error. Por favor intenta nuevamente.');
            }
        } catch (error) {
            alert('Error de conexión. Por favor intenta más tarde.');
        }
    });
    </script>

    <!-- WhatsApp Floating Button -->
    <a href="https://wa.me/5078316172" target="_blank" class="whatsapp-float" title="Agendar Cita por WhatsApp">
        <img src="{{ asset('images/whatsapp.png') }}" alt="WhatsApp">
        <span class="whatsapp-text">Agendar Cita</span>
    </a>

    <style>
        .whatsapp-float {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            align-items: center;
            gap: 10px;
            background: #25D366;
            color: white;
            padding: 12px 20px;
            border-radius: 50px;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(37, 211, 102, 0.4);
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
            font-weight: 500;
        }

        .whatsapp-float:hover {
            background: #128C7E;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(37, 211, 102, 0.6);
            color: white;
        }

        .whatsapp-float img {
            width: 32px;
            height: 32px;
        }

        .whatsapp-text {
            font-size: 16px;
            white-space: nowrap;
        }

        /* Responsive: Solo mostrar icono en móviles */
        @media (max-width: 768px) {
            .whatsapp-float {
                padding: 15px;
                border-radius: 50%;
                width: 60px;
                height: 60px;
                justify-content: center;
            }

            .whatsapp-text {
                display: none;
            }

            .whatsapp-float img {
                width: 30px;
                height: 30px;
            }
        }

        /* Animación de entrada */
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .whatsapp-float {
            animation: slideInUp 0.5s ease-out;
        }
    </style>
</body>
</html>
