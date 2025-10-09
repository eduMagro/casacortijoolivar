<x-app-layout>
    <x-slot name="title">Habitaciones</x-slot>

    <div class="max-w-7xl mx-auto py-8 px-4">

        <div x-data="{ modalAbierto: false }">
            {{-- Botón "Crear habitación" solo si estás autenticado --}}
            @auth
                <div class="mb-4">
                    <button @click="modalAbierto = true"
                        class="bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded">
                        + Nueva habitación
                    </button>
                    <a href="{{ route('habitaciones.calendarioPrecios') }}"
                        class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded shadow">
                        Ver calendario de precios
                    </a>
                </div>
            @endauth


            {{-- Todo tu contenido de la vista aquí --}}

            {{-- Modal --}}
            <div x-show="modalAbierto" x-transition
                class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center">

                <div @click.away="modalAbierto = false"
                    class="bg-white p-6 rounded-lg shadow-lg w-[90vw] sm:w-[500px] max-w-full relative">

                    <h2 class="text-xl font-bold mb-4">Nueva habitación</h2>

                    <form action="{{ route('habitaciones.store') }}" method="POST" enctype="multipart/form-data"
                        class="space-y-4">
                        @csrf

                        {{-- Nombre --}}
                        <div>
                            <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                            <input type="text" name="nombre" id="nombre" required
                                class="w-full border-gray-300 rounded mt-1 px-3 py-2 shadow-sm focus:ring-green-500 focus:border-green-500">
                        </div>

                        {{-- Capacidad --}}
                        <div>
                            <label for="capacidad" class="block text-sm font-medium text-gray-700">Número de
                                huéspedes</label>
                            <input type="number" name="capacidad" id="capacidad" min="1" max="20" required
                                class="w-full border-gray-300 rounded mt-1 px-3 py-2 shadow-sm focus:ring-green-500 focus:border-green-500">
                        </div>

                        {{-- Tipo --}}
                        <div>
                            <label for="tipo" class="block text-sm font-medium text-gray-700">Tipo</label>
                            <select name="tipo" id="tipo" required
                                class="w-full border-gray-300 rounded mt-1 px-3 py-2 shadow-sm focus:ring-green-500 focus:border-green-500">
                                <option value="mixta">Mixta</option>
                                <option value="masculina">Masculina</option>
                                <option value="femenina">Femenina</option>
                            </select>
                        </div>
                        {{-- Modo de reserva --}}
                        <div>
                            <label for="modo_reserva" class="block text-sm font-medium text-gray-700">Modo de
                                reserva</label>
                            <select name="modo_reserva" id="modo_reserva" required
                                class="w-full border-gray-300 rounded mt-1 px-3 py-2 shadow-sm focus:ring-green-500 focus:border-green-500">
                                <option value="completa">Completa (habitación entera)</option>
                                <option value="por_cama">Por cama (individual)</option>
                            </select>
                        </div>

                        {{-- Imágenes --}}
                        <div>
                            <label for="imagenes" class="block text-sm font-medium text-gray-700">Subir imágenes</label>
                            <input type="file" name="imagenes[]" id="imagenes" multiple accept="image/*"
                                class="w-full mt-1 text-sm">
                        </div>

                        {{-- Botones --}}
                        <div class="flex justify-end gap-2 pt-4">
                            <button type="button" @click="modalAbierto = false"
                                class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                                Cancelar
                            </button>
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                                Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        {{-- Lista de habitaciones --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($habitaciones as $habitacion)
                <div class="bg-white rounded-lg shadow-md overflow-hidden flex flex-col">
                    {{-- Imagen si existe --}}
                    @if ($habitacion->imagen)
                        <img src="{{ asset('storage/' . $habitacion->imagen) }}" alt="{{ $habitacion->nombre }}"
                            class="h-48 w-full object-cover">
                    @else
                        <div class="h-48 bg-gray-100 flex items-center justify-center text-gray-400">
                            Sin imagen
                        </div>
                    @endif

                    <div class="p-4 flex flex-col gap-2 flex-1">
                        <h2 class="text-xl font-bold">{{ $habitacion->nombre }}</h2>
                        <p class="text-sm text-gray-600 capitalize">Tipo: {{ $habitacion->tipo }}</p>
                        <p class="text-sm text-gray-600">Capacidad: {{ $habitacion->capacidad }} personas</p>


                        {{-- Descripción recortada --}}
                        @if ($habitacion->descripcion)
                            <p class="text-sm text-gray-700 mt-2 line-clamp-3">
                                {{ Str::limit($habitacion->descripcion, 100) }}
                            </p>
                        @endif

                        {{-- Botones para autenticados --}}
                        @auth
                            <div class="mt-4 flex gap-2">
                                <a href="{{ route('habitaciones.edit', $habitacion) }}"
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm">
                                    Editar
                                </a>

                                <a href="{{ route('habitaciones.show', $habitacion) }}"
                                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm">
                                    Ver
                                </a>

                                <form action="{{ route('habitaciones.destroy', $habitacion) }}" method="POST"
                                    onsubmit="return confirm('¿Seguro que quieres eliminar esta habitación?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">
                                        Eliminar
                                    </button>
                                </form>

                                <a href="{{ route('habitaciones.edit', $habitacion) }}#imagen"
                                    class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-1 rounded text-sm">
                                    Subir imagen
                                </a>
                            </div>
                        @endauth
                    </div>
                </div>
            @empty
                <p class="text-gray-600">No hay habitaciones registradas todavía.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
