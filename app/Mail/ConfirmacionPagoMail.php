<?php

namespace App\Mail;

use App\Models\Reserva;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

// 👇 imports para QR y embed inline
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Symfony\Component\Mime\Part\DataPart;

class ConfirmacionPagoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reserva;

    public function __construct(Reserva $reserva)
    {
        $this->reserva = $reserva;
    }

    public function build()
    {
        // Contenido del QR: por ahora el localizador (simple y efectivo)
        // Si luego quieres, lo cambiamos por una URL de check-in.
        $textoQr = $this->reserva->localizador;

        // Genera PNG binario del QR
        $png = QrCode::format('png')
            ->size(280)          // tamaño razonable para email
            ->margin(1)
            ->errorCorrection('M')
            ->generate($textoQr);

        // Embebe la imagen como CID para que se vea inline en el correo
        $qrCid = null;
        $this->withSymfonyMessage(function ($message) use ($png, &$qrCid) {
            $qrCid = $message->embed(new DataPart($png, 'reserva-qr.png', 'image/png'));
        });

        return $this->subject('¡Pago confirmado! ' . $this->reserva->localizador . ' · Casa Cortijo Olivar')
            ->markdown('emails.reservas.pago_confirmado', [
                'reserva' => $this->reserva,
                'qrCid'   => $qrCid, // 👈 se usa en la vista
            ]);
    }
}
