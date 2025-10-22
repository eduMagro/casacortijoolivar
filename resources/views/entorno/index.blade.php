<x-app-layout>
    <x-slot name="title">Entorno</x-slot>

    <section class="max-w-6xl mx-auto py-16 px-6 space-y-12">
        <header class="text-center">
            <h1 class="text-4xl font-extrabold text-gray-800 mb-4 tracking-tight">
                Entorno de Casa Cortijo Olivar
            </h1>
            <p class="text-lg text-gray-600 max-w-3xl mx-auto leading-relaxed">
                Observa el relieve y la orografía que rodean el cortijo. Este mapa muestra el terreno con
                detalles topográficos reales para que te hagas una idea del entorno natural que te espera.
            </p>
        </header>

        <!-- 🌋 Mapa en modo terreno -->
        <div class="rounded-2xl overflow-hidden shadow-2xl border border-gray-200">
            <iframe width="100%" height="500" style="border:0; border-radius: 1rem;" loading="lazy" allowfullscreen
                src="https://www.google.com/maps?q=37.30666954584435,-5.965819944154808&t=h&z=14&hl=es&output=embed">
            </iframe>
        </div>

        <p class="text-center text-gray-500 text-sm">
            Usa el control de zoom para acercarte y explorar los desniveles del terreno o los cauces naturales
            cercanos al cortijo.
        </p>

        <!-- 🌿 Contenido fijo del entorno -->
        <article class="prose prose-lg max-w-none text-gray-700 leading-relaxed mx-auto">
            <p>
                En <strong>Casa Cortijo Olivar</strong> disfrutarás de un alojamiento cómodo y acogedor,
                situado en una ubicación privilegiada, ideal para explorar lo mejor de Sevilla y sus alrededores.
            </p>

            <h2>Atracciones turísticas</h2>
            <ul>
                <li><strong>Plaza de España</strong>: a solo 9,1 km, un ícono de Sevilla con su arquitectura
                    renacentista y azulejos coloridos.</li>
                <li><strong>Parque de María Luisa</strong>: a 9,3 km, perfecto para pasear y disfrutar de la naturaleza.
                </li>
                <li><strong>Real Alcázar de Sevilla</strong>: a 9 km, majestuoso palacio mudéjar Patrimonio de la
                    Humanidad.</li>
                <li><strong>Archivo de Indias</strong>: también a 9 km, un tesoro de la historia colonial española.</li>
                <li><strong>Puente de Triana – Isabel II</strong>: a 10 km, une el centro con Triana, famoso por su
                    ambiente y gastronomía.</li>
            </ul>

            <h2>Espacios naturales</h2>
            <ul>
                <li><strong>Parque Jardines de Hércules</strong>: a 3,2 km, ideal para relajarte al aire libre.</li>
                <li><strong>Parque de la Alquería Dos Hermanas</strong>: a 5 km, con zonas familiares y de ejercicio.
                </li>
                <li><strong>Río Guadaíra</strong>: a unos 6–7 km, ideal para caminar junto al agua o practicar deportes
                    acuáticos.</li>
            </ul>

            <h2>Ocio y gastronomía</h2>
            <ul>
                <li><strong>Cafeterías y bares</strong>: a 4,5–5 km — Union Bar, Bar Rafael y Anká Er Curro, auténticos
                    puntos locales.</li>
                <li><strong>Acuario Sevilla</strong>: a 8 km, una excelente opción familiar para descubrir la vida
                    marina.</li>
            </ul>

            <h2>Transporte y accesibilidad</h2>
            <p>
                Casa Cortijo Olivar se encuentra a 17 km del Aeropuerto de Sevilla, con estaciones de tren y metro
                cercanas
                (Bellavista y La Salud, a 2,7 y 3 km respectivamente).
            </p>

            <p class="mt-6 text-lg font-medium text-olive-700">
                Te esperamos en Casa Cortijo Olivar, donde la comodidad y la ubicación se unen para ofrecerte
                una experiencia inolvidable en Sevilla.
            </p>
        </article>
    </section>
</x-app-layout>
