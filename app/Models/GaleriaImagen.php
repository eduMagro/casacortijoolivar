<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GaleriaImagen extends Model
{
    protected $table = 'galeria_imagenes';

    protected $fillable = [
        'galeria_id',
        'ruta_imagen',
        'titulo',
        'descripcion',
        'orden',
    ];

    public function galeria(): BelongsTo
    {
        return $this->belongsTo(Galeria::class, 'galeria_id');
    }
}
