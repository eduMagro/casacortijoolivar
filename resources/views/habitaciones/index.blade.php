<x-app-layout>
    <x-slot name="title">Habitaciones</x-slot>
    {{-- Estilo para x-cloak --}}
    <style>
        [x-cloak] {
            display: none !important;
        }

        /* Estilos para FullCalendar */
        .fc {
            font-size: 0.75rem !important;
        }

        .fc-toolbar-title {
            font-size: 1rem !important;
            font-weight: bold;
        }

        /* Hacer las celdas más cuadradas */
        .fc .fc-daygrid-day {
            aspect-ratio: 1 / 1;
            min-height: 0 !important;
        }

        .fc .fc-daygrid-day-frame {
            min-height: 0 !important;
            padding: 2px !important;
        }

        .fc .fc-daygrid-day-top {
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
        }

        .fc .fc-daygrid-day-number {
            padding: 2px 4px !important;
            font-size: 0.7rem !important;
            font-weight: 600;
        }

        /* Estilo para los eventos (número de camas disponibles) */
        .fc-event-title {
            display: flex !important;
            justify-content: center;
            align-items: center;
            font-size: 0.75rem !important;
            font-weight: 700 !important;
            color: #000 !important;
            height: 100%;
            text-align: center;
            opacity: 1 !important;
            padding: 2px !important;
            line-height: 1 !important;
        }

        /* Contenedor de eventos para que no se pise con el número del día */
        .fc .fc-daygrid-day-events {
            margin-top: 0 !important;
            min-height: 18px !important;
        }

        .fc .fc-daygrid-event {
            margin: 1px 0 !important;
            padding: 1px 2px !important;
        }

        /* Por si usas display: 'background' */
        .fc-event.fc-event-background .fc-event-title {
            opacity: 1 !important;
        }

        /* Ajustar altura de las celdas del body */
        .fc .fc-scrollgrid-section-body>td {
            height: auto !important;
        }
    </style>
    <div class="max-w-7xl mx-auto py-8 px-4">
        <div x-data="{
            modalCrear: false,
            modalEditar: false,
            modalImagenes: false,
            habitacionEditar: null,
            habitacionId: null,
            imagenesHabitacion: [],
            imagenSeleccionada: 0,
            setImagenes(imgs) {
                this.imagenesHabitacion = imgs
                this.imagenSeleccionada = 0
            },
            anterior() {
                if (this.imagenesHabitacion.length === 0) return
                this.imagenSeleccionada = (this.imagenSeleccionada - 1 + this.imagenesHabitacion.length) % this.imagenesHabitacion.length
            },
            siguiente() {
                if (this.imagenesHabitacion.length === 0) return
                this.imagenSeleccionada = (this.imagenSeleccionada + 1) % this.imagenesHabitacion.length
            }
        }">
            @auth
                <div class="mb-6 flex flex-wrap gap-3">
                    <button @click="modalCrear = true"
                        class="inline-flex items-center justify-center px-6 py-2.5 rounded-lg 
                        bg-white hover:bg-gray-50 
                        text-gray-800 font-semibold 
                        border border-gray-200 hover:border-gray-300
                        shadow-sm hover:shadow-md 
                        transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Nueva habitación
                    </button>
                    <a href="{{ route('habitaciones.calendarioPrecios') }}"
                        class="inline-flex items-center justify-center px-6 py-2.5 rounded-lg 
                        bg-gray-800 hover:bg-gray-900 
                        text-white font-semibold 
                        shadow-sm hover:shadow-md 
                        transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Ver calendario de precios
                    </a>
                </div>
            @endauth

            {{-- Modal CREAR --}}
            <div x-show="modalCrear" x-cloak x-transition
                class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                <div @click.away="modalCrear = false"
                    class="bg-white rounded-2xl shadow-2xl w-full max-w-md relative overflow-hidden">

                    <div class="px-6 pt-6 pb-4 border-b border-gray-100">
                        <h2 class="text-2xl font-bold text-gray-800">Nueva habitación</h2>
                    </div>

                    <form action="{{ route('habitaciones.store') }}" method="POST" enctype="multipart/form-data"
                        class="p-6 space-y-5">
                        @csrf
                        <div>
                            <label for="nombre" class="block text-sm font-semibold text-gray-700 mb-2">Nombre</label>
                            <input type="text" name="nombre" id="nombre" required
                                class="w-full border border-gray-200 rounded-lg px-4 py-2.5 
                                focus:ring-2 focus:ring-gray-200 focus:border-gray-400 
                                transition-all duration-200">
                        </div>

                        <div>
                            <label for="descripcion"
                                class="block text-sm font-semibold text-gray-700 mb-2">Descripción</label>
                            <textarea name="descripcion" id="descripcion" rows="3"
                                class="w-full border border-gray-200 rounded-lg px-4 py-2.5 
                                focus:ring-2 focus:ring-gray-200 focus:border-gray-400 
                                transition-all duration-200 resize-none"
                                placeholder="Describe las características de la habitación..."></textarea>
                        </div>

                        <div>
                            <label for="capacidad"
                                class="block text-sm font-semibold text-gray-700 mb-2">Capacidad</label>
                            <input type="number" name="capacidad" id="capacidad" min="1" max="20" required
                                class="w-full border border-gray-200 rounded-lg px-4 py-2.5 
                                focus:ring-2 focus:ring-gray-200 focus:border-gray-400 
                                transition-all duration-200">
                        </div>

                        <div>
                            <label for="tipo" class="block text-sm font-semibold text-gray-700 mb-2">Tipo</label>
                            <select name="tipo" id="tipo" required
                                class="w-full border border-gray-200 rounded-lg px-4 py-2.5 
                                focus:ring-2 focus:ring-gray-200 focus:border-gray-400 
                                transition-all duration-200">
                                <option value="mixta">Mixta</option>
                                <option value="masculina">Masculina</option>
                                <option value="femenina">Femenina</option>
                            </select>
                        </div>

                        <div>
                            <label for="modo_reserva" class="block text-sm font-semibold text-gray-700 mb-2">Modo de
                                reserva</label>
                            <select name="modo_reserva" id="modo_reserva" required
                                class="w-full border border-gray-200 rounded-lg px-4 py-2.5 
                                focus:ring-2 focus:ring-gray-200 focus:border-gray-400 
                                transition-all duration-200">
                                <option value="completa">Completa</option>
                                <option value="por_cama">Por cama</option>
                            </select>
                        </div>

                        <div>
                            <label for="imagenes" class="block text-sm font-semibold text-gray-700 mb-2">Subir
                                imágenes</label>
                            <input type="file" name="imagenes[]" multiple accept="image/*"
                                class="w-full text-sm text-gray-600
                                file:mr-4 file:py-2.5 file:px-4
                                file:rounded-lg file:border-0
                                file:bg-gray-50 file:text-gray-700
                                file:font-semibold
                                hover:file:bg-gray-100
                                file:transition-all file:duration-200
                                cursor-pointer">
                        </div>

                        <div class="flex gap-3 pt-4">
                            <button type="button" @click="modalCrear = false"
                                class="flex-1 px-4 py-2.5 rounded-lg 
                                bg-gray-100 hover:bg-gray-200 
                                text-gray-700 font-semibold
                                transition-all duration-200">
                                Cancelar
                            </button>
                            <button type="submit"
                                class="flex-1 px-4 py-2.5 rounded-lg 
                                bg-gray-800 hover:bg-gray-900 
                                text-white font-semibold
                                shadow-sm hover:shadow-md
                                transition-all duration-200">
                                Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Modal EDITAR --}}
            <div x-show="modalEditar" x-cloak x-transition
                class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                <div @click.away="modalEditar = false"
                    class="bg-white rounded-2xl shadow-2xl w-full max-w-md relative overflow-hidden">

                    <div class="px-6 pt-6 pb-4 border-b border-gray-100">
                        <h2 class="text-2xl font-bold text-gray-800">Editar habitación</h2>
                    </div>

                    <form :action="`/habitaciones/${habitacionEditar.id}`" method="POST" class="p-6 space-y-5">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Nombre</label>
                            <input type="text" name="nombre" x-model="habitacionEditar.nombre"
                                class="w-full border border-gray-200 rounded-lg px-4 py-2.5 
                                focus:ring-2 focus:ring-gray-200 focus:border-gray-400 
                                transition-all duration-200">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Descripción</label>
                            <textarea name="descripcion" x-model="habitacionEditar.descripcion" rows="3"
                                class="w-full border border-gray-200 rounded-lg px-4 py-2.5 
                                focus:ring-2 focus:ring-gray-200 focus:border-gray-400 
                                transition-all duration-200 resize-none"
                                placeholder="Describe las características de la habitación..."></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Capacidad</label>
                            <input type="number" name="capacidad" x-model="habitacionEditar.capacidad"
                                class="w-full border border-gray-200 rounded-lg px-4 py-2.5 
                                focus:ring-2 focus:ring-gray-200 focus:border-gray-400 
                                transition-all duration-200">
                        </div>
                        <div class="flex gap-3 pt-4">
                            <button type="button" @click="modalEditar = false"
                                class="flex-1 px-4 py-2.5 rounded-lg 
                                bg-gray-100 hover:bg-gray-200 
                                text-gray-700 font-semibold
                                transition-all duration-200">
                                Cancelar
                            </button>
                            <button type="submit"
                                class="flex-1 px-4 py-2.5 rounded-lg 
                                bg-gray-800 hover:bg-gray-900 
                                text-white font-semibold
                                shadow-sm hover:shadow-md
                                transition-all duration-200">
                                Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Modal IMÁGENES --}}
            <div x-show="modalImagenes" x-cloak x-transition
                class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center overflow-auto p-4">
                <div @click.away="modalImagenes = false"
                    class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl relative overflow-hidden">

                    <div class="px-6 pt-6 pb-4 border-b border-gray-100">
                        <h2 class="text-2xl font-bold text-gray-800">Galería de imágenes</h2>
                    </div>

                    <template x-if="habitacionId">
                        <div class="p-6 space-y-6">
                            <div class="grid grid-cols-2 gap-4">
                                <template x-for="img in imagenesHabitacion" :key="img.id">
                                    <div class="relative group">
                                        <img :src="'{{ asset('storage') }}/' + img.ruta_imagen"
                                            class="rounded-lg shadow-sm w-full h-48 object-cover">

                                        <form :action="'{{ url('habitacion-imagenes') }}/' + img.id" method="POST"
                                            class="absolute top-2 right-2 delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="bg-white hover:bg-red-50 text-red-600 
                                                rounded-full p-2 shadow-md
                                                opacity-0 group-hover:opacity-100
                                                transition-all duration-200 delete-btn">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </template>
                            </div>

                            <form :action="`/habitaciones/${habitacionId}/imagenes`" method="POST"
                                enctype="multipart/form-data" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Agregar más
                                        imágenes</label>
                                    <input type="file" name="imagenes[]" multiple accept="image/*"
                                        class="w-full text-sm text-gray-600
                                        file:mr-4 file:py-2.5 file:px-4
                                        file:rounded-lg file:border-0
                                        file:bg-gray-50 file:text-gray-700
                                        file:font-semibold
                                        hover:file:bg-gray-100
                                        file:transition-all file:duration-200
                                        cursor-pointer">
                                </div>
                                <button type="submit"
                                    class="w-full px-4 py-2.5 rounded-lg 
                                    bg-gray-800 hover:bg-gray-900 
                                    text-white font-semibold
                                    shadow-sm hover:shadow-md
                                    transition-all duration-200">
                                    Subir imágenes
                                </button>
                            </form>

                            <button @click="modalImagenes = false"
                                class="w-full px-4 py-2.5 rounded-lg 
                                bg-gray-100 hover:bg-gray-200 
                                text-gray-700 font-semibold
                                transition-all duration-200">
                                Cerrar
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Listado de habitaciones --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($habitaciones as $h)
                    <div
                        class="bg-white rounded-2xl shadow-sm hover:shadow-lg border border-gray-100 overflow-hidden transition-all duration-300 group">
                        {{-- Imagen --}}
                        <div class="relative h-52 bg-gray-100 overflow-hidden">
                            @if ($h->imagenes->isNotEmpty())
                                <img src="{{ asset('storage/' . $h->imagenes->first()->ruta_imagen) }}"
                                    alt="{{ $h->nombre }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif

                            @auth
                                <button
                                    class="btn-bloqueo absolute top-3 right-3 bg-white/90 backdrop-blur-sm
                                    rounded-full p-2 shadow-md hover:scale-110 transition-all duration-200"
                                    data-id="{{ $h->id }}" data-bloqueada="{{ $h->bloqueada ? 1 : 0 }}">
                                    @if ($h->bloqueada)
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-600"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="11" width="18" height="11" rx="2"
                                                ry="2"></rect>
                                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-600"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="11" width="18" height="11" rx="2"
                                                ry="2"></rect>
                                            <path d="M7 11V7a5 5 0 0 1 9 0"></path>
                                        </svg>
                                    @endif
                                </button>
                            @endauth
                        </div>

                        {{-- Contenido --}}
                        <div class="p-5">
                            <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $h->nombre }}</h3>

                            @if ($h->descripcion)
                                <p class="text-sm text-gray-600 mb-3 leading-relaxed">{{ $h->descripcion }}</p>
                            @endif

                            <div class="space-y-2 mb-4">
                                <p class="text-sm text-gray-600 flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    Capacidad: <span class="font-semibold ml-1">{{ $h->capacidad }}</span>
                                </p>
                                <p class="text-sm text-gray-600 flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                    Tipo: <span class="font-semibold ml-1 capitalize">{{ $h->tipo }}</span>
                                </p>
                                <p class="text-sm text-gray-600 flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    Modo: <span
                                        class="font-semibold ml-1 capitalize">{{ str_replace('_', ' ', $h->modo_reserva) }}</span>
                                </p>
                            </div>

                            @auth
                                <div class="flex gap-2 mb-4">
                                    <button @click="habitacionEditar = {{ json_encode($h) }}; modalEditar = true"
                                        class="flex-1 px-3 py-2 rounded-lg 
                                        bg-gray-50 hover:bg-gray-100 
                                        text-gray-700 text-sm font-semibold
                                        border border-gray-200
                                        transition-all duration-200">
                                        Editar
                                    </button>
                                    <button
                                        @click="habitacionId = {{ $h->id }}; setImagenes({{ json_encode($h->imagenes) }}); modalImagenes = true"
                                        class="flex-1 px-3 py-2 rounded-lg 
                                        bg-gray-50 hover:bg-gray-100 
                                        text-gray-700 text-sm font-semibold
                                        border border-gray-200
                                        transition-all duration-200">
                                        Imágenes
                                    </button>
                                    <form action="{{ route('habitaciones.destroy', $h->id) }}" method="POST"
                                        class="delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="px-3 py-2 rounded-lg 
                                            bg-red-50 hover:bg-red-100 
                                            text-red-600 text-sm font-semibold
                                            border border-red-200
                                            transition-all duration-200 delete-btn">
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            @endauth

                            {{-- Calendario --}}
                            <div id="calendar-{{ $h->id }}"
                                class="rounded-lg overflow-hidden border border-gray-100 min-h-[400px]"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales-all.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Confirmaciones SweetAlert2 --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.delete-btn').forEach(btn => {
                btn.closest('form').addEventListener('submit', async (e) => {
                    e.preventDefault();
                    const form = e.currentTarget;

                    const result = await Swal.fire({
                        title: '¿Estás seguro?',
                        text: 'Esta acción no se puede deshacer',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#EF4444',
                        cancelButtonColor: '#6B7280'
                    });

                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>

    {{-- Calendarios por habitación --}}
    <script>
        function formatoFechaBonita(inicioStr, finStr) {
            const meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre',
                'Octubre', 'Noviembre', 'Diciembre'
            ];
            const inicio = new Date(inicioStr);
            const fin = new Date(finStr);
            const di = inicio.getDate(),
                df = fin.getDate();
            const mi = meses[inicio.getMonth()],
                mf = meses[fin.getMonth()];
            const ai = inicio.getFullYear(),
                af = fin.getFullYear();
            if (mi === mf && ai === af) return `Del ${di} al ${df} de ${mi} de ${ai}`;
            if (ai === af) return `Del ${di} de ${mi} al ${df} de ${mf} de ${ai}`;
            return `Del ${di} de ${mi} de ${ai} al ${df} de ${mf} de ${af}`;
        }

        document.addEventListener('DOMContentLoaded', function() {
            @foreach ($habitaciones as $h)
                (function() {
                    const el = document.getElementById('calendar-{{ $h->id }}');
                    if (!el) return;

                    const urlEventos = @json(route('habitaciones.eventos', $h));
                    const urlPrecio = @json(route('habitaciones.precio', $h));
                    const modo = @json($h->modo_reserva);
                    const esPorCama = (modo === 'por_cama');
                    const capacidad = @json($h->capacidad);
                    const nombre = @json($h->nombre);
                    const habId = @json($h->id);

                    const cal = new FullCalendar.Calendar(el, {
                        initialView: 'dayGridMonth',
                        contentHeight: 'auto',
                        height: 'auto',
                        headerToolbar: {
                            left: 'title',
                            right: 'prev,next'
                        },
                        firstDay: 1,
                        weekends: true,
                        locale: 'es',
                        selectable: true,
                        selectMirror: true,
                        events: urlEventos,
                        select: function(info) {
                            const inicio = info.startStr;

                            let finDate = new Date(info.end || info.start);
                            if (finDate <= new Date(inicio)) {
                                finDate = new Date(inicio);
                                finDate.setDate(finDate.getDate() + 1);
                            }

                            const fin = finDate.toLocaleDateString('sv-SE');
                            let camas = capacidad;

                            const hacerReserva = () => {
                                fetch(`${urlPrecio}?inicio=${inicio}&fin=${fin}&camas=${camas}`)
                                    .then(r => r.json())
                                    .then(data => {
                                        if (data.error) {
                                            Swal.fire('Error', data.error, 'error');
                                            return;
                                        }
                                        const total = data.total;
                                        Swal.fire({
                                            title: 'Confirmar reserva',
                                            html: `
                                                <p><strong>Habitación:</strong> ${nombre}</p>
                                                <p><strong>Fechas:</strong> ${formatoFechaBonita(inicio, fin)}</p>
                                                ${esPorCama ? `<p><strong>Camas:</strong> ${camas}</p>` : `<p><strong>Huéspedes:</strong> ${camas}</p>`}
                                                <p><strong>Total:</strong> ${Number(total).toFixed(2)} €</p>
                                            `,
                                            icon: 'question',
                                            showCancelButton: true,
                                            confirmButtonText: 'Reservar',
                                            cancelButtonText: 'Cancelar',
                                            confirmButtonColor: '#1F2937',
                                            cancelButtonColor: '#6B7280'
                                        }).then(res => {
                                            if (res.isConfirmed) {
                                                const params = new URLSearchParams({
                                                    habitacion_id: habId,
                                                    entrada: inicio,
                                                    salida: fin,
                                                    huespedes: camas
                                                });
                                                window.location.href =
                                                    `{{ route('reservas.create') }}?${params.toString()}`;
                                            }
                                        });
                                    })
                                    .catch(() => Swal.fire('Error',
                                        'No se pudo calcular el precio.', 'error'));
                            };

                            if (esPorCama) {
                                Swal.fire({
                                    title: '¿Cuántas camas?',
                                    input: 'number',
                                    inputAttributes: {
                                        min: 1,
                                        max: capacidad
                                    },
                                    inputValue: 1,
                                    confirmButtonText: 'Continuar',
                                    showCancelButton: true,
                                    cancelButtonText: 'Cancelar',
                                    confirmButtonColor: '#1F2937',
                                    cancelButtonColor: '#6B7280',
                                    inputValidator: v => (!v || v < 1 || v > capacidad) ?
                                        'Introduce una cantidad válida de camas.' :
                                        undefined
                                }).then(res => {
                                    if (res.isConfirmed) {
                                        camas = parseInt(res.value);
                                        hacerReserva();
                                    }
                                });
                            } else {
                                Swal.fire({
                                    title: 'Número de huéspedes',
                                    html: `<p>Capacidad máxima: <strong>${capacidad}</strong> personas</p>`,
                                    input: 'number',
                                    inputAttributes: {
                                        min: 1,
                                        max: capacidad
                                    },
                                    inputValue: 1,
                                    confirmButtonText: 'Continuar',
                                    showCancelButton: true,
                                    cancelButtonText: 'Cancelar',
                                    confirmButtonColor: '#1F2937',
                                    cancelButtonColor: '#6B7280',
                                    inputValidator: v => (!v || v < 1 || v > capacidad) ?
                                        'Introduce un número válido de huéspedes.' :
                                        undefined
                                }).then(res => {
                                    if (res.isConfirmed) {
                                        camas = parseInt(res.value);
                                        hacerReserva();
                                    }
                                });
                            }
                        }
                    });

                    cal.render();
                })();
            @endforeach
        });
    </script>
    {{-- Toggle bloqueo/desbloqueo de habitación --}}
    <script>
        document.addEventListener('click', async (ev) => {
            const btn = ev.target.closest('.btn-bloqueo');
            if (!btn) return;

            const id = btn.dataset.id;
            const bloqueada = Number(btn.dataset.bloqueada) === 1;
            const accion = bloqueada ? 'desbloquear' : 'bloquear';

            const {
                isConfirmed
            } = await Swal.fire({
                title: `¿Seguro que quieres ${accion} esta habitación?`,
                text: bloqueada ?
                    'Pasará a estar disponible para el público (si cumples tus filtros).' :
                    'Quedará bloqueada para usuarios no autenticados.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, continuar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#1F2937',
                cancelButtonColor: '#6B7280'
            });
            if (!isConfirmed) return;

            try {
                const url = "{{ route('habitaciones.toggle-bloqueo', ':id') }}".replace(':id', id);
                const res = await fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                if (res.status === 401 || res.status === 419) {
                    await Swal.fire('Autenticación requerida', 'Inicia sesión para realizar esta acción.',
                        'warning');
                    return;
                }

                const data = await res.json();
                if (!res.ok || !data.ok) throw new Error(data.message || 'No se pudo actualizar el estado.');

                btn.dataset.bloqueada = data.bloqueada ? 1 : 0;
                pintarCandado(btn, data.bloqueada);

                Swal.fire('Hecho', `La habitación ahora está ${data.bloqueada ? 'bloqueada' : 'desbloqueada'}.`,
                    'success');
            } catch (e) {
                console.error(e);
                Swal.fire('Error', e.message || 'Hubo un problema al cambiar el estado.', 'error');
            }
        });

        function pintarCandado(btn, bloqueada) {
            if (bloqueada) {
                btn.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-600" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
            </svg>`;
            } else {
                btn.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-600" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 9 0"></path>
            </svg>`;
            }
        }
    </script>

</x-app-layout>
