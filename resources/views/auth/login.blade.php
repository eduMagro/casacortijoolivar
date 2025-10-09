<x-guest-layout>
    <style>
        .form-login {
            background-color: rgba(255, 255, 255, 0.15);
            /* Opcional: si quieres blur, aunque no siempre funciona */
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
    </style>

    <!-- Contenedor centrado con glassmorphism -->
    <div class="flex items-center justify-center min-h-screen px-4 sm:px-6 lg:px-8">
        <form method="POST" action="{{ route('login') }}"
            class="form-login w-full max-w-md
           border border-white/30
           rounded-2xl shadow-xl
           p-8 flex flex-col gap-6">

            @csrf

            <h1 class="text-center text-2xl font-bold text-white mb-2">Iniciar sesión</h1>

            <!-- Session Status -->
            <x-auth-session-status class="mb-2 text-white" :status="session('status')" />

            <!-- Email Address -->
            <div class="flex flex-col">
                <label for="email" class="text-sm font-medium text-white/90">Email</label>
                <input id="email" name="email" type="email" autocomplete="username" required autofocus
                    class="mt-1 px-4 py-2 rounded-lg
                           bg-white/10 backdrop-blur-3xl
                           text-white placeholder-white/70
                           border border-white/30
                           focus:outline-none focus:ring-2 focus:ring-white
                           [color-scheme:dark]"
                    placeholder="correo@ejemplo.com" value="{{ old('email') }}" />
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-300" />
            </div>

            <!-- Password -->
            <div class="flex flex-col">
                <label for="password" class="text-sm font-medium text-white/90">Contraseña</label>
                <input id="password" name="password" type="password" autocomplete="current-password" required
                    class="mt-1 px-4 py-2 rounded-lg
                           bg-white/10 backdrop-blur-3xl
                           text-white placeholder-white/70
                           border border-white/30
                           focus:outline-none focus:ring-2 focus:ring-white"
                    placeholder="••••••••" />
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-300" />
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between text-white text-sm">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox"
                        class="rounded border-white/30 bg-white/10 text-white focus:ring-white" name="remember">
                    <span class="ml-2">Recordarme</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="underline hover:text-gray-300 transition" href="{{ route('password.request') }}">
                        ¿Olvidaste tu contraseña?
                    </a>
                @endif
            </div>

            <!-- Botón -->
            <button type="submit"
                class="inline-flex items-center justify-center
                       px-6 py-2.5 rounded-lg
                       bg-white hover:bg-gray-200
                       text-black font-semibold
                       shadow-md transition">
                Iniciar sesión
            </button>
        </form>
    </div>
</x-guest-layout>
