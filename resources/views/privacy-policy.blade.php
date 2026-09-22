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
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">1. OBJETO Y ALCANCE</h2>
                        <p>La presente Política de Privacidad establece la forma en que <b>SOLUCIONES MEDITEC, S.A.,</b> en adelante <b>"SAMI"</b>, recopila, utiliza, almacena, protege y, cuando corresponda, comparte datos personales en relación con la plataforma tecnológica SAMI.</p><br/>
                        <p>Esta Política aplica a los usuarios de la plataforma, pacientes, clientes, profesionales de la salud, personal administrativo y demás personas cuyos datos personales sean tratados a través de SAMI.</p><br/>
                        <p>El tratamiento de datos personales se realizará de conformidad con la <b>Ley 81 de 26 de marzo de 2019 sobre Protección de Datos Personales</b>, su reglamentación mediante Decreto Ejecutivo No. 285 de 28 de mayo de 2021 y demás normas aplicables.</p>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">2. DATOS PERSONALES QUE PODEMOS RECOPILAR</h2>
                        <p>Dependiendo de la utilización de la plataforma, SAMI podrá tratar datos tales como:</p>
                        <ul class="list-disc ml-6 mt-2 space-y-1">
                            <li>Nombre y apellidos.</li>
                            <li>Documento de identificación.</li>
                            <li>Información de contacto.</li>
                            <li>Fecha de nacimiento y datos demográficos.</li>
                            <li>Información relacionada con citas y servicios médicos.</li>
                            <li>Información administrativa y de facturación.</li>
                            <li>Datos de usuarios y credenciales de acceso.</li>
                            <li>Información contenida en expedientes o historias clínicas.</li>
                            <li>Datos relativos a la salud y demás información sensible que el usuario o cliente incorpore a la plataforma.</li>
                            <li>Información técnica relacionada con el uso de la plataforma.</li>
                        </ul>
                        <p class="mt-3">SAMI procurará limitar la recopilación y tratamiento de datos a aquellos que sean necesarios para las finalidades correspondientes.</p>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">3. DATOS RELATIVOS A LA SALUD</h2>
                        <p>Los datos relativos a la salud reciben una protección especial conforme a la legislación aplicable.<br/> <br/>  Cuando médicos, clínicas, hospitales u otros clientes incorporen información de pacientes a SAMI, dichos clientes serán responsables de contar con la legitimación, autorización o base legal correspondiente para su recopilación y tratamiento.</p><br/>
                        <p>SAMI tratará dichos datos únicamente en la medida necesaria para prestar los servicios contratados y conforme a las instrucciones, configuraciones y permisos establecidos por el cliente, sin adquirir la propiedad de la información clínica.</p>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">4. FINALIDADES DEL TRATAMIENTO</h2>
                        <p>Los datos personales podrán ser tratados para las siguientes finalidades:</p>
                        <ul class="list-disc ml-6 mt-2 space-y-1">
                            <li>Crear y administrar cuentas de usuarios.</li>
                            <li>Gestionar citas, pacientes y servicios médicos.</li>
                            <li>Permitir la creación, consulta y administración de información clínica dentro de la plataforma.</li>
                            <li>Facilitar procesos administrativos, facturación, cobros y generación de reportes.</li>
                            <li>Facilitar comunicaciones relacionadas con citas y servicios, incluyendo funcionalidades automatizadas disponibles en SAMI.</li>
                            <li>Proporcionar soporte técnico y atención al usuario.</li>
                            <li>Mantener, actualizar, proteger y mejorar la plataforma.</li>
                            <li>Detectar y prevenir accesos no autorizados, fraude, usos indebidos o incidentes de seguridad.</li>
                            <li>Cumplir obligaciones legales, regulatorias o requerimientos de autoridades competentes.</li>
                            <li>Cumplir cualquier otra finalidad legítima informada al titular al momento de la recopilación de sus datos.</li>
                        </ul>
                        <p class="mt-3">SAMI no venderá ni comercializará datos personales o datos de salud de los usuarios o pacientes.</p>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">5. BASE LEGAL DEL TRATAMIENTO</h2>
                        <p>El tratamiento de datos personales se realizará sobre la base jurídica que corresponda en cada caso, incluyendo el consentimiento del titular, la ejecución de una relación contractual, el cumplimiento de obligaciones legales o las demás condiciones permitidas por la legislación panameña.</p><br/>
                        <p>Cuando el tratamiento requiera consentimiento, este deberá ser obtenido por el responsable correspondiente de manera previa, informada y conforme a la normativa aplicable.<br/> <br/>  El consentimiento podrá ser revocado cuando corresponda, sin efectos retroactivos.</p>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">6. RESPONSABILIDAD DEL CLIENTE</h2>
                        <p>Cuando SAMI sea utilizada por médicos, clínicas, hospitales u otras organizaciones para administrar información de sus pacientes, dichas organizaciones serán responsables de:</p>
                        <ul class="list-disc ml-6 mt-2 space-y-1">
                            <li>Obtener y mantener la legitimación necesaria para el tratamiento de los datos.</li>
                            <li>Informar a los titulares sobre el tratamiento de sus datos cuando corresponda.</li>
                            <li>Mantener actualizada y exacta la información bajo su responsabilidad.</li>
                            <li>Administrar adecuadamente los usuarios y permisos de acceso.</li>
                            <li>Cumplir las obligaciones legales, profesionales y sanitarias aplicables.</li>
                        </ul>
                        <p class="mt-3">SAMI actuará como proveedor tecnológico respecto de los datos que procese por cuenta del cliente, de conformidad con el contrato y el Anexo de Tratamiento y Protección de Datos Personales y Datos de Salud.</p>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">7. PROVEEDORES Y TERCEROS</h2>
                        <p>Para prestar adecuadamente sus servicios, SAMI podrá utilizar proveedores tecnológicos especializados en servicios tales como alojamiento, almacenamiento, respaldo, seguridad, comunicaciones, soporte técnico, procesamiento de información y otras funciones necesarias para la operación de la plataforma.</p><br/>
                        <p>SAMI procurará que dichos proveedores estén sujetos a obligaciones de confidencialidad y protección de datos compatibles con la naturaleza de los servicios prestados.<br/> <br/>  Cuando resulte necesario realizar transferencias nacionales o internacionales de datos, estas se efectuarán de conformidad con las condiciones y requisitos establecidos por la legislación aplicable.</p>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">8. INTELIGENCIA ARTIFICIAL Y FUNCIONALIDADES AUTOMATIZADAS</h2>
                        <p>Cuando SAMI incorpore funcionalidades de inteligencia artificial u otras herramientas automatizadas, estas podrán utilizar información proporcionada por el usuario o generada dentro de la plataforma para ofrecer funciones de apoyo.</p><br/>
                        <p>Estas funcionalidades tienen carácter asistencial y tecnológico y no sustituyen el criterio, conocimiento, juicio ni responsabilidad del profesional de la salud.</p><br/>
                        <p>En particular, cuando una funcionalidad proporcione posibles alternativas, sugerencias o información de apoyo relacionada con una consulta médica, corresponde exclusivamente al profesional evaluar dicha información y determinar y documentar el diagnóstico y tratamiento que considere aplicables.</p><br/>
                        <p>SAMI no utilizará datos personales o datos de salud para entrenar modelos de terceros con fines ajenos a la prestación de los servicios contratados, salvo que exista una base legal que lo permita y se haya informado al titular cuando corresponda.</p>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">9. SEGURIDAD Y CONFIDENCIALIDAD</h2>
                        <p>SAMI implementará medidas técnicas y organizativas razonables destinadas a proteger los datos personales contra acceso, uso, modificación, divulgación, pérdida o destrucción no autorizados.</p><br/>
                        <p>El acceso a la información estará sujeto a los mecanismos de autenticación, permisos y controles disponibles en la plataforma.<br/> <br/>  SAMI mantendrá la obligación de confidencialidad respecto de los datos personales a los que tenga acceso con motivo de la prestación de sus servicios.</p>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">10. CONSERVACIÓN DE LOS DATOS</h2>
                        <p>Los datos personales serán conservados durante el tiempo necesario para cumplir las finalidades para las cuales fueron recopilados, atender obligaciones legales o contractuales y ejercer o defender derechos.</p><br/>
                        <p>En el caso de información clínica administrada por médicos, clínicas u hospitales, la conservación deberá además atender los períodos y obligaciones establecidos por la legislación aplicable a dichos responsables.<br/> <br/>  Una vez finalizada la relación contractual, SAMI podrá conservar determinada información cuando exista una obligación legal o una razón legítima para ello. La información que no deba conservarse será eliminada o anonimizada conforme a los procedimientos aplicables.</p>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">11. DERECHOS DE LOS TITULARES</h2>
                        <p>Los titulares de datos personales podrán ejercer los derechos que les reconoce la legislación panameña, incluyendo los derechos de:</p>
                        <ul class="list-disc ml-6 mt-2 space-y-1">
                            <li>Acceso</li>
                            <li>Rectificación</li>
                            <li>Cancelación</li>
                            <li>Oposición</li>
                            <li>Portabilidad</li>
                        </ul>
                        <p class="mt-3">Cuando los datos hayan sido tratados dentro de SAMI por cuenta de un médico, clínica, hospital u otra organización, el titular podrá dirigir su solicitud al responsable correspondiente. SAMI prestará la asistencia razonable que corresponda al cliente para atender dichas solicitudes.</p><br/>
                        <p>Las solicitudes relacionadas con datos cuyo tratamiento sea responsabilidad directa de SAMI podrán dirigirse a:</p><br/>
                        <p><strong>Correo electrónico:</strong> business@meditecpty.com<br><strong>Responsable:</strong> Bárbara Paván</p><br/>
                        <p>SAMI atenderá las solicitudes dentro de los plazos establecidos por la legislación aplicable.</p>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">12. CAMBIOS A LA POLÍTICA DE PRIVACIDAD</h2>
                        <p>SAMI podrá actualizar esta Política de Privacidad cuando resulte necesario por cambios legales, regulatorios, tecnológicos o en sus servicios.<br/> <br/>  Cuando los cambios sean relevantes, SAMI podrá comunicar dichos cambios mediante la plataforma, correo electrónico, sitio web u otros medios razonables.</p><br/>
                        <p>La versión vigente estará disponible en los medios oficiales de SAMI.</p>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">13. ACEPTACIÓN</h2>
                        <p>Al utilizar SAMI, el usuario declara haber tenido acceso a esta Política de Privacidad y comprender el tratamiento de datos personales descrito en ella. <br/> <br/>  Cuando la legislación requiera consentimiento específico para determinado tratamiento, dicho consentimiento será solicitado mediante los mecanismos correspondientes.</p><br/>
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
