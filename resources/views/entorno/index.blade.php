<x-app-layout>
    <x-slot name="title">Entorno y Geología</x-slot>

    <div class="max-w-6xl mx-auto py-10 px-4 space-y-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Geología y entorno del Cortijo Olivar</h1>

        <p class="text-gray-600 mb-6">
            Observa el relieve y la orografía que rodean el cortijo.
            Este mapa muestra el terreno con detalles topográficos reales.
        </p>

        <!-- 🌋 Mapa en modo terreno -->
        <div class="rounded-xl overflow-hidden shadow-lg border border-gray-300">
            <iframe width="100%" height="500" style="border:0; border-radius: 1rem;" loading="lazy" allowfullscreen
                src="https://www.google.com/maps?q=37.30666954584435,-5.965819944154808&t=h&z=14&hl=es&output=embed">
            </iframe>

        </div>

        <p class="text-gray-500 text-sm mt-3">
            Puedes usar el control de zoom para acercarte y ver los desniveles del terreno o los cauces naturales.
        </p>
    </div>
</x-app-layout>
