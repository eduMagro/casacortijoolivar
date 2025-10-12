<x-app-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200 px-4 py-12">
        <div class="bg-white/70 backdrop-blur-xl border border-white/40 rounded-2xl shadow-2xl p-8 w-full max-w-md">

            <h1 class="text-3xl font-extrabold text-center text-gray-800 mb-8 tracking-tight">
                Mis reservas
            </h1>

            {{-- FORMULARIO --}}
            @if (!$reserva)
                <form method="POST" action="{{ route('reservas.buscar') }}" class="space-y-6">
                    @csrf

                    <div class="space-y-2">
                        <label for="localizador" class="block text-sm font-semibold text-gray-700">
                            Código localizador
                        </label>
                        <input type="text" name="localizador" id="localizador" value="{{ old('localizador') }}"
                            class="w-full px-4 py-3 rounded-xl bg-white/80 border border-gray-300 shadow-inner text-gray-800 uppercase focus:ring-4 focus:ring-indigo-200 focus:border-indigo-400 focus:outline-none transition-all duration-200"
                            placeholder="Ejemplo: CCO-3A7F9D">
                    </div>

                    <div class="space-y-2">
                        <label for="email" class="block text-sm font-semibold text-gray-700">
                            Correo electrónico
                        </label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                            class="w-full px-4 py-3 rounded-xl bg-white/80 border border-gray-300 shadow-inner text-gray-800 focus:ring-4 focus:ring-indigo-200 focus:border-indigo-400 focus:outline-none transition-all duration-200"
                            placeholder="tu@email.com">
                    </div>

                    <div class="flex justify-center">
                        <button type="submit"
                            class="px-8 py-3 bg-indigo-600 text-white rounded-xl font-semibold shadow-md hover:bg-indigo-700 hover:shadow-lg active:scale-95 transition-all duration-150">
                            Consultar reserva
                        </button>
                    </div>
                </form>
            @else
                {{-- RESULTADO --}}
                <div
                    class="text-gray-700 space-y-3 text-center bg-white/70 border border-gray-200 rounded-xl p-6 shadow-inner mt-4">
                    <p><strong>Código:</strong> <span class="tracking-widest">{{ $reserva->localizador }}</span></p>
                    <p><strong>Habitación:</strong> {{ $reserva->habitacion->nombre ?? '—' }}</p>
                    <p><strong>Entrada:</strong> {{ \Carbon\Carbon::parse($reserva->fecha_entrada)->format('d/m/Y') }}
                    </p>
                    <p><strong>Salida:</strong> {{ \Carbon\Carbon::parse($reserva->fecha_salida)->format('d/m/Y') }}</p>
                    <p><strong>Estado:</strong>
                        <span
                            class="px-2 py-1 rounded-full text-sm font-medium 
                                {{ $reserva->estado === 'confirmada'
                                    ? 'bg-green-100 text-green-800'
                                    : ($reserva->estado === 'pendiente'
                                        ? 'bg-yellow-100 text-yellow-800'
                                        : 'bg-red-100 text-red-800') }}">
                            {{ ucfirst($reserva->estado) }}
                        </span>
                    </p>
                    <p><strong>Importe total:</strong> {{ number_format($reserva->precio_total, 2) }} €</p>
                </div>

                <div class="mt-6 flex flex-col items-center gap-3">

                    {{-- Mostrar botón solo si no está cancelada --}}
                    @if ($reserva->estado !== 'cancelada')
                        <form id="formCancelar" method="POST" action="{{ route('reservas.cancelar', $reserva->id) }}">
                            @csrf
                            <button type="button" id="btnCancelar"
                                class="px-6 py-2 bg-red-500 text-white rounded-lg font-semibold hover:bg-red-600 transition shadow-md">
                                Cancelar reserva
                            </button>
                        </form>
                    @else
                        <div
                            class="px-4 py-3 bg-red-50 border border-red-200 text-red-800 rounded-lg font-medium text-sm shadow-inner text-center">
                            Esta reserva ya fue cancelada.
                        </div>

                        {{-- Alerta visual --}}
                        <script>
                            Swal.fire({
                                icon: 'info',
                                title: 'Reserva cancelada',
                                text: 'Esta reserva ya fue anulada. No es necesario realizar ninguna acción.',
                                confirmButtonColor: '#6366f1'
                            });
                        </script>
                    @endif

                    <a href="{{ route('reservas.buscar') }}"
                        class="text-indigo-600 hover:text-indigo-800 font-semibold transition">
                        Buscar otra reserva
                    </a>
                </div>

                {{-- SweetAlert confirmación --}}
                @if ($reserva->estado !== 'cancelada')
                    <script>
                        document.getElementById('btnCancelar').addEventListener('click', function() {
                            Swal.fire({
                                title: '¿Cancelar reserva?',
                                text: 'Si confirmas, la reserva será anulada. Esta acción no se puede deshacer.',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#dc2626',
                                cancelButtonColor: '#6b7280',
                                confirmButtonText: 'Sí, cancelar',
                                cancelButtonText: 'No, mantener'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    document.getElementById('formCancelar').submit();
                                }
                            });
                        });
                    </script>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>
