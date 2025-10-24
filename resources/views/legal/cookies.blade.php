<x-app-layout>
    <x-slot name="title">Cookies</x-slot>
    <div class="container mx-auto px-4 py-12 max-w-4xl">
        <h1 class="text-4xl font-bold text-gray-800 mb-8">Política de Cookies</h1>

        <div class="prose prose-lg max-w-none">
            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">¿Qué son las cookies?</h2>
                <p class="text-gray-700 mb-4">
                    Las cookies son pequeños archivos de texto que los sitios web almacenan en su dispositivo
                    (ordenador,
                    tablet o móvil)
                    cuando los visita. Se utilizan ampliamente para hacer que los sitios web funcionen de manera más
                    eficiente,
                    así como para proporcionar información a los propietarios del sitio.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">¿Qué cookies utiliza este sitio web?</h2>

                <div class="mb-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">1. Cookies Técnicas (Obligatorias)</h3>
                    <p class="text-gray-700 mb-4">
                        Son aquellas que permiten al usuario la navegación a través del sitio web y la utilización
                        de las diferentes opciones o servicios que en ella existen. No requieren consentimiento.
                    </p>

                    <div class="bg-gray-50 p-4 rounded-lg mb-4">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left py-2 font-semibold">Cookie</th>
                                    <th class="text-left py-2 font-semibold">Finalidad</th>
                                    <th class="text-left py-2 font-semibold">Duración</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b">
                                    <td class="py-2">XSRF-TOKEN</td>
                                    <td class="py-2">Seguridad contra ataques CSRF</td>
                                    <td class="py-2">Sesión</td>
                                </tr>
                                <tr class="border-b">
                                    <td class="py-2">laravel_session</td>
                                    <td class="py-2">Identificación de sesión del usuario</td>
                                    <td class="py-2">2 horas</td>
                                </tr>
                                <tr>
                                    <td class="py-2">remember_token</td>
                                    <td class="py-2">Mantener sesión activa</td>
                                    <td class="py-2">5 años</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mb-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">2. Cookies de Preferencias</h3>
                    <p class="text-gray-700 mb-4">
                        Permiten recordar información para que el usuario acceda al servicio con determinadas
                        características que pueden diferenciar su experiencia de la de otros usuarios.
                    </p>

                    <div class="bg-gray-50 p-4 rounded-lg mb-4">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left py-2 font-semibold">Cookie</th>
                                    <th class="text-left py-2 font-semibold">Finalidad</th>
                                    <th class="text-left py-2 font-semibold">Duración</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="py-2">cookie_consent</td>
                                    <td class="py-2">Guardar preferencias de cookies</td>
                                    <td class="py-2">1 año</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mb-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">3. Cookies Analíticas (Opcional)</h3>
                    <p class="text-gray-700 mb-4">
                        Permiten cuantificar el número de usuarios y realizar el análisis estadístico de cómo los
                        usuarios
                        utilizan el servicio. Requieren consentimiento previo.
                    </p>

                    <div class="bg-gray-50 p-4 rounded-lg mb-4">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left py-2 font-semibold">Cookie</th>
                                    <th class="text-left py-2 font-semibold">Proveedor</th>
                                    <th class="text-left py-2 font-semibold">Finalidad</th>
                                    <th class="text-left py-2 font-semibold">Duración</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b">
                                    <td class="py-2">_ga</td>
                                    <td class="py-2">Google Analytics</td>
                                    <td class="py-2">Distinguir usuarios</td>
                                    <td class="py-2">2 años</td>
                                </tr>
                                <tr>
                                    <td class="py-2">_gid</td>
                                    <td class="py-2">Google Analytics</td>
                                    <td class="py-2">Distinguir usuarios</td>
                                    <td class="py-2">24 horas</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">Base Legal</h2>
                <p class="text-gray-700 mb-4">
                    El tratamiento de datos mediante cookies se basa en:
                </p>
                <ul class="list-disc pl-6 mb-4 text-gray-700">
                    <li>El consentimiento del usuario para cookies no técnicas</li>
                    <li>El interés legítimo para cookies técnicas necesarias para el funcionamiento del sitio</li>
                    <li>La normativa aplicable: Ley 34/2002 de Servicios de la Sociedad de la Información y RGPD</li>
                </ul>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">Gestión de Cookies</h2>
                <p class="text-gray-700 mb-4">
                    Puede permitir, bloquear o eliminar las cookies instaladas en su equipo mediante la configuración
                    de las opciones de su navegador:
                </p>
                <ul class="list-disc pl-6 mb-4 text-gray-700">
                    <li><strong>Chrome:</strong>
                        <a href="https://support.google.com/chrome/answer/95647" target="_blank"
                            class="text-blue-600 hover:underline">
                            Configuración > Privacidad y seguridad > Cookies
                        </a>
                    </li>
                    <li><strong>Firefox:</strong>
                        <a href="https://support.mozilla.org/es/kb/cookies-informacion-que-los-sitios-web-guardan-en-"
                            target="_blank" class="text-blue-600 hover:underline">
                            Opciones > Privacidad y Seguridad > Cookies
                        </a>
                    </li>
                    <li><strong>Safari:</strong>
                        <a href="https://support.apple.com/es-es/guide/safari/sfri11471/mac" target="_blank"
                            class="text-blue-600 hover:underline">
                            Preferencias > Privacidad > Cookies
                        </a>
                    </li>
                    <li><strong>Edge:</strong>
                        <a href="https://support.microsoft.com/es-es/microsoft-edge/eliminar-cookies-en-microsoft-edge-63947406-40ac-c3b8-57b9-2a946a29ae09"
                            target="_blank" class="text-blue-600 hover:underline">
                            Configuración > Privacidad > Cookies
                        </a>
                    </li>
                </ul>
                <p class="text-gray-700 mb-4">
                    Si bloquea las cookies, es posible que algunas funcionalidades del sitio web no funcionen
                    correctamente.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">Revocación del Consentimiento</h2>
                <p class="text-gray-700 mb-4">
                    Puede revocar en cualquier momento su consentimiento para el uso de cookies mediante el banner
                    de cookies que aparece al acceder al sitio web, o configurando su navegador para bloquear
                    o eliminar las cookies.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">Cookies de Terceros</h2>
                <p class="text-gray-700 mb-4">
                    Este sitio web puede utilizar servicios de terceros que instalan cookies en su dispositivo.
                    No tenemos control sobre estas cookies, por lo que le recomendamos consultar las políticas
                    de privacidad y cookies de estos terceros:
                </p>
                <ul class="list-disc pl-6 mb-4 text-gray-700">
                    <li>
                        <strong>Google Analytics:</strong>
                        <a href="https://policies.google.com/privacy" target="_blank"
                            class="text-blue-600 hover:underline">
                            Política de Privacidad
                        </a>
                    </li>
                </ul>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">Actualización de la Política</h2>
                <p class="text-gray-700 mb-4">
                    Casa Cortijo Olivar se reserva el derecho a modificar esta Política de Cookies para adaptarla
                    a novedades legislativas, técnicas o de servicios. Los cambios serán publicados en esta página.
                </p>
                <p class="text-gray-700 mb-4">
                    <strong>Última actualización:</strong> {{ date('d/m/Y') }}
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">Contacto</h2>
                <p class="text-gray-700 mb-4">
                    Para cualquier duda sobre el uso de cookies, puede contactarnos en:
                    <a href="mailto:reservas@casacortijoolivar.com" class="text-blue-600 hover:underline">
                        reservas@casacortijoolivar.com
                    </a>
                </p>
            </section>
        </div>

        <div class="mt-8 text-center">
            <a href="{{ route('dashboard') }}"
                class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
                Volver al inicio
            </a>
        </div>
    </div>
</x-app-layout>
