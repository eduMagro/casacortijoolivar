<x-app-layout>
    <x-slot name="title">Contacto</x-slot>

    <div class="max-w-3xl mx-auto py-12 px-6">
        <h1 class="text-3xl font-bold text-center mb-8 text-gray-800">
            Contáctanos
        </h1>

        @if (session('success'))
            <div class="mb-6 p-4 bg-green-100 text-green-800 rounded-xl">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('contacto.enviar') }}" class="space-y-6">
            @csrf

            <div>
                <label for="nombre" class="block font-semibold text-gray-700 mb-2">Nombre</label>
                <input type="text" id="nombre" name="nombre"
                    class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-400"
                    value="{{ old('nombre') }}" required>
                @error('nombre')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block font-semibold text-gray-700 mb-2">Correo electrónico</label>
                <input type="email" id="email" name="email"
                    class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-400"
                    value="{{ old('email') }}" required>
                @error('email')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="mensaje" class="block font-semibold text-gray-700 mb-2">Mensaje</label>
                <textarea id="mensaje" name="mensaje" rows="5"
                    class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-400" required>{{ old('mensaje') }}</textarea>
                @error('mensaje')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-center">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-3 rounded-lg transition">
                    Enviar mensaje
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
