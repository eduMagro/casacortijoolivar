<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
// Modelos
use App\Models\Habitacion;
use App\Models\Reserva;
use App\Models\Cliente;

// Stripe
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\PaymentIntent;

// Emails Confirmación
use Illuminate\Support\Facades\Mail;
use App\Mail\ConfirmacionReservaMail;


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
        $huespedes = $request->input('huespedes');

        $habitacion = Habitacion::findOrFail($habitacionId);
        // ✅ Comprobar disponibilidad antes de continuar
        $disponibilidad = $habitacion->verificarDisponibilidad($entrada, $salida, $huespedes);

        if (!$disponibilidad['disponible']) {
            $errores = collect($disponibilidad['detalles'])
                ->map(fn($d) => "• {$d['fecha']}: {$d['error']}")
                ->implode('<br>');

            return back()->with('error', "No se puede continuar con la reserva:<br>{$errores}");
        }

        // 💰 Calcular precio si hay disponibilidad
        $habitacion->precio_total = $habitacion->calcularPrecioTotal($entrada, $salida, $huespedes);


        $nacionalidades = [
            'Afgana',
            'Alemana',
            'Andorrana',
            'Angoleña',
            'Argentina',
            'Australiana',
            'Austriaca',
            'Belga',
            'Boliviana',
            'Brasileña',
            'Británica',
            'Búlgara',
            'Camerunesa',
            'Canadiense',
            'Chilena',
            'China',
            'Colombiana',
            'Costarricense',
            'Cubana',
            'Danesa',
            'Ecuatoriana',
            'Egipcia',
            'Salvadoreña',
            'Escocesa',
            'Española',
            'Estadounidense',
            'Estonia',
            'Etíope',
            'Filipina',
            'Finlandesa',
            'Francesa',
            'Griega',
            'Guatemalteca',
            'Hondureña',
            'India',
            'Indonesa',
            'Irlandesa',
            'Islandesa',
            'Israelí',
            'Italiana',
            'Japonesa',
            'Letona',
            'Lituana',
            'Luxemburguesa',
            'Malaya',
            'Marroquí',
            'Mexicana',
            'Nicaragüense',
            'Noruega',
            'Neozelandesa',
            'Paquistaní',
            'Panameña',
            'Paraguaya',
            'Peruana',
            'Polaca',
            'Portuguesa',
            'Puertorriqueña',
            'Rumana',
            'Rusa',
            'Sudafricana',
            'Sueca',
            'Suiza',
            'Tailandesa',
            'Tunecina',
            'Turca',
            'Ucraniana',
            'Uruguaya',
            'Venezolana',
            'Vietnamita',
            'Otro'
        ];

        return view('reservas.create', compact('habitacion', 'entrada', 'salida', 'huespedes', 'nacionalidades'));
    }
    /**
     * 💳 SISTEMA DE PAGO — RESERVAS CON PREAUTORIZACIÓN STRIPE
     * --------------------------------------------------------
     *
     * Este controlador implementa un sistema de pago con **retención de fondos**
     * (autorización) en lugar de un cobro inmediato. El objetivo es permitir
     * cancelaciones gratuitas hasta 7 días antes del check-in, y capturar el
     * pago solo cuando el cliente realmente vaya a hospedarse.
     *
     * 🔹 FLUJO GENERAL:
     *
     * 1️⃣  El usuario selecciona habitación, fechas y número de huéspedes.
     *      Se calcula el precio total y se redirige a `reservas.create`.
     *
     * 2️⃣  En la vista `reservas.create`, se completan los datos del titular
     *      y los huéspedes, y se introduce la tarjeta (Stripe Elements).
     *      Stripe genera un `token` que llega a este método.
     *
     * 3️⃣  `procesarPago()`:
     *      - Valida todos los datos enviados.
     *      - Crea o reutiliza el cliente titular en base a su email.
     *      - Crea un **PaymentIntent** en Stripe con `capture_method = manual`.
     *        Esto significa que el dinero NO se cobra todavía; Stripe solo
     *        **bloquea los fondos** en la tarjeta del cliente durante un tiempo.
     *      - Guarda una nueva reserva en estado `pendiente` con:
     *          • `stripe_payment_intent_id`
     *          • `stripe_payment_status = 'autorizado'`
     *          • `cancelable_hasta = (check-in - 7 días)`
     *
     * 4️⃣  CRON AUTOMÁTICO:
     *      - Laravel ejecuta diariamente (por scheduler) un comando
     *        `reservas:capturar` que revisa las reservas con estado `pendiente`.
     *      - Si faltan 7 días o menos para el check-in, el sistema **captura**
     *        (cobra) el pago automáticamente desde Stripe.
     *      - El estado cambia a `confirmada` y el dinero se transfiere
     *        al propietario de la cuenta Stripe.
     *
     * 5️⃣  CANCELACIONES:
     *      - Si el cliente cancela **antes de `cancelable_hasta`**, el sistema
     *        libera el bloqueo (Stripe → `PaymentIntent::cancel()`), sin cargo.
     *        La reserva pasa a `cancelada`.
     *      - Si cancela **después de la fecha límite**, el sistema captura
     *        el pago (Stripe → `PaymentIntent::capture()`) y considera la
     *        cancelación como fuera de plazo (sin reembolso).
     *
     * 🧩 CAMPOS CLAVE EN LA TABLA `reservas`:
     *
     *      - `stripe_payment_intent_id`: ID del intento de pago en Stripe
     *      - `stripe_payment_status`: estado del intento (‘autorizado’, ‘capturado’, ‘cancelado’)
     *      - `cancelable_hasta`: fecha límite para cancelar sin coste
     *      - `precio_total`: importe total de la reserva
     *      - `estado`: (‘pendiente’, ‘confirmada’, ‘cancelada’)
     *
     * ✅ VENTAJAS DEL SISTEMA:
     *    - Evita cobros anticipados y facilita devoluciones automáticas.
     *    - Permite políticas de cancelación personalizadas.
     *    - Cumple con las normas PSD2/SCA (autenticación reforzada).
     *
     * ⚠️ IMPORTANTE:
     *    - Stripe cancela automáticamente las preautorizaciones no capturadas
     *      después de 7 días. Por eso, el comando programado debe ejecutarse
     *      cada noche para capturar los pagos a tiempo.
     *    - El cron se configura en Plesk o servidor con:
     *         php /ruta/a/artisan schedule:run
     */


    public function procesarPago(Request $request)
    {
        try {
            $mensajes = [
                // Campos principales
                'habitacion_id.required' => 'Selecciona una habitación antes de continuar.',
                'habitacion_id.exists'   => 'La habitación seleccionada no existe.',
                'entrada.required'       => 'Indica la fecha de entrada.',
                'entrada.date'           => 'La fecha de entrada no es válida.',
                'salida.required'        => 'Indica la fecha de salida.',
                'salida.date'            => 'La fecha de salida no es válida.',
                'salida.after'           => 'La fecha de salida debe ser posterior a la de entrada.',
                'total_huespedes.required' => 'Debes indicar cuántas personas se alojarán.',
                'total_huespedes.numeric'  => 'El número de huéspedes debe ser un valor numérico.',
                'total_huespedes.min'      => 'Debe haber al menos un huésped.',
                'precio.required'        => 'No se pudo calcular el precio de la reserva.',
                'precio.numeric'         => 'El precio debe ser numérico.',
                'precio.min'             => 'El precio no puede ser negativo.',
                'stripeToken.required'   => 'No se ha podido procesar el pago. Intenta de nuevo.',
                'stripeToken.string'     => 'Error interno en el token de pago.',

                // Titular
                'nombre_titular.required' => 'El nombre del titular es obligatorio.',
                'nombre_titular.max'      => 'El nombre del titular es demasiado largo.',
                'apellido1_titular.max'   => 'El primer apellido del titular es demasiado largo.',
                'apellido2_titular.max'   => 'El segundo apellido del titular es demasiado largo.',
                'dni_titular.max'         => 'El DNI/NIE del titular supera el límite permitido.',
                'nacionalidad_titular.max' => 'La nacionalidad del titular es demasiado larga.',
                'edad_titular.integer'    => 'La edad debe ser un número entero.',
                'edad_titular.max'        => 'Introduce una edad realista (máximo 120).',
                'email.email'             => 'El correo electrónico no tiene un formato válido.',
                'email.max'               => 'El correo electrónico es demasiado largo.',

                // Huéspedes
                'huespedes.*.nombre.required'       => 'Cada huésped debe tener un nombre.',
                'huespedes.*.apellido1.required'    => 'Falta el primer apellido de algún huésped.',
                'huespedes.*.apellido2.required'    => 'Falta el segundo apellido de algún huésped.',
                'huespedes.*.dni.required'          => 'Falta el DNI/NIE de algún huésped.',
                'huespedes.*.nacionalidad.required' => 'Indica la nacionalidad de todos los huéspedes.',
                'huespedes.*.edad.required'         => 'Indica la edad de todos los huéspedes.',
                'huespedes.*.edad.integer'          => 'La edad de los huéspedes debe ser un número entero.',
                'huespedes.*.edad.max'              => 'Alguna edad introducida no es realista (máx. 120 años).',
            ];

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

                'huespedes.*.nombre'       => 'required|string|max:100',
                'huespedes.*.apellido1'    => 'required|string|max:100',
                'huespedes.*.apellido2'    => 'required|string|max:100',
                'huespedes.*.dni'          => 'required|string|max:20',
                'huespedes.*.nacionalidad' => 'required|string|max:100',
                'huespedes.*.edad'         => 'required|integer|min:0|max:120',
            ], $mensajes);
        } catch (ValidationException $e) {
            // Log detallado del fallo de validación
            Log::error('Error de validación en procesarPago', [
                'errores' => $e->errors(),
                'input'   => $request->all(),
                'usuario' => auth()->user()->id ?? 'visitante',
            ]);

            return back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', 'Por favor revisa los campos del formulario. Algunos datos no son válidos.');
        }


        if (count($request->huespedes ?? []) !== (int) $request->total_huespedes) {
            Log::warning('Número de huéspedes inconsistente en procesarPago', [
                'total' => $request->total_huespedes,
                'enviados' => count($request->huespedes ?? []),
            ]);

            return back()->with('error', 'El número de huéspedes no coincide con los datos enviados.');
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));
        DB::beginTransaction();

        try {
            // 1️⃣ Crear cliente titular
            $cliente = Cliente::firstOrCreate(
                ['email' => $request->email],
                [
                    'nombre'       => $request->nombre_titular,
                    'apellido1'    => $request->apellido1_titular,
                    'apellido2'    => $request->apellido2_titular,
                    'dni'          => $request->dni_titular,
                    'nacionalidad' => $request->nacionalidad_titular,
                    'edad'         => $request->edad_titular,
                ]
            );

            $cancelableHasta = Carbon::parse($request->entrada)->subDays(7);
            $hoy = Carbon::today();
            $debeCobrarYa = $hoy->greaterThanOrEqualTo($cancelableHasta);

            // 2️⃣ Crear PaymentIntent
            $paymentIntent = PaymentIntent::create([
                'amount' => (int) round($request->precio * 100),
                'currency' => 'eur',
                'payment_method_types' => ['card'],
                'capture_method' => 'manual',
                'confirm' => true,
                'payment_method_data' => [
                    'type' => 'card',
                    'card' => ['token' => $request->stripeToken],
                ],
                'description' => 'Reserva habitación ID ' . $request->habitacion_id,
                'metadata' => [
                    'habitacion_id' => $request->habitacion_id,
                    'entrada' => $request->entrada,
                    'salida' => $request->salida,
                    'email_cliente' => $request->email,
                ],
            ]);

            // 3️⃣ Crear reserva
            $reserva = Reserva::create([
                'habitacion_id'            => $request->habitacion_id,
                'cliente_id'               => $cliente->id,
                'fecha_entrada'            => $request->entrada,
                'fecha_salida'             => $request->salida,
                'personas'                 => (int) $request->total_huespedes,
                'estado'                   => $debeCobrarYa ? 'confirmada' : 'pendiente',
                'precio_total'             => $request->precio,
                'stripe_payment_intent_id' => $paymentIntent->id,
                'stripe_payment_status'    => $debeCobrarYa ? 'capturado' : 'autorizado',
                'cancelable_hasta'         => $cancelableHasta,
                'notas'                    => 'Pago preautorizado por Stripe. ID: ' . $paymentIntent->id,
            ]);

            // 4️⃣ Capturar pago y correo
            if ($debeCobrarYa) {
                $paymentIntent->capture();
                Mail::to($cliente->email)->send(new \App\Mail\ConfirmacionPagoMail($reserva));
                $mensaje = 'Reserva confirmada y pago realizado con éxito.';
            } else {
                Mail::to($cliente->email)->send(new \App\Mail\ConfirmacionReservaMail($reserva));
                $mensaje = 'Reserva completada correctamente. Pago programado 7 días antes del check-in.';
            }

            // 5️⃣ Guardar huéspedes adicionales
            foreach ($request->huespedes as $huesped) {
                if (
                    strtolower($huesped['dni']) !== strtolower($request->dni_titular)
                    || empty($huesped['dni'])
                ) {
                    Cliente::firstOrCreate(['dni' => $huesped['dni']], $huesped);
                }
            }

            DB::commit();

            Log::info('Reserva creada correctamente', [
                'reserva_id' => $reserva->id,
                'cliente' => $cliente->email,
                'habitacion' => $request->habitacion_id,
                'precio' => $request->precio,
                'estado' => $reserva->estado,
            ]);

            return redirect()->route('dashboard')->with('success', $mensaje);
        } catch (\Throwable $e) {
            DB::rollBack();

            // Log del error completo
            Log::error('Error al procesar pago con Stripe o crear reserva', [
                'mensaje' => $e->getMessage(),
                'archivo' => $e->getFile(),
                'linea' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->all(),
            ]);

            return back()->withInput()->with('error', 'Error en el pago: ' . $e->getMessage());
        }
    }

    public function cancelarReserva($id)
    {
        $reserva = Reserva::findOrFail($id);

        if ($reserva->estado === 'cancelada') {
            return back()->with('info', 'Esta reserva ya estaba cancelada previamente.');
        }

        if (!$reserva->stripe_payment_intent_id) {
            $reserva->update(['estado' => 'cancelada', 'notas' => 'Cancelada manualmente (sin pago asociado)']);
            return back()->with('success', 'Reserva cancelada correctamente.');
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $hoy = Carbon::today();
            $cancelableHasta = Carbon::parse($reserva->cancelable_hasta);

            // ✅ Cancelación gratuita
            if ($hoy->lessThanOrEqualTo($cancelableHasta)) {
                $intent = PaymentIntent::retrieve($reserva->stripe_payment_intent_id);
                $intent->cancel();

                $reserva->update([
                    'estado' => 'cancelada',
                    'stripe_payment_status' => 'cancelado',
                    'notas' => 'Cancelada sin penalización antes de la fecha límite.',
                ]);

                return back()->with('success', 'La reserva fue cancelada sin coste alguno.');
            }

            // ⚠️ Fuera de plazo: cobro completo
            if ($reserva->stripe_payment_status === 'autorizado') {
                $intent = PaymentIntent::retrieve($reserva->stripe_payment_intent_id);
                $intent->capture();

                $reserva->update([
                    'estado' => 'confirmada',
                    'stripe_payment_status' => 'capturado',
                    'notas' => 'El cliente canceló fuera de plazo. Se cobró la penalización completa.',
                ]);

                return back()->with('error', 'La cancelación se realizó fuera del plazo gratuito. El importe fue cobrado.');
            }

            if ($reserva->stripe_payment_status === 'capturado') {
                return back()->with('info', 'El pago ya había sido cobrado anteriormente.');
            }

            return back()->with('info', 'La reserva no se puede cancelar en su estado actual.');
        } catch (\Throwable $e) {
            Log::error("Error al cancelar reserva {$reserva->id}: {$e->getMessage()}");
            return back()->with('error', 'Error al procesar la cancelación: ' . $e->getMessage());
        }
    }

    //Localizar tu reserva

    public function buscar(Request $request)
    {
        $reserva = null;

        if ($request->isMethod('post')) {
            $request->validate([
                'localizador' => 'required|string|max:20',
                'email' => 'required|email',
            ]);

            $reserva = \App\Models\Reserva::where('localizador', strtoupper(trim($request->localizador)))
                ->whereHas('cliente', fn($q) => $q->where('email', $request->email))
                ->first();

            if (!$reserva) {
                return back()
                    ->withInput()
                    ->with('error', 'No se ha encontrado ninguna reserva con esos datos.');
            }
        }

        return view('reservas.mis-reservas', compact('reserva'));
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
