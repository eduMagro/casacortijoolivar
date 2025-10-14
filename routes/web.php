<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HabitacionController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ReservaController;

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

/*
|--------------------------------------------------------------------------
| Rutas de autenticación (login, registro, etc.)
|--------------------------------------------------------------------------
*/
// Si decides luego proteger:
Route::middleware('auth')->group(function () {
    // Aquí metes las rutas sensibles
});


require __DIR__ . '/auth.php';
