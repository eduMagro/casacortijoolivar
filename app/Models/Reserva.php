<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    protected $table = 'reservas';

    protected $fillable = [
        'habitacion_id',
        'cliente_id',
        'email',
        'telefono',
        'fecha_entrada',
        'fecha_salida',
        'personas',
        'estado',
        'notas'
    ];

    public function habitacion()
    {
        return $this->belongsTo(Habitacion::class);
    }
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
