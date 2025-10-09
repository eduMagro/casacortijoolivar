@props(['tipo' => 'success'])

@php
    $colores = [
        'success' => 'bg-green-100 text-green-800 border-green-400',
        'error' => 'bg-red-100 text-red-800 border-red-400',
    ];

    $clase = $colores[$tipo] ?? $colores['success'];
@endphp

<div x-data="{ show: true }" x-show="show"
    {{ $attributes->merge([
        'class' => "border-l-4 p-4 rounded mb-4 {$clase}",
    ]) }}>
    <div class="flex justify-between items-start gap-4">
        <div>{{ $slot }}</div>
        <button @click="show = false" class="text-xl leading-none font-bold">&times;</button>
    </div>
</div>
