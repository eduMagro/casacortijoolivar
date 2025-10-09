<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrecioDia extends Model
{
    protected $table = 'precios_dia';
    protected $fillable = ['habitacion_id', 'fecha', 'precio'];
    public $timestamps = true;

    protected $casts = [
        'fecha'  => 'date',
        'precio' => 'float',
    ];
}
