<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Términos de Servicio - {{ config('app.name') }}</title>
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
                <h1 class="text-3xl font-bold text-gray-900">Términos de Servicio</h1>
                <p class="text-sm text-gray-500 mt-2">{{ config('app.name') }}</p>
                <p class="text-sm text-gray-500">Última actualización: {{ date('d/m/Y') }}</p>
            </div>

            <!-- Content -->
            <div class="px-6 py-8">
                <div class="prose max-w-none space-y-6 text-gray-700">

                    <section>
                        <p class="mb-4">Los presentes Términos y Condiciones regulan el acceso y utilización de la plataforma tecnológica SAMI por parte de sus usuarios.</p>
                        <p>Al registrarse, acceder o utilizar SAMI, el usuario declara haber leído y aceptado estos Términos y Condiciones.</p>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">1. DEFINICIÓN DE SAMI</h2>
                        <p>SAMI es una plataforma tecnológica destinada para facilitar la gestión administrativa y clínica de profesionales de la salud, consultorios, clínicas y establecimientos de servicios médicos.</p><br/>
                        <p>Entre otras funcionalidades, SAMI puede permitir la gestión de pacientes, agenda, citas, información clínica, cobros, reportes y otras herramientas disponibles según el plan contratado.</p>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">2. USUARIO AUTORIZADO</h2>
                        <p>El acceso a SAMI está reservado a usuarios debidamente registrados y autorizados por el cliente que haya contratado el servicio.</p><br/>
                        <p>Cada usuario deberá utilizar sus propias credenciales de acceso y no podrá compartirlas con terceros.<br/> <br/>  El usuario será responsable de mantener la confidencialidad de sus credenciales y de informar inmediatamente cualquier uso no autorizado de su cuenta.</p>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">3. USO DE LA PLATAFORMA</h2>
                        <p>El usuario se obliga a utilizar SAMI exclusivamente para fines legítimos y relacionados con las actividades autorizadas por el cliente.</p>
                        <br/> <p>El usuario no podrá:</p><br/>
                        <ul class="list-disc ml-6 mt-2 space-y-1">
                            <li>utilizar SAMI para actividades ilícitas;</li>
                            <li>acceder a información de pacientes sin autorización;</li>
                            <li>compartir información clínica con personas no autorizadas;</li>
                            <li>utilizar las credenciales de otro usuario;</li>
                            <li>intentar vulnerar, alterar o afectar la seguridad de la plataforma;</li>
                            <li>copiar, modificar, reproducir o realizar ingeniería inversa sobre el software;</li>
                            <li>introducir información maliciosa, código, virus u otros elementos que puedan afectar la plataforma; o</li>
                            <li>utilizar SAMI de una manera que pueda afectar su funcionamiento o el de otros usuarios.</li>
                        </ul>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">4. INFORMACIÓN DE LOS PACIENTES</h2>
                        <p>El usuario reconoce que la información relacionada con pacientes puede constituir información personal sensible y datos relacionados con la salud.</p><br/>
                        <p>El usuario únicamente deberá acceder y utilizar dicha información cuando esté autorizado para hacerlo y cuando resulte necesario para el desempeño de sus funciones.<br/> <br/>  El usuario deberá mantener la confidencialidad de la información a la que tenga acceso mediante SAMI.</p>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">5. INFORMACIÓN INGRESADA POR EL USUARIO</h2>
                        <p>El usuario será responsable de procurar que la información que introduzca en SAMI sea correcta, completa y adecuada para la finalidad correspondiente.</p><br/>
                        <p>SAMI no será responsable por errores, omisiones o decisiones derivadas de información incorrecta o incompleta introducida por el usuario.</p>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">6. HISTORIA CLÍNICA</h2>
                        <p>SAMI proporciona herramientas tecnológicas para facilitar la administración electrónica de información clínica.</p><br/>
                        <p>El uso de SAMI no sustituye las obligaciones profesionales, éticas, legales o regulatorias que correspondan a los profesionales de la salud respecto de la elaboración, actualización, integridad, custodia y conservación de la historia clínica.<br/><br/>   El usuario deberá utilizar las funcionalidades de historia clínica de conformidad con las obligaciones que le resulten aplicables.</p>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">7. RESPONSABILIDAD MÉDICA</h2>
                        <p>SAMI no presta servicios médicos.<br/> <br/>  La plataforma no sustituye el criterio profesional del médico ni constituye por sí misma diagnóstico, tratamiento, prescripción o recomendación médica.<br/> <br/>  Toda decisión relacionada con la atención de un paciente deberá ser adoptada por el profesional de la salud correspondiente.</p>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">8. FUNCIONALIDADES DE INTELIGENCIA ARTIFICIAL</h2>
                        <p>Cuando SAMI disponga de funcionalidades basadas en inteligencia artificial, estas tendrán carácter de apoyo tecnológico.</p><br/>
                        <p>Los resultados, sugerencias, análisis o información generados por dichas herramientas deberán ser revisados y evaluados por el profesional correspondiente antes de ser utilizados en la atención de un paciente.<br/> <br/>  El usuario reconoce que los sistemas de inteligencia artificial pueden generar resultados incompletos, incorrectos o imprecisos.<br/> <br/>  El usuario no deberá considerar dichos resultados como sustitutos de su criterio profesional.</p>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">9. SEGURIDAD</h2>
                        <p>El usuario deberá adoptar medidas razonables para proteger sus credenciales, dispositivos y accesos a SAMI.<br/><br/>   Cuando tenga conocimiento de una vulneración, pérdida de credenciales o acceso no autorizado, deberá comunicarlo inmediatamente al administrador correspondiente y/o a SAMI.</p><br/>
                        <p>SAMI podrá suspender temporalmente una cuenta cuando resulte necesario para proteger la seguridad de la plataforma, de la información o de otros usuarios.</p>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">10. PRIVACIDAD Y PROTECCIÓN DE DATOS</h2>
                        <p>El tratamiento de los datos personales se realizará conforme a la legislación aplicable y a la Política de Privacidad de SAMI.</p><br/>
                        <p>Cuando el usuario acceda a información de pacientes por cuenta del cliente, deberá utilizarla exclusivamente para las finalidades autorizadas y relacionadas con sus funciones.<br/> <br/>  El usuario no podrá copiar, descargar, divulgar o utilizar información de pacientes para fines personales o ajenos a sus funciones.</p>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">11. PROPIEDAD INTELECTUAL</h2>
                        <p>SAMI y todos sus elementos tecnológicos, incluyendo software, código, interfaces, diseños, marcas, contenidos, funcionalidades y documentación, pertenecen a SAMI o a sus respectivos licenciantes.</p><br/>
                        <p>El acceso a la plataforma no concede al usuario ningún derecho de propiedad sobre dichos elementos.</p>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">12. DISPONIBILIDAD DEL SERVICIO</h2>
                        <p>SAMI procurará mantener la plataforma disponible y operativa.</p><br/>
                        <p>Sin embargo, podrán producirse interrupciones temporales debido a mantenimiento, actualizaciones, fallas de proveedores tecnológicos, problemas de conectividad, incidentes de seguridad, fuerza mayor o circunstancias fuera del control razonable de SAMI.</p>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">13. SUSPENSIÓN O CANCELACIÓN DEL ACCESO</h2>
                        <p>SAMI o el administrador designado por EL CLIENTE podrán suspender o cancelar el acceso de un usuario cuando:</p>
                        <ul class="list-disc ml-6 mt-2 space-y-1">
                            <li>se incumplan estos Términos;</li>
                            <li>exista un riesgo para la seguridad de la plataforma;</li>
                            <li>se detecte uso fraudulento o ilícito;</li>
                            <li>el usuario deje de estar autorizado por EL CLIENTE; o</li>
                            <li>exista cualquier otra causa justificada conforme al Contrato.</li>
                        </ul>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">14. ACTUALIZACIONES</h2>
                        <p>SAMI podrá actualizar estos Términos cuando resulte necesario por cambios en la plataforma, nuevas funcionalidades, modificaciones regulatorias o mejoras en sus servicios.<br/> <br/> Cuando los cambios sean materiales, SAMI podrá comunicarlos mediante la plataforma, correo electrónico u otros medios disponibles.</p><br/>
                        <p>El uso continuado de SAMI después de la entrada en vigor de los cambios constituirá aceptación de los Términos actualizados, salvo que la legislación aplicable exija un mecanismo distinto.</p>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">15. RELACIÓN CON EL CONTRATO PRINCIPAL</h2>
                        <p>Estos Términos forman parte del marco contractual de SAMI y complementan el Contrato de Suscripción y Prestación de Servicios y el Anexo de Tratamiento y Protección de Datos.</p><br/>
                        <p>En caso de contradicción:</p><br/>
                        <ul class="list-disc ml-6 mt-2 space-y-1">
                            <li>el Contrato principal prevalecerá respecto de las condiciones comerciales y contractuales entre SAMI y EL CLIENTE;</li>
                            <li>el Anexo de Tratamiento y Protección de Datos prevalecerá respecto de las materias relacionadas con protección de datos personales; y</li>
                            <li>estos Términos prevalecerán respecto de las reglas de utilización de la plataforma por los usuarios.</li>
                        </ul>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">16. LEGISLACIÓN APLICABLE</h2>
                        <p>Estos Términos se regirán por las leyes de la República de Panamá.</p>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">17. ACEPTACIÓN</h2>
                        <p>Al registrarse, acceder o utilizar SAMI, el usuario declara que ha leído, comprendido y aceptado estos Términos y Condiciones.</p>
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
