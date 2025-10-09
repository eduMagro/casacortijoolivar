<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\PrecioDia;

class Habitacion extends Model
{
    protected $table = 'habitaciones';

    protected $fillable = [
        'nombre',
        'tipo',
        'modo_reserva',
        'capacidad',
        'precio_noche',
        'estado',
        'descripcion',
        'imagen',
    ];

    // Relaciones
    public function reservas()
    {
        return $this->hasMany(Reserva::class);
    }


    public function camasLibresEn($fechaEntrada, $fechaSalida)
    {
        if ($this->modo_reserva === 'completa') {
            // Si tiene alguna reserva que se solape, no hay camas libres
            $hayReserva = $this->reservas()
                ->where(function ($query) use ($fechaEntrada, $fechaSalida) {
                    $query->whereBetween('fecha_entrada', [$fechaEntrada, $fechaSalida])
                        ->orWhereBetween('fecha_salida', [$fechaEntrada, $fechaSalida])
                        ->orWhere(function ($q) use ($fechaEntrada, $fechaSalida) {
                            $q->where('fecha_entrada', '<=', $fechaEntrada)
                                ->where('fecha_salida', '>=', $fechaSalida);
                        });
                })
                ->exists();

            return $hayReserva ? 0 : $this->capacidad;
        }

        // Si es por cama, restamos las ocupadas
        $entrada = \Carbon\Carbon::parse($fechaEntrada)->startOfDay();
        $salida  = \Carbon\Carbon::parse($fechaSalida)->endOfDay();

        $personasOcupadas = $this->reservas()
            ->where(function ($query) use ($entrada, $salida) {
                $query->whereBetween('fecha_entrada', [$entrada, $salida])
                    ->orWhereBetween('fecha_salida', [$entrada, $salida])
                    ->orWhere(function ($q) use ($entrada, $salida) {
                        $q->where('fecha_entrada', '<=', $entrada)
                            ->where('fecha_salida', '>=', $salida);
                    });
            })
            ->sum('personas');

        return max(0, $this->capacidad - $personasOcupadas);
    }


    public function calcularPrecioTotal($entrada, $salida)
    {
        $total = 0;
        $fechaActual = Carbon::parse($entrada);
        $fechaFin = Carbon::parse($salida);

        while ($fechaActual->lt($fechaFin)) {
            $precioDia = PrecioDia::where('habitacion_id', $this->id)
                ->where('fecha', $fechaActual->format('Y-m-d'))
                ->value('precio');

            $total += $precioDia ?? $this->precio_noche;

            $fechaActual->addDay();
        }

        return $total;
    }
}
