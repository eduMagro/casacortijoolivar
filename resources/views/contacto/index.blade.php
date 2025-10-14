<x-app-layout>
    <x-slot name="title">Contacto</x-slot>

    <section class="relative bg-gradient-to-br from-green-50 via-white to-yellow-50 min-h-screen py-16">
        <!-- Encabezado -->
        <div class="text-center mb-16">
            <h1 class="text-4xl font-extrabold text-gray-800 mb-4 tracking-tight">
                Contacta con <span class="text-green-700">Casa Cortijo Olivar</span>
            </h1>
            <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                Si tienes cualquier duda sobre tus reservas, disponibilidad o nuestras instalaciones, estaremos
                encantados de ayudarte.
            </p>
        </div>

        <!-- Contenedor principal -->
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-2 gap-10">

            <!-- Información de contacto -->
            <div class="flex flex-col justify-center space-y-8">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Información de contacto</h2>
                    <p class="text-gray-600 leading-relaxed">
                        Puedes escribirnos a través del formulario o mediante cualquiera de los siguientes medios:
                    </p>
                </div>

                <div class="space-y-6 text-gray-700">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-green-100 rounded-xl">
                            <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 5h2l.4 2M7 13h10l4-8H5.4M7 13l-1.293 1.293a1 1 0 00.707 1.707H17a1 1 0 00.707-1.707L17 13M7 13V6h10v7M12 19a2 2 0 100-4 2 2 0 000 4z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold">Teléfono</p>
                            <p class="text-gray-600">+34 678 123 456</p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-green-100 rounded-xl">
                            <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16 12H8m0 0l4-4m-4 4l4 4m8-4a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold">Correo</p>
                            <p class="text-gray-600">info@casacortijoolivar.es</p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-green-100 rounded-xl">
                            <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 11c.828 0 1.5-.672 1.5-1.5S12.828 8 12 8s-1.5.672-1.5 1.5S11.172 11 12 11z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 22s8-4.5 8-10a8 8 0 10-16 0c0 5.5 8 10 8 10z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold">Dirección</p>
                            <p class="text-gray-600">Carretera del Olivar s/n, 41640 Osuna, Sevilla</p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-green-100 rounded-xl">
                            <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 8v4l3 3m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold">Horario</p>
                            <p class="text-gray-600">Lunes a domingo: 9:00 - 20:00</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulario -->
            <div class="bg-white/90 backdrop-blur-xl border border-gray-100 shadow-2xl rounded-2xl p-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">Envíanos un mensaje</h2>

                @if (session('success'))
                    <div class="mb-6 p-4 bg-green-100 text-green-800 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('contacto.enviar') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="nombre" class="block font-medium text-gray-700 mb-2">Nombre</label>
                        <input type="text" id="nombre" name="nombre"
                            class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-green-400"
                            value="{{ old('nombre') }}" required>
                        @error('nombre')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block font-medium text-gray-700 mb-2">Correo electrónico</label>
                        <input type="email" id="email" name="email"
                            class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-green-400"
                            value="{{ old('email') }}" required>
                        @error('email')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="mensaje" class="block font-medium text-gray-700 mb-2">Mensaje</label>
                        <textarea id="mensaje" name="mensaje" rows="5"
                            class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-green-400" required>{{ old('mensaje') }}</textarea>
                        @error('mensaje')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white font-semibold px-8 py-3 rounded-lg transition">
                            Enviar mensaje
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Mapa -->
        <div class="mt-20">
            <iframe class="w-full h-96 border-0 rounded-t-2xl shadow-inner"
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3193.3498357096744!2d-5.968692784712136!3d37.23539827986573!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xd1279abf0e6a3b9%3A0x25a03f7d301a30a8!2sOsuna%2C%20Sevilla!5e0!3m2!1ses!2ses!4v1698353948652!5m2!1ses!2ses"
                allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </section>
</x-app-layout>
