<x-app-layout>
    <x-slot name="title">Reservas</x-slot>

    <!-- Barra check-in/out rápido -->
    <div class="mb-4 bg-white rounded-lg border border-gray-200 p-3 flex flex-wrap items-center gap-2">
        <label class="text-sm font-medium text-gray-700">Check-in/out rápido</label>
        <input id="checkin-code" placeholder="Pega localizador (p.ej. CCO-3A7F9D) o URL del QR"
            class="flex-1 min-w-[260px] border border-gray-300 bg-white text-gray-800 rounded-lg px-3 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
        <button id="btn-checkin-enviar" class="px-3 py-2 rounded-md bg-emerald-600 text-white hover:bg-emerald-700">
            Marcar check-in
        </button>
        <button id="btn-checkout-enviar" class="px-3 py-2 rounded-md bg-slate-700 text-white hover:bg-slate-800">
            Marcar check-out
        </button>
        <button id="btn-checkin-escanear" class="px-3 py-2 rounded-md bg-gray-700 text-white hover:bg-gray-800">
            Escanear QR (in)
        </button>
        <button id="btn-checkout-escanear" class="px-3 py-2 rounded-md bg-gray-600 text-white hover:bg-gray-700">
            Escanear QR (out)
        </button>
    </div>

    <!-- Modal escáner -->
    <div id="modal-qr" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/60"></div>
        <div class="relative z-10 max-w-lg mx-auto mt-20 bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="flex items-center justify-between px-4 py-3 border-b">
                <h3 class="font-semibold">Escanear QR de reserva</h3>
                <button id="qr-cerrar" class="text-gray-500 hover:text-gray-800">✕</button>
            </div>
            <div class="p-4">
                <div id="qr-reader" class="rounded-lg overflow-hidden border border-gray-200"></div>
                <p class="text-xs text-gray-500 mt-2">Consejo: usa la cámara trasera en móvil.</p>
                <div id="qr-estado" class="mt-2 text-sm"></div>
            </div>
        </div>
    </div>

    <!-- Librería lector QR -->
    <script src="https://unpkg.com/html5-qrcode@2.3.10/minified/html5-qrcode.min.js"></script>

    <div class="py-6">
        <div class="bg-white p-4 rounded shadow">
            <div id="calendario" class="h-[80vh] w-full"></div>
        </div>

        <!-- Leyenda reservas -->
        <div id="leyenda-reservas" class="mt-4">
            <div class="bg-white rounded-lg border border-gray-200 p-3">
                <div class="text-xs font-semibold text-gray-700 mb-2">Leyenda</div>
                <ul class="flex flex-wrap items-center gap-4 text-sm text-gray-700">
                    <li class="leyenda-item">
                        <span class="leyenda-dot" style="background:#10B981"></span>
                        Confirmada
                    </li>
                    <li class="leyenda-item">
                        <span class="leyenda-dot" style="background:#F59E0B"></span>
                        Pendiente
                    </li>
                    <li class="leyenda-item">
                        <span class="leyenda-dot" style="background:#EF4444"></span>
                        Cancelada
                    </li>
                    <li class="leyenda-item">
                        <span class="leyenda-dot" style="background:#6B7280"></span>
                        Completada
                    </li>
                    <li class="leyenda-item">
                        <span class="leyenda-dot" style="background:#4F46E5"></span>
                        Otras / Sin estado
                    </li>
                    <li class="leyenda-item">
                        <span class="ev-badge">3n · 2p</span>
                        <span class="text-gray-600 text-xs">= 3 noches, 2 personas</span>
                    </li>
                </ul>
            </div>
        </div>

        <style>
            .leyenda-item {
                display: flex;
                align-items: center;
                gap: .5rem;
            }

            .leyenda-dot {
                width: 14px;
                height: 14px;
                border-radius: 4px;
                box-shadow: 0 0 0 1px rgba(0, 0, 0, .08) inset;
            }

            .ev-badge {
                display: inline-flex;
                align-items: center;
                gap: .25rem;
                font-size: 11px;
                font-weight: 600;
                padding: .15rem .45rem;
                border-radius: .375rem;
                background: rgba(0, 0, 0, .06);
            }

            /* Estilo de inputs de filtro sin @apply */
            .filtro-input {
                width: 100%;
                border: 1px solid #D1D5DB;
                background: #fff;
                color: #1F2937;
                border-radius: .5rem;
                padding: .5rem .75rem;
                box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
                font-size: 12px;
            }

            .filtro-input:focus {
                outline: none;
                border-color: #3B82F6;
                box-shadow: 0 0 0 2px rgba(59, 130, 246, .3);
            }
        </style>
    </div>

    <h2 class="text-xl font-bold mt-8 mb-4 flex items-center justify-between">
        Lista de reservas
        <button id="btn-checkins-hoy"
            class="text-xs px-3 py-1 rounded-md bg-gray-600 text-white hover:bg-gray-700 focus:ring-2 focus:ring-gray-400">
            Entradas de hoy
        </button>
    </h2>

    <!-- Popup “Entradas de hoy” -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const todayISO = (() => {
                const d = new Date();
                const y = d.getFullYear();
                const m = String(d.getMonth() + 1).padStart(2, '0');
                const dd = String(d.getDate()).padStart(2, '0');
                return `${y}-${m}-${dd}`;
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
                    const tds = tr.querySelectorAll('td');
                    if (tds.length < 6) return;

                    const entradaISO = tr.dataset?.entrada || toISOFromDMY(tds[2].textContent.trim());
                    if (entradaISO !== todayISO) return;

                    const cliente = tr.dataset?.cliente || tds[0].textContent.trim();
                    const habitacion = tr.dataset?.habitacionNombre || tds[1].textContent.trim();
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
                        </div>`;
                }

                const hoyFmt = todayISO.split('-').reverse().join('/');

                if (window.Swal) {
                    Swal.fire({
                        title: `Entradas de hoy (${hoyFmt})`,
                        html,
                        icon: items && items.length ? 'info' : 'question',
                        width: 700,
                        confirmButtonText: 'Cerrar',
                        showCloseButton: true,
                    });
                } else {
                    alert(`Entradas de hoy (${hoyFmt})`);
                }
            };

            const btn = document.getElementById('btn-checkins-hoy');
            if (btn) btn.addEventListener('click', mostrarPopupEntradas);

            const hayEntradas = (recogerEntradasHoy() || []).length > 0;
            if (hayEntradas) mostrarPopupEntradas();
        });
    </script>

    <!-- Tabla -->
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
                    <th class="p-1 border">
                        <input name="f_cliente" placeholder="Nombre cliente" class="filtro-input" />
                    </th>
                    <th class="p-1 border">
                        <input name="f_habitacion" placeholder="Nombre habitación" class="filtro-input" />
                    </th>
                    <th class="p-1 border">
                        <div class="flex gap-1">
                            <input type="date" name="f_entrada_desde" class="filtro-input" />
                            <input type="date" name="f_entrada_hasta" class="filtro-input" />
                        </div>
                    </th>
                    <th class="p-1 border">
                        <div class="flex gap-1">
                            <input type="date" name="f_salida_desde" class="filtro-input" />
                            <input type="date" name="f_salida_hasta" class="filtro-input" />
                        </div>
                    </th>
                    <th class="p-1 border">
                        <div class="flex gap-1">
                            <input type="number" min="1" name="f_personas_min" placeholder="Mín"
                                class="filtro-input w-1/2" />
                            <input type="number" min="1" name="f_personas_max" placeholder="Máx"
                                class="filtro-input w-1/2" />
                        </div>
                    </th>
                    <th class="p-1 border">
                        <input name="f_estado" placeholder="Estado (p.ej. Confirmada)" class="filtro-input" />
                    </th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                @forelse ($reservas as $reserva)
                    <tr data-row="reserva" data-localizador="{{ $reserva->localizador }}"
                        data-cliente="{{ $reserva->cliente->nombre_completo }}"
                        data-habitacion-id="{{ $reserva->habitacion->id ?? '' }}"
                        data-habitacion-nombre="{{ $reserva->habitacion->nombre ?? '' }}"
                        data-entrada="{{ \Carbon\Carbon::parse($reserva->fecha_entrada)->toDateString() }}"
                        data-salida="{{ \Carbon\Carbon::parse($reserva->fecha_salida)->toDateString() }}"
                        data-personas="{{ $reserva->personas }}" data-estado="{{ strtolower($reserva->estado) }}">
                        <td class="px-4 py-2">{{ $reserva->cliente->nombre_completo }}</td>
                        <td class="px-4 py-2">{{ $reserva->habitacion->nombre ?? '-' }}</td>
                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($reserva->fecha_entrada)->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($reserva->fecha_salida)->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-2">{{ $reserva->personas }}</td>
                        <td class="px-4 py-2">{{ ucfirst($reserva->estado) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-2 text-center text-gray-500">No hay reservas registradas.
                        </td>
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

    <!-- Init calendario -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendario');
            window.reservasCalendar = new FullCalendar.Calendar(calendarEl, {
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
            window.reservasCalendar.render();
        });
    </script>

    <!-- Filtros en vivo -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tabla = document.querySelector('.overflow-x-auto table') || document.querySelector('table');
            const tbody = tabla.querySelector('tbody');
            const filtros = document.getElementById('filtros-reservas');

            const inputs = Array.from(filtros.querySelectorAll('input'));
            const filas = () => Array.from(tbody.querySelectorAll('tr[data-row="reserva"]'));

            const normaliza = (s = '') => String(s).toLowerCase()
                .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                .replace(/\s+/g, ' ').trim();

            const getVal = (name) => filtros.querySelector(`[name="${name}"]`)?.value ?? '';

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
                const vHab = normaliza(getVal('f_habitacion'));
                const vEnDe = getVal('f_entrada_desde');
                const vEnHa = getVal('f_entrada_hasta');
                const vSaDe = getVal('f_salida_desde');
                const vSaHa = getVal('f_salida_hasta');
                const vPerMin = parseInt(getVal('f_personas_min') || '');
                const vPerMax = parseInt(getVal('f_personas_max') || '');
                const vEstado = normaliza(getVal('f_estado'));

                let visibles = 0;

                filas().forEach(tr => {
                    const dCliente = normaliza(tr.dataset.cliente || '');
                    const dHabNombre = normaliza(tr.dataset.habitacionNombre || '');
                    const dEntrada = tr.dataset.entrada || '';
                    const dSalida = tr.dataset.salida || '';
                    const dPers = parseInt(tr.dataset.personas || '0');
                    const dEstado = normaliza(tr.dataset.estado || '');

                    const okCliente = !vCliente || dCliente.includes(vCliente);
                    const okHab = !vHab || dHabNombre.includes(vHab);

                    const okEntrada = (!vEnDe || dEntrada >= vEnDe) && (!vEnHa || dEntrada <= vEnHa);
                    const okSalida = (!vSaDe || dSalida >= vSaDe) && (!vSaHa || dSalida <= vSaHa);

                    const okPerMin = isNaN(vPerMin) || dPers >= vPerMin;
                    const okPerMax = isNaN(vPerMax) || dPers <= vPerMax;

                    const okEstadoF = !vEstado || dEstado.includes(vEstado);

                    const visible = okCliente && okHab && okEntrada && okSalida && okPerMin &&
                        okPerMax && okEstadoF;

                    tr.style.display = visible ? '' : 'none';
                    if (visible) visibles++;
                });

                muestraNoResultados(visibles === 0);
            };

            let t;
            const debounced = () => {
                clearTimeout(t);
                t = setTimeout(filtra, 160);
            };

            inputs.forEach(el => {
                el.addEventListener('input', debounced);
                el.addEventListener('change', debounced);
            });

            filtra();
        });
    </script>

    <!-- Check-in / Check-out (unificado) -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const $ = (s, c = document) => c.querySelector(s);
            const $$ = (s, c = document) => Array.from(c.querySelectorAll(s));

            const URL_IN = '{{ route('reservas.check', ['tipo' => 'in']) }}';
            const URL_OUT = '{{ route('reservas.check', ['tipo' => 'out']) }}';

            let busy = false;
            let modoQR = 'in';

            function toast(type, title) {
                if (window.Swal) {
                    Swal.fire({
                        icon: type,
                        title,
                        timer: 1300,
                        showConfirmButton: false
                    });
                } else {
                    alert(title);
                }
            }

            function pintarEstado(localizador, tipo, fecha) {
                const tr = $$('tbody tr[data-row="reserva"]').find(
                    x => (x.dataset.localizador || '').toUpperCase() === String(localizador || '').toUpperCase()
                );
                if (!tr) return;

                const td = tr.querySelector('td:last-child');
                const hora = (fecha || '').slice(11, 16); // HH:MM

                if (td) {
                    td.innerHTML = (tipo === 'in') ?
                        `<span class="inline-flex items-center gap-1 text-emerald-700 text-xs font-semibold">✓ Check-in ${hora || ''}</span>` :
                        `<span class="inline-flex items-center gap-1 text-slate-700 text-xs font-semibold">↩︎ Check-out ${hora || ''}</span>`;
                }

                tr.style.transition = 'background-color .6s';
                tr.style.backgroundColor = (tipo === 'in') ? '#ecfdf5' : '#eef2ff';
                setTimeout(() => tr.style.backgroundColor = '', 900);
            }

            async function hacerCheck(tipo, code) {
                if (busy) return;
                busy = true;

                ['#btn-checkin-enviar', '#btn-checkin-escanear', '#btn-checkout-enviar',
                    '#btn-checkout-escanear'
                ]
                .forEach(id => $(id)?.setAttribute('disabled', ''));

                try {
                    const url = (tipo === 'in') ? URL_IN : URL_OUT;
                    const resp = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            code
                        })
                    });
                    const data = await resp.json();

                    if (!resp.ok || !data.ok) {
                        throw new Error(data.message || 'Operación no disponible');
                    }

                    toast('success', data.already ?
                        (tipo === 'in' ? 'Ya estaba con check-in' : 'Ya estaba con check-out') :
                        (tipo === 'in' ? 'Check-in realizado' : 'Check-out realizado')
                    );

                    const fecha = (tipo === 'in') ? data.checkin_at : data.checkout_at;
                    pintarEstado(data.localizador, tipo, fecha);
                    window.reservasCalendar?.refetchEvents();

                    return data;
                } finally {
                    busy = false;
                    ['#btn-checkin-enviar', '#btn-checkin-escanear', '#btn-checkout-enviar',
                        '#btn-checkout-escanear'
                    ]
                    .forEach(id => $(id)?.removeAttribute('disabled'));
                }
            }

            // Enter → por defecto, check-in
            $('#checkin-code')?.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    $('#btn-checkin-enviar')?.click();
                }
            });

            // Botones enviar
            $('#btn-checkin-enviar')?.addEventListener('click', async () => {
                const val = $('#checkin-code')?.value?.trim();
                if (!val) return toast('error', 'Introduce un localizador o URL');
                try {
                    await hacerCheck('auto', val);

                    $('#checkin-code').value = '';
                } catch (e) {
                    toast('error', e.message);
                }
            });

            $('#btn-checkout-enviar')?.addEventListener('click', async () => {
                const val = $('#checkin-code')?.value?.trim();
                if (!val) return toast('error', 'Introduce un localizador o URL');
                try {
                    await hacerCheck('out', val);
                    $('#checkin-code').value = '';
                } catch (e) {
                    toast('error', e.message);
                }
            });

            // ====== Modal QR (reutilizado) ======
            let html5QrCode = null,
                lastText = '';

            async function abrirQR() {
                $('#modal-qr').classList.remove('hidden');
                if (!window.Html5Qrcode) {
                    $('#qr-estado').textContent = 'No se pudo cargar el lector.';
                    return;
                }

                html5QrCode = new Html5Qrcode('qr-reader');
                try {
                    const devices = await Html5Qrcode.getCameras();
                    const back = devices.find(d => /back|environment|trasera/i.test(d.label))?.id || devices[0]
                        ?.id;

                    await html5QrCode.start({
                            deviceId: {
                                exact: back
                            }
                        }, {
                            fps: 10,
                            qrbox: 250,
                            rememberLastUsedCamera: true
                        },
                        async decodedText => {
                            if (!decodedText || decodedText === lastText) return;
                            lastText = decodedText;
                            try {
                                await hacerCheck(modoQR, decodedText);
                                try {
                                    new Audio(
                                        'https://actions.google.com/sounds/v1/cartoon/clang_and_wobble.ogg'
                                    ).play();
                                } catch (_) {}
                                setTimeout(cerrarQR, 1200);
                            } catch (e) {
                                $('#qr-estado').innerHTML =
                                    `<span class="text-red-600">✗ ${e.message}</span>`;
                                try {
                                    new Audio(
                                        'https://actions.google.com/sounds/v1/cartoon/wood_plank_flicks.ogg'
                                    ).play();
                                } catch (_) {}
                                setTimeout(() => lastText = '', 1500);
                            }
                        }
                    );
                } catch (e) {
                    $('#qr-estado').textContent = 'No hay cámaras disponibles o no hay permisos.';
                }
            }

            async function cerrarQR() {
                $('#modal-qr').classList.add('hidden');
                if (html5QrCode) {
                    try {
                        await html5QrCode.stop();
                    } catch (_) {}
                    try {
                        await html5QrCode.clear();
                    } catch (_) {}
                    html5QrCode = null;
                }
                lastText = '';
                $('#qr-estado').textContent = '';
            }

            $('#btn-checkin-escanear')?.addEventListener('click', () => {
                modoQR = 'in';
                abrirQR();
            });
            $('#btn-checkout-escanear')?.addEventListener('click', () => {
                modoQR = 'out';
                abrirQR();
            });
            $('#qr-cerrar')?.addEventListener('click', cerrarQR);
            $('#modal-qr')?.addEventListener('click', (e) => {
                if (e.target.id === 'modal-qr') cerrarQR();
            });
        });
    </script>
</x-app-layout>
