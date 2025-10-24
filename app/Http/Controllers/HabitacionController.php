<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Reserva;
use App\Models\Habitacion;
use App\Models\PrecioDia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class HabitacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $habitaciones = Habitacion::with('imagenes')
            ->when(!Auth::check(), fn($query) => $query->visibles())
            ->orderBy('modo_reserva')
            ->orderBy('nombre')
            ->get();

        return view('habitaciones.index', compact('habitaciones'));
    }
    public function toggleBloqueo(\App\Models\Habitacion $habitacion)
    {
        // $this->authorize('update', $habitacion); // opcional si tienes Policy

        try {
            $habitacion->bloqueada = $habitacion->bloqueada ? 0 : 1;
            $habitacion->save();

            return response()->json([
                'ok'           => true,
                'habitacion_id' => $habitacion->id,
                'bloqueada'    => (bool) $habitacion->bloqueada,
            ]);
        } catch (\Throwable $e) {
            \Log::error('[Habitacion] toggleBloqueo error', ['id' => $habitacion->id, 'e' => $e->getMessage()]);
            return response()->json([
                'ok'      => false,
                'message' => 'No se pudo cambiar el estado de bloqueo.'
            ], 500);
        }
    }


    public function eventosDisponibilidad(Request $request, Habitacion $habitacion)
    {
        try {
            $eventos = [];

            // Fechas desde FullCalendar o por defecto el mes actual
            $inicio = $request->query('start')
                ? Carbon::parse($request->query('start'))->startOfDay()
                : now()->startOfMonth();

            $fin = $request->query('end')
                ? Carbon::parse($request->query('end'))->endOfDay()
                : now()->endOfMonth();

            if ($habitacion->modo_reserva === 'completa') {
                $periodo = new \DatePeriod($inicio, new \DateInterval('P1D'), $fin->copy()->addDay());

                foreach ($periodo as $dia) {
                    $fecha = $dia->format('Y-m-d');

                    $hayReserva = $habitacion->reservas()
                        ->whereDate('fecha_entrada', '<=', $fecha)
                        ->whereDate('fecha_salida', '>=', $fecha)
                        ->exists();

                    $eventos[] = [
                        'title' => $hayReserva ? 'Ocupada' : 'Libre',
                        'start' => $fecha,
                        'display' => 'background',
                        'color' => $hayReserva ? '#ef4444' : '#10b981',
                    ];
                }
            } else {
                $periodo = new \DatePeriod($inicio, new \DateInterval('P1D'), $fin->copy()->addDay());

                foreach ($periodo as $dia) {
                    $fecha = $dia->format('Y-m-d');

                    $ocupadas = $habitacion->reservas()
                        ->whereDate('fecha_entrada', '<=', $fecha)
                        ->whereDate('fecha_salida', '>=', $fecha)
                        ->sum('personas');

                    $libres = max(0, $habitacion->capacidad - $ocupadas);

                    $eventos[] = [
                        'title' => $libres . ' libres',
                        'start' => $fecha,
                        'color' => $libres === 0 ? '#ef4444' : '#10b981',
                        'display' => 'background',
                        'textColor' => '#000',
                    ];
                }
            }

            return response()->json($eventos);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => true,
                'message' => 'Error al cargar eventos',
                'exception' => $e->getMessage()
            ], 500);
        }
    }
    public function calcularPrecio(Request $request, Habitacion $habitacion)
    {
        $entrada = $request->query('inicio');
        $salida = $request->query('fin');
        $camas = (int) $request->query('camas', 1);

        if (!$entrada || !$salida) {
            Log::warning('CalcularPrecio: Fechas incompletas', [
                'habitacion_id' => $habitacion->id,
                'inicio' => $entrada,
                'fin' => $salida
            ]);
            return response()->json(['error' => 'Fechas incompletas'], 400);
        }

        try {
            Log::info('CalcularPrecio: Inicio cálculo', [
                'habitacion_id' => $habitacion->id,
                'inicio' => $entrada,
                'fin' => $salida,
                'camas' => $camas,
                'modo_reserva' => $habitacion->modo_reserva
            ]);

            $precioBase = $habitacion->calcularPrecioTotal($entrada, $salida);

            $total = $habitacion->modo_reserva === 'por_cama'
                ? $precioBase * $camas
                : $precioBase;

            Log::info('CalcularPrecio: Resultado', [
                'habitacion_id' => $habitacion->id,
                'total' => $total
            ]);

            return response()->json([
                'total' => round($total, 2),
                'habitacion' => $habitacion->nombre,
                'inicio' => $entrada,
                'fin' => $salida,
            ]);
        } catch (\Throwable $e) {
            Log::error('CalcularPrecio: Excepción', [
                'habitacion_id' => $habitacion->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Error al calcular precio',
                'msg' => $e->getMessage()
            ], 500);
        }
    }

    public function vistaCalendarioPrecios()
    {
        $habitaciones = Habitacion::select('id', 'nombre')->orderBy('nombre')->get();
        return view('habitaciones.calendario-precios', compact('habitaciones'));
    }

    public function precios(Request $request)
    {
        // Normalizamos los parámetros que manda FullCalendar
        $startRaw   = $request->query('start');      // puede venir con T y zona
        $endRaw     = $request->query('end');        // exclusivo
        $recursoRaw = $request->query('resourceId'); // id de habitación (opcional)

        // A Y-m-d, como Dios manda
        $inicio   = $startRaw ? Carbon::parse($startRaw)->toDateString() : null;
        $finExcl  = $endRaw   ? Carbon::parse($endRaw)->toDateString()   : null;
        $finIncl  = $finExcl  ? Carbon::parse($finExcl)->subDay()->toDateString() : null;

        $q = DB::table('precios_dia')
            ->select('id', 'habitacion_id', 'fecha', 'precio');

        if ($inicio && $finIncl) {
            // Comparación de fechas consistente (columna DATE vs Y-m-d)
            $q->whereDate('fecha', '>=', $inicio)
                ->whereDate('fecha', '<=', $finIncl);
        }

        if (!empty($recursoRaw)) {
            $q->where('habitacion_id', $recursoRaw);
        }

        $filas = $q->orderBy('habitacion_id')->orderBy('fecha')->get();

        $eventos = $filas->map(function ($r) {
            // allDay -> end exclusivo = fecha + 1
            $end = Carbon::parse($r->fecha)->addDay()->toDateString();
            return [
                'id'         => (string) $r->id,
                'title'      => number_format((float) $r->precio, 2, ',', '.') . ' €',
                'start'      => $r->fecha,
                'end'        => $end,
                'allDay'     => true,
                'resourceId' => (string) $r->habitacion_id,
                'extendedProps' => [
                    'precio' => (float) $r->precio,
                ],
            ];
        });

        // Debug opcional: /api/precios?...&debug=1 para ver qué estás filtrando
        if ($request->boolean('debug')) {
            Log::debug('precios()', [
                'startRaw' => $startRaw,
                'endRaw'   => $endRaw,
                'inicio'   => $inicio,
                'finIncl'  => $finIncl,
                'resource' => $recursoRaw,
                'count'    => $filas->count(),
            ]);
        }

        return response()->json($eventos);
    }

    // MÉTODO UPDATE (línea 414)
    // Reemplaza el método update que está vacío
    // ========================================

    public function update(Request $request, string $id)
    {
        try {
            $habitacion = Habitacion::findOrFail($id);

            $validated = $request->validate([
                'nombre'        => 'required|string|max:100|unique:habitaciones,nombre,' . $id,
                'descripcion'   => 'nullable|string|max:500',  // ✅ NUEVO
                'capacidad'     => 'required|integer|min:1|max:20',
            ], [
                'nombre.required'         => 'El nombre de la habitación es obligatorio.',
                'nombre.unique'           => 'Ya existe una habitación con ese nombre.',
                'descripcion.max'         => 'La descripción no puede superar los 500 caracteres.',  // ✅ NUEVO
                'capacidad.required'      => 'La capacidad es obligatoria.',
                'capacidad.integer'       => 'La capacidad debe ser un número entero.',
                'capacidad.min'           => 'Debe haber al menos 1 huésped.',
                'capacidad.max'           => 'No se permiten más de 20 huéspedes.',
            ]);

            $habitacion->update($validated);

            return redirect()->route('habitaciones.index')
                ->with('success', 'Habitación actualizada correctamente.');
        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', 'Por favor revisa los campos del formulario.');
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->with('error', 'Error inesperado: ' . $e->getMessage());
        }
    }

    // POST: actualizar un único día
    public function updatePrecio(Request $request)
    {
        $request->validate([
            'id'     => 'required|integer|exists:precios_dia,id',
            'precio' => 'required|numeric|min:0',
        ]);

        $precioDia = PrecioDia::findOrFail($request->id);
        $precioDia->precio = (float) $request->precio;
        $precioDia->save();

        return response()->json(['ok' => true, 'message' => 'Precio actualizado']);
    }

    // POST: actualizar varias fechas para una habitación
    public function bulkUpdate(Request $request)
    {
        $datos = $request->validate([
            'habitacion_id' => 'required|integer|exists:habitaciones,id',
            'fechas'        => 'required|array|min:1',
            'fechas.*'      => 'required|date_format:Y-m-d',
            'precio'        => 'required|numeric|min:0',
        ]);

        $habitacionId = (int) $datos['habitacion_id'];
        $precio = (float) $datos['precio'];
        $fechas = $datos['fechas'];

        $ahora = now();
        $filas = [];
        foreach ($fechas as $f) {
            $filas[] = [
                'habitacion_id' => $habitacionId,
                'fecha'         => $f,
                'precio'        => $precio,
                'created_at'    => $ahora,
                'updated_at'    => $ahora,
            ];
        }

        DB::beginTransaction();
        try {
            // upsert contra precios_dia, con índice único (habitacion_id, fecha)
            DB::table('precios_dia')->upsert(
                $filas,
                ['habitacion_id', 'fecha'],
                ['precio', 'updated_at']
            );

            DB::commit();
            return response()->json(['ok' => true, 'actualizados' => count($filas)]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'ok'      => false,
                'message' => 'Error al actualizar precios en bloque',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nombre'        => 'required|string|max:100|unique:habitaciones',
                'tipo'          => 'required|in:masculina,femenina,mixta',
                'capacidad'     => 'required|integer|min:1|max:20',
                'modo_reserva'  => 'required|in:completa,por_cama',
                'precio_noche'  => 'nullable|numeric|min:0',
                'imagenes.*'    => 'image|max:10240',
            ], [
                'nombre.required'         => 'El nombre de la habitación es obligatorio.',
                'nombre.unique'           => 'Ya existe una habitación con ese nombre.',
                'tipo.required'           => 'Debes seleccionar el tipo de habitación.',
                'tipo.in'                 => 'El tipo de habitación no es válido.',
                'capacidad.required'      => 'La capacidad es obligatoria.',
                'capacidad.integer'       => 'La capacidad debe ser un número entero.',
                'capacidad.min'           => 'Debe haber al menos 1 huésped.',
                'capacidad.max'           => 'No se permiten más de 20 huéspedes.',
                'modo_reserva.required'   => 'Debes seleccionar un modo de reserva.',
                'modo_reserva.in'         => 'El modo de reserva no es válido.',
                'precio_noche.numeric'    => 'El precio debe ser un número.',
                'precio_noche.min'        => 'El precio no puede ser negativo.',
                'imagenes.*.image'        => 'Cada archivo debe ser una imagen válida.',
                'imagenes.*.max'          => 'Cada imagen no puede superar los 10 MB.',
            ]);
        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', 'Por favor revisa los campos del formulario.');
        }

        try {
            DB::beginTransaction();

            // ✅ Crear la habitación
            $habitacion = Habitacion::create($validated);

            // ✅ Guardar múltiples imágenes en la nueva tabla
            if ($request->hasFile('imagenes')) {
                foreach ($request->file('imagenes') as $index => $imagen) {
                    $nombreArchivo = uniqid() . '.' . $imagen->getClientOriginalExtension();
                    $ruta = 'img_habitaciones/' . $nombreArchivo;

                    Storage::disk('public')->putFileAs('img_habitaciones', $imagen, $nombreArchivo);


                    $habitacion->imagenes()->create([
                        'ruta_imagen' => $ruta,
                        'orden' => $index,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('habitaciones.index')
                ->with('success', 'Habitación creada correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Error inesperado: ' . $e->getMessage());
        }
    }

    public function subirImagenes(Request $request, Habitacion $habitacion)
    {
        $request->validate([
            'imagenes.*' => 'image|max:10240', // Máximo 10 MB por imagen
        ]);

        foreach ($request->file('imagenes', []) as $imagen) {
            $ruta = $imagen->store('img_habitaciones', 'public');
            $habitacion->imagenes()->create(['ruta_imagen' => $ruta]);
        }

        return back()->with('success', 'Imágenes añadidas correctamente.');
    }
    public function eliminarImagen($id)
    {
        try {
            // Busca la imagen en la tabla habitaciones_imagenes
            $imagen = \App\Models\HabitacionImagen::findOrFail($id);

            // Borra el archivo físico si existe
            if (Storage::disk('public')->exists($imagen->ruta_imagen)) {
                Storage::disk('public')->delete($imagen->ruta_imagen);
            }

            // Borra el registro de la base de datos
            $imagen->delete();

            return back()->with('success', 'Imagen eliminada correctamente.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Error al eliminar la imagen: ' . $e->getMessage());
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $habitacion = Habitacion::findOrFail($id); // 🔐 más seguro que binding implícito

            if ($habitacion->imagen && Storage::disk('public')->exists($habitacion->imagen)) {
                Storage::disk('public')->delete($habitacion->imagen);
            }

            $habitacion->delete();

            return redirect()->route('habitaciones.index')->with('success', 'Habitación eliminada correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar la habitación: ' . $e->getMessage());
        }
    }

    public function instalaciones()
    {
        // Si quieres pasar datos (ejemplo: habitaciones con piscina o spa)
        $habitaciones = Habitacion::where('tipo', '!=', 'mixta')->get();

        return view('instalaciones.index', compact('habitaciones'));
    }

    public function entorno()
    {
        // Puedes pasar datos de contexto (ejemplo: actividades cercanas)
        $habitaciones = Habitacion::all();

        return view('entorno.index', compact('habitaciones'));
    }

    public function contacto()
    {
        return view('contacto.index');
    }

    public function enviarContacto(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'email' => 'required|email',
            'mensaje' => 'required|string|max:1000',
        ]);

        Mail::raw(
            "Mensaje de: {$request->nombre}\nEmail: {$request->email}\n\n{$request->mensaje}",
            function ($m) use ($request) {
                $m->to('reservas@casacortijoolivar.com', 'Casa Cortijo Olivar')
                    ->subject('Nuevo mensaje de contacto');
            }
        );

        return back()->with('success', 'Gracias por tu mensaje. Te responderemos pronto.');
    }

    // ========================================
    // MÉTODOS PARA HABITACIONCONTROLLER
    // ========================================
    // Añadir estos métodos al archivo app/Http/Controllers/HabitacionController.php

    /**
     * Mostrar la política de privacidad
     */
    public function politicaPrivacidad()
    {
        return view('legal.politicaPrivacidad');
    }

    /**
     * Mostrar la política de cookies
     */
    public function politicaCookies()
    {
        return view('legal.cookies');
    }

    /**
     * Mostrar el aviso legal
     */
    public function avisoLegal()
    {
        return view('legal.avisoLegal');
    }
}
