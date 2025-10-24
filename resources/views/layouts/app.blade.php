<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- ✅ Iconos para dispositivos -->
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('imagenesWeb/ico/favicon-96x96.png') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('imagenesWeb/ico/favicon.svg') }}">
    <link rel="shortcut icon" href="{{ asset('imagenesWeb/ico/favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('imagenesWeb/ico/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('imagenesWeb/ico/site.webmanifest') }}">

    <meta name="theme-color" content="#ffffff">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- ✅ Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <!-- ✅ Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('scripts')

    <!-- ✅ Librerías que no bloquean renderizado -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs/qrcode.min.js" defer></script>

    <!-- ✅ Estilos personalizados -->
    <style>
        /* Mejoras para dispositivos táctiles */
        @media (hover: none) and (pointer: coarse) {

            button,
            a {
                min-height: 44px;
                min-width: 44px;
            }
        }

        /* Suavizar scroll */
        html {
            scroll-behavior: smooth;
            -webkit-overflow-scrolling: touch;
        }

        /* Prevenir zoom en inputs en iOS */
        @media screen and (max-width: 768px) {

            input[type="text"],
            input[type="email"],
            input[type="tel"],
            input[type="password"],
            textarea,
            select {
                font-size: 16px !important;
            }
        }

        /* Optimizar imágenes */
        img {
            max-width: 100%;
            height: auto;
        }

        body {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
    </style>
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content - SIN PADDING ni max-width -->
        <main>
            {{-- Mensajes flash - SIN contenedor con max-width --}}
            @if (session('success') || session('error') || $errors->any())
                <div class="px-4 py-2">
                    @if (session('success'))
                        <x-alerta tipo="success">
                            {{ session('success') }}
                        </x-alerta>
                    @endif

                    @if (session('error'))
                        <x-alerta tipo="error">
                            {{ session('error') }}
                        </x-alerta>
                    @endif

                    @if ($errors->any())
                        <x-alerta tipo="error">
                            <ul class="list-disc pl-4 text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </x-alerta>
                    @endif
                </div>
            @endif

            {{-- El slot ahora ocupa todo el ancho disponible --}}
            {{ $slot }}
        </main>

        {{-- Footer compacto --}}
        <footer class="bg-black text-gray-400 py-6 px-4 sm:px-6 mt-8">
            <div class="max-w-7xl mx-auto">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-3">
                    <!-- Copyright -->
                    <p class="text-xs sm:text-sm text-center sm:text-left">
                        © {{ date('Y') }} Casa Cortijo Olivar. Todos los derechos reservados.
                    </p>

                    <!-- Links legales -->
                    <div class="flex flex-wrap gap-4 text-xs sm:text-sm justify-center">
                        <a href="{{ route('politica-privacidad') }}" class="hover:text-white transition">
                            Política de Privacidad
                        </a>
                        <a href="{{ route('politica-cookies') }}" class="hover:text-white transition">
                            Cookies
                        </a>
                        <a href="{{ route('aviso-legal') }}" class="hover:text-white transition">
                            Aviso Legal
                        </a>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Script para orientación -->
    <script>
        window.addEventListener('orientationchange', function() {
            document.body.scrollTop = 0;
        });
    </script>
</body>

</html>
