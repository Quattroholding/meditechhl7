<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="facebook-domain-verification" content="v6w57zlpwwn256rbqabs1ws6702zpk" />
    <title>Soluciones Meditec</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --azul-oscuro: #0a2342;
            --azul: #0f4471;
            --celeste: #00b0ff;
            --verde: #2bb673;
            --gris-fondo: #f9f9f9;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Arial', sans-serif; line-height: 1.6; color: #333; }

        /* HEADER */
        header {
            background: linear-gradient(135deg, var(--azul-oscuro), var(--azul));
            color: #fff; padding: 15px 40px;
        }
        nav { display: flex; justify-content: space-between; align-items: center; }
        nav .logo { font-size: 1.4rem; font-weight: bold; color: var(--verde); }
        nav ul { list-style: none; display: flex; gap: 20px; }
        nav ul li a { color: white; font-weight: bold; transition: 0.3s; }
        nav ul li a:hover { color: var(--celeste); }
        .search input {
            border: none; outline: none; padding: 5px 12px;
            border-radius: 20px; font-size: 0.9rem;
        }

        /* HERO */
        .hero {
            background: linear-gradient(135deg, var(--azul-oscuro), var(--azul));
            color: white; text-align: left; padding: 80px 40px;
        }
        .hero h1 { font-size: 2.5rem; margin-bottom: 15px; color: var(--verde); }
        .hero p { font-size: 1.2rem; }

        /* SECCIONES */
        section { padding: 60px 40px; max-width: 1100px; margin: auto; }
        section h2 {
            margin-bottom: 25px; color: var(--azul); font-size: 1.8rem;
        }

        /* FEATURES */
        .features { display: grid; gap: 20px; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); }
        .card {
            background: var(--gris-fondo); padding: 25px; border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1); transition: 0.3s;
        }
        .card:hover { transform: translateY(-5px); }
        .card h3 { color: var(--verde); margin-bottom: 10px; }
        .card i { font-size: 2rem; color: var(--celeste); margin-bottom: 15px; display: block; }

        /* AUDIENCIA */
        .audience { display: grid; gap: 20px; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); text-align: center; }
        .audience .card { font-weight: bold; }

        /* CTA */
        .cta {
            text-align: center; background: #f0f7ff;
            padding: 60px 20px; border-radius: 12px;
        }
        .cta h2 { margin-bottom: 20px; }
        .cta p { font-size: 1.1rem; margin-bottom: 25px; }
        .cta button {
            background: var(--celeste); color: white;
            padding: 14px 30px; border: none;
            border-radius: 25px; font-size: 1rem;
            cursor: pointer; transition: 0.3s;
        }
        .cta button:hover { background: var(--verde); }

        /* FOOTER */
        footer {
            background: var(--azul); color: white;
            text-align: center; padding: 20px; margin-top: 40px;
        }
        footer a { color: var(--celeste); }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            nav ul { flex-direction: column; gap: 10px; }
        }
    </style>
</head>
<body>
<!-- HEADER -->
<header>
    <nav>
        <div class="logo">Soluciones Meditec</div>
        <ul>
            <li><a href="#">Inicio</a></li>
            <li><a href="#">Quiénes Somos</a></li>
            <li><a href="#">Cómo Funciona</a></li>
            <li><a href="#">Por Qué Elegirnos</a></li>
        </ul>
        <div class="search">
            <input type="text" placeholder="Buscar...">
        </div>
    </nav>
</header>

<!-- HERO -->
<section class="hero">
    <h1>Innovación tecnológica al servicio de la salud.</h1>
    <p>Soluciones Meditec, mucho más que un software médico.</p>
</section>

<!-- QUE ES -->
<section>
    <h2>¿Qué es Soluciones Meditec?</h2>
    <p>
        Es una plataforma integral diseñada para optimizar la forma en que pacientes, médicos, clínicas y hospitales gestionan la información y se conectan entre sí.
    </p>
    <div class="features">
        <div class="card">
            <i class="fa-solid fa-notes-medical"></i>
            <h3>1</h3>
            <p>Gestionar historias clínicas electrónicas de forma segura, ágil y actualizada.</p>
        </div>
        <div class="card">
            <i class="fa-solid fa-tablet-screen-button"></i>
            <h3>2</h3>
            <p>Acceder a la información del paciente en cualquier momento y desde cualquier dispositivo.</p>
        </div>
        <div class="card">
            <i class="fa-solid fa-shield-halved"></i>
            <h3>3</h3>
            <p>Cumplir con regulaciones de privacidad y estándares de seguridad internacional.</p>
        </div>
    </div>
</section>

<!-- AUDIENCIA -->
<section>
    <h2>¿A quién está dirigida?</h2>
    <div class="audience">
        <div class="card"><i class="fa-solid fa-user-doctor"></i> Profesionales de la salud independientes</div>
        <div class="card"><i class="fa-solid fa-hospital"></i> Clínicas y centros médicos</div>
        <div class="card"><i class="fa-solid fa-stethoscope"></i> Hospitales y redes de salud corporativas</div>
    </div>
</section>

<!-- FUNCIONALIDADES -->
<section>
    <h2>Funcionalidades clave</h2>
    <div class="features">
        <div class="card"><i class="fa-solid fa-file-medical"></i> Historia clínica digital centralizada</div>
        <div class="card"><i class="fa-solid fa-users"></i> Directorio médico inteligente</div>
        <div class="card"><i class="fa-solid fa-calendar-check"></i> Gestión de citas y personal</div>
        <div class="card"><i class="fa-solid fa-chart-line"></i> Reportes y métricas</div>
        <div class="card"><i class="fa-solid fa-mobile-screen"></i> Multidispositivo</div>
    </div>
</section>

<!-- CTA -->
<section class="cta">
    <h2>¿Por qué elegir Soluciones Meditec?</h2>
    <p>
        ✔ Mayor control y organización <br>
        ✔ Datos seguros y siempre disponibles <br>
        ✔ Menos papeleo = más tiempo para tus pacientes <br>
        ✔ Una mejor experiencia para tus pacientes y tu equipo
    </p>
    <button>Regístrate aquí</button>
</section>

<!-- FOOTER -->
<footer>
    <p>Contáctanos: <a href="mailto:napellido@solucionesmeditec.com">napellido@solucionesmeditec.com</a> | +507 123-4567</p>
</footer>
</body>
</html>
