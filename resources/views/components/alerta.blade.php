@props(['tipo' => 'success', 'mensaje' => ''])

@php
    $colores = [
        'success' => 'alerta-success',
        'error' => 'alerta-error',
    ];

    $iconos = [
        'success' => '',
        'error' => '',
    ];

    $clase = $colores[$tipo] ?? $colores['success'];
    $icono = $iconos[$tipo] ?? 'ℹ️';
@endphp

<div x-data="{ show: true }" x-show="show" x-transition.opacity.duration.400ms class="alerta-overlay">
    <div class="alerta-base {{ $clase }}" x-show="show" x-transition:enter="opacity-0 scale-90"
        x-transition:enter-end="opacity-100 scale-100" x-transition:leave="opacity-0 scale-90">
        <div class="text-4xl mb-2">{{ $icono }}</div>
        <div class="text-center font-semibold text-lg mb-3">
            {{ $slot ?: $mensaje }}
        </div>
        <button @click="show = false" class="cerrar-alerta">
            Cerrar
        </button>
    </div>
</div>

<style>
    /* Fondo translúcido que cubre la pantalla */
    .alerta-overlay {
        position: fixed;
        inset: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        background-color: rgba(0, 0, 0, 0.25);
        backdrop-filter: blur(4px);
    }

    /* Caja base */
    .alerta-base {
        min-width: 320px;
        max-width: 420px;
        border-radius: 1rem;
        padding: 2rem 2.5rem;
        text-align: center;
        color: #fff;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
        backdrop-filter: blur(10px);
        transition: transform 0.3s ease, opacity 0.3s ease;
    }

    /* Tipos de alerta */
    .alerta-success {
        background: rgba(34, 197, 94, 0.9);
        /* verde */
        border: 1px solid rgba(21, 128, 61, 0.8);
    }

    .alerta-error {
        background: rgba(239, 68, 68, 0.9);
        /* rojo */
        border: 1px solid rgba(185, 28, 28, 0.8);
    }

    /* Botón de cierre */
    .cerrar-alerta {
        margin-top: 0.5rem;
        font-size: 0.9rem;
        font-weight: 600;
        background: transparent;
        color: #fff;
        border: 2px solid rgba(255, 255, 255, 0.4);
        padding: 0.4rem 1rem;
        border-radius: 0.5rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .cerrar-alerta:hover {
        background-color: rgba(255, 255, 255, 0.15);
        border-color: rgba(255, 255, 255, 0.6);
    }
</style>
