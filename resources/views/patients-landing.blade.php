<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Agenda tu cita médica fácil y rápido por WhatsApp. Atención inmediata con los mejores especialistas.">
    <meta name="keywords" content="citas médicas, agendar cita, whatsapp médico, telemedicina, doctores">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="Agenda tu Cita Médica por WhatsApp">
    <meta property="og:description" content="Obtén atención médica rápida y profesional. Agenda tu cita en segundos por WhatsApp.">
    <meta property="og:image" content="{{ asset('images/og-image.jpg') }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:title" content="Agenda tu Cita Médica por WhatsApp">
    <meta property="twitter:description" content="Obtén atención médica rápida y profesional. Agenda tu cita en segundos por WhatsApp.">

    <title>Agenda tu Cita Médica por WhatsApp | Soluciones Meditec</title>
    <link rel="icon" href="{{ url('images/favicon.ico') }}" type="image/x-icon">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Custom animations */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(37, 211, 102, 0.4); }
            50% { box-shadow: 0 0 40px rgba(37, 211, 102, 0.8); }
        }

        @keyframes slide-up {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-float {
            animation: float 3s ease-in-out infinite;
        }

        .animate-pulse-glow {
            animation: pulse-glow 2s ease-in-out infinite;
        }

        .animate-slide-up {
            animation: slide-up 0.6s ease-out forwards;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .gradient-bg-green {
            background: linear-gradient(135deg, #25d366 0%, #128c7e 100%);
        }

        /* WhatsApp green theme */
        .whatsapp-btn {
            background: linear-gradient(135deg, #25d366 0%, #128c7e 100%);
            transition: all 0.3s ease;
        }

        .whatsapp-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(37, 211, 102, 0.3);
        }

        /* Glassmorphism effect */
        .glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        /* Medical Directory Section */


        .specialty-badge {
            display: inline-block;
            padding: 6px 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .whatsapp-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 24px;
            background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 14px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-top: 10px;
        }

        .whatsapp-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 20px rgba(37, 211, 102, 0.4);
            color: white;
            text-decoration: none;
        }

        .whatsapp-icon {
            width: 24px;
            height: 24px;
        }
    </style>
</head>
<body class="antialiased">

    <!-- Hero Section -->
    <section class="relative min-h-screen flex items-center justify-center overflow-hidden gradient-bg">
        <!-- Animated background elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute w-96 h-96 bg-white/10 rounded-full blur-3xl -top-48 -left-48 animate-float"></div>
            <div class="absolute w-96 h-96 bg-white/10 rounded-full blur-3xl -bottom-48 -right-48 animate-float" style="animation-delay: 1s;"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center">
            <!-- Logo -->
            <div class="mb-8 animate-slide-up">
                <img src="{{ asset('images/logoSAMI.jpg') }}" alt="SAMI por Soluciones Meditec" class="h-16 md:h-20 mx-auto">
            </div>

            <!-- Main Headline -->
            <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold text-white mb-6 animate-slide-up" style="animation-delay: 0.1s;">
                Agenda tu Cita Médica<br>
                <span class="text-green-300">en Segundos</span>
            </h1>

            <!-- Subheadline -->
            <p class="text-xl md:text-2xl text-white/90 mb-12 max-w-3xl mx-auto animate-slide-up" style="animation-delay: 0.2s;">
                Atención médica profesional y rápida.<br class="hidden sm:inline">
                Conecta con los mejores especialistas por WhatsApp.
            </p>

            <!-- CTA Button -->
            <div class="animate-slide-up" style="animation-delay: 0.3s;">
                <a href="https://wa.me/{{ $whatsappNumber }}?text={{ $whatsappMessage }}"
                   target="_blank"
                   class="inline-flex items-center gap-3 px-8 py-5 text-xl md:text-2xl font-bold text-white rounded-full whatsapp-btn animate-pulse-glow">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                    </svg>
                    Agendar Ahora por WhatsApp
                </a>
            </div>

            <!-- Trust Indicators -->
            <div class="mt-16 flex flex-wrap justify-center gap-8 text-white/80 animate-slide-up" style="animation-delay: 0.4s;">
                <div class="flex items-center gap-2">
                    <svg class="w-6 h-6 text-green-300" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="font-semibold">Atención 24/7</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-6 h-6 text-green-300" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="font-semibold">Respuesta Inmediata</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-6 h-6 text-green-300" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="font-semibold">Especialistas Certificados</span>
                </div>
            </div>
        </div>

        <!-- Scroll indicator -->
        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 animate-bounce">
            <svg class="w-6 h-6 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
            </svg>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-5xl font-bold text-gray-900 mb-4">
                    ¿Cómo Funciona?
                </h2>
                <p class="text-xl text-gray-600">
                    Agendar tu cita médica nunca fue tan fácil
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 lg:gap-12">
                <!-- Step 1 -->
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full gradient-bg-green text-white text-3xl font-bold mb-6">
                        1
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">
                        Envía un Mensaje
                    </h3>
                    <p class="text-lg text-gray-600">
                        Haz clic en el botón de WhatsApp y cuéntanos qué especialista necesitas
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full gradient-bg-green text-white text-3xl font-bold mb-6">
                        2
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">
                        Elige tu Horario
                    </h3>
                    <p class="text-lg text-gray-600">
                        Nuestro asistente te mostrará horarios disponibles al instante
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full gradient-bg-green text-white text-3xl font-bold mb-6">
                        3
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">
                        ¡Listo!
                    </h3>
                    <p class="text-lg text-gray-600">
                        Recibirás confirmación y recordatorios de tu cita por WhatsApp
                    </p>
                </div>
            </div>

            <!-- CTA -->
            <div class="text-center mt-16">
                <a href="https://wa.me/{{ $whatsappNumber }}?text={{ $whatsappMessage }}"
                   target="_blank"
                   class="inline-flex items-center gap-3 px-8 py-4 text-lg font-bold text-white rounded-full whatsapp-btn">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                    </svg>
                    Comienza Ahora
                </a>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-5xl font-bold text-gray-900 mb-4">
                    ¿Por Qué Agendar con Nosotros?
                </h2>
                <p class="text-xl text-gray-600">
                    La forma más moderna y conveniente de cuidar tu salud
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Benefit 1 -->
                <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-shadow">
                    <div class="w-16 h-16 rounded-full bg-purple-100 flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">
                        Sin Esperas
                    </h3>
                    <p class="text-gray-600">
                        Olvídate de las llamadas interminables. Agenda en minutos desde tu móvil.
                    </p>
                </div>

                <!-- Benefit 2 -->
                <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-shadow">
                    <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">
                        100% Seguro
                    </h3>
                    <p class="text-gray-600">
                        Tu información está protegida con los más altos estándares de seguridad.
                    </p>
                </div>

                <!-- Benefit 3 -->
                <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-shadow">
                    <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">
                        Múltiples Especialidades
                    </h3>
                    <p class="text-gray-600">
                        Accede a una amplia red de especialistas médicos certificados.
                    </p>
                </div>

                <!-- Benefit 4 -->
                <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-shadow">
                    <div class="w-16 h-16 rounded-full bg-orange-100 flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">
                        Recordatorios Automáticos
                    </h3>
                    <p class="text-gray-600">
                        Recibe notificaciones y nunca olvides tu cita médica.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Medical Directory Section -->
    <section class="py-20 bg-gradient-to-br from-gray-50 to-purple-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-5xl font-bold text-gray-900 mb-4">
                    Nuestros Médicos Especialistas
                </h2>
                <p class="text-xl text-gray-600">
                    Encuentra al especialista que necesitas y agenda tu cita al instante
                </p>
            </div>

            <!-- Specialty Filter -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-12">
                <label for="specialty-select" class="text-lg font-semibold text-gray-700">
                    Filtrar por especialidad:
                </label>
                <select id="specialty-select" class="px-6 py-3 text-base border-2 border-purple-300 rounded-full bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all min-w-[280px]">
                    <option value="">Todas las especialidades</option>
                    @foreach($specialties as $specialty)
                        <option value="{{ $specialty->id }}">{{ $specialty->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Practitioners Grid -->
            <div id="practitioners-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-8">
                @foreach($practitioners as $practitioner)
                    <div class="practitioner-card bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden transform hover:-translate-y-2"
                         data-specialties="{{ $practitioner->specialties->pluck('id')->join(',') }}">
                        <!-- Avatar -->
                        <div class="relative h-48 bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center">
                            @if($practitioner->avatar())
                                <img src="{{ asset('storage/' . $practitioner->avatar()->path) }}"
                                     alt="{{ $practitioner->name }}"
                                     class="w-32 h-32 rounded-full border-4 border-white shadow-xl object-cover">
                            @else
                                <div class="w-32 h-32 rounded-full border-4 border-white shadow-xl bg-white flex items-center justify-center">
                                    <svg class="w-20 h-20 text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <!-- Info -->
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-3 text-center">
                                {{ $practitioner->name }}
                            </h3>

                            <!-- Specialties -->
                            <div class="flex flex-wrap gap-2 justify-center mb-4">
                                @foreach($practitioner->specialties as $specialty)
                                    <span class="specialty-badge">
                                        {{ $specialty->name }}
                                    </span>
                                @endforeach
                            </div>

                            <!-- WhatsApp Button -->
                            <a href="https://wa.me/{{ $whatsappNumber }}?text={{ urlencode('Hola quisiera una cita con ' . $practitioner->name) }}"
                               target="_blank"
                               class="flex items-center justify-center gap-2 w-full px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-bold rounded-full transition-all duration-300 transform hover:scale-105 shadow-md hover:shadow-lg">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                </svg>
                                Agendar por WhatsApp
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- No Results Message -->
            <div id="no-results" class="hidden text-center py-12">
                <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-xl text-gray-500 font-semibold">No se encontraron médicos con esta especialidad</p>
                <p class="text-gray-400 mt-2">Intenta seleccionar otra especialidad</p>
            </div>
        </div>
    </section>

    <!-- Specialties Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-5xl font-bold text-gray-900 mb-4">
                    Especialidades Disponibles
                </h2>
                <p class="text-xl text-gray-600">
                    Encuentra el especialista que necesitas
                </p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-12">
                <div class="bg-gray-50 rounded-xl p-6 text-center hover:bg-purple-50 transition-colors">
                    <div class="text-3xl mb-3">🩺</div>
                    <p class="font-semibold text-gray-900">Medicina General</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-6 text-center hover:bg-purple-50 transition-colors">
                    <div class="text-3xl mb-3">❤️</div>
                    <p class="font-semibold text-gray-900">Cardiología</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-6 text-center hover:bg-purple-50 transition-colors">
                    <div class="text-3xl mb-3">👶</div>
                    <p class="font-semibold text-gray-900">Pediatría</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-6 text-center hover:bg-purple-50 transition-colors">
                    <div class="text-3xl mb-3">🦴</div>
                    <p class="font-semibold text-gray-900">Traumatología</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-6 text-center hover:bg-purple-50 transition-colors">
                    <div class="text-3xl mb-3">🧠</div>
                    <p class="font-semibold text-gray-900">Neurología</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-6 text-center hover:bg-purple-50 transition-colors">
                    <div class="text-3xl mb-3">👁️</div>
                    <p class="font-semibold text-gray-900">Oftalmología</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-6 text-center hover:bg-purple-50 transition-colors">
                    <div class="text-3xl mb-3">🦷</div>
                    <p class="font-semibold text-gray-900">Odontología</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-6 text-center hover:bg-purple-50 transition-colors">
                    <div class="text-3xl mb-3">💆</div>
                    <p class="font-semibold text-gray-900">Dermatología</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-6 text-center hover:bg-purple-50 transition-colors">
                    <div class="text-3xl mb-3">🤰</div>
                    <p class="font-semibold text-gray-900">Ginecología</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-6 text-center hover:bg-purple-50 transition-colors">
                    <div class="text-3xl mb-3">➕</div>
                    <p class="font-semibold text-gray-900">Y muchas más...</p>
                </div>
            </div>

            <div class="text-center">
                <a href="https://wa.me/{{ $whatsappNumber }}?text={{ $whatsappMessage }}"
                   target="_blank"
                   class="inline-flex items-center gap-3 px-8 py-4 text-lg font-bold text-white rounded-full whatsapp-btn">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                    </svg>
                    Buscar Mi Especialista
                </a>
            </div>
        </div>
    </section>

    <!-- Social Proof / Testimonials -->
    <section class="py-20 gradient-bg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-5xl font-bold text-white mb-4">
                    Lo Que Dicen Nuestros Pacientes
                </h2>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Testimonial 1 -->
                <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-8 text-white">
                    <div class="flex items-center gap-1 mb-4">
                        <svg class="w-5 h-5 text-yellow-300" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <svg class="w-5 h-5 text-yellow-300" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <svg class="w-5 h-5 text-yellow-300" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <svg class="w-5 h-5 text-yellow-300" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <svg class="w-5 h-5 text-yellow-300" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </div>
                    <p class="text-lg mb-4">
                        "Increíble lo fácil que fue agendar mi cita. En menos de 2 minutos tenía mi consulta confirmada. ¡Súper recomendado!"
                    </p>
                    <p class="font-bold">- María González</p>
                </div>

                <!-- Testimonial 2 -->
                <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-8 text-white">
                    <div class="flex items-center gap-1 mb-4">
                        <svg class="w-5 h-5 text-yellow-300" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <svg class="w-5 h-5 text-yellow-300" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <svg class="w-5 h-5 text-yellow-300" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <svg class="w-5 h-5 text-yellow-300" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <svg class="w-5 h-5 text-yellow-300" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </div>
                    <p class="text-lg mb-4">
                        "El servicio es excelente. Los médicos son muy profesionales y la atención fue rápida. Ya no necesito perder tiempo en llamadas."
                    </p>
                    <p class="font-bold">- Carlos Rodríguez</p>
                </div>

                <!-- Testimonial 3 -->
                <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-8 text-white">
                    <div class="flex items-center gap-1 mb-4">
                        <svg class="w-5 h-5 text-yellow-300" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <svg class="w-5 h-5 text-yellow-300" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <svg class="w-5 h-5 text-yellow-300" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <svg class="w-5 h-5 text-yellow-300" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <svg class="w-5 h-5 text-yellow-300" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </div>
                    <p class="text-lg mb-4">
                        "Me encanta poder agendar citas para toda mi familia desde WhatsApp. Es muy conveniente y los recordatorios son geniales."
                    </p>
                    <p class="font-bold">- Ana Martínez</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA Section -->
    <section class="py-20 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-5xl font-bold text-gray-900 mb-6">
                ¿Listo para Cuidar tu Salud?
            </h2>
            <p class="text-xl text-gray-600 mb-10">
                No esperes más. Agenda tu cita médica ahora y recibe atención profesional cuando la necesites.
            </p>
            <a href="https://wa.me/{{ $whatsappNumber }}?text={{ $whatsappMessage }}"
               target="_blank"
               class="inline-flex items-center gap-3 px-10 py-6 text-2xl font-bold text-white rounded-full whatsapp-btn animate-pulse-glow">
                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                </svg>
                Agendar Mi Cita Ahora
            </a>

            <p class="mt-8 text-gray-500">
                Sin costo por agendar • Respuesta inmediata • 100% seguro
            </p>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-3 gap-8 mb-8">
                <div>
                    <img src="{{ asset('landing/images/Icono-8.png') }}" alt="Logo" class="h-12 mb-4">
                    <p class="text-gray-400">
                        Innovación tecnológica al servicio de la salud
                    </p>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-4">Contacto</h3>
                    <p class="text-gray-400 mb-2">
                        <a href="mailto:business@meditecpty.com" class="hover:text-white transition-colors">
                            business@meditecpty.com
                        </a>
                    </p>
                    <p class="text-gray-400">
                        <a href="tel:+5078316100" class="hover:text-white transition-colors">
                            +507 831 6100
                        </a>
                    </p>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-4">Enlaces</h3>
                    <ul class="space-y-2">
                        <li>
                            <a href="{{ route('welcome') }}" class="text-gray-400 hover:text-white transition-colors">
                                Para Médicos y Clínicas
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('privacy.policy') }}" class="text-gray-400 hover:text-white transition-colors">
                                Política de Privacidad
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('terms.service') }}" class="text-gray-400 hover:text-white transition-colors">
                                Términos de Servicio
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} Soluciones Meditec. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- Floating WhatsApp Button -->
    <a href="https://wa.me/{{ $whatsappNumber }}?text={{ $whatsappMessage }}"
       target="_blank"
       class="fixed bottom-6 right-6 z-50 flex items-center gap-3 px-6 py-4 bg-green-500 hover:bg-green-600 text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-300 group">
        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
        </svg>
        <span class="font-bold hidden sm:inline group-hover:inline">Agendar Cita</span>
    </a>

    <script>
        // Simple scroll animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe all sections
        document.querySelectorAll('section').forEach(section => {
            section.style.opacity = '0';
            section.style.transform = 'translateY(20px)';
            section.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';
            observer.observe(section);
        });

        // Medical Directory Filter
        const specialtySelect = document.getElementById('specialty-select');
        const practitionersGrid = document.getElementById('practitioners-grid');
        const noResults = document.getElementById('no-results');

        if (specialtySelect && practitionersGrid && noResults) {
            specialtySelect.addEventListener('change', function() {
                const selectedSpecialty = this.value;
                const practitionerCards = practitionersGrid.querySelectorAll('.practitioner-card');
                let visibleCount = 0;

                practitionerCards.forEach(card => {
                    const cardSpecialties = card.dataset.specialties.split(',').filter(s => s.trim() !== '');

                    if (selectedSpecialty === '' || cardSpecialties.includes(selectedSpecialty)) {
                        card.classList.remove('hidden');
                        visibleCount++;
                    } else {
                        card.classList.add('hidden');
                    }
                });

                // Show/hide no results message
                if (visibleCount === 0) {
                    noResults.classList.remove('hidden');
                    practitionersGrid.classList.add('hidden');
                } else {
                    noResults.classList.add('hidden');
                    practitionersGrid.classList.remove('hidden');
                }
            });
        }
    </script>
</body>
</html>
