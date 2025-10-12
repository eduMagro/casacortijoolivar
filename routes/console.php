<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ===========================================================
// 🕒 Programación de tareas automáticas (Laravel 11+ compatible)
// ===========================================================

// Ejecutar el comando 'reservas:capturar' cada día a las 03:00
Schedule::command('reservas:capturar')
    ->dailyAt('03:00')
    ->withoutOverlapping()
    ->sendOutputTo(storage_path('logs/scheduler.log'));

// (Opcional) Registrar aquí otros comandos Artisan que quieras planificar
// Schedule::command('emails:recordatorios')->dailyAt('09:00');

// ===========================================================
// 🧰 Comprobación manual (para desarrollo)
// ===========================================================

// Puedes probar el comando manualmente con:
// php artisan reservas:capturar
//
// O listar todas las tareas programadas con:
// php artisan schedule:list
