<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Charge;
use App\Models\Habitacion;
use Stripe\Stripe;
use App\Models\Reserva;
use App\Models\Cliente; // si tienes un modelo para huéspedes
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReservaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reservas = Reserva::with('cliente', 'habitacion')
            ->orderBy('fecha_entrada', 'asc') // más cercanas primero
            ->get();


        return view('reservas.index', compact('reservas'));
    }


    public function apiReservas()
    {
        $reservas = Reserva::with('cliente', 'habitacion')->get();

        $eventos = $reservas->map(function ($reserva) {
            return [
                'title' => $reserva->cliente->nombre_completo . ' - ' . $reserva->habitacion->nombre . ' - ' . $reserva->personas . ' personas',
                'start' => $reserva->fecha_entrada,
                'end'   => Carbon::parse($reserva->fecha_salida)->addDay()->toDateString(), // incluir fecha_salida
                'color' => '#4F46E5',
            ];
        });

        return response()->json($eventos);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $habitacionId = $request->input('habitacion_id');
        $entrada = $request->input('entrada');
        $salida = $request->input('salida');
        $huespedes = $request->input('huespedes'); // 👈 AÑADIMOS ESTO

        $habitacion = Habitacion::findOrFail($habitacionId);
        // Añadir propiedad calculada "precio_total"

        $habitacion->precio_total = $habitacion->calcularPrecioTotal($entrada, $salida);

        return view('reservas.create', compact('habitacion', 'entrada', 'salida', 'huespedes'));
    }


    public function procesarPago(Request $request)
    {


        $request->validate([
            'habitacion_id'   => 'required|exists:habitaciones,id',
            'entrada'         => 'required|date',
            'salida'          => 'required|date|after:entrada',
            'total_huespedes' => 'required|numeric|min:1',
            'precio'          => 'required|numeric|min:0',
            'stripeToken'     => 'required|string',

            'nombre_titular'      => 'required|string|max:100',
            'apellido1_titular'   => 'nullable|string|max:100',
            'apellido2_titular'   => 'nullable|string|max:100',
            'dni_titular'         => 'nullable|string|max:20',
            'nacionalidad_titular' => 'nullable|string|max:100',
            'edad_titular'        => 'nullable|integer|min:0|max:120',
            'email'               => 'nullable|email|max:150',

            'huespedes.*.nombre'        => 'required|string|max:100',
            'huespedes.*.apellido1'     => 'required|string|max:100',
            'huespedes.*.apellido2'     => 'required|string|max:100',
            'huespedes.*.dni'           => 'required|string|max:20',
            'huespedes.*.nacionalidad'  => 'required|string|max:100',
            'huespedes.*.edad'          => 'required|integer|min:0|max:120',
        ], [
            'total_huespedes.required' => 'Debes indicar cuántos huéspedes hay.',
            'total_huespedes.numeric'  => 'El número de huéspedes debe ser un número.',
            'total_huespedes.min'      => 'Debe haber al menos un huésped.',
        ]);

        Stripe::setApiKey(env('STRIPE_SECRET'));
        if (count($request->huespedes ?? []) !== (int) $request->total_huespedes) {
            return back()->with('error', 'El número de huéspedes no coincide con los datos enviados.');
        }

        DB::beginTransaction();
        try {
            // 1) Cobro
            $charge = Charge::create([
                'amount'      => (int) round($request->precio * 100),
                'currency'    => 'eur',
                'description' => 'Reserva habitación ID ' . $request->habitacion_id,
                'source'      => $request->stripeToken,
                'receipt_email' => $request->email,
            ]);

            // 2) Cliente titular
            $cliente = Cliente::firstOrCreate(
                ['email' => $request->email],
                [
                    'nombre'     => $request->nombre_titular,
                    'apellido1'  => $request->apellido1_titular ?? null,
                    'apellido2'  => $request->apellido2_titular ?? null,
                    'dni'        => $request->dni_titular ?? null,
                    'nacionalidad' => $request->nacionalidad_titular ?? null,
                    'edad'       => $request->edad_titular ?? null,
                ]
            );

            // 3) Reserva
            $reserva = Reserva::create([
                'habitacion_id'  => $request->habitacion_id,
                'cliente_id'     => $cliente->id,
                'fecha_entrada'  => $request->entrada,
                'fecha_salida'   => $request->salida,
                'personas'       => (int) $request->total_huespedes,
                'estado'         => 'confirmada',
                'notas'          => 'Reserva pagada por Stripe. Cargo: ' . $charge->id,
            ]);

            // 4) Guardar huéspedes (sin repetir el titular)
            foreach ($request->huespedes as $huesped) {
                if (
                    strtolower($huesped['dni']) !== strtolower($request->dni_titular)
                    || empty($huesped['dni'])
                ) {
                    Cliente::firstOrCreate(
                        ['dni' => $huesped['dni']],
                        $huesped
                    );
                }
            }

            DB::commit();
            return redirect()->route('dashboard')->with('success', 'Reserva completada correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Error en el pago: ' . $e->getMessage());
        }
    }



    public function exito(Request $request)
    {
        $sessionId = $request->get('session_id');

        $session = \Stripe\Checkout\Session::retrieve($sessionId);
        $metadata = $session->metadata;

        // Guardar la reserva
        $reserva = Reserva::create([
            'habitacion_id' => $metadata->habitacion_id,
            'fecha_entrada' => $metadata->entrada,
            'fecha_salida' => $metadata->salida,
            'nombre_titular' => $session->customer_details->name ?? 'Sin nombre',
            'email_titular' => $session->customer_email,
            'precio_total' => $session->amount_total / 100,
            'huespedes' => $metadata->huespedes,
        ]);

        // Guardar en sesión datos para mostrar
        session([
            'habitacion' => 'Habitación #' . $metadata->habitacion_id,
            'entrada' => $metadata->entrada,
            'salida' => $metadata->salida,
            'precio' => number_format($session->amount_total / 100, 2),
        ]);

        return view('reservas.exito');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
