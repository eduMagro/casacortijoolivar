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
                        <input type="text" id="nacionalidad_titular" name="nacionalidad_titular"
                            value="{{ old('nacionalidad_titular') }}"
                            class="mt-1 block w-full border border-gray-300 rounded px-3 py-2">
                        @error('nacionalidad_titular')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
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
                                <input type="text" name="huespedes[{{ $i }}][nacionalidad]" required
                                    value="{{ old("huespedes.$i.nacionalidad") }}"
                                    class="mt-1 block w-full border border-gray-300 rounded px-3 py-2">
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
</x-app-layout>
