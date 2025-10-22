{{-- resources/views/galerias/index.blade.php --}}
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

        <div class="space-y-10">
            @php
                $galerias = $galerias ?? collect(); // fallback por si se carga mal
            @endphp

            @forelse ($galerias as $galeria)
                <section id="galeria-{{ $galeria->id }}"
                    class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    {{-- Cabecera con título/descripcion + edición --}}
                    <div class="p-6 border-b border-gray-100" x-data="{ edit: false }">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h2 class="text-2xl font-semibold text-gray-900">{{ $galeria->titulo }}</h2>
                                @if ($galeria->descripcion)
                                    <p class="mt-1 text-gray-600">{{ $galeria->descripcion }}</p>
                                @endif
                            </div>

                            @auth
                                <button @click="edit = !edit"
                                    class="inline-flex items-center gap-2 px-3 py-2 rounded-md bg-gray-900 text-white text-sm hover:bg-gray-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24"
                                        fill="currentColor">
                                        <path
                                            d="M4 21h4l11-11-4-4L4 17v4zM18.7 2.3a1 1 0 0 1 1.4 0l1.6 1.6a1 1 0 0 1 0 1.4l-1.3 1.3-3-3 1.3-1.3z" />
                                    </svg>
                                    Editar sección
                                </button>
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
                                            class="block w-full aspect-square rounded-lg overflow-hidden ring-1 ring-gray-200">
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
                            <div x-show="abierta" x-transition.opacity
                                class="fixed inset-0 z-50 bg-black/80 flex items-center justify-center p-4"
                                @keydown.escape.window="cerrar" @click.self="cerrar" style="display:none">
                                <div class="relative w-full max-w-5xl">
                                    <button @click="cerrar"
                                        class="absolute -top-10 right-0 text-white/80 hover:text-white text-sm">Cerrar
                                        ✕</button>
                                    <div class="relative bg-black rounded-lg overflow-hidden">
                                        <img :src="actual().src" :alt="actual().alt"
                                            class="w-full max-h-[80vh] object-contain">
                                        <button @click="prev"
                                            class="absolute left-2 top-1/2 -translate-y-1/2 px-3 py-2 bg-white/20 hover:bg-white/30 rounded">‹</button>
                                        <button @click="next"
                                            class="absolute right-2 top-1/2 -translate-y-1/2 px-3 py-2 bg-white/20 hover:bg-white/30 rounded">›</button>
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
                                            “{{ $galeria->titulo }}”</span>
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
                </section>

                {{-- Alpine helper por galería --}}
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
                <p class="text-gray-500">No hay apartados aún.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
