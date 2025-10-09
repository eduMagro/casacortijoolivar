<x-app-layout>
    <x-slot name="title">Reservas</x-slot>

    <div class="py-6">
        <div class="bg-white p-4 rounded shadow">
            <div id="calendario" class="h-[80vh] w-full"></div>
        </div>
    </div>
    <h2 class="text-xl font-bold mt-8 mb-4">Lista de reservas</h2>

    <div class="overflow-x-auto bg-white rounded shadow">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left">Cliente</th>
                    <th class="px-4 py-2 text-left">Habitación</th>
                    <th class="px-4 py-2 text-left">Entrada</th>
                    <th class="px-4 py-2 text-left">Salida</th>
                    <th class="px-4 py-2 text-left">Personas</th>
                    <th class="px-4 py-2 text-left">Estado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($reservas as $reserva)
                    <tr>
                        <td class="px-4 py-2">{{ $reserva->cliente->nombre_completo }}</td>
                        <td class="px-4 py-2">{{ $reserva->habitacion->id ?? '-' }}</td>
                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($reserva->fecha_entrada)->format('d/m/Y') }}</td>
                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($reserva->fecha_salida)->format('d/m/Y') }}</td>
                        <td class="px-4 py-2">{{ $reserva->personas }}</td>
                        <td class="px-4 py-2">{{ ucfirst($reserva->estado) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-2 text-center text-gray-500">No hay reservas registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- ✅ FullCalendar Scheduler -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.8/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales-all.global.min.js"></script>

    {{-- TOOLTIP (opcional) --}}
    <link rel="stylesheet" href="https://unpkg.com/tippy.js@6/dist/tippy.css" />
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6"></script>
    <link rel="stylesheet" href="https://unpkg.com/tippy.js@6/themes/light.css" />

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendario');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
                locale: 'es',
                firstDay: 1,
                buttonText: {
                    today: 'Hoy',
                    month: 'Mes',
                    week: 'Semana',
                    day: 'Día',
                    list: 'Lista'
                },
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek'
                },
                events: '{{ route('api.reservas') }}',
            });
            calendar.render();
        });
    </script>
</x-app-layout>
