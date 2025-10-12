<?php

namespace App\Mail;

use App\Models\Reserva;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConfirmacionPagoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reserva;

    /**
     * Crea una nueva instancia del mailable.
     */
    public function __construct(Reserva $reserva)
    {
        $this->reserva = $reserva;
    }

    /**
     * Construye el correo de confirmación de pago.
     */
    public function build()
    {
        return $this->subject('¡Pago confirmado! Gracias por tu reserva en Casa Cortijo Olivar')
            ->markdown('emails.reservas.pago_confirmado')
            ->with([
                'reserva' => $this->reserva,
            ]);
    }
}
