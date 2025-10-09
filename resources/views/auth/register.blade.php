<x-guest-layout>

    <style>
        .contenedor-transparente {
            background-color: rgba(255, 255, 255, 0.15);
            /* Transparencia visible */
            backdrop-filter: blur(8px);
            /* Difuminado elegante */
            -webkit-backdrop-filter: blur(8px);
            /* Compatibilidad Safari */
        }
    </style>

    <!-- Contenedor -->
    <div class="flex items-center justify-center min-h-screen px-4 sm:px-6 lg:px-8">
        <form method="POST" action="{{ route('clientes.store') }}"
            class="w-full max-w-xl contenedor-transparente
           border border-white/20 rounded-2xl shadow-2xl
           p-8 sm:p-12 flex flex-col gap-6">

            @csrf

            <h1 class="text-center text-2xl font-bold text-white">Registro de cliente</h1>

            <!-- Nombre -->
            <div class="flex flex-col">
                <label for="nombre" class="text-sm font-medium text-white/90">Nombre</label>
                <input id="nombre" name="nombre" required placeholder="Nombre"
                    class="mt-1 px-4 py-2 rounded-lg
                           bg-white/10 backdrop-blur-3xl
                           text-white placeholder-white/70
                           border border-white/30
                           focus:outline-none focus:ring-2 focus:ring-white" />
                <x-input-error :messages="$errors->get('nombre')" class="mt-2 text-red-300" />
            </div>

            <!-- Apellidos -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="flex flex-col">
                    <label for="apellido1" class="text-sm font-medium text-white/90">Primer apellido</label>
                    <input id="apellido1" name="apellido1" required placeholder="Apellido 1"
                        class="mt-1 px-4 py-2 rounded-lg
                               bg-white/10 backdrop-blur-3xl
                               text-white placeholder-white/70
                               border border-white/30
                               focus:outline-none focus:ring-2 focus:ring-white" />
                    <x-input-error :messages="$errors->get('apellido1')" class="mt-2 text-red-300" />
                </div>
                <div class="flex flex-col">
                    <label for="apellido2" class="text-sm font-medium text-white/90">Segundo apellido</label>
                    <input id="apellido2" name="apellido2" placeholder="Apellido 2"
                        class="mt-1 px-4 py-2 rounded-lg
                               bg-white/10 backdrop-blur-3xl
                               text-white placeholder-white/70
                               border border-white/30
                               focus:outline-none focus:ring-2 focus:ring-white" />
                    <x-input-error :messages="$errors->get('apellido2')" class="mt-2 text-red-300" />
                </div>
            </div>

            <!-- Email -->
            <div class="flex flex-col">
                <label for="email" class="text-sm font-medium text-white/90">Email</label>
                <input id="email" name="email" type="email" required placeholder="correo@ejemplo.com"
                    class="mt-1 px-4 py-2 rounded-lg
                           bg-white/10 backdrop-blur-3xl
                           text-white placeholder-white/70
                           border border-white/30
                           focus:outline-none focus:ring-2 focus:ring-white" />
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-300" />
            </div>

            <!-- Teléfono y DNI -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="flex flex-col">
                    <label for="telefono" class="text-sm font-medium text-white/90">Teléfono</label>
                    <input id="telefono" name="telefono" placeholder="+34 600 000 000"
                        class="mt-1 px-4 py-2 rounded-lg
                               bg-white/10 backdrop-blur-3xl
                               text-white placeholder-white/70
                               border border-white/30
                               focus:outline-none focus:ring-2 focus:ring-white" />
                    <x-input-error :messages="$errors->get('telefono')" class="mt-2 text-red-300" />
                </div>
                <div class="flex flex-col">
                    <label for="dni" class="text-sm font-medium text-white/90">DNI</label>
                    <input id="dni" name="dni" placeholder="12345678X"
                        class="mt-1 px-4 py-2 rounded-lg
                               bg-white/10 backdrop-blur-3xl
                               text-white placeholder-white/70
                               border border-white/30
                               focus:outline-none focus:ring-2 focus:ring-white" />
                    <x-input-error :messages="$errors->get('dni')" class="mt-2 text-red-300" />
                </div>
            </div>

            <!-- Dirección -->
            <div class="flex flex-col">
                <label for="direccion" class="text-sm font-medium text-white/90">Dirección</label>
                <input id="direccion" name="direccion" placeholder="Calle, número, piso..."
                    class="mt-1 px-4 py-2 rounded-lg
                           bg-white/10 backdrop-blur-3xl
                           text-white placeholder-white/70
                           border border-white/30
                           focus:outline-none focus:ring-2 focus:ring-white" />
                <x-input-error :messages="$errors->get('direccion')" class="mt-2 text-red-300" />
            </div>

            <!-- Localidad y País -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="flex flex-col">
                    <label for="localidad" class="text-sm font-medium text-white/90">Localidad</label>
                    <input id="localidad" name="localidad" placeholder="Sevilla"
                        class="mt-1 px-4 py-2 rounded-lg
                               bg-white/10 backdrop-blur-3xl
                               text-white placeholder-white/70
                               border border-white/30
                               focus:outline-none focus:ring-2 focus:ring-white" />
                    <x-input-error :messages="$errors->get('localidad')" class="mt-2 text-red-300" />
                </div>
                <div class="flex flex-col">
                    <label for="pais" class="text-sm font-medium text-white/90">País</label>
                    <input id="pais" name="pais" placeholder="España"
                        class="mt-1 px-4 py-2 rounded-lg
                               bg-white/10 backdrop-blur-3xl
                               text-white placeholder-white/70
                               border border-white/30
                               focus:outline-none focus:ring-2 focus:ring-white" />
                    <x-input-error :messages="$errors->get('pais')" class="mt-2 text-red-300" />
                </div>
            </div>

            <!-- Notas -->
            <div class="flex flex-col">
                <label for="notas" class="text-sm font-medium text-white/90">Notas</label>
                <textarea id="notas" name="notas" rows="3" placeholder="Información adicional..."
                    class="mt-1 px-4 py-2 rounded-lg
                           bg-white/10 backdrop-blur-3xl
                           text-white placeholder-white/70
                           border border-white/30
                           focus:outline-none focus:ring-2 focus:ring-white"></textarea>
                <x-input-error :messages="$errors->get('notas')" class="mt-2 text-red-300" />
            </div>

            <!-- Botón -->
            <div class="flex items-center justify-between">
                <a class="underline text-sm text-white hover:text-gray-200 transition" href="{{ route('login') }}">
                    ¿Ya estás registrado?
                </a>
                <button type="submit"
                    class="inline-flex items-center justify-center
                           px-6 py-2.5 rounded-lg
                           bg-white hover:bg-gray-200
                           text-black font-semibold
                           shadow-md transition">
                    Registrarse
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
