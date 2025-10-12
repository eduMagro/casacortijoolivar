@component('mail::message')
    # ¡Tu reserva está confirmada!

    Hola **{{ $reserva->cliente->nombre ?? 'viajero' }}**,
    hemos recibido tu solicitud de reserva para **{{ $reserva->habitacion->nombre }}**.

    ---

    **Detalles de la reserva:**

    - 🏨 **Habitación:** {{ $reserva->habitacion->nombre }}
    - 📅 **Entrada:** {{ \Carbon\Carbon::parse($reserva->fecha_entrada)->format('d/m/Y') }}
    - 📅 **Salida:** {{ \Carbon\Carbon::parse($reserva->fecha_salida)->format('d/m/Y') }}
    - 👥 **Huéspedes:** {{ $reserva->personas }}
    - 💰 **Importe total:** {{ number_format($reserva->precio_total, 2) }} €

    ---

    **Estado:**
    Tu pago ha sido **preautorizado**, pero **aún no se ha cobrado**.
    Podrás cancelar sin coste hasta el
    **{{ \Carbon\Carbon::parse($reserva->cancelable_hasta)->translatedFormat('d F Y') }}**.

    A partir de esa fecha, el sistema cobrará automáticamente el importe y tu reserva quedará confirmada de forma
    definitiva.

    ---

    @component('mail::button', ['url' => route('reservas.show', $reserva->id)])
        Ver mi reserva
    @endcomponent

    Gracias por confiar en **Casa Cortijo Olivar**.
    ¡Te esperamos con ilusión!

    Saludos,
    **El equipo de Casa Cortijo Olivar**
@endcomponent
