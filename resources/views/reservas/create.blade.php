<x-app-layout>
    <div class="max-w-3xl mx-auto py-8 px-4 bg-white rounded shadow">
        <h2 class="text-2xl font-bold mb-6">Confirmar Reserva</h2>

        {{-- Resumen --}}
        <div class="mb-6 space-y-1 text-sm">
            <p><strong>Habitación:</strong> {{ $habitacion->nombre }}</p>
            <p><strong>Tipo:</strong> {{ ucfirst($habitacion->tipo) }}</p>
            <p><strong>Fechas:</strong> {{ $entrada }} al {{ $salida }}</p>
            <p><strong>Total:</strong> {{ number_format($habitacion->precio_total, 2) }} €</p>
        </div>
        <button type="button" id="btn-autocompletar"
            class="mb-4 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            Autocompletar huéspedes
        </button>

        <form action="{{ route('pago.reserva') }}" method="POST" id="form-pago" class="space-y-8">
            @csrf

            {{-- Hidden inputs --}}
            <input type="hidden" name="habitacion_id" value="{{ $habitacion->id }}">
            <input type="hidden" name="entrada" value="{{ $entrada }}">
            <input type="hidden" name="salida" value="{{ $salida }}">
            <input type="hidden" name="total_huespedes" value="{{ $huespedes }}"> {{-- CAMBIADO --}}
            <input type="hidden" name="precio" value="{{ $habitacion->precio_total }}">


            {{-- Titular (Datos de facturación) --}}
            <section class="space-y-4">
                <h3 class="text-lg font-semibold">Datos del Titular (Facturación)</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="nombre_titular" class="block text-sm font-medium text-gray-700">Nombre</label>
                        <input type="text" id="nombre_titular" name="nombre_titular" required
                            value="{{ old('nombre_titular') }}"
                            class="mt-1 block w-full border border-gray-300 rounded px-3 py-2">
                        @error('nombre_titular')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" id="email" name="email" required value="{{ old('email') }}"
                            class="mt-1 block w-full border border-gray-300 rounded px-3 py-2">
                        @error('email')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="apellido1_titular" class="block text-sm font-medium text-gray-700">Primer
                            apellido</label>
                        <input type="text" id="apellido1_titular" name="apellido1_titular"
                            value="{{ old('apellido1_titular') }}"
                            class="mt-1 block w-full border border-gray-300 rounded px-3 py-2">
                        @error('apellido1_titular')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="apellido2_titular" class="block text-sm font-medium text-gray-700">Segundo
                            apellido</label>
                        <input type="text" id="apellido2_titular" name="apellido2_titular"
                            value="{{ old('apellido2_titular') }}"
                            class="mt-1 block w-full border border-gray-300 rounded px-3 py-2">
                        @error('apellido2_titular')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="dni_titular" class="block text-sm font-medium text-gray-700">DNI / Pasaporte</label>
                        <input type="text" id="dni_titular" name="dni_titular" value="{{ old('dni_titular') }}"
                            class="mt-1 block w-full border border-gray-300 rounded px-3 py-2">
                        @error('dni_titular')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="nacionalidad_titular"
                            class="block text-sm font-medium text-gray-700">Nacionalidad</label>
                        <select id="nacionalidad_titular" name="nacionalidad_titular"
                            class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 bg-white text-gray-900">
                            <option value="">Selecciona una nacionalidad</option>
                            @foreach ($nacionalidades as $nac)
                                <option value="{{ $nac }}"
                                    {{ old('nacionalidad_titular') == $nac ? 'selected' : '' }}>
                                    {{ $nac }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                    <div>
                        <label for="edad_titular" class="block text-sm font-medium text-gray-700">Edad</label>
                        <input type="number" id="edad_titular" name="edad_titular" min="0" max="120"
                            value="{{ old('edad_titular') }}"
                            class="mt-1 block w-full border border-gray-300 rounded px-3 py-2">
                        @error('edad_titular')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex items-end">
                        <p class="text-xs text-gray-500">Estos datos se usarán para la factura y control de huéspedes.
                        </p>
                    </div>
                </div>
            </section>

            {{-- Huéspedes --}}
            <section class="space-y-4">
                <h3 class="text-lg font-semibold">Datos de los {{ $huespedes }} huésped(es)</h3>
                <div class="mb-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" id="copiar-titular-huesped" class="form-checkbox h-5 w-5 text-blue-600">
                        <span class="ml-2 text-sm text-gray-700">El titular también es uno de los huéspedes</span>
                    </label>
                </div>

                @for ($i = 1; $i <= $huespedes; $i++)
                    <div class="border p-4 rounded-md bg-gray-50 space-y-3">
                        <p class="text-sm font-medium">Huésped {{ $i }}</p>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm text-gray-700">Nombre</label>
                                <input type="text" name="huespedes[{{ $i }}][nombre]" required
                                    value="{{ old("huespedes.$i.nombre") }}"
                                    class="mt-1 block w-full border border-gray-300 rounded px-3 py-2">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-700">Primer apellido</label>
                                <input type="text" name="huespedes[{{ $i }}][apellido1]" required
                                    value="{{ old("huespedes.$i.apellido1") }}"
                                    class="mt-1 block w-full border border-gray-300 rounded px-3 py-2">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-700">Segundo apellido</label>
                                <input type="text" name="huespedes[{{ $i }}][apellido2]" required
                                    value="{{ old("huespedes.$i.apellido2") }}"
                                    class="mt-1 block w-full border border-gray-300 rounded px-3 py-2">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm text-gray-700">DNI / Pasaporte</label>
                                <input type="text" name="huespedes[{{ $i }}][dni]" required
                                    value="{{ old("huespedes.$i.dni") }}"
                                    class="mt-1 block w-full border border-gray-300 rounded px-3 py-2">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-700">Nacionalidad</label>
                                <select name="huespedes[{{ $i }}][nacionalidad]"
                                    class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 bg-white text-gray-900"
                                    required>
                                    <option value="">Selecciona una nacionalidad</option>
                                    @foreach ($nacionalidades as $nac)
                                        <option value="{{ $nac }}"
                                            {{ old("huespedes.$i.nacionalidad") == $nac ? 'selected' : '' }}>
                                            {{ $nac }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm text-gray-700">Edad</label>
                                <input type="number" name="huespedes[{{ $i }}][edad]" min="0"
                                    max="120" required value="{{ old("huespedes.$i.edad") }}"
                                    class="mt-1 block w-full border border-gray-300 rounded px-3 py-2">
                            </div>
                        </div>
                    </div>
                @endfor
            </section>

            {{-- Pago con tarjeta --}}
            <section class="space-y-3">
                <h3 class="text-lg font-semibold">Pago con Tarjeta</h3>
                <div id="card-element" class="border p-4 rounded bg-gray-50"></div>
                <div id="card-errors" class="text-sm text-red-600 mt-1"></div>
                <p class="text-xs text-gray-500">Usa tarjeta de prueba de Stripe (p. ej. 4242 4242 4242 4242) si estás
                    en modo test.</p>
            </section>

            {{-- Botón de pago --}}
            <div>
                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Pagar y Reservar
                </button>
            </div>
        </form>
    </div>

    {{-- Stripe --}}
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        const stripe = Stripe('{{ env('STRIPE_KEY') }}');
        const elements = stripe.elements();
        const card = elements.create('card', {
            style: {
                base: {
                    color: '#111827',
                    fontFamily: '"Inter", system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial',
                    fontSmoothing: 'antialiased',
                    fontSize: '16px',
                    '::placeholder': {
                        color: '#9CA3AF'
                    }
                },
                invalid: {
                    color: '#DC2626',
                    iconColor: '#DC2626'
                }
            }
        });
        card.mount('#card-element');

        const form = document.getElementById('form-pago');
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const {
                token,
                error
            } = await stripe.createToken(card);
            const errorEl = document.getElementById('card-errors');

            if (error) {
                errorEl.textContent = error.message;
                errorEl.classList.remove('hidden');
            } else {
                errorEl.textContent = '';
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'stripeToken';
                input.value = token.id;
                form.appendChild(input);
                form.submit();
            }
        });
    </script>
    <script>
        (function() {
            const checkbox = document.getElementById('copiar-titular-huesped');
            if (!checkbox) return;

            // Mapeo: id del titular => name del primer huésped
            const map = {
                'nombre_titular': 'huespedes[1][nombre]',
                'apellido1_titular': 'huespedes[1][apellido1]',
                'apellido2_titular': 'huespedes[1][apellido2]',
                'dni_titular': 'huespedes[1][dni]',
                'nacionalidad_titular': 'huespedes[1][nacionalidad]',
                'edad_titular': 'huespedes[1][edad]'
            };

            // Guarda listeners para poder removerlos si se desmarca
            const titularListeners = [];

            function copiarDatos() {
                for (const titularId in map) {
                    const titular = document.getElementById(titularId);
                    const huesped = document.querySelector(`[name="${map[titularId]}"]`);
                    if (!titular || !huesped) continue;

                    // Para selects y inputs tratamos igual: asignar value
                    huesped.value = titular.value ?? '';

                    // Si es select y no existe el value en opciones, fallback a ''
                    if (huesped.tagName === 'SELECT') {
                        const hasOption = Array.from(huesped.options).some(opt => opt.value === titular.value);
                        if (!hasOption) huesped.selectedIndex = 0;
                    }
                }
            }

            function limpiarDatos() {
                for (const titularId in map) {
                    const huesped = document.querySelector(`[name="${map[titularId]}"]`);
                    if (!huesped) continue;

                    if (huesped.tagName === 'SELECT') {
                        huesped.selectedIndex = 0;
                    } else {
                        huesped.value = '';
                    }
                }
            }

            function attachTitularSync() {
                // Añadimos listeners 'input' para mantener en sincronía mientras esté marcado
                for (const titularId in map) {
                    const titular = document.getElementById(titularId);
                    if (!titular) continue;

                    const handler = () => {
                        // Solo copiar si sigue marcado
                        if (checkbox.checked) {
                            const huesped = document.querySelector(`[name="${map[titularId]}"]`);
                            if (!huesped) return;
                            huesped.value = titular.value ?? '';
                            if (huesped.tagName === 'SELECT') {
                                const hasOption = Array.from(huesped.options).some(opt => opt.value === titular
                                    .value);
                                if (!hasOption) huesped.selectedIndex = 0;
                            }
                        }
                    };

                    titular.addEventListener('input', handler);
                    titularListeners.push({
                        el: titular,
                        handler
                    });
                }
            }

            function detachTitularSync() {
                titularListeners.forEach(({
                    el,
                    handler
                }) => el.removeEventListener('input', handler));
                titularListeners.length = 0;
            }

            // Handler principal del checkbox
            checkbox.addEventListener('change', function() {
                if (this.checked) {
                    copiarDatos();
                    attachTitularSync();
                } else {
                    limpiarDatos();
                    detachTitularSync();
                }
            });

            // Si al cargar ya está marcado (tests), forzamos copia y sync
            if (checkbox.checked) {
                copiarDatos();
                attachTitularSync();
            }
        })();
    </script>

    <script>
        document.getElementById('btn-autocompletar').addEventListener('click', () => {
            const nombres = ['Ana', 'Luis', 'Carlos', 'Marta', 'Juan', 'Laura', 'David', 'Lucía'];
            const apellidos = ['Gómez', 'Fernández', 'López', 'Pérez', 'Martínez', 'Rodríguez'];
            const nacionalidades = ['Española', 'Italiana', 'Francesa', 'Alemana', 'Argentina', 'Colombiana'];

            const getRandom = (arr) => arr[Math.floor(Math.random() * arr.length)];

            // Encuentra todos los grupos de inputs de huéspedes
            document.querySelectorAll('[name^="huespedes["]').forEach((input) => {
                const name = input.getAttribute('name');

                if (name.includes('[nombre]')) {
                    input.value = getRandom(nombres);
                } else if (name.includes('[apellido1]')) {
                    input.value = getRandom(apellidos);
                } else if (name.includes('[apellido2]')) {
                    input.value = getRandom(apellidos);
                } else if (name.includes('[dni]')) {
                    input.value = 'X' + Math.floor(Math.random() * 90000000 + 10000000);
                } else if (name.includes('[nacionalidad]')) {
                    input.value = getRandom(nacionalidades);
                } else if (name.includes('[edad]')) {
                    input.value = Math.floor(Math.random() * 50 + 18);
                }
            });
            document.getElementById('nombre_titular').value = getRandom(nombres);
            document.getElementById('apellido1_titular').value = getRandom(apellidos);
            document.getElementById('apellido2_titular').value = getRandom(apellidos);
            document.getElementById('dni_titular').value = 'Y' + Math.floor(Math.random() * 90000000 + 10000000);
            document.getElementById('nacionalidad_titular').value = getRandom(nacionalidades);
            document.getElementById('edad_titular').value = Math.floor(Math.random() * 50 + 25);
            document.getElementById('email').value = `${getRandom(nombres).toLowerCase()}@prueba.com`;

        });
    </script>

</x-app-layout>
