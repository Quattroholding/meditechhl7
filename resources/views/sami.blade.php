<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SAMI Landing</title>
    <link rel="icon" href="{{url('images/iconoSAMI.ico')}}" type="image/x-icon">
    <link rel="stylesheet" href="{{url('styles/sami.css?time'.time())}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Montserrat', sans-serif; }
    </style>
</head>
<body>

<!-- NAV + HERO = MISMA SECCIÓN -->
<section class="top-hero" id="home">
    <header class="header">
        <div class="container header__inner">
            <a href="#" class="brand">
                <img src="{{url('images/logoSAMI.png')}}" alt="SAMI" class="brand__logo">
            </a>

            <nav class="nav principal-nav">
                <ul class="sidebar">
                    <li onclick="hideSidebar()"><a href="#"><i class="fa fa-times" aria-hidden="true"></i></a></li>
                    <li><a href="#home">HOME</a></li>
                    <li><a href="#quienes">QUIÉNES SOMOS</a></li>
                    <li><a href="#como">CÓMO FUNCIONA</a></li>
                    <li><a href="#planes">PLANES</a></li>
                    <li><a href="{{route('patients.landing')}}">SOY PACIENTE</a></li>
                    <li><a href="{{url('http://samirx.' . str_replace('sami.', '', request()->getHost()))}}">SAMI RECETAS</a></li>
                    <li><a href="{{url('http://' . str_replace('sami.', '', request()->getHost()))}}">SOLUCIONES MEDITEC</a></li>
                </ul>
                <ul class="mainbar">
                    <li class="hidden-mobile"><a href="#home">HOME</a></li>
                    <li class="hidden-mobile"><a href="#quienes">QUIÉNES SOMOS</a></li>
                    <li class="hidden-mobile"><a href="#como">CÓMO FUNCIONA</a></li>
                    <li class="hidden-mobile"><a href="#planes">PLANES</a></li>
                    <li class="hidden-mobile"><a href="{{route('patients.landing')}}">SOY PACIENTE</a></li>
                    <li class="hidden-mobile"><a href="{{url('http://samirx.' . str_replace('sami.', '', request()->getHost()))}}">SAMI RECETAS</a></li>
                    <li class="hidden-mobile"><a href="{{url('http://' . str_replace('sami.', '', request()->getHost()))}}">SOLUCIONES MEDITEC</a></li>
                                        <li class="menu-hamburguesa" onclick="showSidebar()">
                        <i class="fa fa-bars" aria-hidden="true"></i>
                    </li>
                </ul>
            </nav>

            <div class="header__actions">
                <a href="{{route('login')}}" class="btn btn--outline btn--small btn-login">Ingresar</a>
            </div>

            <button class="burger" aria-label="Abrir menú" onclick="document.body.classList.toggle('menu-open')">
                <span></span><span></span><span></span>
            </button>
        </div>

        <!-- mobile menu -->
        <div class="nav-mobile">
            <a href="#home">HOME</a>
            <a href="#quienes">QUIÉNES SOMOS</a>
            <a href="#como">CÓMO FUNCIONA</a>
            <a href="#planes">PLANES</a>
            <a href="{{route('login')}}">Ingresar</a>
        </div>
    </header>

    <div class="container hero">
        <div class="hero__left">
            <h1>
                Innovación <br>
                tecnológica al <br>
                servicio de la <br>
                salud.
            </h1>

            <div class="hero__buttons">
                <a href="#quienes" class="btn btn--primary">Conoce más</a>
                <a href="{{route('login')}}" class="btn btn--outline">Ingresar</a>
            </div>
        </div>

        <div class="hero__right">
            <div class="hero__img2">
                <img src="{{ asset('landing/images/hero-sami.png') }}" alt="Hero">
            </div>
        </div>
    </div>
</section>
<!-- ¿QUÉ ES SAMI? -->
<section class="sami" id="quienes">
    <div class="container">

        <div class="sami__title">
            <span class="badge badge--white">¿Qué es SAMI?</span>
        </div>

        <div class="sami__intro">
            <p>
                SAMI es mucho más que un software médico.
            </p>
            <p>
                Es una plataforma integral diseñada para optimizar la forma en que médicos,<br/>
                clínicas y hospitales gestionan la información de sus pacientes y se conectan con ellos.
            </p>
            <p>
                Centraliza procesos, mejora la eficiencia y eleva la calidad de atención, permitiendo:
            </p>
        </div>

        <!-- ROW 1 -->
        <div class="sami-row">
            <div class="sami-row__img">
                <img src="{{ asset('landing/images/Foto-2.png') }}" alt="">
            </div>
            <div class="sami-row__text">
                <div class="num num--right">1</div>
            </div>
            <div class="sami-row__text">
                <span class="title">Gestionar historias clínicas electrónicas <br/> de forma segura, ágil y actualizada.</span>
            </div>
        </div>

        <!-- ROW 2 -->
        <div class="sami-row sami-row--alt">
            <div class="sami-row__text">
                <div class="num num--left">2</div>
            </div>
            <div class="sami-row__text">
                <span class="title">Acceder a la información del paciente <br/>   en cualquier momento y desde cualquier <br/> dispositivo (PC, tablet o smartphone).</span>
            </div>
            <div class="sami-row__img samrow-2">
                <img src="{{ asset('landing/images/Foto-3.png') }}" alt="">
            </div>
        </div>

        <!-- ROW 3 -->
        <div class="sami-row">
            <div class="sami-row__img">
                <img src="{{ asset('landing/images/Foto-1.png') }}" alt="">
            </div>

            <div class="sami-row__text">
                <div class="num num--right">3</div>
            </div>
            <div class="sami-row__text">
                <span class="title">Cumplir con regulaciones de privacidad <br/>  y estándares de seguridad internacional.</span>
            </div>
        </div>

    </div>
</section>

<!-- ¿A QUIÉN ESTÁ DIRIGIDA? -->
<section class="audience" id="como">
    <div class="container">

        <div class="audience__title">¿A quién está dirigida? </div>

        <p class="audience__subtitle">
            SAMI ha sido desarrollado para atender las necesidades de:
        </p>

        <div class="audience__grid">
            <article class="audience__card">
                <img src="{{ asset('landing/images/Foto-4.png') }}" alt="">
                <h4>Profesionales de la<br>salud independientes</h4>
            </article>

            <article class="audience__card">
                <img src="{{ asset('landing/images/Foto-5.png') }}" alt="">
                <h4>Clínicas y centros médicos<br>de todos los tamaños</h4>
            </article>

            <article class="audience__card">
                <img src="{{ asset('landing/images/Foto-6.png') }}" alt="">
                <h4>Hospitales y redes de<br>salud corporativas</h4>
            </article>
        </div>

    </div>
</section>

<!-- FUNCIONALIDADES CLAVE -->
<section class="features">
    <div class="container">
        <div class="features__title">
            <span class="badge badge--white">Funcionalidades clave</span>
        </div>

        <div class="features__grid">
            <div class="feature">
                <div class="feature__icon">
                    <img src="{{ asset('landing/images/funcion-1.png') }}" alt="">
                </div>
                <h3>Historia clínica<br>digital centralizada</h3>
                <p>Consulta, actualiza y gestiona el historial médico de cada paciente con facilidad y precisión.</p>
            </div>


            <div class="feature">
                <div class="feature__icon">
                    <img src="{{ asset('landing/images/funcion-2.png') }}" alt="">
                </div>
                <h3>Directorio médico<br>inteligente</h3>
                <p>Consulta, y agenda con todos nuestros especialistas.</p>
            </div>
            <!-- OMITIDO Directorio Médico Inteligente -->

            <div class="feature">
                <div class="feature__icon">
                    <img src="{{ asset('landing/images/funcion-3.png') }}" alt="">
                </div>
                <h3>Gestión de citas<br>y personal</h3>
                <p>Coordina horarios, agenda citas en línea y gestiona a todo tu equipo médico y administrativo desde un solo lugar.</p>
            </div>
            <div class="feature">
                <div class="feature__icon">
                    <img src="{{ asset('landing/images/funcion-4.png') }}" alt="">
                </div>
                <h3>Reportes y métricas</h3>
                <p>Accede a estadísticas relevantes, indicadores de atención y reportes personalizados.</p>
            </div>
            <div class="feature">
                <div class="feature__icon">
                    <img src="{{ asset('landing/images/funcion-4.png') }}" alt="">
                </div>
                <h3>Reportes y métricas</h3>
                <p>Accede a estadísticas relevantes, indicadores de atención y reportes personalizados.</p>
            </div>
            <div class="feature">
                <div class="feature__icon">
                    <img src="{{ asset('landing/images/funcion-5.png') }}" alt="">
                </div>
                <h3>Multidispositivo</h3>
                <p>Accede desde donde estés. No requiere instalaciones complejas, solo conexión a internet.</p>
            </div>

        </div>

        {{--}}<div class="features__grid_alt">
            <div></div>
                        <div class="feature">
                <div class="feature__icon">
                    <img src="{{ asset('landing/images/funcion-4.png') }}" alt="">
                </div>
                <h3>Reportes y métricas</h3>
                <p>Accede a estadísticas relevantes, indicadores de atención y reportes personalizados.</p>
            </div>
            <div class="feature">
                <div class="feature__icon">
                    <img src="{{ asset('landing/images/funcion-4.png') }}" alt="">
                </div>
                <h3>Reportes y métricas</h3>
                <p>Accede a estadísticas relevantes, indicadores de atención y reportes personalizados.</p>
            </div>
            <div class="feature">
                <div class="feature__icon">
                    <img src="{{ asset('landing/images/funcion-5.png') }}" alt="">
                </div>
                <h3>Multidispositivo</h3>
                <p>Accede desde donde estés. No requiere instalaciones complejas, solo conexión a internet.</p>
            </div>

            <div></div>
        </div>{{--}}
    </div>
</section>

<!-- PLANES -->
<section class="plans" id="planes">
    <div class="container">

        <div class="plans__title">
            <span class="badge badge--green">Nuestros Planes de Suscripción</span>
        </div>

        <p class="plans__subtitle">
            Elige el plan perfecto para tus necesidades. Todos incluyen soporte técnico y actualizaciones constantes.
        </p>

        <div class="plans__grid">
            @foreach(\App\Models\Package::where('is_active', true)->orderBy('base_price')->get() as $package)
             <article class="plan @if($package->id==4) plan--navy @endif @if($loop->index==1) plan--featured @endif">
                <div class="plan__head">
                    <h3 style=" @if($package->id==4) color:#fff!important; @endif">{{ $package->name }}</h3>
                    @if($package->id <>4)
                    <p class="subtitle">{{ $package->max_users }} {{ $package->max_users == 1 ? 'Usuario' : 'Usuarios' }}</p>
                    @else
                        <p>&nbsp;</p>
                    @endif
                </div>
                <div class="featrues @if($loop->index==0)   plan--cyan  @elseif($loop->index==1) plan--blue @elseif($loop->index==2) plan--green @elseif($loop->index==3) plan--navy @else plan--light @endif">
                    @foreach($package->features ?? [] as $feature)
                    <i class="fas fa-check @if($package->id==3) text__white @else text__green @endif"></i>
                    <span>{{ $feature }}</span>
                    @endforeach
                </div>

                @if($package->id<>4)
                     <div class="plan__price text__cyan">@isset($package->base_price) ${{ number_format($package->base_price,2) }} @else XXX @endif</div>
                     <div class="plan__month_text text__cyan"><span>al mes</span></div>
                     <a href="{{ route('public.register', ['package' => $package->id]) }}" class="btn  @if($loop->index==1) btn--navy @else btn--primary @endif">Suscribirse ahora</a>
                @else
                     <p>&nbsp;</p>
                     <button onclick="openEnterpriseModal()" class="btn btn--primary btn--full">Contactar ventas</button>
                     <p>&nbsp;</p>
                @endif

            </article>
            @endforeach
        </div>
    </div>
</section>

<!-- POR QUÉ -->
<section class="why" id="porque">
    <div class="container">
        <div class="why__title">
            <span class="badge badge--blue">¿Por qué elegir SAMI?</span>
        </div>

        <p class="why__lead">
            Porque creemos que la tecnología debe estar al servicio de la salud.
        </p>

        <p class="why__lead">
            Con SAMI, tendrás:
        </p>

        <div class="why__bars">
            <div class="bar">Mayor control y organización</div>
            <div class="bar">Datos seguros y siempre disponibles</div>
            <div class="bar">Menos papeleo = más tiempo para tus pacientes</div>
            <div class="bar">Una mejor experiencia para tus pacientes y tu equipo</div>
        </div>

        <div class="why__cta">
            <p>¿Listo para dar el siguiente paso en la transformación digital de tu práctica médica?</p>
            <a href="{{route('public.register')}}" class="btn btn--green">Regístrate aquí</a>
            <small>Forma parte de la revolución SAMI</small>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer class="footer">
    <div class="container footer__inner">
        <div class="footer__contact">
            <span>Contáctanos:</span>
            <a href="mailto:business@meditecpty.com">business@meditecpty.com</a>
            <span class="sep">|</span>
            <a href="tel:+5071234567">+507 123-4567</a>
        </div>
    </div>
</footer>

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
<script>
    //sidebar - responsive menu 
    const sidebar = document.querySelector('.sidebar')
    function showSidebar(){
        sidebar.style.display = 'flex'
    }
    function hideSidebar(){
        sidebar.style.display = 'none'
    }
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
</body>
</html>
