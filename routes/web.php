<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HabitacionController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\GaleriaController;
use App\Http\Controllers\GaleriaImagenController;

Route::get('/', function () {
    return view('dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');


// Perfil (edición pública, si es intencionado)
Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

// Habitaciones
Route::get('/habitaciones/{habitacion}/eventos', [HabitacionController::class, 'eventosDisponibilidad'])
    ->name('habitaciones.eventos');
Route::get('/habitaciones/{habitacion}/precio', [HabitacionController::class, 'calcularPrecio'])
    ->name('habitaciones.precio');

Route::get('/habitaciones/disponibles', [HabitacionController::class, 'disponibles'])
    ->name('habitaciones.disponibles');

Route::get('/habitaciones/calendario-precios', [HabitacionController::class, 'vistaCalendarioPrecios'])
    ->name('habitaciones.calendarioPrecios');

Route::get('/api/precios', [HabitacionController::class, 'precios'])
    ->name('api.precios');
Route::post('/api/precios/update', [HabitacionController::class, 'updatePrecio'])
    ->name('api.precios.update');
Route::post('/api/precios/bulk-update', [HabitacionController::class, 'bulkUpdate'])
    ->name('api.precios.bulkUpdate');
Route::patch('/habitaciones/{habitacion}/bloqueo', [HabitacionController::class, 'toggleBloqueo'])
    ->name('habitaciones.toggle-bloqueo');

// 👇 ESTE SIEMPRE AL FINAL
Route::resource('habitaciones', HabitacionController::class);

// 👇 Y ESTE DESPUÉS DEL RESOURCE
Route::post('/habitaciones/{habitacion}/imagenes', [HabitacionController::class, 'subirImagenes'])
    ->name('habitaciones.imagenes');
Route::delete('/habitacion-imagenes/{id}', [HabitacionController::class, 'eliminarImagen'])
    ->name('habitaciones.imagenes.eliminar');
// Páginas adicionales
Route::get('/instalaciones', [HabitacionController::class, 'instalaciones'])
    ->name('instalaciones.index');

Route::get('/entorno', [HabitacionController::class, 'entorno'])
    ->name('entorno.index');
Route::get('/contacto', [HabitacionController::class, 'contacto'])->name('contacto.index');
Route::post('/contacto', [HabitacionController::class, 'enviarContacto'])->name('contacto.enviar');

// Clientes
Route::resource('clientes', ClienteController::class);

// Reservas
Route::resource('reservas', ReservaController::class);
Route::get('/api/reservas', [ReservaController::class, 'apiReservas'])->name('api.reservas');
Route::post('/pago/reserva', [ReservaController::class, 'procesarPago'])->name('pago.reserva');

Route::match(['get', 'post'], '/mis-reservas', [ReservaController::class, 'buscar'])->name('reservas.buscar');
Route::delete('/reservas/{reserva}/cancelar', [ReservaController::class, 'cancelarReserva'])->name('reservas.cancelar');
Route::post('/reservas/check/{tipo}', [ReservaController::class, 'marcarCheck'])
    ->whereIn('tipo', ['in', 'out', 'auto'])
    ->name('reservas.check')
    ->middleware('auth');

// Galerías
Route::get('/galerias', [GaleriaController::class, 'index'])->name('instalaciones.index');
Route::post('/galerias', [GaleriaController::class, 'store'])->name('galerias.store');

// Acciones que requieren sesión
Route::middleware('auth')->group(function () {
    Route::patch('/galerias/{galeria}', [GaleriaController::class, 'update'])->name('galerias.update');

    // Imágenes de una galería concreta
    Route::post('/galerias/{galeria}/imagenes', [GaleriaImagenController::class, 'store'])
        ->name('galerias.imagenes.store');

    Route::delete('/galerias/imagenes/{imagen}', [GaleriaImagenController::class, 'destroy'])
        ->name('galerias.imagenes.destroy');
});

require __DIR__ . '/auth.php';
