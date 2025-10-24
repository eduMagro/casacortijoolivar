{{-- resources/views/instalaciones/index.blade.php --}}
<x-app-layout>
    <x-slot name="title">Galerías</x-slot>

    <div class="max-w-7xl mx-auto py-10 px-4">
        <header class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Galerías de Casa Cortijo Olivar</h1>
        </header>

        @auth
            <section class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Crear nueva sección</h2>
                <form method="POST" action="{{ route('galerias.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <label class="block">
                        <span class="block text-sm font-medium text-gray-700 mb-1">Título</span>
                        <input type="text" name="titulo" required
                            class="w-full rounded-md border-gray-300 focus:border-gray-900 focus:ring-gray-900"
                            placeholder="Ej: Zona chill-out, Terraza, Jardines..." />
                    </label>

                    <label class="block">
                        <span class="block text-sm font-medium text-gray-700 mb-1">Descripción</span>
                        <textarea name="descripcion" rows="3"
                            class="w-full rounded-md border-gray-300 focus:border-gray-900 focus:ring-gray-900"
                            placeholder="Texto introductorio del apartado..."></textarea>
                    </label>

                    <label class="block">
                        <span class="block text-sm font-medium text-gray-700 mb-1">Imágenes iniciales</span>
                        <input type="file" name="imagenes[]" multiple accept="image/*"
                            class="block w-full text-sm file:mr-4 file:py-2 file:px-4
                        file:rounded-md file:border-0 file:text-sm
                        file:font-semibold file:bg-gray-900 file:text-white
                        hover:file:bg-gray-800 cursor-pointer" />
                        <span class="text-xs text-gray-500">JPG/PNG, se subirán junto a la nueva sección.</span>
                    </label>

                    <div class="flex items-center justify-end">
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-gray-600 text-white text-sm font-medium hover:bg-gray-700">
                            Crear galería
                        </button>
                    </div>
                </form>
            </section>
        @endauth

        <div class="space-y-10 mt-10">
            @php
                $galerias = $galerias ?? collect();
            @endphp

            @forelse ($galerias as $galeria)
                {{-- ✅ AÑADIR x-data aquí con el estado del modal --}}
                <section id="galeria-{{ $galeria->id }}"
                    class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden"
                    x-data="{
                        edit: false,
                        modalAbierto: false,
                        abrirModal() {
                            this.modalAbierto = true;
                            document.body.style.overflow = 'hidden';
                        },
                        cerrarModal() {
                            this.modalAbierto = false;
                            document.body.style.overflow = '';
                        }
                    }">

                    {{-- Cabecera con título/descripcion + edición --}}
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <h2 class="text-2xl font-semibold text-gray-900">{{ $galeria->titulo }}</h2>
                                @if ($galeria->descripcion)
                                    <p class="mt-1 text-gray-600">{{ $galeria->descripcion }}</p>
                                @endif
                            </div>

                            @auth
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    {{-- Botón editar --}}
                                    <button @click="edit = !edit" title="Editar sección"
                                        class="p-2.5 rounded-full bg-blue-100 text-blue-600 hover:bg-blue-200 active:scale-95 transition-all duration-150">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                        </svg>
                                    </button>

                                    {{-- Botón eliminar - ✅ USAR abrirModal() --}}
                                    <button @click="abrirModal()" title="Eliminar sección"
                                        class="p-2.5 rounded-full bg-red-100 text-red-600 hover:bg-red-200 active:scale-95 transition-all duration-150">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </button>
                                </div>
                            @endauth
                        </div>

                        @auth
                            <div x-show="edit" x-transition class="mt-5" style="display:none">
                                <form method="POST" action="{{ route('galerias.update', $galeria->id) }}"
                                    class="grid sm:grid-cols-2 gap-4">
                                    @csrf
                                    @method('PATCH')
                                    <label class="block">
                                        <span class="block text-sm font-medium text-gray-700 mb-1">Título</span>
                                        <input name="titulo" value="{{ old('titulo', $galeria->titulo) }}" required
                                            class="w-full rounded-md border-gray-300 focus:border-gray-900 focus:ring-gray-900">
                                    </label>
                                    <label class="block sm:col-span-2">
                                        <span class="block text-sm font-medium text-gray-700 mb-1">Descripción</span>
                                        <textarea name="descripcion" rows="3"
                                            class="w-full rounded-md border-gray-300 focus:border-gray-900 focus:ring-gray-900">{{ old('descripcion', $galeria->descripcion) }}</textarea>
                                    </label>
                                    <div class="sm:col-span-2 flex items-center justify-end gap-2">
                                        <button type="button" @click="edit=false"
                                            class="px-4 py-2 rounded-md border text-sm">Cancelar</button>
                                        <button type="submit"
                                            class="px-4 py-2 rounded-md bg-gray-600 text-white text-sm hover:bg-gray-700">Guardar</button>
                                    </div>
                                </form>
                            </div>
                        @endauth
                    </div>

                    {{-- Galería de imágenes --}}
                    <div class="p-6" x-data="galeria_{{ $galeria->id }}()">
                        @php $imgs = $galeria->imagenes ?? collect(); @endphp

                        @if ($imgs->count())
                            <ul class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                                @foreach ($imgs as $img)
                                    <li class="relative group">
                                        <button type="button" @click="abrir({{ $loop->index }})"
                                            class="block w-full aspect-square rounded-lg overflow-hidden ring-1 ring-gray-200 hover:ring-2 hover:ring-gray-400 transition-all">
                                            <img src="{{ asset('storage/' . $img->ruta_imagen) }}"
                                                alt="{{ $img->titulo ?: 'Imagen ' . $loop->iteration . ' de ' . $galeria->titulo }}"
                                                class="w-full h-full object-cover" loading="lazy">
                                        </button>

                                        @auth
                                            <form method="POST"
                                                action="{{ route('galerias.imagenes.destroy', $img->id) }}"
                                                class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="p-1.5 rounded-md bg-white/90 text-red-600 shadow hover:bg-white"
                                                    onclick="return confirm('¿Eliminar esta imagen?')" title="Eliminar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                        viewBox="0 0 24 24" fill="currentColor">
                                                        <path
                                                            d="M9 3h6a1 1 0 0 1 1 1v1h4v2H4V5h4V4a1 1 0 0 1 1-1zm1 7h2v8h-2v-8zm4 0h2v8h-2v-8zM8 10h2v8H8v-8z" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endauth
                                    </li>
                                @endforeach
                            </ul>

                            {{-- Lightbox --}}
                            <div x-show="abierta" x-transition.opacity @keydown.escape.window="cerrar"
                                @click.self="cerrar"
                                class="fixed inset-0 z-50 bg-black/90 flex items-center justify-center p-4"
                                style="display:none">
                                <div class="relative w-full max-w-5xl">
                                    <button @click="cerrar"
                                        class="absolute -top-10 right-0 text-white/80 hover:text-white text-sm font-medium">
                                        Cerrar ✕
                                    </button>
                                    <div class="relative bg-black rounded-lg overflow-hidden">
                                        <img :src="actual().src" :alt="actual().alt"
                                            class="w-full max-h-[85vh] object-contain">
                                        <button @click="prev"
                                            class="absolute left-2 top-1/2 -translate-y-1/2 px-4 py-3 bg-white/20 hover:bg-white/30 rounded-lg text-white text-2xl transition-colors">‹</button>
                                        <button @click="next"
                                            class="absolute right-2 top-1/2 -translate-y-1/2 px-4 py-3 bg-white/20 hover:bg-white/30 rounded-lg text-white text-2xl transition-colors">›</button>
                                    </div>
                                </div>
                            </div>
                        @else
                            <p class="text-sm text-gray-500">Aún no hay imágenes en este apartado.</p>
                        @endif

                        {{-- Subida múltiple (solo autenticados) --}}
                        @auth
                            <div class="mt-6 border-t pt-6">
                                <form method="POST" action="{{ route('galerias.imagenes.store', $galeria->id) }}"
                                    enctype="multipart/form-data" class="space-y-3">
                                    @csrf
                                    <label class="block">
                                        <span class="block text-sm font-medium text-gray-700 mb-1">Añadir imágenes a
                                            "{{ $galeria->titulo }}"</span>
                                        <input type="file" name="imagenes[]" multiple accept="image/*"
                                            class="block w-full text-sm file:mr-4 file:py-2 file:px-4
                                                  file:rounded-md file:border-0 file:text-sm
                                                  file:font-semibold file:bg-gray-900 file:text-white
                                                  hover:file:bg-gray-800 cursor-pointer" />
                                        <span class="text-xs text-gray-500">JPG/PNG, ajusta límites en validación.</span>
                                    </label>
                                    <div class="flex items-center justify-end">
                                        <button type="submit"
                                            class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-gray-600 text-white text-sm font-medium hover:bg-gray-700">
                                            Subir
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endauth
                    </div>

                    {{-- ✅ MODAL SIMPLIFICADO con x-show --}}
                    @auth
                        <div x-show="modalAbierto" x-transition.opacity @click.self="cerrarModal()"
                            @keydown.escape.window="cerrarModal()"
                            class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 p-4"
                            style="display: none;">
                            <div class="bg-white rounded-lg p-6 max-w-md w-full shadow-2xl" @click.stop>
                                <div class="flex items-center gap-3 mb-4">
                                    <div
                                        class="flex-shrink-0 w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900">¿Eliminar sección?</h3>
                                        <p class="text-sm text-gray-500 mt-1">{{ $galeria->titulo }}</p>
                                    </div>
                                </div>

                                <p class="text-gray-600 mb-6">
                                    Esta acción no se puede deshacer. Se eliminarán todas las imágenes de esta galería.
                                </p>

                                <div class="flex gap-3 justify-end">
                                    <button type="button" @click="cerrarModal()"
                                        class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md text-sm transition-colors">
                                        Cancelar
                                    </button>
                                    <form method="POST" action="{{ route('galerias.destroy', $galeria->id) }}"
                                        class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md text-sm flex items-center gap-2 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endauth
                </section>

                {{-- Script Alpine para el lightbox de imágenes --}}
                <script>
                    function galeria_{{ $galeria->id }}() {
                        const base = @json(
                            ($galeria->imagenes ?? collect())->map(fn($i) => [
                                    'src' => asset('storage/' . $i->ruta_imagen),
                                    'alt' => $i->titulo ?: 'Imagen de ' . $galeria->titulo,
                                ]));
                        return {
                            abierta: false,
                            idx: 0,
                            abrir(i = 0) {
                                this.idx = i;
                                this.abierta = true;
                                document.body.style.overflow = 'hidden';
                            },
                            cerrar() {
                                this.abierta = false;
                                document.body.style.overflow = '';
                            },
                            next() {
                                if (!base.length) return;
                                this.idx = (this.idx + 1) % base.length;
                            },
                            prev() {
                                if (!base.length) return;
                                this.idx = (this.idx - 1 + base.length) % base.length;
                            },
                            actual() {
                                return base[this.idx] || {
                                    src: '',
                                    alt: ''
                                };
                            }
                        }
                    }
                </script>
            @empty
                <p class="text-gray-500 text-center py-8">No hay apartados aún.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
