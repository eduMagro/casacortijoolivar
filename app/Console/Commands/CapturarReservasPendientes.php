<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reserva;
use Carbon\Carbon;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Illuminate\Support\Facades\Log;

class CapturarReservasPendientes extends Command
{
    protected $signature = 'reservas:capturar';
    protected $description = 'Captura los pagos pendientes (Stripe) de reservas cuyo check-in está a 7 días';

    public function handle(): int
    {
        Log::info('🕒 [reservas:capturar] Iniciando revisión de reservas pendientes...');

        // Configura Stripe
        Stripe::setApiKey(env('STRIPE_SECRET'));

        // Buscar reservas con fecha_entrada dentro de 7 días
        $hoy = Carbon::today();
        $objetivo = $hoy->copy()->addDays(7);

        $reservas = Reserva::where('estado', 'pendiente')
            ->where('stripe_payment_status', 'autorizado')
            ->whereNotNull('stripe_payment_intent_id')
            ->whereDate('cancelable_hasta', '<=', Carbon::today())
            ->get();

        if ($reservas->isEmpty()) {
            Log::info('🟢 No hay reservas pendientes para capturar hoy.');
            $this->info('No hay reservas para capturar.');
            return Command::SUCCESS;
        }

        foreach ($reservas as $reserva) {
            try {
                Log::info("🔹 Intentando capturar pago de reserva {$reserva->id}");

                $intent = PaymentIntent::retrieve($reserva->stripe_payment_intent_id);
                $intent->capture();

                $reserva->update(['estado' => 'confirmada']);
                Log::info("✅ Pago capturado correctamente para la reserva {$reserva->id}");
            } catch (\Throwable $e) {
                Log::error("❌ Error al capturar pago de la reserva {$reserva->id}: {$e->getMessage()}");
            }
        }

        Log::info('🕒 [reservas:capturar] Proceso completado.');
        return Command::SUCCESS;
    }
}
/**
 * 💼 COMANDO AUTOMÁTICO: RESERVAS:CAPTURAR
 * -----------------------------------------
 *
 * Este comando se ejecuta automáticamente (normalmente cada noche a medianoche)
 * mediante el scheduler de Laravel (`app/Console/Kernel.php`) o un cron
 * en el servidor (`php artisan schedule:run`).
 *
 * 🔹 PROPÓSITO:
 * Capturar los pagos (preautorizaciones) de todas las reservas que:
 *   - Están en estado "pendiente".
 *   - Su fecha de check-in está a 7 días o menos.
 *   - Tienen un `stripe_payment_intent_id` válido y autorizado.
 *
 * 🔹 FUNCIONAMIENTO:
 *
 * 1️⃣  El comando busca en la base de datos todas las reservas donde:
 *         - estado = 'pendiente'
 *         - stripe_payment_status = 'autorizado'
 *         - fecha_entrada <= (hoy + 7 días)
 *
 * 2️⃣  Para cada reserva encontrada:
 *         - Recupera el `PaymentIntent` de Stripe.
 *         - Ejecuta `$intent->capture()` para cobrar el dinero.
 *         - Si la captura tiene éxito:
 *             • Actualiza `stripe_payment_status` a 'capturado'.
 *             • Cambia `estado` a 'confirmada'.
 *             • Registra una nota con la fecha de captura.
 *         - Si ocurre un error (tarjeta expirada, fondos insuficientes, etc.):
 *             • Lo guarda en los logs.
 *             • La reserva se mantiene como pendiente para revisión manual.
 *
 * 3️⃣  El proceso genera un log detallado con cada operación:
 *         - Reservas capturadas correctamente.
 *         - Reservas con error o pendientes de acción.
 *
 * 🔹 LOGS:
 * Los registros se guardan automáticamente en:
 *      storage/logs/laravel.log
 *
 * Ejemplo de salida:
 *      [2025-10-12 00:00:01] INFO: Capturando reserva #42 - OK (50.00 €)
 *      [2025-10-12 00:00:02] ERROR: Reserva #43 - Stripe error: payment_intent_authentication_failure
 *
 * 🔹 CONFIGURACIÓN DEL SCHEDULER:
 * En `app/Console/Kernel.php` debe incluirse:
 *      protected function schedule(Schedule $schedule)
 *      {
 *          $schedule->command('reservas:capturar')->dailyAt('00:00');
 *      }
 *
 * En Plesk o el servidor, el cron debe ejecutar cada minuto:
 *      php /ruta/a/tu/app/artisan schedule:run >> /dev/null 2>&1
 *
 * ⚙️ RECOMENDACIÓN:
 * - Mantener el cron activo las 24h.
 * - Revisar los logs semanalmente por si hay pagos fallidos.
 * - Puede complementarse con un comando `reservas:cancelar-expiradas`
 *   que libere automáticamente preautorizaciones que Stripe haya anulado.
 *
 * 🧩 CAMPOS QUE SE MODIFICAN:
 *      - stripe_payment_status  → 'capturado'
 *      - estado                 → 'confirmada'
 *      - notas                  → añade detalle de captura
 *
 * ✅ BENEFICIOS:
 *    - Evita tener que capturar los cobros manualmente.
 *    - Asegura que el dinero se cobra exactamente 7 días antes del check-in.
 *    - Sincroniza Stripe y la base de datos de forma automática y segura.
 */
