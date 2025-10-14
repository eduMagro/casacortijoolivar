<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-10">
        <div class="flex justify-between items-center h-16">

            <!-- Left Section -->
            <div class="flex items-center space-x-10">
                <!-- Logo -->
                <a href="{{ url('/') }}" class="flex items-center space-x-2">
                    <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                </a>


                <!-- Navigation Links -->
                <div class="hidden sm:flex sm:space-x-8">
                    <x-nav-link :href="url('/')" :active="request()->is('/')">
                        Inicio
                    </x-nav-link>

                    <x-nav-link :href="route('habitaciones.index')" :active="request()->routeIs('habitaciones.index')">
                        Habitaciones
                    </x-nav-link>

                    {{-- Nueva sección: Instalaciones --}}
                    <x-nav-link :href="route('instalaciones.index')" :active="request()->routeIs('instalaciones.index')">
                        Instalaciones
                    </x-nav-link>

                    {{-- Nueva sección: Entorno --}}
                    <x-nav-link :href="route('entorno.index')" :active="request()->routeIs('entorno.index')">
                        Entorno
                    </x-nav-link>

                    <x-nav-link :href="route('reservas.buscar')" :active="request()->routeIs('reservas.buscar')">
                        Mis reservas
                    </x-nav-link>

                    @auth
                        <x-nav-link :href="route('reservas.index')" :active="request()->routeIs('reservas.index')">
                            Reservas
                        </x-nav-link>
                    @endauth
                </div>

            </div>

            <!-- Right Section -->
            <div class="hidden sm:flex sm:items-center space-x-6">
                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-600 bg-transparent hover:text-gray-900 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()->name }}</div>
                                <svg class="ml-1 w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                Perfil
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    Cerrar sesión
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <div class="flex items-center space-x-5">
                        <x-nav-link :href="route('login')" :active="request()->routeIs('login')">
                            Iniciar sesión
                        </x-nav-link>
                        @auth
                            <x-nav-link :href="route('register')" :active="request()->routeIs('register')">
                                Registrarse
                            </x-nav-link>
                        @endauth

                    </div>
                @endauth
            </div>

            <!-- Hamburger (mobile) -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="url('/')" :active="request()->is('/')">
                Inicio
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('habitaciones.index')" :active="request()->routeIs('habitaciones.index')">
                Habitaciones
            </x-responsive-nav-link>

            {{-- Nueva sección: Instalaciones --}}
            <x-responsive-nav-link :href="route('instalaciones.index')" :active="request()->routeIs('instalaciones.index')">
                Instalaciones
            </x-responsive-nav-link>

            {{-- Nueva sección: Entorno --}}
            <x-responsive-nav-link :href="route('entorno.index')" :active="request()->routeIs('entorno.index')">
                Entorno
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('reservas.buscar')" :active="request()->routeIs('reservas.buscar')">
                Mis reservas
            </x-responsive-nav-link>

            @auth
                <x-responsive-nav-link :href="route('reservas.index')" :active="request()->routeIs('reservas.index')">
                    Reservas
                </x-responsive-nav-link>
            @endauth
        </div>


        <!-- Responsive Auth Settings -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            @auth
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Perfil') }}
                    </x-responsive-nav-link>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Cerrar sesión') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            @else
                <div class="space-y-1 px-4">
                    <x-responsive-nav-link :href="route('login')" :active="request()->routeIs('login')">
                        Iniciar sesión
                    </x-responsive-nav-link>
                    @auth
                        <x-responsive-nav-link :href="route('register')" :active="request()->routeIs('register')">
                            Registrarse
                        </x-responsive-nav-link>
                    @endauth

                </div>
            @endauth
        </div>
    </div>
</nav>
