<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Casa Cortijo Olivar') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- ✅ Tailwind CSS desde CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <!-- ✅ Librerías JS defer -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs/qrcode.min.js" defer></script>
</head>


<body class="font-sans text-gray-900 antialiased">
    <!-- 🌄 Fondo hero (fondo global detrás de todo) -->
    <div class="fixed inset-0 -z-10">
        <img src="{{ asset('imagenesWeb/hero.jpg') }}" alt="Fondo" class="w-full h-full object-cover" />
    </div>

    <!-- 🧭 Contenido por encima del fondo -->
    <div class="relative z-0 min-h-screen">
        @include('layouts.navigation')

        {{ $slot }}

        <footer class="bg-black text-gray-400 py-8 px-6 md:px-20 mt-12">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-sm text-center md:text-left">
                    © {{ date('Y') }} Casa Cortijo Olivar. Todos los derechos reservados.
                </p>
                <div class="flex flex-wrap gap-6 text-sm justify-center">
                    <a href="{{ url('/politica-privacidad') }}" class="hover:text-white transition">
                        Política de Privacidad
                    </a>
                    <a href="{{ url('/cookies') }}" class="hover:text-white transition">
                        Cookies
                    </a>
                    <a href="{{ url('/aviso-legal') }}" class="hover:text-white transition">
                        Aviso Legal
                    </a>
                </div>
            </div>
        </footer>
    </div>
</body>

</html>
