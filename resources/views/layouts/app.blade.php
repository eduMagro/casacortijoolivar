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

    <!-- Scripts -->
    <!-- ✅ Tailwind (si lo usas como principal) -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('scripts')
    <!-- ✅ Solo una versión de Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <!-- ✅ Librerías que no bloquean renderizado -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs/qrcode.min.js" defer></script>
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main>
            {{-- Mensajes flash de éxito o error --}}
            <div class="max-w-7xl mx-auto px-4">
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
                        <ul class="list-disc pl-5 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </x-alerta>
                @endif

            </div>

            {{ $slot }}
        </main>
        {{-- Footer global --}}
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
