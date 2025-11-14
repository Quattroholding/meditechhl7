<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Política de Privacidad - {{ config('app.name') }}</title>
    <link rel="icon" href="{{url('images/favicon.ico')}}" type="image/x-icon">
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
                <h1 class="text-3xl font-bold text-gray-900">Política de Privacidad</h1>
                <p class="text-sm text-gray-500 mt-2">{{ config('app.name') }}</p>
                <p class="text-sm text-gray-500">Última actualización: {{ date('d/m/Y') }}</p>
            </div>

            <!-- Content -->
            <div class="px-6 py-8">
                <div class="prose max-w-none space-y-6 text-gray-700">

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">1. Introducción</h2>
                        <p>Esta Política de Privacidad describe cómo {{ config('app.name') }} recopila, utiliza, almacena y protege la información personal y médica en nuestra plataforma de gestión sanitaria. Cumplimos con todas las regulaciones de privacidad médica aplicables.</p>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">2. Información que Recopilamos</h2>
                        <p>Recopilamos los siguientes tipos de información:</p>
                        <ul class="list-disc ml-6 mt-2 space-y-1">
                            <li><strong>Datos de Usuarios:</strong> Nombre, email, teléfono, credenciales de acceso</li>
                            <li><strong>Datos de Pacientes:</strong> Información médica, historial clínico, datos demográficos</li>
                            <li><strong>Datos de Profesionales:</strong> Qualificaciones, especialidades, licencias médicas</li>
                            <li><strong>Datos de Actividad:</strong> Registros de acceso, acciones realizadas, logs de auditoría</li>
                            <li><strong>Datos Técnicos:</strong> Direcciones IP, información del navegador, cookies</li>
                        </ul>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">3. Cómo Utilizamos la Información</h2>
                        <p>Utilizamos la información para:</p>
                        <ul class="list-disc ml-6 mt-2 space-y-1">
                            <li>Proporcionar servicios de gestión médica y clínica</li>
                            <li>Facilitar la comunicación entre profesionales de la salud</li>
                            <li>Generar informes y análisis médicos</li>
                            <li>Cumplir con requisitos regulatorios y de auditoría</li>
                            <li>Mejorar la calidad y seguridad de nuestros servicios</li>
                            <li>Enviar notificaciones importantes del sistema</li>
                        </ul>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">4. Base Legal para el Procesamiento</h2>
                        <p>Procesamos datos médicos bajo las siguientes bases legales:</p>
                        <ul class="list-disc ml-6 mt-2 space-y-1">
                            <li>Consentimiento explícito del paciente</li>
                            <li>Necesidad médica vital</li>
                            <li>Cumplimiento de obligaciones legales</li>
                            <li>Interés público en el ámbito de la salud pública</li>
                        </ul>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">5. Compartir Información</h2>
                        <p>Solo compartimos información médica:</p>
                        <ul class="list-disc ml-6 mt-2 space-y-1">
                            <li>Con profesionales autorizados dentro del equipo de atención</li>
                            <li>Con el consentimiento explícito del paciente</li>
                            <li>Cuando sea requerido por ley o autoridades sanitarias</li>
                            <li>En situaciones de emergencia médica</li>
                            <li>Para auditorías de calidad y cumplimiento</li>
                        </ul>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">6. Seguridad de los Datos</h2>
                        <p>Implementamos múltiples capas de seguridad:</p>
                        <ul class="list-disc ml-6 mt-2 space-y-1">
                            <li>Cifrado de datos en tránsito y en reposo</li>
                            <li>Control de acceso basado en roles</li>
                            <li>Autenticación multifactor</li>
                            <li>Registros de auditoría completos</li>
                            <li>Copias de seguridad automáticas y cifradas</li>
                            <li>Monitoreo continuo de seguridad</li>
                        </ul>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">7. Retención de Datos</h2>
                        <p>Retenemos datos médicos según:</p>
                        <ul class="list-disc ml-6 mt-2 space-y-1">
                            <li>Requisitos legales y regulatorios aplicables</li>
                            <li>Necesidades médicas continuas del paciente</li>
                            <li>Políticas de retención de la institución médica</li>
                            <li>Mínimo de 7 años para registros médicos completos</li>
                        </ul>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">8. Derechos del Paciente</h2>
                        <p>Los pacientes tienen derecho a:</p>
                        <ul class="list-disc ml-6 mt-2 space-y-1">
                            <li>Acceder a sus datos médicos</li>
                            <li>Solicitar correcciones de información inexacta</li>
                            <li>Solicitar restricciones en el uso de sus datos</li>
                            <li>Recibir una copia de sus registros médicos</li>
                            <li>Presentar quejas sobre el manejo de sus datos</li>
                        </ul>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">9. Transferencias Internacionales</h2>
                        <p>Si realizamos transferencias internacionales de datos, garantizamos niveles adecuados de protección mediante contratos de transferencia apropiados y salvaguardas técnicas.</p>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">10. Cookies y Tecnologías Similares</h2>
                        <p>Utilizamos cookies esenciales para el funcionamiento del sistema y cookies analíticas para mejorar nuestros servicios. Puede configurar las preferencias de cookies en su navegador.</p>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">11. Cambios a esta Política</h2>
                        <p>Notificaremos cualquier cambio material a esta política con al menos 30 días de anticipación a través de la plataforma y por email.</p>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">12. Contacto</h2>
                        <p>Para preguntas sobre privacidad, contacte a nuestro Oficial de Protección de Datos a través de los canales oficiales de {{ config('app.name') }}.</p>
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
