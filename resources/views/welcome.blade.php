<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Soluciones Meditec</title>
    <link rel="icon" href="{{url('images/favicon.ico')}}" type="image/x-icon">
    <link rel="stylesheet" href="{{url('styles/welcome.css?time='.time())}}">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Montserrat', sans-serif; }
    </style>
    @livewireStyles
</head>
<body>

<!-- HEADER -->
<header class="header">
    <div class="container header__inner">
        <a href="#" class="brand">
            <img src="{{url('images/logoFull.png')}}" alt="Soluciones Meditec" class="brand__logo">
        </a>

        <nav class="nav">
            <a href="#home">Home</a>
            <a href="#productos">Productos</a>
            <a href="#mision">Misión</a>
            <a href="#vision">Visión</a>
            <a href="#contacto">Contacto</a>
        </nav>

        <a class="btn btn--green" href="#contacto-form">Contáctanos</a>

        <button class="burger" aria-label="Abrir menú" onclick="document.body.classList.toggle('menu-open')">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>

<!-- HERO -->
<main id="home" class="hero">


    <div class=" hero__content">
        <div class="hero__bg">
            <!-- placeholder hero -->
            <img src="{{ asset('landing/images/hero-welcome.jpg') }}" alt="Hero">
        </div>
        <div class="container">
            <div class="hero__line"></div>

            <div class=" hero__card">
                <h1>Conócenos</h1>
                <p>
                    Somos una empresa de tecnología en salud que integra plataformas digitales diseñadas
                    para optimizar la gestión médica y mejorar la experiencia del paciente.
                </p>
            </div>
        </div>

    </div>

    <!-- HEXAGON overlay decoration (simulado con gradientes) -->
    <div class="hero__hex"></div>
</main>

<!-- BLUE BLOCK -->
<section class="container2 blueblock">
    <div class="container blueblock__inner">
        <p>
            Nuestras plataformas permiten a médicos, hospitales, clínicas y centros de salud administrar agendas,
            historias clínicas electrónicas, consultas, así como procesos administrativos de forma eficiente y segura.
        </p>
        <p>
            Al mismo tiempo, ofrece herramientas orientadas a los pacientes, facilitando el acceso a su información médica,
            resultados, citas, recetas y seguimiento de su salud desde un solo lugar.
        </p>
    </div>
</section>

<!-- BIG TEXT -->
<section class="container bigtext" id="sobre">

    <div class="container2">
        <h2>
            Soluciones Meditec conecta a profesionales y pacientes a través de tecnología confiable,
            contribuyendo a una atención médica más ágil, organizada y centrada en las personas.
        </h2>
    </div>

</section>

<!-- MISION / VISION -->
<section class="mv">
    <div class=" container mv__grid">
        <div class="green__line"></div>
        <div class="cyan__line"></div>
        <div class="mv__left">

            <div class="mv__block" id="mision">
                <span class="mv__tag mv__tag--dark">Misión</span>
                <p class="text-blue">
                    Brindar soluciones tecnológicas integrales en salud que conecten a instituciones, médicos y pacientes,
                    facilitando una gestión de salud eficiente, segura y accesible, y contribuyendo a una atención más organizada y humana.
                </p>
            </div>

            <div class="mv__block" id="vision">
                <span class="mv__tag mv__tag--light">Visión</span>
                <p class="text-cyan">
                    Ser una empresa líder en innovación tecnológica en salud en la región, reconocida por transformar la manera en que
                    se gestionan y viven los servicios médicos, impulsando un ecosistema digital que mejore la calidad de la atención y el bienestar de las personas.
                </p>
            </div>
        </div>
        <aside class="mv__right">
            <img src="{{ asset('landing/images/lateral-welcome.jpg') }}" alt="Imagen sección">
        </aside>

    </div>
</section>

<!-- PRODUCTOS SECTION -->
<section class="products" id="productos">
    <div class="container">
        <div class="products__header">
            <h2 class="products__title">Nuestros Productos</h2>
            <p class="products__subtitle">Soluciones digitales diseñadas para transformar la atención médica</p>
        </div>

        <div class="products__grid">

            <!-- SAMI Sistema -->
            <div class="product-card">
                <div class="product-card__image">
                    <div class="carousel" data-carousel="sami">
                        <div class="carousel__track">
                            <div class="carousel__slide active">
                                <img src="{{url('images/tutorial/dashboard/global.png')}}" alt="SAMI Dashboard">
                            </div>
                            <div class="carousel__slide">
                                <img src="{{url('images/tutorial/appointments/appointment-schedule.png')}}" alt="Agenda de Citas">
                            </div>
                            <div class="carousel__slide">
                                <img src="{{url('images/tutorial/encounters/encounter_start.png')}}" alt="Consultas Médicas">
                            </div>
                            <div class="carousel__slide">
                                <img src="{{url('images/tutorial/patients/profile.png')}}" alt="Perfil de Paciente">
                            </div>
                            <div class="carousel__slide">
                                <img src="{{url('images/tutorial/invoices/invoice_list.png')}}" alt="Facturación">
                            </div>
                        </div>
                        <button class="carousel__btn carousel__btn--prev" aria-label="Anterior">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M15 18l-6-6 6-6"/>
                            </svg>
                        </button>
                        <button class="carousel__btn carousel__btn--next" aria-label="Siguiente">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 18l6-6-6-6"/>
                            </svg>
                        </button>
                        <div class="carousel__indicators">
                            <span class="carousel__dot active"></span>
                            <span class="carousel__dot"></span>
                            <span class="carousel__dot"></span>
                            <span class="carousel__dot"></span>
                            <span class="carousel__dot"></span>
                        </div>
                    </div>
                    <div class="product-card__badge">Sistema Completo</div>
                </div>

                <div class="product-card__content">
                    <h3 class="product-card__name">SAMI</h3>
                    <p class="product-card__tagline">Sistema de Gestión para Clínicas y Hospitales</p>

                    <ul class="product-card__features">
                        <li>
                            <svg class="check-icon" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span>Gestión completa de citas y consultas</span>
                        </li>
                        <li>
                            <svg class="check-icon" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span>Historias clínicas electrónicas</span>
                        </li>
                        <li>
                            <svg class="check-icon" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span>Facturación y reportes integrados</span>
                        </li>
                        <li>
                            <svg class="check-icon" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span>Portal para pacientes</span>
                        </li>
                    </ul>

                    <a href="{{ url('http://sami.' . str_replace('www.', '', request()->getHost())) }}" class="product-card__btn btn btn--blue">
                        Conocer más
                        <svg class="arrow-icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- SAMI Recetas -->
            <div class="product-card">
                <div class="product-card__image">
                    <div class="carousel" data-carousel="recetas">
                        <div class="carousel__track">
                            <div class="carousel__slide active">
                                <img src="{{url('landing/images/sami_recetas_movil_dashboard.jpeg')}}" alt="SAMI Recetas Dashboard">
                            </div>
                            <div class="carousel__slide">
                                <img src="{{url('landing/images/sami_recetas_movil_registro.jpeg')}}" alt="Registro SAMI Recetas">
                            </div>
                            <div class="carousel__slide">
                                <img src="{{url('landing/images/sami_recetas_movil_nueva_receta.jpeg')}}" alt="Nueva Receta">
                            </div>
                        </div>
                        <button class="carousel__btn carousel__btn--prev" aria-label="Anterior">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M15 18l-6-6 6-6"/>
                            </svg>
                        </button>
                        <button class="carousel__btn carousel__btn--next" aria-label="Siguiente">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 18l6-6-6-6"/>
                            </svg>
                        </button>
                        <div class="carousel__indicators">
                            <span class="carousel__dot active"></span>
                            <span class="carousel__dot"></span>
                            <span class="carousel__dot"></span>
                        </div>
                    </div>
                    <div class="product-card__badge product-card__badge--free">100% Gratis</div>
                </div>

                <div class="product-card__content">
                    <h3 class="product-card__name">SAMI Recetas</h3>
                    <p class="product-card__tagline">Recetas Médicas Digitales para Panamá</p>

                    <ul class="product-card__features">
                        <li>
                            <svg class="check-icon" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span>Recetas ilimitadas completamente gratis</span>
                        </li>
                        <li>
                            <svg class="check-icon" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span>Válidas para República de Panamá</span>
                        </li>
                        <li>
                            <svg class="check-icon" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span>Disponible en iOS y Android</span>
                        </li>
                        <li>
                            <svg class="check-icon" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span>Rápido y fácil de usar</span>
                        </li>
                    </ul>

                    <a href="{{ url('http://samirx.' . str_replace('www.', '', request()->getHost())) }}" class="product-card__btn btn btn--green">
                        Conocer más
                        <svg class="arrow-icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- CONTACT FORM SECTION -->
<section class="contact-section" id="contacto-form">
    <div class="container">
        <div class="contact-header">
            <h2 class="contact-title">¿Listo para transformar tu práctica médica?</h2>
            <p class="contact-subtitle">Escríbenos y uno de nuestros especialistas te contactará para mostrarte cómo nuestras soluciones pueden ayudarte</p>
        </div>

        <div class="contact-grid">
            <div class="contact-info">
                <h3>Información de Contacto</h3>
                <div class="info-item">
                    <svg class="info-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 8L10.89 13.26C11.5 13.67 12.5 13.67 13.11 13.26L21 8M5 19H19C20.1046 19 21 18.1046 21 17V7C21 5.89543 20.1046 5 19 5H5C3.89543 5 3 5.89543 3 7V17C3 18.1046 3.89543 19 5 19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <div>
                        <strong>Email</strong>
                        <a href="mailto:business@meditecpty.com">business@meditecpty.com</a>
                    </div>
                </div>

                <div class="info-item">
                    <svg class="info-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 5C3 3.89543 3.89543 3 5 3H8.27924C8.70967 3 9.09181 3.27543 9.22792 3.68377L10.7257 8.17721C10.8831 8.64932 10.6694 9.16531 10.2243 9.38787L7.96701 10.5165C9.06925 12.9612 11.0388 14.9308 13.4835 16.033L14.6121 13.7757C14.8347 13.3306 15.3507 13.1169 15.8228 13.2743L20.3162 14.7721C20.7246 14.9082 21 15.2903 21 15.7208V19C21 20.1046 20.1046 21 19 21H18C9.71573 21 3 14.2843 3 6V5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <div>
                        <strong>Teléfono</strong>
                        <a href="tel:+5071234567">+507-123-4567</a>
                    </div>
                </div>

                <div class="info-item">
                    <svg class="info-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.657 16.657L13.414 20.9C13.039 21.275 12.535 21.487 12.01 21.487C11.485 21.487 10.981 21.275 10.606 20.9L6.343 16.657C5.22422 15.5381 4.46234 14.1127 4.15369 12.5608C3.84504 11.009 4.00349 9.40047 4.60901 7.93868C5.21452 6.4769 6.2399 5.22749 7.55548 4.34846C8.87107 3.46943 10.4178 3 12 3C13.5822 3 15.1289 3.46943 16.4445 4.34846C17.7601 5.22749 18.7855 6.4769 19.391 7.93868C19.9965 9.40047 20.155 11.009 19.8463 12.5608C19.5377 14.1127 18.7758 15.5381 17.657 16.657Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12 13C13.1046 13 14 12.1046 14 11C14 9.89543 13.1046 9 12 9C10.8954 9 10 9.89543 10 11C10 12.1046 10.8954 13 12 13Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <div>
                        <strong>Ubicación</strong>
                        <span>Panamá, República de Panamá</span>
                    </div>
                </div>

                <div class="social-links">
                    <a href="https://wa.me/5071234567" target="_blank" rel="noopener" class="social-link">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                    </a>
                </div>
            </div>

            <div class="contact-form-container">
                @livewire('contact-form')
            </div>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer class="footer">
    <div class="container footer__inner">
        <p>© <span id="year"></span> Soluciones Meditec. Todos los derechos reservados.</p>
    </div>
</footer>

<script>
    document.getElementById('year').textContent = new Date().getFullYear();

    // Carousel functionality
    document.querySelectorAll('.carousel').forEach(carousel => {
        const track = carousel.querySelector('.carousel__track');
        const slides = Array.from(track.querySelectorAll('.carousel__slide'));
        const prevBtn = carousel.querySelector('.carousel__btn--prev');
        const nextBtn = carousel.querySelector('.carousel__btn--next');
        const dots = Array.from(carousel.querySelectorAll('.carousel__dot'));
        let currentIndex = 0;
        let autoplayInterval;

        function updateCarousel(index) {
            slides.forEach(slide => slide.classList.remove('active'));
            dots.forEach(dot => dot.classList.remove('active'));

            slides[index].classList.add('active');
            dots[index].classList.add('active');
            currentIndex = index;
        }

        function nextSlide() {
            const newIndex = (currentIndex + 1) % slides.length;
            updateCarousel(newIndex);
        }

        function prevSlide() {
            const newIndex = (currentIndex - 1 + slides.length) % slides.length;
            updateCarousel(newIndex);
        }

        function startAutoplay() {
            autoplayInterval = setInterval(nextSlide, 4000);
        }

        function stopAutoplay() {
            clearInterval(autoplayInterval);
        }

        // Event listeners
        nextBtn.addEventListener('click', () => {
            nextSlide();
            stopAutoplay();
            startAutoplay();
        });

        prevBtn.addEventListener('click', () => {
            prevSlide();
            stopAutoplay();
            startAutoplay();
        });

        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                updateCarousel(index);
                stopAutoplay();
                startAutoplay();
            });
        });

        carousel.addEventListener('mouseenter', stopAutoplay);
        carousel.addEventListener('mouseleave', startAutoplay);

        // Start autoplay
        startAutoplay();
    });
</script>

@livewireScripts
</body>
</html>
