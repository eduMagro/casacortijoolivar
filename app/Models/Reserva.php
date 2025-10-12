<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Reserva extends Model
{
    protected $table = 'reservas';

    protected $fillable = [
        'localizador',
        'habitacion_id',
        'cliente_id',
        'email',
        'telefono',
        'fecha_entrada',
        'fecha_salida',
        'personas',
        'estado',
        'notas',
        'precio_total',
        'stripe_payment_intent_id',
        'stripe_payment_status',
        'cancelable_hasta',
    ];

    protected $dates = [
        'fecha_entrada',
        'fecha_salida',
        'cancelable_hasta',
        'created_at',
        'updated_at',
    ];

    /** 
     * RELACIONES
     */
    public function habitacion()
    {
        return $this->belongsTo(Habitacion::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * ACCESORES / HELPERS
     */

    // Saber si puede cancelarse aún
    public function getEsCancelableAttribute(): bool
    {
        return $this->cancelable_hasta && now()->lessThanOrEqualTo($this->cancelable_hasta);
    }

    // Saber si ya fue cobrada
    public function getEstaCobradaAttribute(): bool
    {
        return $this->stripe_payment_status === 'capturado' || $this->estado === 'confirmada';
    }

    // Saber si está en plazo de captura (7 días antes)
    public function getDebeCapturarseAttribute(): bool
    {
        return $this->estado === 'pendiente'
            && $this->fecha_entrada
            && now()->greaterThanOrEqualTo(Carbon::parse($this->fecha_entrada)->subDays(7));
    }

    protected static function booted()
    {
        static::creating(function ($reserva) {
            // Genera algo tipo "CCO-3A7F9D"
            do {
                $codigo = 'CCO-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
            } while (self::where('localizador', $codigo)->exists());

            $reserva->localizador = $codigo;
        });
    }
}
/**
 * 💼 COMANDO AUTOMÁTICO: RESERVAS:CAPTURAR
 * -----------------------------------------
 * Cada día a las 03:00 se ejecuta el comando 'reservas:capturar' que revisa
 * las reservas con estado 'pendiente' cuyo check-in es en 7 días, e intenta
 * capturar el pago pendiente a través de Stripe.
 *
 * Esto asegura que las reservas se confirmen automáticamente si el pago se
 * realiza correctamente, mejorando la eficiencia del proceso de reserva.
 *
 * El comando está programado en routes/console.php usando el scheduler de
 * Laravel, y registra su actividad en storage/logs/scheduler.log.
 *
 * Puedes probar el comando manualmente con:
 * php artisan reservas:capturar
 *
 * O listar todas las tareas programadas con:
 * php artisan schedule:list
 */
