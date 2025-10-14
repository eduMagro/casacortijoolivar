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
                                    d="M16 12H8m0 0l4-4m-4 4l4 4m8-4a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold">Correo</p>
                            <p class="text-gray-600">reservas@casacortijoolivar.com</p>
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
                            <p class="text-gray-600">Urbanización Casquero c/Z nº97, 41703 Dos Hermanas, Sevilla</p>
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
                            <p class="text-gray-600">Lunes a domingo: 14:00 - 22:00</p>
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
    </section>
</x-app-layout>
