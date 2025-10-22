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
    - 🔑 **Localizador:** **{{ $reserva->localizador }}**

    @component('mail::panel')
        <p style="margin:0 0 8px;font-weight:600">Tu código QR</p>
        @if (!empty($qrCid))
            <img src="{{ $qrCid }}" alt="QR de la reserva {{ $reserva->localizador }}" width="160" height="160"
                style="display:block;margin:8px auto;border-radius:8px;">
        @else
            <p>Si no ves el QR, presenta este localizador al llegar:
                <strong>{{ $reserva->localizador }}</strong>
            </p>
        @endif
    @endcomponent

    Tu reserva está **confirmada y pagada**.
    Te esperamos con los brazos abiertos el día
    **{{ \Carbon\Carbon::parse($reserva->fecha_entrada)->translatedFormat('d F Y') }}**.

    Si tienes cualquier duda o necesitas modificar algo, contáctanos en
    📧 **reservas@casacortijoolivar.com**

    Gracias por confiar en nosotros,
    **El equipo de Casa Cortijo Olivar**
@endcomponent
