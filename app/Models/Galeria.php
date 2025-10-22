<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Galeria extends Model
{
    protected $table = 'galerias';

    protected $fillable = [
        'titulo',
        'descripcion',
        'slug',
        'orden',
        'visible',
    ];

    protected $casts = [
        'visible' => 'boolean',
    ];

    public function imagenes(): HasMany
    {
        return $this->hasMany(GaleriaImagen::class, 'galeria_id')->orderBy('orden')->orderBy('id');
    }
}
