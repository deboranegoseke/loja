<x-app-layout>
    <x-slot name="header">
        @php($user = auth()->user())
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
        
            </h2>
            <h6>resources\views\dashboard.blade.php</h6>
            {{-- Navegação rápida --}}

            <nav class="flex flex-wrap items-center gap-2">
                <a href="{{ url('/') }}" class="px-3 py-1.5 rounded-full text-sm bg-gray-100 hover:bg-gray-200">Vitrine</a>

                @if($user->hasRole(['adm','gerente']) && Route::has('cliente.sac.index'))
                    <a href="{{ route('cliente.sac.index') }}" class="px-3 py-1.5 rounded-full text-sm bg-gray-100 hover:bg-gray-200">SAC</a>
                @endif

                @if($user->hasRole(['adm','gerente']) && Route::has('adm.produtos.index'))
                    <a href="{{ route('adm.produtos.index') }}" class="px-3 py-1.5 rounded-full text-sm bg-gray-100 hover:bg-gray-200">Catálogo</a>
                @endif

                @if($user->hasRole('gerente') && Route::has('gerente.usuarios.index'))
                    <a href="{{ route('gerente.usuarios.index') }}" class="px-3 py-1.5 rounded-full text-sm bg-gray-100 hover:bg-gray-200">Usuários</a>
                @endif

                @if($user->hasRole('gerente') && Route::has('gerente.pedidos.index'))
                    <a href="{{ route('gerente.pedidos.index') }}" class="px-3 py-1.5 rounded-full text-sm bg-gray-100 hover:bg-gray-200">
                        Pedidos (gerente)
                    </a>
                @endif
            </nav>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-4 rounded-lg bg-green-50 text-green-800 px-4 py-3 border border-green-200">
                    {{ session('status') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="mb-4 rounded-lg bg-red-50 text-red-800 px-4 py-3 border border-red-200">
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- Ações rápidas para staff --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 flex flex-wrap gap-3">
                    @if(Route::has('adm.produtos.create'))
                        <a href="{{ route('adm.produtos.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                            Cadastrar produto
                        </a>
                    @endif
                    @if(Route::has('adm.produtos.index'))
                        <a href="{{ route('adm.produtos.index') }}" class="inline-flex items-center rounded-md border px-3 py-2 text-sm font-medium hover:bg-gray-50">
                            Ver todos os produtos
                        </a>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
