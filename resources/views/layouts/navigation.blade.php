<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ url('/') }}" title="Vitrine">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                @php($user = auth()->user())

                <!-- Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link :href="url('/')" :active="request()->is('/')">Vitrine</x-nav-link>

                    @auth
                        {{-- DASHBOARD: só adm/gerente --}}
                        @if($user->hasRole(['adm','gerente']))
                            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">Dashboard</x-nav-link>
                        @endif

                        @if($user->hasRole(['cliente','adm','gerente']) && Route::has('cliente.pedidos.index'))
                            <x-nav-link :href="route('cliente.pedidos.index')" :active="request()->routeIs('cliente.pedidos.*')">Meus Pedidos</x-nav-link>
                        @endif

                        @if($user->hasRole(['cliente','adm','gerente']) && Route::has('cliente.sac.index'))
                            <x-nav-link :href="route('cliente.sac.index')" :active="request()->routeIs('cliente.sac.*')">SAC</x-nav-link>
                        @endif

                        @if($user->hasRole(['adm','gerente']) && Route::has('adm.produtos.index'))
                            <x-nav-link :href="route('adm.produtos.index')" :active="request()->routeIs('adm.produtos.*')">Catálogo</x-nav-link>
                        @endif

                        @if($user->hasRole('gerente') && Route::has('gerente.usuarios.index'))
                            <x-nav-link :href="route('gerente.usuarios.index')" :active="request()->routeIs('gerente.usuarios.*')">Usuários</x-nav-link>
                        @endif

                        @if($user->hasRole('gerente') && Route::has('gerente.pedidos.index'))
                            <x-nav-link :href="route('gerente.pedidos.index')" :active="request()->routeIs('gerente.pedidos.*')">
                                Pedidos (gerente)
                            </x-nav-link>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Direita -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @auth
                    <!-- Settings Dropdown -->
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition">
                                <div>{{ $user?->name }}</div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">Perfil</x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    Sair
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @endauth

                @guest
                    <div class="flex items-center gap-3">
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">Entrar</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Cadastrar</a>
                        @endif
                    </div>
                @endguest
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Menu responsivo -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="url('/')" :active="request()->is('/')">Vitrine</x-responsive-nav-link>

            @auth
                {{-- DASHBOARD: só adm/gerente --}}
                @if($user->hasRole(['adm','gerente']))
                    <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">Dashboard</x-responsive-nav-link>
                @endif

                @if($user->hasRole(['cliente','adm','gerente']) && Route::has('cliente.pedidos.index'))
                    <x-responsive-nav-link :href="route('cliente.pedidos.index')" :active="request()->routeIs('cliente.pedidos.*')">Meus Pedidos</x-responsive-nav-link>
                @endif

                @if($user->hasRole(['cliente','adm','gerente']) && Route::has('cliente.sac.index'))
                    <x-responsive-nav-link :href="route('cliente.sac.index')" :active="request()->routeIs('cliente.sac.*')">SAC</x-responsive-nav-link>
                @endif

                @if($user->hasRole(['adm','gerente']) && Route::has('adm.produtos.index'))
                    <x-responsive-nav-link :href="route('adm.produtos.index')" :active="request()->routeIs('adm.produtos.*')">Catálogo</x-responsive-nav-link>
                @endif

                @if($user->hasRole('gerente') && Route::has('gerente.usuarios.index'))
                    <x-responsive-nav-link :href="route('gerente.usuarios.index')" :active="request()->routeIs('gerente.usuarios.*')">Usuários</x-responsive-nav-link>
                @endif

                @if($user->hasRole('gerente') && Route::has('gerente.pedidos.index'))
                    <x-responsive-nav-link :href="route('gerente.pedidos.index')" :active="request()->routeIs('gerente.pedidos.*')">
                        Pedidos (gerente)
                    </x-responsive-nav-link>
                @endif
            @endauth

            @guest
                <x-responsive-nav-link :href="route('login')">Entrar</x-responsive-nav-link>
                @if (Route::has('register'))
                    <x-responsive-nav-link :href="route('register')">Cadastrar</x-responsive-nav-link>
                @endif
            @endguest
        </div>

        @auth
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800">{{ $user?->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ $user?->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">Perfil</x-responsive-nav-link>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                            Sair
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @endauth
    </div>
</nav>
