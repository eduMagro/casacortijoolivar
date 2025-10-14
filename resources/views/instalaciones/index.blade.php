<x-app-layout>
    <x-slot name="title">Instalaciones</x-slot>

    <div class="max-w-6xl mx-auto py-10 px-4">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Nuestras instalaciones</h1>

        <p class="mb-8 text-gray-600">
            Disfruta de nuestras zonas comunes y espacios para el descanso.
        </p>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($habitaciones as $habitacion)
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <img src="{{ asset('storage/' . optional($habitacion->imagenes->first())->ruta_imagen) }}"
                        alt="{{ $habitacion->nombre }}" class="h-48 w-full object-cover">
                    <div class="p-4">
                        <h2 class="font-bold text-lg">{{ $habitacion->nombre }}</h2>
                        <p class="text-sm text-gray-600">Capacidad: {{ $habitacion->capacidad }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
