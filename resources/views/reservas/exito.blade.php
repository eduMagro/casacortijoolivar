<x-app-layout>
    <div class="max-w-2xl mx-auto py-10 px-6 bg-white rounded shadow">
        <h2 class="text-2xl font-bold mb-6 text-green-700">✅ ¡Reserva confirmada!</h2>

        <p class="mb-4">Gracias por tu pago. Se ha confirmado tu reserva.</p>

        <p><strong>Habitación:</strong> {{ session('habitacion') }}</p>
        <p><strong>Fechas:</strong> {{ session('entrada') }} al {{ session('salida') }}</p>
        <p><strong>Total pagado:</strong> {{ session('precio') }} €</p>

        <div class="mt-6">
            <a href="{{ route('inicio') }}"
                class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                Volver al inicio
            </a>
        </div>
    </div>
</x-app-layout>
