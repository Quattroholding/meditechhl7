<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Soluciones Meditec</title>
    <link rel="stylesheet" href="{{url('styles/welcome.css')}}">
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
            <a href="#sobre">Sobre Nosotros</a>
            <a href="#mision">Misión</a>
            <a href="#vision">Visión</a>
            <a href="#contacto">Contacto</a>
        </nav>

        <a class="btn btn--green" href="#contacto">Contáctanos</a>

        <button class="burger" aria-label="Abrir menú" onclick="document.body.classList.toggle('menu-open')">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>

<!-- HERO -->
<main id="home" class="hero">
    <div class="hero__bg">
        <!-- placeholder hero -->
        <img src="https://placehold.co/1400x800?text=HERO+IMAGE" alt="Hero">
    </div>

    <div class="container hero__content">
        <div class="hero__line"></div>

        <div class="hero__card">
            <h1>Conócenos</h1>
            <p>
                Somos una empresa de tecnología en salud que integra plataformas digitales diseñadas
                para optimizar la gestión médica y mejorar la experiencia del paciente.
            </p>

            <a href="#contacto" class="btn btn--blue">Contáctanos</a>
        </div>
    </div>

    <!-- HEXAGON overlay decoration (simulado con gradientes) -->
    <div class="hero__hex"></div>
</main>

<!-- BLUE BLOCK -->
<section class="blueblock">
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
<section class="bigtext" id="sobre">
    <div class="container">
        <h2>
            Soluciones Meditec conecta a profesionales y pacientes a través de tecnología confiable,
            contribuyendo a una atención médica más ágil, organizada y centrada en las personas.
        </h2>
    </div>
</section>

<!-- MISION / VISION -->
<section class="mv">
    <div class="container mv__grid">

        <div class="mv__left">
            <div class="mv__block" id="mision">
                <span class="mv__tag mv__tag--dark">Misión</span>
                <p>
                    Brindar soluciones tecnológicas integrales en salud que conecten a instituciones, médicos y pacientes,
                    facilitando una gestión de salud eficiente, segura y accesible, y contribuyendo a una atención más organizada y humana.
                </p>
            </div>

            <div class="mv__block" id="vision">
                <span class="mv__tag mv__tag--light">Visión</span>
                <p>
                    Ser una empresa líder en innovación tecnológica en salud en la región, reconocida por transformar la manera en que
                    se gestionan y viven los servicios médicos, impulsando un ecosistema digital que mejore la calidad de la atención y el bienestar de las personas.
                </p>
            </div>

            <div class="contact-row" id="contacto">
                <a href="#contacto" class="btn btn--green btn--wide">Contáctanos</a>

                <a class="whatsapp" href="https://wa.me/5071234567" target="_blank" rel="noopener">
                    <span class="wa-icon">🟢</span>
                </a>

                <a class="phone" href="tel:+5071234567">+507-123-4567</a>
            </div>
        </div>

        <aside class="mv__right">
            <img src="https://placehold.co/520x560?text=IMAGE" alt="Imagen sección">
        </aside>

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
</script>
</body>
</html>
