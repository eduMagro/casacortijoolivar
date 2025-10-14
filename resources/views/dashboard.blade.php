<x-app-layout>
    <style>
        .contenedor-transparente {
            background-color: rgba(255, 255, 255, 0.15);
            /* 🎯 Semitransparente */
            /* Puedes ajustar a 0.3 o 0.5 si quieres más opaco */
            backdrop-filter: blur(8px);
            /* opcional: solo si quieres blur */
            -webkit-backdrop-filter: blur(8px);
            /* soporte Safari */
        }
    </style>

    <div class="relative w-full h-screen overflow-hidden">
        {{-- Fondo --}}
        <img src="{{ asset('imagenesWeb/hero.jpg') }}" alt="Hero" class="absolute inset-0 w-full h-full object-cover">
        <div class="absolute inset-0 bg-black/10"></div>

        {{-- Contenido centrado --}}
        <div class="relative z-10 h-full flex items-center justify-center px-4">
            <div
                class="w-full max-w-5xl contenedor-transparente
            border border-white/20 rounded-2xl shadow-2xl
            p-6 md:p-10 flex flex-col gap-6">

                {{-- Texto arriba --}}
                <p class="text-white/95 text-lg md:text-2xl text-center leading-relaxed font-semibold">
                    No importa si viajas solo, en pareja o en grupo, aquí encontrarás la habitación perfecta para ti.
                </p>

                {{-- Botón centrado --}}
                <div class="flex justify-center">
                    <a href="{{ route('habitaciones.index') }}"
                        class="inline-flex items-center justify-center
                  px-8 py-3 rounded-lg
                  bg-white hover:bg-gray-200
                  text-black font-semibold
                  shadow-md transition text-base md:text-lg">
                        Ver disponibilidad
                    </a>
                </div>
            </div>

        </div>
    </div>

    {{-- Sección de bienvenida --}}
    <div class="bg-white py-16 px-6 md:px-20 text-center">
        <h2 class="text-3xl font-bold text-gray-800 mb-4">Bienvenido a Casa Cortijo Olivar</h2>
        <p class="text-lg text-gray-600 max-w-3xl mx-auto">
            Ubicada en la tranquila <b>Urbanización Casquero</b>, a tan solo 9 km de Sevilla.
            Disfruta de <b>WiFi gratis</b>, <b>parking privado</b> y un entorno perfecto
            para descansar después de explorar la ciudad.
        </p>
    </div>

    {{-- Servicios destacados --}}
    <div class="bg-gray-50 py-16">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-10">Servicios que te encantarán</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
            <div class="p-6 bg-white shadow rounded-xl">
                <span class="text-4xl">📶</span>
                <h3 class="font-semibold text-lg mt-4">WiFi Gratis</h3>
                <p class="text-gray-600">Conexión rápida y estable en todo el cortijo.</p>
            </div>
            <div class="p-6 bg-white shadow rounded-xl">
                <span class="text-4xl">🚗</span>
                <h3 class="font-semibold text-lg mt-4">Parking Privado</h3>
                <p class="text-gray-600">Aparca sin preocupaciones, incluido en tu estancia.</p>
            </div>
            <div class="p-6 bg-white shadow rounded-xl">
                <span class="text-4xl">❄️</span>
                <h3 class="font-semibold text-lg mt-4">Aire Acondicionado</h3>
                <p class="text-gray-600">Habitaciones frescas y cómodas todo el año.</p>
            </div>
        </div>
    </div>

    {{-- Opiniones --}}
    <div class="bg-white py-16">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-10">Lo que dicen nuestros huéspedes</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-6xl mx-auto">
            <div class="p-6 border rounded-xl shadow">
                <p class="italic text-gray-600">“Excelente hacienda, muy tranquila y limpia. Emilio es un gran
                    anfitrión.”</p>
                <p class="mt-3 font-bold text-gray-800">– Jhon ⭐⭐⭐⭐⭐</p>
            </div>
            <div class="p-6 border rounded-xl shadow">
                <p class="italic text-gray-600">“La ubicación es perfecta, cerca de Sevilla y en zona muy tranquila.”
                </p>
                <p class="mt-3 font-bold text-gray-800">– García ⭐⭐⭐⭐</p>
            </div>
            <div class="p-6 border rounded-xl shadow">
                <p class="italic text-gray-600">“La cama cómoda, silencio por la noche y personal super amable.”</p>
                <p class="mt-3 font-bold text-gray-800">– Victoria ⭐⭐⭐⭐</p>
            </div>
        </div>
    </div>

</x-app-layout>
