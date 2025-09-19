{{-- resources/views/layouts/navigation.blade.php --}}
<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <style>[x-cloak]{display:none!important}</style>

    @php
        /** @var \App\Models\User|null $user */
        $user      = auth()->user();
        $isCliente = $user?->hasRole('cliente');
        $isStaff   = $user?->hasRole('gerente') || $user?->hasRole('adm');

        $roleLabel = match($user?->role){
            'gerente' => 'Gerente',
            'adm'     => 'Adm',
            'cliente' => 'Cliente',
            default   => null,
        };
    @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                {{-- sem logo/ícone --}}

                {{-- DESKTOP --}}
                <div class="hidden sm:flex sm:items-center sm:-my-px space-x-8">
                    {{-- CARRINHO --}}
                    @if($isCliente)
                        {{-- Cliente: sem submenu (links diretos) --}}
                        <x-nav-link class="h-16" :href="route('cart.index')" :active="request()->routeIs('cart.*')">Carrinho</x-nav-link>
                        @if(Route::has('cliente.pedidos.index'))
                            <x-nav-link class="h-16" :href="route('cliente.pedidos.index')" :active="request()->routeIs('cliente.pedidos.*')">Meus pedidos</x-nav-link>
                        @endif
                        @if(Route::has('enderecos.index'))
                            <x-nav-link class="h-16" :href="route('enderecos.index')" :active="request()->routeIs('enderecos.*')">Cadastro endereço</x-nav-link>
                        @endif
                    @else
                        {{-- Guest / Staff: com submenu --}}
                        <div x-data="{drop:false}"
                             @mouseenter="drop=true" @mouseleave="drop=false" @click.outside="drop=false"
                             class="relative sm:flex sm:items-center">
                            <x-nav-link class="h-16"
                                        :href="route('cart.index')"
                                        :active="request()->routeIs('cart.*')"
                                        aria-haspopup="true"
                                        x-bind:aria-expanded="drop ? 'true' : 'false'">
                                Carrinho
                            </x-nav-link>
                            <div x-cloak x-show="drop"
                                 class="absolute left-0 top-full mt-0 w-56 rounded-md bg-white shadow border z-50">
                                <div class="py-1">
                                    @auth
                                        @if(Route::has('cliente.pedidos.index'))
                                            <a href="{{ route('cliente.pedidos.index') }}"
                                               class="block px-4 py-2 text-sm hover:bg-gray-50">Meus pedidos</a>
                                        @endif
                                        @if(Route::has('enderecos.index'))
                                            <a href="{{ route('enderecos.index') }}"
                                               class="block px-4 py-2 text-sm hover:bg-gray-50">Cadastro endereço</a>
                                        @endif
                                    @else
                                        <a href="{{ route('login') }}"
                                           class="block px-4 py-2 text-sm hover:bg-gray-50">Entrar para ver pedidos</a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- DASHBOARD (staff) --}}
                    @if($isStaff && Route::has('dashboard'))
                        <x-nav-link class="h-16" :href="route('dashboard')" :active="request()->routeIs('dashboard')">Dashboard</x-nav-link>
                    @endif

                    {{-- ENCOMENDAS (gerente) --}}
                    @if($user?->hasRole('gerente') && Route::has('gerente.pedidos.index'))
                        <x-nav-link class="h-16" :href="route('gerente.pedidos.index')" :active="request()->routeIs('gerente.pedidos.*')">Encomendas</x-nav-link>
                    @endif

                    {{-- CATÁLOGO (staff) --}}
                    @if($isStaff && Route::has('adm.produtos.index'))
                        <div x-data="{drop:false}"
                             @mouseenter="drop=true" @mouseleave="drop=false" @click.outside="drop=false"
                             class="relative sm:flex sm:items-center">
                            <x-nav-link class="h-16"
                                        :href="route('adm.produtos.index')"
                                        :active="request()->routeIs('adm.produtos.*')"
                                        aria-haspopup="true"
                                        x-bind:aria-expanded="drop ? 'true' : 'false'">
                                Catálogo
                            </x-nav-link>
                            <div x-cloak x-show="drop"
                                 class="absolute left-0 top-full mt-0 w-56 rounded-md bg-white shadow border z-50">
                                <div class="py-1">
                                    @if(Route::has('adm.produtos.create'))
                                        <a href="{{ route('adm.produtos.create') }}"
                                           class="block px-4 py-2 text-sm hover:bg-gray-50">Novo produto</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- RELATÓRIOS (gerente) --}}
                    @if($user?->hasRole('gerente') && Route::has('gerente.relatorios.index'))
                        <x-nav-link class="h-16" :href="route('gerente.relatorios.index')" :active="request()->routeIs('gerente.relatorios.*')">Relatórios</x-nav-link>
                    @endif

                    {{-- SAC (todos logados) --}}
                    @if($user?->hasRole(['cliente','adm','gerente']) && Route::has('cliente.sac.index'))
                        <x-nav-link class="h-16" :href="route('cliente.sac.index')" :active="request()->routeIs('cliente.sac.*')">SAC</x-nav-link>
                    @endif

                    {{-- USUÁRIOS (gerente) --}}
                    @if($user?->hasRole('gerente') && Route::has('gerente.usuarios.index'))
                        <x-nav-link class="h-16" :href="route('gerente.usuarios.index')" :active="request()->routeIs('gerente.usuarios.*')">Usuários</x-nav-link>
                    @endif

                    {{-- VITRINE (público) --}}
                    <x-nav-link class="h-16" :href="url('/')" :active="request()->is('/')">Vitrine</x-nav-link>
                </div>
            </div>

            {{-- PERFIL / LOGIN (direita) --}}
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @auth
                    <x-dropdown align="right" width="56">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition" title="Perfil">
                                <div>
                                    {{ $user?->name }}
                                    @if($roleLabel)
                                        <span class="ml-1 text-xs text-gray-500">({{ $roleLabel }})</span>
                                    @endif
                                </div>
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
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
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

            {{-- HAMBURGUER (mobile) --}}
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none transition" aria-label="Abrir menu">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- MOBILE --}}
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden" x-data="{ cartOpen:false, catalogOpen:false }">
        <div class="pt-2 pb-3 space-y-1">
            {{-- Carrinho / Meus pedidos / Endereço --}}
            @if($isCliente)
                <x-responsive-nav-link :href="route('cart.index')" :active="request()->routeIs('cart.*')">Carrinho</x-responsive-nav-link>
                @if(Route::has('cliente.pedidos.index'))
                    <x-responsive-nav-link :href="route('cliente.pedidos.index')" :active="request()->routeIs('cliente.pedidos.*')">Meus pedidos</x-responsive-nav-link>
                @endif
                @if(Route::has('enderecos.index'))
                    <x-responsive-nav-link :href="route('enderecos.index')" :active="request()->routeIs('enderecos.*')">Cadastro endereço</x-responsive-nav-link>
                @endif
            @else
                {{-- Grupo com submenu (guest/staff) --}}
                <x-responsive-nav-link :href="route('cart.index')" :active="request()->routeIs('cart.*')"
                                       @click.prevent="cartOpen=!cartOpen"
                                       x-bind:aria-expanded="cartOpen ? 'true' : 'false'">
                    Carrinho
                </x-responsive-nav-link>
                <div x-show="cartOpen" x-cloak class="pl-4 space-y-1">
                    @auth
                        @if(Route::has('cliente.pedidos.index'))
                            <x-responsive-nav-link :href="route('cliente.pedidos.index')" :active="request()->routeIs('cliente.pedidos.*')">Meus pedidos</x-responsive-nav-link>
                        @endif
                        @if(Route::has('enderecos.index'))
                            <x-responsive-nav-link :href="route('enderecos.index')" :active="request()->routeIs('enderecos.*')">Cadastro endereço</x-responsive-nav-link>
                        @endif
                    @endauth
                </div>
            @endif

            {{-- Dashboard --}}
            @if($isStaff && Route::has('dashboard'))
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">Dashboard</x-responsive-nav-link>
            @endif

            {{-- Encomendas --}}
            @if($user?->hasRole('gerente') && Route::has('gerente.pedidos.index'))
                <x-responsive-nav-link :href="route('gerente.pedidos.index')" :active="request()->routeIs('gerente.pedidos.*')">Encomendas</x-responsive-nav-link>
            @endif

            {{-- Catálogo (grupo staff) --}}
            @if($isStaff && Route::has('adm.produtos.index'))
                <x-responsive-nav-link :href="route('adm.produtos.index')" :active="request()->routeIs('adm.produtos.*')"
                                       @click.prevent="catalogOpen=!catalogOpen"
                                       x-bind:aria-expanded="catalogOpen ? 'true' : 'false'">
                    Catálogo
                </x-responsive-nav-link>
                <div x-show="catalogOpen" x-cloak class="pl-4 space-y-1">
                    @if(Route::has('adm.produtos.create'))
                        <x-responsive-nav-link :href="route('adm.produtos.create')" :active="request()->routeIs('adm.produtos.create')">Novo produto</x-responsive-nav-link>
                    @endif
                </div>
            @endif

            {{-- Relatórios --}}
            @if($user?->hasRole('gerente') && Route::has('gerente.relatorios.index'))
                <x-responsive-nav-link :href="route('gerente.relatorios.index')" :active="request()->routeIs('gerente.relatorios.*')">Relatórios</x-responsive-nav-link>
            @endif

            {{-- SAC --}}
            @if($user?->hasRole(['cliente','adm','gerente']) && Route::has('cliente.sac.index'))
                <x-responsive-nav-link :href="route('cliente.sac.index')" :active="request()->routeIs('cliente.sac.*')">SAC</x-responsive-nav-link>
            @endif

            {{-- Usuários --}}
            @if($user?->hasRole('gerente') && Route::has('gerente.usuarios.index'))
                <x-responsive-nav-link :href="route('gerente.usuarios.index')" :active="request()->routeIs('gerente.usuarios.*')">Usuários</x-responsive-nav-link>
            @endif

            {{-- Vitrine --}}
            <x-responsive-nav-link :href="url('/')" :active="request()->is('/')">Vitrine</x-responsive-nav-link>
        </div>

        @auth
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800">
                        {{ $user?->name }}
                        @if($roleLabel)
                            <span class="ml-1 text-xs text-gray-500">({{ $roleLabel }})</span>
                        @endif
                    </div>
                    <div class="font-medium text-sm text-gray-500">{{ $user?->email }}</div>
                </div>
                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">Perfil</x-responsive-nav-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                            Sair
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @endauth
    </div>
</nav>
