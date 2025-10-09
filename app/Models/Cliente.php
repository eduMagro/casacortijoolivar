<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'clientes';

    protected $fillable = [
        'nombre',
        'apellido1',
        'apellido2',
        'email',
        'telefono',
        'dni',
        'direccion',
        'localidad',
        'pais',
        'notas'
    ];

    public function reservas()
    {
        return $this->hasMany(Reserva::class);
    }

    public function getNombreCompletoAttribute()
    {
        $partes = [
            ucfirst(strtolower($this->nombre)),
            ucfirst(strtolower($this->apellido1)),
            ucfirst(strtolower($this->apellido2)),
        ];

        return implode(' ', array_filter($partes));
    }
}
