@props([
    'type' => 'text',
    'name',
    'id' => $name,
    'placeholder' => '',
    'value' => '',
])

<input type="{{ $type }}" name="{{ $name }}" id="{{ $id }}" value="{{ old($name, $value) }}"
    placeholder="{{ $placeholder }}"
    {{ $attributes->merge([
        'class' => 'w-full mt-1 px-4 py-2
                        bg-white/10 backdrop-blur-md
                        text-white placeholder-white/70
                        rounded-lg border border-white/20
                        focus:outline-none focus:ring-2 focus:ring-indigo-500
                        transition',
    ]) }} />
