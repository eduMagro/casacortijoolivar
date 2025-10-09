<x-app-layout>
    <div class="container mx-auto px-4 mt-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Habitaciones disponibles</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($habitaciones as $habitacion)
                <div class="bg-white rounded-lg shadow-md overflow-hidden flex flex-col">
                    @if ($habitacion->imagen)
                        <img src="{{ asset('storage/' . $habitacion->imagen) }}"
                            alt="Imagen habitación {{ $habitacion->nombre }}" class="w-full h-48 object-cover">
                    @endif

                    <div class="p-4 flex-1 flex flex-col justify-between">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-800 mb-1">{{ $habitacion->nombre }}</h3>
                            <p class="text-sm text-gray-600">Tipo: {{ ucfirst($habitacion->tipo) }}</p>
                            <p class="text-sm text-gray-600">Capacidad: {{ $habitacion->capacidad }}</p>
                        </div>

                        {{-- Calendario --}}
                        <div class="mt-4">
                            <div id="calendario-habitacion-{{ $habitacion->id }}" class="text-xs"></div>
                        </div>
                    </div>
                </div>
            @empty
                <p class="col-span-full text-center text-gray-600">No hay habitaciones registradas aún.</p>
            @endforelse
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

    {{-- FullCalendar --}}
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales-all.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @foreach ($habitaciones as $habitacion)
                const elCalendario{{ $habitacion->id }} = document.getElementById(
                    'calendario-habitacion-{{ $habitacion->id }}');

                const urlEventos{{ $habitacion->id }} = `{{ route('habitaciones.eventos', $habitacion) }}`;
                const modoReserva{{ $habitacion->id }} = '{{ $habitacion->modo_reserva }}';
                const nombreHabitacion{{ $habitacion->id }} = '{{ $habitacion->nombre }}';

                const calendario{{ $habitacion->id }} = new FullCalendar.Calendar(
                    elCalendario{{ $habitacion->id }}, {
                        initialView: 'dayGridMonth',
                        contentHeight: 'auto',
                        headerToolbar: {
                            left: 'title',
                            right: 'prev,next'
                        },
                        locale: 'es', // idioma español
                        selectable: true,
                        selectMirror: true,

                        select: function(info) {
                            const inicio = info.startStr;
                            const fin = info.endStr;
                            const esPorCama = modoReserva{{ $habitacion->id }} === 'por_cama';
                            let camas = 1;

                            const urlPrecio = @json(route('habitaciones.precio', $habitacion));

                            const calcularYConfirmar = () => {
                                fetch(`${urlPrecio}?inicio=${inicio}&fin=${fin}&camas=${camas}`)
                                    .then(res => res.json())
                                    .then(data => {
                                        if (data.error) {
                                            Swal.fire('Error', data.error, 'error');
                                            return;
                                        }

                                        const total = esPorCama ? data.total * camas : data.total;

                                        Swal.fire({
                                            title: 'Confirmar reserva',
                                            html: `
                    <p><strong>Habitación:</strong> ${data.habitacion}</p>
                    <p><strong>Desde:</strong> ${data.inicio}</p>
                    <p><strong>Hasta:</strong> ${data.fin}</p>
                    ${esPorCama ? `<p><strong>Camas:</strong> ${camas}</p>` : ''}
                    <p><strong>Total:</strong> ${total.toFixed(2)} €</p>
                `,
                                            icon: 'question',
                                            showCancelButton: true,
                                            confirmButtonText: 'Reservar',
                                            cancelButtonText: 'Cancelar',
                                        }).then(result => {
                                            if (result.isConfirmed) {
                                                Swal.fire('Reservado',
                                                    'La reserva se ha registrado (ficticiamente).',
                                                    'success');
                                            }
                                        });
                                    }).catch(err => {
                                        console.error('Error al llamar a precio:', err);
                                        Swal.fire('Error', 'No se pudo calcular el precio.',
                                            'error');
                                    });
                            };


                            if (esPorCama) {
                                Swal.fire({
                                    title: '¿Cuántas camas?',
                                    input: 'number',
                                    inputAttributes: {
                                        min: 1,
                                        max: {{ $habitacion->capacidad }},
                                    },
                                    inputValue: 1,
                                    confirmButtonText: 'Continuar',
                                    showCancelButton: true,
                                    cancelButtonText: 'Cancelar',
                                    inputValidator: (value) => {
                                        if (!value || value < 1) {
                                            return 'Introduce una cantidad válida.';
                                        }
                                    }
                                }).then(result => {
                                    if (result.isConfirmed) {
                                        camas = parseInt(result.value);
                                        calcularYConfirmar();
                                    }
                                });
                            } else {
                                calcularYConfirmar();
                            }
                        },

                        events: urlEventos{{ $habitacion->id }}
                    });

                calendario{{ $habitacion->id }}.render();
            @endforeach
        });
    </script>
</x-app-layout>
