<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'check_in_at',
        'check_out_at',
        'personas',
        'estado',
        'notas',
        'precio_total',
        'stripe_payment_intent_id',
        'stripe_payment_status',
        'cancelable_hasta',
    ];

    // 👇 Aquí van los casts (no en $fillable)
    protected $casts = [
        'fecha_entrada'    => 'date',
        'fecha_salida'     => 'date',
        'cancelable_hasta' => 'datetime',
        'check_in_at'      => 'datetime',
        'check_out_at'     => 'datetime',
        'personas'         => 'integer',
        'precio_total'     => 'decimal:2',
        'created_at'       => 'datetime',
        'updated_at'       => 'datetime',
    ];

    /** RELACIONES */
    public function habitacion()
    {
        return $this->belongsTo(Habitacion::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    /** ACCESORES / HELPERS */

    public function getEsCancelableAttribute(): bool
    {
        return $this->cancelable_hasta !== null
            && now()->lessThanOrEqualTo($this->cancelable_hasta);
    }

    public function getEstaCobradaAttribute(): bool
    {
        return $this->stripe_payment_status === 'capturado'
            || $this->estado === 'confirmada';
    }

    public function getDebeCapturarseAttribute(): bool
    {
        if ($this->estado !== 'pendiente' || !$this->fecha_entrada) {
            return false;
        }
        // fecha_entrada ya es Carbon por el cast
        return now()->greaterThanOrEqualTo($this->fecha_entrada->copy()->subDays(7));
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
