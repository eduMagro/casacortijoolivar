<x-app-layout>
    <x-slot name="title">Calendario de Precios</x-slot>

    <div id="calendario" class="bg-white p-4 shadow rounded-lg"></div>

    <!-- ✅ FullCalendar Scheduler (tu set) -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

    <!-- 👇 NECESARIO para select/dateClick/click en celdas vacías -->
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@6.1.8/index.global.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.8/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales-all.global.min.js"></script>

    <!-- Tooltip (opcional) -->
    <link rel="stylesheet" href="https://unpkg.com/tippy.js@6/dist/tippy.css" />
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6"></script>
    <link rel="stylesheet" href="https://unpkg.com/tippy.js@6/themes/light.css" />

    <!-- ✅ SweetAlert2 para modal de precio -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendario');

            // Helpers en LOCAL (nada de UTC)
            const z = n => String(n).padStart(2, '0');
            const ymd = d => `${d.getFullYear()}-${z(d.getMonth()+1)}-${z(d.getDate())}`;

            function obtenerFechasISO(inicio, finExclusivo) {
                const fechas = [];
                const cursor = new Date(inicio.getFullYear(), inicio.getMonth(), inicio.getDate());
                const limite = new Date(finExclusivo.getFullYear(), finExclusivo.getMonth(), finExclusivo
            .getDate());
                while (cursor < limite) {
                    fechas.push(ymd(cursor)); // ← sin toISOString()
                    cursor.setDate(cursor.getDate() + 1);
                }
                return fechas;
            }

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'resourceTimelineWeek',
                schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
                height: 'auto',
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
                    right: ''
                },
                resourceAreaHeaderContent: 'Habitaciones',
                slotDuration: {
                    days: 1
                },
                slotLabelFormat: [{
                    weekday: 'short',
                    day: 'numeric',
                    month: 'short'
                }],
                slotMinTime: "00:00:00",
                slotMaxTime: "24:00:00",
                weekends: true,
                editable: true,

                // interaction activado
                selectable: true,
                selectMirror: true,
                unselectAuto: true,

                // ✅ NUEVO: click en celda vacía (domingo incluido) para un solo día
                dateClick: async function(info) {
                    const recurso = info.resource;
                    if (!recurso) return;

                    const fecha = ymd(info.date);
                    const {
                        isConfirmed,
                        value
                    } = await Swal.fire({
                        title: 'Precio para un día',
                        html: `<div style="text-align:left">
                                <p><strong>Habitación:</strong> ${recurso.title}</p>
                                <p><strong>Fecha:</strong> ${fecha}</p>
                               </div>`,
                        input: 'number',
                        inputLabel: 'Precio (€)',
                        inputAttributes: {
                            min: '0',
                            step: '0.01',
                            inputmode: 'decimal'
                        },
                        inputAutoTrim: true,
                        showCancelButton: true,
                        confirmButtonText: 'Guardar',
                        cancelButtonText: 'Cancelar',
                        preConfirm: (v) => {
                            const n = parseFloat(String(v).replace(',', '.'));
                            if (isNaN(n)) {
                                Swal.showValidationMessage('Precio inválido.');
                                return false;
                            }
                            if (n < 0) {
                                Swal.showValidationMessage('No negativos.');
                                return false;
                            }
                            return n;
                        }
                    });
                    if (!isConfirmed) return;

                    try {
                        const resp = await fetch(`{{ route('api.precios.bulkUpdate') }}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                habitacion_id: recurso.id,
                                fechas: [fecha],
                                precio: value
                            })
                        });
                        if (!resp.ok) {
                            const err = await resp.json().catch(() => ({}));
                            throw new Error(err.message || 'Error al guardar');
                        }
                        await calendar.refetchEvents();
                        Swal.fire('Listo', `Precio guardado para ${fecha}.`, 'success');
                    } catch (e) {
                        Swal.fire('Error', e.message || 'No se pudo guardar.', 'error');
                    }
                },

                /* Selección arrastrable para cambiar varios días de golpe */
                select: async function(info) {
                    const recurso = info.resource;
                    if (!recurso) {
                        calendar.unselect();
                        return;
                    }

                    const fechas = obtenerFechasISO(info.start, info.end);
                    if (!fechas.length) {
                        calendar.unselect();
                        return;
                    }

                    const rangoTexto = `${fechas[0]} → ${fechas[fechas.length - 1]}`;
                    const {
                        isConfirmed,
                        value
                    } = await Swal.fire({
                        title: 'Nuevo precio',
                        html: `<div style="text-align:left">
                                <p><strong>Habitación:</strong> ${recurso.title}</p>
                                <p><strong>Rango:</strong> ${rangoTexto} (${fechas.length} día(s))</p>
                               </div>`,
                        input: 'number',
                        inputLabel: 'Precio (€)',
                        inputAttributes: {
                            min: '0',
                            step: '0.01',
                            inputmode: 'decimal'
                        },
                        inputAutoTrim: true,
                        showCancelButton: true,
                        confirmButtonText: 'Aplicar',
                        cancelButtonText: 'Cancelar',
                        preConfirm: (v) => {
                            const n = parseFloat(String(v).replace(',', '.'));
                            if (isNaN(n)) {
                                Swal.showValidationMessage('Pon un número decente.');
                                return false;
                            }
                            if (n < 0) {
                                Swal.showValidationMessage(
                                    'El precio no puede ser negativo.');
                                return false;
                            }
                            return n;
                        }
                    });

                    if (!isConfirmed) {
                        calendar.unselect();
                        return;
                    }

                    try {
                        const resp = await fetch(`{{ route('api.precios.bulkUpdate') }}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                habitacion_id: recurso.id,
                                fechas,
                                precio: value
                            })
                        });

                        if (!resp.ok) {
                            const err = await resp.json().catch(() => ({}));
                            throw new Error(err.message || 'Error al actualizar precios');
                        }

                        await calendar.refetchEvents();
                        Swal.fire('Listo', 'Precios aplicados al rango seleccionado.', 'success');
                    } catch (e) {
                        Swal.fire('Error', e.message || 'Algo ha petado, sorpresa.', 'error');
                    } finally {
                        calendar.unselect();
                    }
                },

                /* Click en evento para editar un único día */
                eventClick: function(info) {
                    const nuevoPrecio = prompt(
                        `Nuevo precio para ${info.event.title} (${info.event.startStr}):`,
                        info.event.extendedProps.precio
                    );

                    if (nuevoPrecio !== null) {
                        fetch(`{{ route('api.precios.update') }}`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    id: info.event.id,
                                    precio: nuevoPrecio
                                })
                            })
                            .then(response => {
                                if (response.ok) {
                                    info.event.setProp('title', `${nuevoPrecio} €`);
                                    info.event.setExtendedProp('precio', nuevoPrecio);
                                    alert('Precio actualizado');
                                } else {
                                    alert('Error al actualizar el precio');
                                }
                            });
                    }
                },

                resources: [
                    @foreach ($habitaciones as $habitacion)
                        {
                            id: '{{ $habitacion->id }}',
                            title: '{{ $habitacion->nombre }}'
                        },
                    @endforeach
                ],
                events: {
                    url: '{{ route('api.precios') }}',
                    method: 'GET',
                    failure: () => {
                        alert('Error cargando precios.');
                    }
                }
            });

            calendar.render();
        });
    </script>
</x-app-layout>
