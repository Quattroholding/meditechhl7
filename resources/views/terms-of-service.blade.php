<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Términos de Servicio - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md">
            <!-- Header -->
            <div class="border-b border-gray-200 px-6 py-6">
                <h1 class="text-3xl font-bold text-gray-900">Términos de Servicio</h1>
                <p class="text-sm text-gray-500 mt-2">{{ config('app.name') }}</p>
                <p class="text-sm text-gray-500">Última actualización: {{ date('d/m/Y') }}</p>
            </div>

            <!-- Content -->
            <div class="px-6 py-8">
                <div class="prose max-w-none space-y-6 text-gray-700">

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">1. Aceptación de los Términos</h2>
                        <p>Al acceder y utilizar {{ config('app.name') }}, usted acepta estar sujeto a estos Términos de Servicio y a todas las leyes y regulaciones aplicables. Si no está de acuerdo con alguno de estos términos, no debe utilizar este servicio.</p>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">2. Descripción del Servicio</h2>
                        <p>{{ config('app.name') }} es una plataforma integral de gestión sanitaria que proporciona herramientas para la administración de consultas médicas, gestión de pacientes, programación de citas, historiales médicos electrónicos y documentación clínica, cumpliendo con los estándares FHIR para interoperabilidad sanitaria.</p>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">3. Registro de Usuario</h2>
                        <p>Para acceder a nuestros servicios, debe:</p>
                        <ul class="list-disc ml-6 mt-2 space-y-1">
                            <li>Proporcionar información verdadera, precisa y completa</li>
                            <li>Mantener la seguridad de su contraseña</li>
                            <li>Notificar inmediatamente cualquier uso no autorizado de su cuenta</li>
                            <li>Ser responsable de todas las actividades realizadas bajo su cuenta</li>
                        </ul>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">4. Uso Médico y Profesional</h2>
                        <p>Este sistema está diseñado para uso por profesionales de la salud calificados. Los usuarios se comprometen a:</p>
                        <ul class="list-disc ml-6 mt-2 space-y-1">
                            <li>Cumplir con todas las regulaciones médicas aplicables</li>
                            <li>Mantener la confidencialidad de la información del paciente</li>
                            <li>Utilizar el sistema solo para fines médicos legítimos</li>
                            <li>Seguir las mejores prácticas médicas y éticas</li>
                        </ul>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">5. Protección de Datos Médicos</h2>
                        <p>Reconocemos la naturaleza sensible de los datos médicos y nos comprometemos a:</p>
                        <ul class="list-disc ml-6 mt-2 space-y-1">
                            <li>Implementar medidas de seguridad técnicas y organizativas apropiadas</li>
                            <li>Cumplir con las regulaciones de privacidad médica aplicables</li>
                            <li>Mantener registros de auditoría completos</li>
                            <li>Proporcionar acceso controlado basado en roles</li>
                        </ul>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">6. Limitaciones de Uso</h2>
                        <p>Está prohibido:</p>
                        <ul class="list-disc ml-6 mt-2 space-y-1">
                            <li>Utilizar el servicio para actividades ilegales</li>
                            <li>Intentar acceder a datos de pacientes sin autorización</li>
                            <li>Compartir credenciales de acceso con terceros no autorizados</li>
                            <li>Modificar o alterar el software sin autorización</li>
                            <li>Realizar ingeniería inversa del sistema</li>
                        </ul>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">7. Responsabilidades del Usuario</h2>
                        <p>El usuario es responsable de:</p>
                        <ul class="list-disc ml-6 mt-2 space-y-1">
                            <li>La precisión de los datos ingresados</li>
                            <li>El cumplimiento de las regulaciones médicas locales</li>
                            <li>La obtención del consentimiento apropiado del paciente</li>
                            <li>El mantenimiento de copias de seguridad de datos críticos</li>
                        </ul>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">8. Disponibilidad del Servicio</h2>
                        <p>Aunque nos esforzamos por mantener el servicio disponible 24/7, no garantizamos disponibilidad continua. Nos reservamos el derecho de realizar mantenimiento programado con notificación previa.</p>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">9. Modificaciones de los Términos</h2>
                        <p>Nos reservamos el derecho de modificar estos términos en cualquier momento. Los cambios serán efectivos inmediatamente después de su publicación en la plataforma.</p>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">10. Contacto</h2>
                        <p>Para preguntas sobre estos términos, contáctenos a través de los canales oficiales de soporte técnico de {{ config('app.name') }}.</p>
                    </section>

                </div>
            </div>

            <!-- Footer -->
            <div class="border-t border-gray-200 px-6 py-4 bg-gray-50">
                <p class="text-sm text-gray-600 text-center">
                    © {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.
                </p>
            </div>
        </div>
    </div>
</body>
</html>