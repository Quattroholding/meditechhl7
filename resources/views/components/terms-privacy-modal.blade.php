@props(['name' => 'terms-privacy'])

<x-modal :name="$name" maxWidth="full_w" focusable>
    <div class="bg-white rounded-lg h-full overflow-auto relative z-10">
        <div class="flex justify-between items-center p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Términos de Servicio y Políticas de Privacidad</h2>
            <button x-on:click="$dispatch('close-modal', '{{ $name }}')"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div class="p-6 overflow-y-auto max-h-[calc(100vh-200px)]">
            <!-- Tabs -->
            <div x-data="{ activeTab: 'terms' }" class="w-full">
                <!-- Tab buttons -->
                <div class="flex border-b border-gray-200 mb-6">
                    <button @click="activeTab = 'terms'"
                            :class="{'border-blue-500 text-blue-600': activeTab === 'terms', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'terms'}"
                            class="py-2 px-4 border-b-2 font-medium text-sm">
                        Términos de Servicio
                    </button>
                    <button @click="activeTab = 'privacy'"
                            :class="{'border-blue-500 text-blue-600': activeTab === 'privacy', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'privacy'}"
                            class="ml-8 py-2 px-4 border-b-2 font-medium text-sm">
                        Política de Privacidad
                    </button>
                </div>

                <!-- Terms of Service Tab -->
                <div x-show="activeTab === 'terms'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                    <div class="prose max-w-none">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Términos de Servicio de {{env('app_name')}}</h3>
                        <p class="text-sm text-gray-500 mb-6">Última actualización: {{ date('d/m/Y') }}</p>

                        <div class="space-y-4 text-gray-700">
                            <section>
                                <h4 class="font-semibold text-gray-900 mb-2">1. Aceptación de los Términos</h4>
                                <p>Al acceder y utilizar {{env('app_name')}}, usted acepta estar sujeto a estos Términos de Servicio y a todas las leyes y regulaciones aplicables. Si no está de acuerdo con alguno de estos términos, no debe utilizar este servicio.</p>
                            </section>

                            <section>
                                <h4 class="font-semibold text-gray-900 mb-2">2. Descripción del Servicio</h4>
                                <p>{{env('app_name')}} es una plataforma integral de gestión sanitaria que proporciona herramientas para la administración de consultas médicas, gestión de pacientes, programación de citas, historiales médicos electrónicos y documentación clínica, cumpliendo con los estándares FHIR para interoperabilidad sanitaria.</p>
                            </section>

                            <section>
                                <h4 class="font-semibold text-gray-900 mb-2">3. Registro de Usuario</h4>
                                <p>Para acceder a nuestros servicios, debe:</p>
                                <ul class="list-disc ml-6 mt-2">
                                    <li>Proporcionar información verdadera, precisa y completa</li>
                                    <li>Mantener la seguridad de su contraseña</li>
                                    <li>Notificar inmediatamente cualquier uso no autorizado de su cuenta</li>
                                    <li>Ser responsable de todas las actividades realizadas bajo su cuenta</li>
                                </ul>
                            </section>

                            <section>
                                <h4 class="font-semibold text-gray-900 mb-2">4. Uso Médico y Profesional</h4>
                                <p>Este sistema está diseñado para uso por profesionales de la salud calificados. Los usuarios se comprometen a:</p>
                                <ul class="list-disc ml-6 mt-2">
                                    <li>Cumplir con todas las regulaciones médicas aplicables</li>
                                    <li>Mantener la confidencialidad de la información del paciente</li>
                                    <li>Utilizar el sistema solo para fines médicos legítimos</li>
                                    <li>Seguir las mejores prácticas médicas y éticas</li>
                                </ul>
                            </section>

                            <section>
                                <h4 class="font-semibold text-gray-900 mb-2">5. Protección de Datos Médicos</h4>
                                <p>Reconocemos la naturaleza sensible de los datos médicos y nos comprometemos a:</p>
                                <ul class="list-disc ml-6 mt-2">
                                    <li>Implementar medidas de seguridad técnicas y organizativas apropiadas</li>
                                    <li>Cumplir con las regulaciones de privacidad médica aplicables</li>
                                    <li>Mantener registros de auditoría completos</li>
                                    <li>Proporcionar acceso controlado basado en roles</li>
                                </ul>
                            </section>

                            <section>
                                <h4 class="font-semibold text-gray-900 mb-2">6. Limitaciones de Uso</h4>
                                <p>Está prohibido:</p>
                                <ul class="list-disc ml-6 mt-2">
                                    <li>Utilizar el servicio para actividades ilegales</li>
                                    <li>Intentar acceder a datos de pacientes sin autorización</li>
                                    <li>Compartir credenciales de acceso con terceros no autorizados</li>
                                    <li>Modificar o alterar el software sin autorización</li>
                                    <li>Realizar ingeniería inversa del sistema</li>
                                </ul>
                            </section>

                            <section>
                                <h4 class="font-semibold text-gray-900 mb-2">7. Responsabilidades del Usuario</h4>
                                <p>El usuario es responsable de:</p>
                                <ul class="list-disc ml-6 mt-2">
                                    <li>La precisión de los datos ingresados</li>
                                    <li>El cumplimiento de las regulaciones médicas locales</li>
                                    <li>La obtención del consentimiento apropiado del paciente</li>
                                    <li>El mantenimiento de copias de seguridad de datos críticos</li>
                                </ul>
                            </section>

                            <section>
                                <h4 class="font-semibold text-gray-900 mb-2">8. Disponibilidad del Servicio</h4>
                                <p>Aunque nos esforzamos por mantener el servicio disponible 24/7, no garantizamos disponibilidad continua. Nos reservamos el derecho de realizar mantenimiento programado con notificación previa.</p>
                            </section>

                            <section>
                                <h4 class="font-semibold text-gray-900 mb-2">9. Modificaciones de los Términos</h4>
                                <p>Nos reservamos el derecho de modificar estos términos en cualquier momento. Los cambios serán efectivos inmediatamente después de su publicación en la plataforma.</p>
                            </section>

                            <section>
                                <h4 class="font-semibold text-gray-900 mb-2">10. Contacto</h4>
                                <p>Para preguntas sobre estos términos, contáctenos a través de los canales oficiales de soporte técnico de {{env('app_name')}}.</p>
                            </section>
                        </div>
                    </div>
                </div>

                <!-- Privacy Policy Tab -->
                <div x-show="activeTab === 'privacy'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                    <div class="prose max-w-none">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Política de Privacidad de {{env('app_name')}}</h3>
                        <p class="text-sm text-gray-500 mb-6">Última actualización: {{ date('d/m/Y') }}</p>

                        <div class="space-y-4 text-gray-700">
                            <section>
                                <h4 class="font-semibold text-gray-900 mb-2">1. Introducción</h4>
                                <p>Esta Política de Privacidad describe cómo {{env('app_name')}} recopila, utiliza, almacena y protege la información personal y médica en nuestra plataforma de gestión sanitaria. Cumplimos con todas las regulaciones de privacidad médica aplicables.</p>
                            </section>

                            <section>
                                <h4 class="font-semibold text-gray-900 mb-2">2. Información que Recopilamos</h4>
                                <p>Recopilamos los siguientes tipos de información:</p>
                                <ul class="list-disc ml-6 mt-2">
                                    <li><strong>Datos de Usuarios:</strong> Nombre, email, teléfono, credenciales de acceso</li>
                                    <li><strong>Datos de Pacientes:</strong> Información médica, historial clínico, datos demográficos</li>
                                    <li><strong>Datos de Profesionales:</strong> Qualificaciones, especialidades, licencias médicas</li>
                                    <li><strong>Datos de Actividad:</strong> Registros de acceso, acciones realizadas, logs de auditoría</li>
                                    <li><strong>Datos Técnicos:</strong> Direcciones IP, información del navegador, cookies</li>
                                </ul>
                            </section>

                            <section>
                                <h4 class="font-semibold text-gray-900 mb-2">3. Cómo Utilizamos la Información</h4>
                                <p>Utilizamos la información para:</p>
                                <ul class="list-disc ml-6 mt-2">
                                    <li>Proporcionar servicios de gestión médica y clínica</li>
                                    <li>Facilitar la comunicación entre profesionales de la salud</li>
                                    <li>Generar informes y análisis médicos</li>
                                    <li>Cumplir con requisitos regulatorios y de auditoría</li>
                                    <li>Mejorar la calidad y seguridad de nuestros servicios</li>
                                    <li>Enviar notificaciones importantes del sistema</li>
                                </ul>
                            </section>

                            <section>
                                <h4 class="font-semibold text-gray-900 mb-2">4. Base Legal para el Procesamiento</h4>
                                <p>Procesamos datos médicos bajo las siguientes bases legales:</p>
                                <ul class="list-disc ml-6 mt-2">
                                    <li>Consentimiento explícito del paciente</li>
                                    <li>Necesidad médica vital</li>
                                    <li>Cumplimiento de obligaciones legales</li>
                                    <li>Interés público en el ámbito de la salud pública</li>
                                </ul>
                            </section>

                            <section>
                                <h4 class="font-semibold text-gray-900 mb-2">5. Compartir Información</h4>
                                <p>Solo compartimos información médica:</p>
                                <ul class="list-disc ml-6 mt-2">
                                    <li>Con profesionales autorizados dentro del equipo de atención</li>
                                    <li>Con el consentimiento explícito del paciente</li>
                                    <li>Cuando sea requerido por ley o autoridades sanitarias</li>
                                    <li>En situaciones de emergencia médica</li>
                                    <li>Para auditorías de calidad y cumplimiento</li>
                                </ul>
                            </section>

                            <section>
                                <h4 class="font-semibold text-gray-900 mb-2">6. Seguridad de los Datos</h4>
                                <p>Implementamos múltiples capas de seguridad:</p>
                                <ul class="list-disc ml-6 mt-2">
                                    <li>Cifrado de datos en tránsito y en reposo</li>
                                    <li>Control de acceso basado en roles</li>
                                    <li>Autenticación multifactor</li>
                                    <li>Registros de auditoría completos</li>
                                    <li>Copias de seguridad automáticas y cifradas</li>
                                    <li>Monitoreo continuo de seguridad</li>
                                </ul>
                            </section>

                            <section>
                                <h4 class="font-semibold text-gray-900 mb-2">7. Retención de Datos</h4>
                                <p>Retenemos datos médicos según:</p>
                                <ul class="list-disc ml-6 mt-2">
                                    <li>Requisitos legales y regulatorios aplicables</li>
                                    <li>Necesidades médicas continuas del paciente</li>
                                    <li>Políticas de retención de la institución médica</li>
                                    <li>Mínimo de 7 años para registros médicos completos</li>
                                </ul>
                            </section>

                            <section>
                                <h4 class="font-semibold text-gray-900 mb-2">8. Derechos del Paciente</h4>
                                <p>Los pacientes tienen derecho a:</p>
                                <ul class="list-disc ml-6 mt-2">
                                    <li>Acceder a sus datos médicos</li>
                                    <li>Solicitar correcciones de información inexacta</li>
                                    <li>Solicitar restricciones en el uso de sus datos</li>
                                    <li>Recibir una copia de sus registros médicos</li>
                                    <li>Presentar quejas sobre el manejo de sus datos</li>
                                </ul>
                            </section>

                            <section>
                                <h4 class="font-semibold text-gray-900 mb-2">9. Transferencias Internacionales</h4>
                                <p>Si realizamos transferencias internacionales de datos, garantizamos niveles adecuados de protección mediante contratos de transferencia apropiados y salvaguardas técnicas.</p>
                            </section>

                            <section>
                                <h4 class="font-semibold text-gray-900 mb-2">10. Cookies y Tecnologías Similares</h4>
                                <p>Utilizamos cookies esenciales para el funcionamiento del sistema y cookies analíticas para mejorar nuestros servicios. Puede configurar las preferencias de cookies en su navegador.</p>
                            </section>

                            <section>
                                <h4 class="font-semibold text-gray-900 mb-2">11. Cambios a esta Política</h4>
                                <p>Notificaremos cualquier cambio material a esta política con al menos 30 días de anticipación a través de la plataforma y por email.</p>
                            </section>

                            <section>
                                <h4 class="font-semibold text-gray-900 mb-2">12. Contacto</h4>
                                <p>Para preguntas sobre privacidad, contacte a nuestro Oficial de Protección de Datos a través de los canales oficiales de {{env('app_name')}}.</p>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer with accept button -->
        <div class="flex justify-end items-center p-6 border-t border-gray-200 bg-gray-50">
            <button x-on:click="
                document.querySelector('input[name=\'terms_and_privacy\']').checked = true;
                $dispatch('close-modal', '{{ $name }}')
            "
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition-colors">
                He leído y acepto
            </button>
        </div>
    </div>
</x-modal>
