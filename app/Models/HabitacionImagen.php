<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HabitacionImagen extends Model
{
    use HasFactory;
    protected $table = 'habitacion_imagenes';
    protected $fillable = ['habitacion_id', 'ruta_imagen', 'orden'];

    public function habitacion()
    {
        return $this->belongsTo(Habitacion::class);
    }
}
