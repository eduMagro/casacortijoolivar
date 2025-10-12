<x-mail::layout>
    {{-- Header --}}
    <x-slot:header>
        <a href="{{ config('app.url') }}">
            <img src="https://tudominio.es/logo.png" alt="Casa Cortijo Olivar" style="height: 60px;">
        </a>

    </x-slot:header>

    {{-- Body --}}
    {{ $slot }}

    {{-- Subcopy --}}
    @isset($subcopy)
        <x-slot:subcopy>
            <x-mail::subcopy>
                {{ $subcopy }}
            </x-mail::subcopy>
        </x-slot:subcopy>
    @endisset

    {{-- Footer --}}
    <x-slot:footer>
        <x-mail::footer>
            © {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.')
        </x-mail::footer>
    </x-slot:footer>
</x-mail::layout>
