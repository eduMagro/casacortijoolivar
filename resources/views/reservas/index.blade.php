<x-app-layout>
    <x-slot name="title">Reservas</x-slot>

    <div class="py-6">
        <div class="bg-white p-4 rounded shadow">
            <div id="calendario" class="h-[80vh] w-full"></div>
        </div>
    </div>

    <h2 class="text-xl font-bold mt-8 mb-4 flex items-center justify-between">
        Lista de reservas
        <button id="btn-checkins-hoy"
            class="text-xs px-3 py-1 rounded-md bg-gray-600 text-white hover:bg-gray-700 focus:ring-2 focus:ring-gray-400">
            Entradas de hoy
        </button>
    </h2>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Helpers
            const todayISO = (() => {
                const d = new Date();
                const y = d.getFullYear();
                const m = String(d.getMonth() + 1).padStart(2, '0');
                const dd = String(d.getDate()).padStart(2, '0');
                return `${y}-${m}-${dd}`; // Local TZ
            })();

            const toISOFromDMY = (txt) => {
                const m = String(txt || '').match(/(\d{2})\/(\d{2})\/(\d{4})/);
                return m ? `${m[3]}-${m[2]}-${m[1]}` : '';
            };

            const esc = (s = '') => String(s)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');

            const tbody = document.querySelector('table tbody');
            if (!tbody) return;

            const recogerEntradasHoy = () => {
                const rows = Array.from(tbody.querySelectorAll('tr'));
                const items = [];

                rows.forEach(tr => {
                    // Ignora filas “vacías” o de aviso
                    const tds = tr.querySelectorAll('td');
                    if (tds.length < 6) return;

                    // Preferimos data-* si existen (más fiables)
                    const entradaISO = tr.dataset?.entrada || toISOFromDMY(tds[2].textContent.trim());
                    if (entradaISO !== todayISO) return;

                    const cliente = tr.dataset?.cliente || tds[0].textContent.trim();
                    const habitacion = tr.dataset?.habitacion || tds[1].textContent.trim();
                    const personas = tr.dataset?.personas || tds[4].textContent.trim();
                    const estado = tr.dataset?.estado || tds[5].textContent.trim();

                    items.push({
                        cliente,
                        habitacion,
                        personas,
                        estado
                    });
                });

                return items;
            };

            const mostrarPopupEntradas = () => {
                const items = recogerEntradasHoy();

                let html;
                if (!items || items.length === 0) {
                    html = `<div class="text-gray-600">No hay clientes con entrada hoy.</div>`;
                } else {
                    const filas = items.map(i => `
                <tr class="border-b">
                    <td class="px-2 py-1 font-medium">${esc(i.cliente)}</td>
                    <td class="px-2 py-1 text-center">${esc(i.habitacion || '-')}</td>
                    <td class="px-2 py-1 text-center">${esc(i.personas)}</td>
                    <td class="px-2 py-1">${esc(i.estado)}</td>
                </tr>
            `).join('');

                    html = `
                <div class="overflow-x-auto">
                    <table class="w-full text-sm border border-gray-200 rounded">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-2 py-1 text-left">Cliente</th>
                                <th class="px-2 py-1 text-center">Hab.</th>
                                <th class="px-2 py-1 text-center">Pers.</th>
                                <th class="px-2 py-1 text-left">Estado</th>
                            </tr>
                        </thead>
                        <tbody>${filas}</tbody>
                    </table>
                </div>
            `;
                }

                const hoyFmt = todayISO.split('-').reverse().join('/'); // dd/mm/yyyy

                Swal.fire({
                    title: `Entradas de hoy (${hoyFmt})`,
                    html,
                    icon: items && items.length ? 'info' : 'question',
                    width: 700,
                    confirmButtonText: 'Cerrar',
                    showCloseButton: true,
                });
            };

            // Botón manual
            const btn = document.getElementById('btn-checkins-hoy');
            if (btn) btn.addEventListener('click', mostrarPopupEntradas);

            // Auto-popup si hay entradas hoy
            const hayEntradas = (recogerEntradasHoy() || []).length > 0;
            if (hayEntradas) mostrarPopupEntradas();
        });
    </script>

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

                {{-- Filtros en vivo --}}
                <tr id="filtros-reservas" class="text-xs uppercase bg-white">
                    {{-- Cliente --}}
                    <th class="p-1 border">
                        <input name="f_cliente" placeholder="Nombre cliente" class="filtro-input" />
                    </th>

                    {{-- Habitación (ID) --}}
                    <th class="p-1 border">
                        <input name="f_habitacion" type="number" min="1" placeholder="ID"
                            class="filtro-input" />
                    </th>

                    {{-- Entrada: desde / hasta --}}
                    <th class="p-1 border">
                        <div class="flex gap-1">
                            <input type="date" name="f_entrada_desde" class="filtro-input" />
                            <input type="date" name="f_entrada_hasta" class="filtro-input" />
                        </div>
                    </th>

                    {{-- Salida: desde / hasta --}}
                    <th class="p-1 border">
                        <div class="flex gap-1">
                            <input type="date" name="f_salida_desde" class="filtro-input" />
                            <input type="date" name="f_salida_hasta" class="filtro-input" />
                        </div>
                    </th>

                    {{-- Personas min / max --}}
                    <th class="p-1 border">
                        <div class="flex gap-1">
                            <input type="number" min="1" name="f_personas_min" placeholder="Mín"
                                class="filtro-input w-1/2" />
                            <input type="number" min="1" name="f_personas_max" placeholder="Máx"
                                class="filtro-input w-1/2" />
                        </div>
                    </th>

                    {{-- Estado --}}
                    <th class="p-1 border">
                        <input name="f_estado" placeholder="Estado (p.ej. Confirmada)" class="filtro-input" />
                    </th>
                </tr>
            </thead>

            {{-- Estilo fino para inputs --}}
            <style>
                .filtro-input {
                    @apply w-full border border-gray-300 bg-white text-gray-800 rounded-lg px-3 py-2 placeholder-gray-400 text-[12px] shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500;
                }
            </style>


            <tbody class="divide-y divide-gray-100">
                @forelse ($reservas as $reserva)
                    <tr data-row="reserva" data-cliente="{{ $reserva->cliente->nombre_completo }}"
                        data-habitacion="{{ $reserva->habitacion->id ?? '' }}"
                        data-entrada="{{ \Carbon\Carbon::parse($reserva->fecha_entrada)->toDateString() }}"
                        data-salida="{{ \Carbon\Carbon::parse($reserva->fecha_salida)->toDateString() }}"
                        data-personas="{{ $reserva->personas }}" data-estado="{{ strtolower($reserva->estado) }}">
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
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tabla = document.querySelector('.overflow-x-auto table') || document.querySelector('table');
            const tbody = tabla.querySelector('tbody');
            const filtros = document.getElementById('filtros-reservas');

            const inputs = Array.from(filtros.querySelectorAll('input'));
            const filas = () => Array.from(tbody.querySelectorAll('tr[data-row="reserva"]'));

            const normaliza = (s = '') =>
                String(s).toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '') // quita acentos
                .replace(/\s+/g, ' ')
                .trim();

            const getVal = (name) => {
                const el = filtros.querySelector(`[name="${name}"]`);
                return el ? el.value : '';
            };

            const muestraNoResultados = (mostrar) => {
                let noRow = tbody.querySelector('#filtro-no-resultados');
                if (mostrar) {
                    if (!noRow) {
                        noRow = document.createElement('tr');
                        noRow.id = 'filtro-no-resultados';
                        noRow.innerHTML = `<td colspan="6" class="px-4 py-3 text-center text-gray-500">
                    No hay reservas que coincidan con los filtros.
                </td>`;
                        tbody.appendChild(noRow);
                    }
                } else if (noRow) {
                    noRow.remove();
                }
            };

            const filtra = () => {
                const vCliente = normaliza(getVal('f_cliente'));
                const vHab = String(getVal('f_habitacion')).trim();
                const vEnDe = getVal('f_entrada_desde');
                const vEnHa = getVal('f_entrada_hasta');
                const vSaDe = getVal('f_salida_desde');
                const vSaHa = getVal('f_salida_hasta');
                const vPerMin = parseInt(getVal('f_personas_min') || ''); // NaN si vacío
                const vPerMax = parseInt(getVal('f_personas_max') || '');
                const vEstado = normaliza(getVal('f_estado'));

                let visibles = 0;

                filas().forEach(tr => {
                    const dCliente = normaliza(tr.dataset.cliente || '');
                    const dHab = String(tr.dataset.habitacion || '').trim();
                    const dEntrada = tr.dataset.entrada || ''; // YYYY-MM-DD
                    const dSalida = tr.dataset.salida || '';
                    const dPers = parseInt(tr.dataset.personas || '0');
                    const dEstado = normaliza(tr.dataset.estado || '');

                    const okCliente = !vCliente || dCliente.includes(vCliente);
                    const okHab = !vHab || dHab === vHab;

                    const okEntrada =
                        (!vEnDe || dEntrada >= vEnDe) &&
                        (!vEnHa || dEntrada <= vEnHa);

                    const okSalida =
                        (!vSaDe || dSalida >= vSaDe) &&
                        (!vSaHa || dSalida <= vSaHa);

                    const okPerMin = isNaN(vPerMin) || dPers >= vPerMin;
                    const okPerMax = isNaN(vPerMax) || dPers <= vPerMax;

                    const okEstado = !vEstado || dEstado.includes(vEstado);

                    const visible = okCliente && okHab && okEntrada && okSalida && okPerMin &&
                        okPerMax && okEstado;

                    tr.style.display = visible ? '' : 'none';
                    if (visible) visibles++;
                });

                muestraNoResultados(visibles === 0);
            };

            // debounce suave
            let t;
            const debounced = () => {
                clearTimeout(t);
                t = setTimeout(filtra, 160);
            };

            inputs.forEach(el => {
                el.addEventListener('input', debounced);
                el.addEventListener('change', debounced);
            });

            // inicial
            filtra();
        });
    </script>

</x-app-layout>
