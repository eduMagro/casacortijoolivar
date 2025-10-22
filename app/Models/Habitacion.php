<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\PrecioDia;
use Illuminate\Support\Facades\Log;

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
        'bloqueada',
    ];

    protected $casts = [
        'bloqueada' => 'boolean',
    ];

    // Relaciones
    public function reservas()
    {
        return $this->hasMany(Reserva::class);
    }

    public function imagenes()
    {
        return $this->hasMany(HabitacionImagen::class);
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



    public function calcularPrecioTotal($entrada, $salida, $cantidad = 1)
    {
        $total = 0;
        $fechaActual = Carbon::parse($entrada);
        $fechaFin = Carbon::parse($salida)->startOfDay();

        Log::debug("🧾 Cálculo de precio para habitación {$this->id} ({$this->nombre})", [
            'modo_reserva' => $this->modo_reserva,
            'capacidad' => $this->capacidad,
            'entrada' => $entrada,
            'salida' => $salida,
            'cantidad' => $cantidad,
        ]);

        while ($fechaActual->lt($fechaFin)) {
            $fecha = $fechaActual->format('Y-m-d');

            $precioDia = PrecioDia::where('habitacion_id', $this->id)
                ->whereDate('fecha', $fecha)
                ->value('precio');

            $precioBase = $precioDia ?? $this->precio_noche;

            // Calculamos según el modo
            if ($this->modo_reserva === 'por_cama') {
                $precioFinal = $precioBase * $cantidad;
                $detalle = "{$precioBase} × {$cantidad} camas";
            } else {
                $precioFinal = $precioBase;
                $detalle = "{$precioBase} habitación";
            }

            $total += $precioFinal;

            Log::debug("🗓 Día {$fecha} → {$detalle} = {$precioFinal}");

            $fechaActual->addDay();
        }

        Log::debug("💰 Total calculado para habitación {$this->id}: {$total} €");

        return round($total, 2);
    }

    public function verificarDisponibilidad(string $inicio, string $fin, int $cantidad = 1): array
    {
        $inicio = Carbon::parse($inicio);
        $fin = Carbon::parse($fin);
        $modo = $this->modo_reserva;
        $detalles = [];
        $disponible = true;

        $periodo = CarbonPeriod::create($inicio, $fin->subDay()); // excluye el día de salida

        foreach ($periodo as $dia) {
            $fecha = $dia->format('Y-m-d');

            if ($modo === 'completa') {
                $ocupada = Reserva::where('habitacion_id', $this->id)
                    ->where('estado', '!=', 'cancelada')
                    ->whereDate('fecha_entrada', '<=', $dia)
                    ->whereDate('fecha_salida', '>', $dia)
                    ->exists();

                if ($ocupada) {
                    $disponible = false;
                    $detalles[] = [
                        'fecha' => $fecha,
                        'error' => 'La habitación completa ya está reservada.'
                    ];
                }

                continue;
            }

            if ($modo === 'por_cama') {
                $ocupadas = Reserva::where('habitacion_id', $this->id)
                    ->where('estado', '!=', 'cancelada')
                    ->whereDate('fecha_entrada', '<=', $dia)
                    ->whereDate('fecha_salida', '>', $dia)
                    ->sum('personas');

                $libres = $this->capacidad - $ocupadas;

                if ($cantidad > $libres) {
                    $disponible = false;
                    $detalles[] = [
                        'fecha' => $fecha,
                        'error' => "Solo quedan $libres camas libres y has pedido $cantidad."
                    ];
                }
            }
        }

        if (!$disponible) {
            Log::warning("❌ Reserva rechazada por falta de disponibilidad", [
                'habitacion_id' => $this->id,
                'modo' => $modo,
                'detalles' => $detalles
            ]);
        }

        return [
            'disponible' => $disponible,
            'detalles' => $detalles
        ];
    }

    public function scopeVisibles($query)
    {
        return $query->where('bloqueada', false);
    }

    public function disponibilidadPorFecha(string $fecha): string|int
    {
        // Trae todas las reservas que cruzan con esa fecha exacta
        $reservasEseDia = $this->reservas()
            ->where('fecha_entrada', '<=', $fecha)
            ->where('fecha_salida', '>', $fecha)
            ->get();

        if ($this->modo_reserva === 'completa') {
            return $reservasEseDia->isNotEmpty() ? 'reservada' : 'disponible';
        }

        if ($this->modo_reserva === 'por_cama') {
            $camasOcupadas = $reservasEseDia->sum('personas');
            return max(0, $this->capacidad - $camasOcupadas);
        }

        return 'desconocido';
    }
}
