@component('mail::message')
    # ¡Tu pago se ha completado con éxito!

    Hola **{{ $reserva->cliente->nombre ?? 'viajero' }}**,
    ¡gracias por confirmar tu estancia en **Casa Cortijo Olivar**!

    ---

    **Detalles de tu reserva:**

    - 🏨 **Habitación:** {{ $reserva->habitacion->nombre }}
    - 📅 **Entrada:** {{ \Carbon\Carbon::parse($reserva->fecha_entrada)->format('d/m/Y') }}
    - 📅 **Salida:** {{ \Carbon\Carbon::parse($reserva->fecha_salida)->format('d/m/Y') }}
    - 👥 **Huéspedes:** {{ $reserva->personas }}
    - 💰 **Importe pagado:** {{ number_format($reserva->precio_total, 2) }} €

    ---

    Tu reserva está **confirmada y pagada**.
    Te esperamos con los brazos abiertos el día
    **{{ \Carbon\Carbon::parse($reserva->fecha_entrada)->translatedFormat('d F Y') }}**.

    @component('mail::button', ['url' => route('reservas.show', $reserva->id)])
        Ver mi reserva
    @endcomponent

    Si tienes cualquier duda o necesitas modificar algo, contáctanos en
    📧 **info@casacortijoolivar.es** o ☎️ **+34 600 123 456**

    Gracias por confiar en nosotros,
    **El equipo de Casa Cortijo Olivar**
@endcomponent
