<x-app-layout>
    <x-slot name="title">Habitaciones</x-slot>

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
                <div class="mb-4">
                    <button @click="modalCrear = true"
                        class="bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded">
                        + Nueva habitación
                    </button>
                    <a href="{{ route('habitaciones.calendarioPrecios') }}"
                        class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded shadow">
                        Ver calendario de precios
                    </a>
                </div>
            @endauth

            {{-- Modal CREAR --}}
            <div x-show="modalCrear" x-transition class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
                <div @click.away="modalCrear = false"
                    class="bg-white p-6 rounded-lg shadow-lg w-[90vw] sm:w-[500px] max-w-full relative">
                    <h2 class="text-xl font-bold mb-4">Nueva habitación</h2>

                    <form action="{{ route('habitaciones.store') }}" method="POST" enctype="multipart/form-data"
                        class="space-y-4">
                        @csrf
                        <div>
                            <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                            <input type="text" name="nombre" id="nombre" required
                                class="w-full border-gray-300 rounded mt-1 px-3 py-2 shadow-sm focus:ring-green-500 focus:border-green-500">
                        </div>

                        <div>
                            <label for="capacidad" class="block text-sm font-medium text-gray-700">Capacidad</label>
                            <input type="number" name="capacidad" id="capacidad" min="1" max="20" required
                                class="w-full border-gray-300 rounded mt-1 px-3 py-2 shadow-sm">
                        </div>

                        <div>
                            <label for="tipo" class="block text-sm font-medium text-gray-700">Tipo</label>
                            <select name="tipo" id="tipo" required
                                class="w-full border-gray-300 rounded mt-1 px-3 py-2 shadow-sm">
                                <option value="mixta">Mixta</option>
                                <option value="masculina">Masculina</option>
                                <option value="femenina">Femenina</option>
                            </select>
                        </div>

                        <div>
                            <label for="modo_reserva" class="block text-sm font-medium text-gray-700">Modo de
                                reserva</label>
                            <select name="modo_reserva" id="modo_reserva" required
                                class="w-full border-gray-300 rounded mt-1 px-3 py-2 shadow-sm">
                                <option value="completa">Completa</option>
                                <option value="por_cama">Por cama</option>
                            </select>
                        </div>

                        <div>
                            <label for="imagenes" class="block text-sm font-medium text-gray-700">Subir imágenes</label>
                            <input type="file" name="imagenes[]" multiple accept="image/*"
                                class="w-full mt-1 text-sm">
                        </div>

                        <div class="flex justify-end gap-2 pt-4">
                            <button type="button" @click="modalCrear = false"
                                class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancelar</button>
                            <button type="submit"
                                class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Modal EDITAR --}}
            <div x-show="modalEditar" x-transition
                class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
                <div @click.away="modalEditar = false"
                    class="bg-white p-6 rounded-lg shadow-lg w-[90vw] sm:w-[500px] relative">
                    <h2 class="text-xl font-bold mb-4">Editar habitación</h2>

                    <form :action="`/habitaciones/${habitacionEditar.id}`" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nombre</label>
                            <input type="text" name="nombre" x-model="habitacionEditar.nombre"
                                class="w-full border-gray-300 rounded mt-1 px-3 py-2 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Capacidad</label>
                            <input type="number" name="capacidad" x-model="habitacionEditar.capacidad"
                                class="w-full border-gray-300 rounded mt-1 px-3 py-2 shadow-sm">
                        </div>
                        <div class="flex justify-end gap-2 pt-4">
                            <button type="button" @click="modalEditar = false"
                                class="px-4 py-2 bg-gray-300 rounded">Cancelar</button>
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Modal IMÁGENES --}}
            <div x-show="modalImagenes" x-transition
                class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center overflow-auto">
                <div @click.away="modalImagenes = false"
                    class="bg-white p-6 rounded-lg shadow-lg w-[95vw] sm:w-[600px] max-w-full relative">

                    <h2 class="text-xl font-bold mb-4">Galería de imágenes</h2>

                    <template x-if="habitacionId">
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <template x-for="img in imagenesHabitacion" :key="img.id">
                                    <div class="relative">
                                        <img :src="'{{ asset('storage') }}/' + img.ruta_imagen" class="rounded shadow">

                                        <form :action="'{{ url('habitacion-imagenes') }}/' + img.id" method="POST"
                                            class="absolute top-1 right-1 delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="bg-red-500 text-white rounded-full p-1 text-xs hover:bg-red-700 delete-btn">
                                                ✕
                                            </button>

                                        </form>
                                    </div>
                                </template>
                            </div>

                            {{-- Subida múltiple --}}
                            <form :action="'{{ url('habitaciones') }}/' + habitacionId + '/imagenes'" method="POST"
                                enctype="multipart/form-data" class="space-y-4 border-t pt-4">
                                @csrf
                                <input type="file" name="imagenes[]" multiple accept="image/*"
                                    class="w-full mt-1 text-sm border-gray-300 rounded">
                                <div class="flex justify-end gap-2 pt-4">
                                    <button type="button" @click="modalImagenes = false"
                                        class="px-4 py-2 bg-gray-300 rounded">Cerrar</button>
                                    <button type="submit"
                                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Subir</button>
                                </div>
                            </form>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Tarjetas de habitaciones + Calendarios --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-8">
                @forelse ($habitaciones as $habitacion)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden flex flex-col relative">

                        {{-- Carrusel --}}

                        @if ($habitacion->imagenes->isNotEmpty())
                            <div class="relative h-48 bg-gray-100 flex items-center justify-center text-gray-400"
                                x-data="{
                                    idx: 0,
                                    imgs: {{ $habitacion->imagenes->pluck('ruta_imagen')->toJson() }},
                                    get tieneImagenes() { return this.imgs && this.imgs.length > 0 },
                                    anterior() { if (this.tieneImagenes) this.idx = (this.idx - 1 + this.imgs.length) % this.imgs.length },
                                    siguiente() { if (this.tieneImagenes) this.idx = (this.idx + 1) % this.imgs.length }
                                }">

                                <template x-if="tieneImagenes">
                                    <img :src="'{{ asset('storage') }}/' + imgs[idx]"
                                        class="absolute inset-0 h-48 w-full object-cover rounded transition-all duration-500">

                                </template>

                                <button @click="anterior()" x-show="imgs.length > 1"
                                    class="absolute left-2 top-1/2 -translate-y-1/2 bg-black/50 text-white px-2 py-1 rounded-full">‹</button>
                                <button @click="siguiente()" x-show="imgs.length > 1"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 bg-black/50 text-white px-2 py-1 rounded-full">›</button>
                            </div>
                        @else
                            <div class="h-48 bg-gray-100 flex items-center justify-center text-gray-400">
                                Sin imagen
                            </div>
                        @endif

                        <div class="p-4 flex flex-col gap-2">
                            <h2 class="text-xl font-bold">{{ $habitacion->nombre }}</h2>
                            @auth
                                <button type="button"
                                    class="btn-bloqueo shrink-0 inline-flex items-center justify-center w-9 h-9 rounded-full bg-white shadow border border-gray-200 hover:bg-gray-50"
                                    data-id="{{ $habitacion->id }}" data-bloqueada="{{ (int) $habitacion->bloqueada }}"
                                    title="{{ $habitacion->bloqueada ? 'Habitación bloqueada' : 'Habitación desbloqueada' }}">
                                    @if ($habitacion->bloqueada)
                                        {{-- Candado CERRADO (rojo) --}}
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-600"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="11" width="18" height="11" rx="2"
                                                ry="2"></rect>
                                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                        </svg>
                                    @else
                                        {{-- Candado ABIERTO (verde) --}}
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
                            <p class="text-sm text-gray-600 capitalize">Tipo: {{ $habitacion->tipo }}</p>
                            <p class="text-sm text-gray-600">Capacidad: {{ $habitacion->capacidad }} personas</p>
                            <p class="text-xs text-gray-500">{{ $habitacion->imagenes->count() }} imágenes</p>

                            @auth
                                <div class="mt-2 flex flex-wrap gap-2">
                                    <button @click="habitacionEditar = {{ $habitacion }}, modalEditar = true"
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm">
                                        Editar
                                    </button>
                                    <button
                                        @click="habitacionId = {{ $habitacion->id }}; setImagenes({{ $habitacion->imagenes->toJson() }}); modalImagenes = true"
                                        class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-1 rounded text-sm">
                                        Imágenes
                                    </button>
                                    <form action="{{ route('habitaciones.destroy', $habitacion) }}" method="POST"
                                        class="delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm delete-btn">
                                            Eliminar habitación
                                        </button>
                                    </form>
                                </div>
                            @endauth

                            {{-- Calendario de disponibilidad (debajo de la tarjeta) --}}
                            <div class="mt-4">
                                <div id="cal-{{ $habitacion->id }}" class="text-xs"></div>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-600">No hay habitaciones registradas todavía.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Estilos para ajustar FullCalendar --}}
    <style>
        .fc {
            font-size: 0.75rem !important;
        }

        .fc-toolbar-title {
            font-size: 1rem !important;
            font-weight: bold;
        }

        .fc-event-title {
            display: flex !important;
            justify-content: center;
            align-items: center;
            font-size: 0.9rem !important;
            /* más grande */
            font-weight: 700 !important;
            /* más duro */
            color: #000 !important;
            /* más contraste */
            height: 100%;
            text-align: center;
            opacity: 1 !important;
        }

        /* Por si usas display: 'background', que a veces difumina texto */
        .fc-event.fc-event-background .fc-event-title {
            opacity: 1 !important;
        }
    </style>

    {{-- CDNs: FullCalendar + SweetAlert2 (si no los cargas en layout) --}}
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales-all.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Confirmaciones SweetAlert2 --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.delete-btn').forEach(btn => {
                btn.addEventListener('click', e => {
                    e.preventDefault();
                    const form = btn.closest('.delete-form');
                    Swal.fire({
                        title: '¿Seguro?',
                        text: 'Esta acción no se puede deshacer',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then(result => {
                        if (result.isConfirmed) form.submit();
                    });
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
            @foreach ($habitaciones as $habitacion)
                (function() {
                    const el = document.getElementById('cal-{{ $habitacion->id }}');
                    if (!el) return;

                    const urlEventos = @json(route('habitaciones.eventos', $habitacion));
                    const urlPrecio = @json(route('habitaciones.precio', $habitacion));
                    const modo = @json($habitacion->modo_reserva);
                    const esPorCama = (modo === 'por_cama');
                    const capacidad = @json($habitacion->capacidad);
                    const nombre = @json($habitacion->nombre);
                    const habId = @json($habitacion->id);

                    const cal = new FullCalendar.Calendar(el, {
                        initialView: 'dayGridMonth',
                        contentHeight: 'auto',
                        headerToolbar: {
                            left: 'title',
                            right: 'prev,next'
                        },
                        firstDay: 1, // ← lunes como primer día
                        weekends: true, // ← muestra sábado y domingo (por si acaso)
                        locale: 'es',
                        selectable: true,
                        selectMirror: true,
                        events: urlEventos,
                        select: function(info) {
                            const inicio = info.startStr;

                            // fuerza fin: si el usuario selecciona un solo día, aseguramos que la salida sea el día siguiente
                            let finDate = new Date(info.end || info.start);
                            if (finDate <= new Date(inicio)) {
                                finDate = new Date(inicio);
                                finDate.setDate(finDate.getDate() + 1);
                            }

                            // aseguramos formato YYYY-MM-DD local sin zonas
                            const fin = finDate.toLocaleDateString('sv-SE');
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
                                                ${esPorCama ? `<p><strong>Camas:</strong> ${camas}</p>` : ''}
                                                <p><strong>Total:</strong> ${Number(total).toFixed(2)} €</p>
                                            `,
                                            icon: 'question',
                                            showCancelButton: true,
                                            confirmButtonText: 'Reservar',
                                            cancelButtonText: 'Cancelar'
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
            const bloqueada = Number(btn.dataset.bloqueada) === 1; // true si 1
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
                cancelButtonText: 'Cancelar'
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

                // Actualiza dataset e icono
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
