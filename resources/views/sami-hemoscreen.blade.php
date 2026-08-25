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
    <link rel="icon" href="{{url('images/iconoSAMI.ico')}}" type="image/x-icon">
    <title>HemoScreen - Analizador CBC Portátil | SAMI Panamá</title>
    <meta name="description" content="HemoScreen: análisis completo de sangre (CBC) en minutos. Agente autorizado en Panamá. Resultados precisos, portátil, fácil de usar.">

    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hover-lift {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .hover-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .feature-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.9);
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .floating {
            animation: float 6s ease-in-out infinite;
        }
    </style>
</head>
<body class="bg-gray-50">
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MP5P532M"
                  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
    <!-- Navigation -->
    <nav class="bg-white shadow-sm fixed w-full z-50 top-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="{{route('welcome')}}" class="brand">
                        <img src="{{url('images/logo1.png')}}" alt="SAMI" class="brand__logo">
                    </a>
                    <span class="ml-2 text-gray-600">| HemoScreen</span>
                </div>
                <!-- Desktop Menu - Always visible on medium+ screens -->
                <div class="flex items-center gap-8">
                    <a href="#caracteristicas" class="text-gray-800 hover:text-purple-600 transition font-semibold text-sm whitespace-nowrap">Características</a>
                    <a href="#beneficios" class="text-gray-800 hover:text-purple-600 transition font-semibold text-sm whitespace-nowrap">Beneficios</a>
                    <a href="#especificaciones" class="text-gray-800 hover:text-purple-600 transition font-semibold text-sm whitespace-nowrap">Especificaciones</a>
                    <a href="{{ route('login') }}" class="text-gray-800 hover:text-purple-600 transition flex items-center font-semibold text-sm whitespace-nowrap">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Iniciar Sesión
                    </a>
                    <a href="#contacto" class="bg-purple-600 text-white px-6 py-2.5 rounded-full hover:bg-purple-700 transition shadow-md font-semibold text-sm whitespace-nowrap">
                        Solicitar Demo
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-32 pb-20 gradient-bg text-white overflow-hidden min-h-screen flex items-center">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
            <div class="grid md:grid-cols-2 gap-16 items-center">
                <!-- Left Column: Título y Copy -->
                <div class="text-left">
                    <h1 class="text-5xl md:text-6xl lg:text-7xl font-bold mb-6 leading-tight">
                        Análisis Completo de Sangre en
                        <span class="text-yellow-300">Minutos</span>
                    </h1>
                    <p class="text-xl md:text-2xl mb-8 text-white leading-relaxed font-light">
                        HemoScreen es el analizador CBC portátil de última generación que revoluciona el diagnóstico médico.
                        Resultados precisos, rápidos y confiables en la palma de tu mano.
                    </p>
                </div>

                <!-- Right Column: Botones y Badge -->
                <div class="space-y-8">
                    <!-- Botones -->
                    <div class="flex flex-col gap-4">

                    </div>
                    <p>&nbsp;</p>
                    <!-- Badge y Rating -->
                    <div class="bg-white rounded-2xl p-6 shadow-2xl">
                        <div class="flex items-center gap-4 mb-3 justify-center">
                            <div>
                                <p class="text-sm font-bold text-purple-700 text-center">Agente Autorizado en Panamá</p>
                            </div>
                        </div>
                        <div class="flex text-yellow-500 text-lg justify-center gap-1">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p class="text-center text-gray-700 text-sm mt-2 font-semibold">Calidad certificada PixCell Medical</p>
                    </div>

                    <!-- Imagen/Ícono flotante (opcional) -->
                    <div class="relative hidden lg:block">
                        <div class="floating">
                            <img src="{{url('images/hemoscreen-analyser.png')}}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Trust Bar -->
    <section class="bg-white py-8 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div>
                    <div class="text-3xl font-bold text-purple-600">5 min</div>
                    <div class="text-gray-600 text-sm">Resultados CBC</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-purple-600">13</div>
                    <div class="text-gray-600 text-sm">Parámetros Medidos</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-purple-600">99.8%</div>
                    <div class="text-gray-600 text-sm">Precisión</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-purple-600">100%</div>
                    <div class="text-gray-600 text-sm">Portátil</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Características Principales -->
    <section id="caracteristicas" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-4">¿Por qué HemoScreen?</h2>
                <p class="text-xl text-gray-600">La revolución en análisis de sangre portátil</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="feature-card rounded-2xl p-8 hover-lift">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-bolt text-3xl text-purple-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4">Resultados Rápidos</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Obtén resultados completos de CBC en menos de 5 minutos. Sin esperas, sin laboratorios externos.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="feature-card rounded-2xl p-8 hover-lift">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-check-circle text-3xl text-blue-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4">Precisión Clínica</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Tecnología validada clínicamente con 99.8% de precisión. Resultados confiables para diagnósticos certeros.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="feature-card rounded-2xl p-8 hover-lift">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-hand-holding-medical text-3xl text-green-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4">Fácil de Usar</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Interfaz intuitiva que requiere mínima capacitación. Cualquier profesional de salud puede operarlo.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="feature-card rounded-2xl p-8 hover-lift">
                    <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-wifi text-3xl text-yellow-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4">Conectividad Total</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Integración automática con SAMI. Los resultados se envían directamente a tu sistema de gestión.
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="feature-card rounded-2xl p-8 hover-lift">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-heartbeat text-3xl text-red-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4">Sin Mantenimiento</h3>
                    <p class="text-gray-600 leading-relaxed">
                        No requiere calibración diaria ni mantenimiento complejo. Listo para usar cuando lo necesites.
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="feature-card rounded-2xl p-8 hover-lift">
                    <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-suitcase text-3xl text-indigo-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4">100% Portátil</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Compacto y ligero. Llévalo a donde lo necesites: consultorio, domicilio, o emergencias.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CBC Parameters -->
    <section id="especificaciones" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-4">13 Parámetros CBC Completos</h2>
                <p class="text-xl text-gray-600">Todo lo que necesitas en un solo análisis</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @php
                $parameters = [
                    ['name' => 'Hemoglobin (HGB)', 'icon' => 'fa-droplet', 'color' => 'red'],
                    ['name' => 'Glóbulos Rojos (RBC)', 'icon' => 'fa-circle', 'color' => 'red'],
                    ['name' => 'Glóbulos Blancos (WBC)', 'icon' => 'fa-shield-virus', 'color' => 'blue'],
                    ['name' => 'Hematocrito (HCT)', 'icon' => 'fa-percentage', 'color' => 'purple'],
                    ['name' => 'Volumen Corpuscular Medio (MCV)', 'icon' => 'fa-arrows-alt', 'color' => 'green'],
                    ['name' => 'Hemoglobina Corpuscular Media (MCH)', 'icon' => 'fa-weight', 'color' => 'yellow'],
                    ['name' => 'Concentración de Hemoglobina (MCHC)', 'icon' => 'fa-chart-line', 'color' => 'indigo'],
                    ['name' => 'Plaquetas (PLT)', 'icon' => 'fa-puzzle-piece', 'color' => 'pink'],
                    ['name' => 'Neutrófilos', 'icon' => 'fa-bacteria', 'color' => 'cyan'],
                    ['name' => 'Linfocitos', 'icon' => 'fa-virus', 'color' => 'teal'],
                    ['name' => 'Monocitos', 'icon' => 'fa-cell', 'color' => 'orange'],
                    ['name' => 'Eosinófilos', 'icon' => 'fa-allergies', 'color' => 'lime'],
                    ['name' => 'Basófilos', 'icon' => 'fa-microscope', 'color' => 'violet']
                ];
                @endphp

                @foreach($parameters as $param)
                <div class="flex items-center p-4 bg-gray-50 rounded-xl hover:shadow-md transition">
                    <div class="w-12 h-12 bg-{{ $param['color'] }}-100 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                        <i class="fas {{ $param['icon'] }} text-{{ $param['color'] }}-600"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-800">{{ $param['name'] }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Beneficios -->
    <section id="beneficios" class="py-20 bg-linear-to-br from-purple-50 to-blue-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-4xl font-bold mb-6">Transforma tu Práctica Médica</h2>
                    <div class="space-y-6">
                        <div class="flex items-start">
                            <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center mr-4 flex-shrink-0 mt-1">
                                <i class="fas fa-check text-white"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold mb-2">Diagnóstico Inmediato</h3>
                                <p class="text-gray-600">Toma decisiones clínicas en el momento de la consulta, sin esperar días por resultados.</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center mr-4 flex-shrink-0 mt-1">
                                <i class="fas fa-check text-white"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold mb-2">Mayor Satisfacción del Paciente</h3>
                                <p class="text-gray-600">Ofrece un servicio completo en una sola visita. Tus pacientes lo agradecerán.</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center mr-4 flex-shrink-0 mt-1">
                                <i class="fas fa-check text-white"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold mb-2">Aumenta tus Ingresos</h3>
                                <p class="text-gray-600">Agrega un servicio de alto valor a tu consultorio. ROI en meses, no años.</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center mr-4 flex-shrink-0 mt-1">
                                <i class="fas fa-check text-white"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold mb-2">Integración con SAMI</h3>
                                <p class="text-gray-600">Los resultados se registran automáticamente en el expediente del paciente.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl p-8 shadow-2xl">
                    <div class="mb-6">
                        <div class="text-5xl font-bold text-purple-600 mb-2"></div>
                        <div class="text-gray-600">Inversión que se paga sola</div>
                    </div>
                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between items-center pb-3 border-b">
                            <span class="text-gray-600">Dispositivo HemoScreen</span>
                            <span class="font-semibold">Incluido</span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b">
                            <span class="text-gray-600">Integración SAMI</span>
                            <span class="font-semibold">Incluido</span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b">
                            <span class="text-gray-600">Capacitación</span>
                            <span class="font-semibold">Incluido</span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b">
                            <span class="text-gray-600">Soporte Técnico</span>
                            <span class="font-semibold">Incluido</span>
                        </div>
                    </div>
                    <a href="#contacto" class="block w-full bg-purple-600 text-white text-center py-4 rounded-full font-semibold hover:bg-purple-700 transition">
                        Solicitar Cotización
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Contacto -->
    <section id="contacto" class="py-20 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold mb-4">¿Listo para Revolucionar tu Consultorio?</h2>
                <p class="text-xl text-gray-600">Solicita una demostración sin compromiso</p>
            </div>

            <div class="bg-linear-to-br from-purple-50 to-blue-50 rounded-3xl p-8 md:p-12">
                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                        <strong>Por favor corrige los siguientes errores:</strong>
                        <ul class="mt-2 list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
                        <strong>✅ {{ session('success') }}</strong>
                    </div>
                @endif

                <form method="POST" action="{{ route('hemoscreen.demo-request') }}" class="space-y-6">
                    @csrf
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Nombre Completo</label>
                            <input type="text" name="name" required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-purple-600 focus:border-transparent" placeholder="Dr. Juan Pérez" value="{{ old('name') }}">
                            @error('name')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                            <input type="email" name="email" required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-purple-600 focus:border-transparent" placeholder="doctor@ejemplo.com" value="{{ old('email') }}">
                            @error('email')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Teléfono</label>
                            <input type="tel" name="phone" required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-purple-600 focus:border-transparent" placeholder="+507 6XXX-XXXX" value="{{ old('phone') }}">
                            @error('phone')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Especialidad</label>
                            <select name="specialty" required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                                <option value="">Seleccione</option>
                                @foreach(\App\Models\MedicalSpeciality::get() as $ms)
                                    <option value="{{$ms->name}}" {{ old('specialty') == $ms->name ? 'selected' : '' }}>{{ $ms->name}}</option>
                                @endforeach
                            </select>
                            @error('specialty')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Mensaje</label>
                        <textarea rows="4" name="message" required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-purple-600 focus:border-transparent" placeholder="Cuéntanos sobre tu consultorio y cómo HemoScreen puede ayudarte...">{{ old('message') }}</textarea>
                        @error('message')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="w-full bg-purple-600 text-white py-4 rounded-full font-semibold hover:bg-purple-700 transition shadow-lg">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Solicitar Demostración Gratuita
                    </button>

                    <p class="text-center text-sm text-gray-600">
                        También puedes contactarnos directamente:
                        <a href="tel: +507 831 6100" class="text-purple-600 font-semibold">+ +507 831 6100</a> |
                        <a href="mailto:business@meditecpty.com" class="text-purple-600 font-semibold">business@meditecpty.com</a>
                    </p>
                </form>
            </div>
        </div>
    </section>

    <!-- Sección para Usuarios Existentes -->
    <section class="py-16 bg-linear-to-r from-purple-600 to-indigo-600 text-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="mb-8">
                <i class="fas fa-user-check text-6xl mb-4 opacity-90"></i>
                <h2 class="text-3xl md:text-4xl font-bold mb-4 text-white">¿Ya tienes HemoScreen?</h2>
                <p class="text-xl text-white mb-8 font-light">
                    Accede a tu dashboard para ver tus resultados de análisis CBC
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-6 max-w-4xl mx-auto">
                <a href="{{ route('login') }}" class="bg-white text-purple-700 px-8 py-6 rounded-2xl font-bold hover:bg-purple-50 transition shadow-2xl flex flex-col items-center justify-center transform hover:scale-105">
                    <i class="fas fa-sign-in-alt text-4xl mb-3 text-purple-600"></i>
                    <span class="text-xl">Iniciar Sesión</span>
                    <span class="text-sm text-gray-600 mt-1 font-normal">Accede a tu cuenta</span>
                </a>

                <a href="{{ route('hemoscreen.dashboard') }}" class="bg-linear-to-br from-yellow-400 to-orange-500 text-gray-900 px-8 py-6 rounded-2xl font-bold hover:from-yellow-500 hover:to-orange-600 transition shadow-2xl flex flex-col items-center justify-center transform hover:scale-105">
                    <i class="fas fa-chart-line text-4xl mb-3 text-gray-900"></i>
                    <span class="text-xl">Ver Dashboard</span>
                    <span class="text-sm text-gray-800 mt-1 font-normal">Resultados y tendencias</span>
                </a>

                <a href="/storage/HemoScreen Gateway Setup 1.0.0.exe" download class="bg-linear-to-br from-green-500 to-teal-600 text-white px-8 py-6 rounded-2xl font-bold hover:from-green-600 hover:to-teal-700 transition shadow-2xl flex flex-col items-center justify-center transform hover:scale-105">
                    <i class="fas fa-download text-4xl mb-3 text-white"></i>
                    <span class="text-xl">Descargar Gateway</span>
                    <span class="text-sm text-gray-100 mt-1 font-normal">v1.0.0</span>
                </a>

            </div>
            <p style="margin: 20px 0;"><a href="{{route('hemoscreen.gateway-config',array('hemoscreen'))}}"  target="_blank">Ver guia de instalación</a> </p>
            <div class="mt-8 text-white text-sm">
                <p>¿Olvidaste tu contraseña? <a href="{{ route('forgot-password') }}" class="text-yellow-300 font-bold underline hover:no-underline">Recupérala aquí</a></p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-3 gap-8 mb-8">
                <div>
                    <h3 class="text-2xl font-bold mb-4 gradient-text">SAMI</h3>
                    <p class="text-gray-400 mb-4">
                        Agente autorizado de HemoScreen en Panamá. Transformando la atención médica con tecnología de vanguardia.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-purple-600 transition">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-purple-600 transition">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-purple-600 transition">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>

                <div>
                    <h4 class="text-lg font-semibold mb-4">Enlaces Rápidos</h4>
                    <ul class="space-y-2">
                        <li><a href="#caracteristicas" class="text-gray-400 hover:text-white transition">Características</a></li>
                        <li><a href="#beneficios" class="text-gray-400 hover:text-white transition">Beneficios</a></li>
                        <li><a href="#especificaciones" class="text-gray-400 hover:text-white transition">Especificaciones</a></li>
                        <li><a href="/login" class="text-gray-400 hover:text-white transition">Portal SAMI</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-lg font-semibold mb-4">Contacto</h4>
                    <ul class="space-y-3 text-gray-400">
                        <li class="flex items-center">
                            <i class="fas fa-map-marker-alt mr-3"></i>
                            Ciudad de Panamá, Panamá
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone mr-3"></i>
                            +507 831 6100
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-3"></i>
                            business@meditecpty.com
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} SAMI - Sistema de Administración Médica Integral. Todos los derechos reservados.</p>
                <p class="mt-2 text-sm">HemoScreen es una marca registrada de PixCell Medical. SAMI es agente autorizado en Panamá.</p>
            </div>
        </div>
    </footer>

    <!-- Smooth Scroll -->
    <script>
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
    </script>
</body>
</html>
